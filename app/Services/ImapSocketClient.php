<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class ImapSocketClient
{
    protected $socket = null;
    protected $tagCount = 1;

    /**
     * Connect to IMAP server using pure PHP SSL/TLS stream socket.
     *
     * @param string $host
     * @param int $port
     * @param string $encryption ('ssl', 'tls', or 'none')
     * @param int $timeout
     * @return bool
     * @throws Exception
     */
    public function connect($host, $port = 993, $encryption = 'ssl', $timeout = 15)
    {
        $prefix = ($encryption === 'ssl' || $port == 993) ? 'ssl://' : '';
        $remoteSocket = $prefix . $host . ':' . $port;

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ]
        ]);

        $this->socket = @stream_socket_client(
            $remoteSocket,
            $errno,
            $errstr,
            $timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$this->socket) {
            throw new Exception("Gagal terhubung ke server IMAP ({$remoteSocket}): {$errstr} ({$errno})");
        }

        stream_set_timeout($this->socket, $timeout);

        // Read server welcome banner
        $this->readLine();

        return true;
    }

    /**
     * Login to IMAP server.
     *
     * @param string $username
     * @param string $password
     * @return bool
     * @throws Exception
     */
    public function login($username, $password)
    {
        $cleanUser = addcslashes($username, '"\\');
        $cleanPass = addcslashes($password, '"\\');

        $response = $this->sendCommand("LOGIN \"{$cleanUser}\" \"{$cleanPass}\"");

        if (!$this->isOk($response)) {
            $err = $this->getResponseMessage($response);
            if (str_contains($err, 'Invalid credentials') || str_contains($err, 'AUTHENTICATIONFAILED') || str_contains($err, 'Bad credentials') || str_contains($err, '535')) {
                throw new Exception("Autentikasi IMAP Gagal. Pastikan Username dan Password email Anda sudah benar.");
            }
            throw new Exception("Autentikasi IMAP ditolak: " . $err);
        }

        return true;
    }

    /**
     * List all folders on the server.
     *
     * @return array
     */
    public function listFolders()
    {
        $response = $this->sendCommand('LIST "" "*"');
        $folders = [];

        foreach ($response as $line) {
            if (preg_match('/^\*\s+LIST\s+\((.*?)\)\s+("[^"]*"|\S+)\s+(.+)$/i', $line, $m)) {
                $flags = $m[1];
                $delimiter = trim($m[2], '" ');
                $folderName = trim($m[3], " \"\r\n");

                $folders[] = [
                    'name' => $folderName,
                    'flags' => $flags,
                    'delimiter' => $delimiter,
                ];
            }
        }

        return $folders;
    }

    /**
     * Auto-detect the Sent Mail folder name on the connected IMAP server.
     *
     * @return string|null
     */
    public function detectSentFolder()
    {
        $allFolders = $this->listFolders();

        foreach ($allFolders as $f) {
            if (stripos($f['flags'], '\\Sent') !== false) {
                return $f['name'];
            }
        }

        $candidates = [
            'INBOX.Sent',
            'Sent',
            '[Gmail]/Sent Mail',
            '[Gmail]/Pesan Terkirim',
            'Sent Items',
            'Sent Messages',
            'Terkirim'
        ];

        foreach ($candidates as $candidate) {
            foreach ($allFolders as $f) {
                if (strcasecmp($f['name'], $candidate) === 0) {
                    return $f['name'];
                }
            }
        }

        foreach ($candidates as $candidate) {
            $check = $this->selectFolder($candidate);
            if ($check['success'] && $check['exists'] > 0) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Select a mailbox folder.
     *
     * @param string $folder
     * @return array
     */
    public function selectFolder($folder = 'INBOX')
    {
        $cleanFolder = addcslashes($folder, '"\\');
        $response = $this->sendCommand("SELECT \"{$cleanFolder}\"");
        $exists = 0;

        foreach ($response as $line) {
            if (preg_match('/\* (\d+) EXISTS/i', $line, $m)) {
                $exists = (int)$m[1];
            }
        }

        return [
            'success' => $this->isOk($response),
            'exists' => $exists,
        ];
    }

    /**
     * Fetch recent emails safely from a folder in reverse chronological order.
     *
     * @param string $folder
     * @param int $limit
     * @return array
     */
    public function fetchRecentEmailsFromFolder($folder = 'INBOX', $limit = 20)
    {
        $folderInfo = $this->selectFolder($folder);
        if (!$folderInfo['success'] || $folderInfo['exists'] <= 0) {
            return [];
        }

        $total = $folderInfo['exists'];
        $start = max(1, $total - $limit + 1);

        $parsedEmails = [];

        // Fetch each email using lightweight slices (headers + body text + inline images)
        for ($msgNum = $total; $msgNum >= $start; $msgNum--) {
            try {
                $parsed = $this->fetchSingleEmailParsed($msgNum);
                if ($parsed && !empty($parsed['subject'])) {
                    $parsedEmails[] = $parsed;
                }
            } catch (Exception $e) {
                Log::warning("Error fetching IMAP message #{$msgNum}: " . $e->getMessage());
            }
        }

        return $parsedEmails;
    }

    /**
     * Fetch single email with memory safety.
     */
    protected function fetchSingleEmailParsed($msgNum)
    {
        $tag = 'TAG_M' . ($this->tagCount++);
        $cmd = "{$tag} FETCH {$msgNum} (BODY.PEEK[HEADER] BODY.PEEK[TEXT]<0.2500000>)\r\n";
        fwrite($this->socket, $cmd);

        $rawBuffer = '';
        $inLiteral = false;
        $literalBytesLeft = 0;

        while (!feof($this->socket)) {
            if ($inLiteral) {
                $readChunkSize = min(8192, $literalBytesLeft);
                $chunk = fread($this->socket, $readChunkSize);
                if ($chunk === false || strlen($chunk) === 0) break;

                $rawBuffer .= $chunk;
                $literalBytesLeft -= strlen($chunk);

                if ($literalBytesLeft <= 0) {
                    $inLiteral = false;
                }
                continue;
            }

            $line = fgets($this->socket, 4096);
            if ($line === false) break;

            if (preg_match('/\{(\d+)\}\r?\n$/', $line, $m)) {
                $literalBytesLeft = (int)$m[1];
                $inLiteral = true;
                continue;
            }

            if (strpos($line, $tag . ' ') === 0) {
                break;
            }

            if (strpos($line, '* ') !== 0) {
                $rawBuffer .= $line;
            }
        }

        return $this->parseMimeEmail($rawBuffer);
    }

    /**
     * Parse raw MIME message into structured array.
     */
    public function parseMimeEmail($rawEmail)
    {
        // Unfold multiline headers
        $unfolded = preg_replace("/\r?\n[ \t]+/", ' ', $rawEmail);

        // Extract Subject
        $subject = '(Tanpa Subjek)';
        if (preg_match('/^Subject:\s*(.*)$/mi', $unfolded, $sm)) {
            $subject = $this->decodeMimeStr(trim($sm[1]));
        }

        // Extract From
        $fromRaw = '';
        if (preg_match('/^From:\s*(.*)$/mi', $unfolded, $fm)) {
            $fromRaw = trim($fm[1]);
        }

        $senderName = 'Pengirim';
        $senderEmail = 'unknown@domain.com';

        if (preg_match('/(.*)<(.+?)>/', $fromRaw, $m)) {
            $senderName = $this->decodeMimeStr(trim($m[1], " \t\n\r\0\x0B\"'"));
            $senderEmail = trim($m[2]);
        } elseif (filter_var($fromRaw, FILTER_VALIDATE_EMAIL)) {
            $senderEmail = $fromRaw;
            $senderName = explode('@', $fromRaw)[0];
        } else {
            $senderName = $this->decodeMimeStr($fromRaw);
        }

        // Extract To / Recipient
        $toRaw = '';
        if (preg_match('/^To:\s*(.*)$/mi', $unfolded, $tm)) {
            $toRaw = trim($tm[1]);
        }

        $recipientName = 'Penerima';
        $recipientEmail = '';

        if (preg_match('/(.*)<(.+?)>/', $toRaw, $m)) {
            $recipientName = $this->decodeMimeStr(trim($m[1], " \t\n\r\0\x0B\"'"));
            $recipientEmail = trim($m[2]);
        } elseif (filter_var($toRaw, FILTER_VALIDATE_EMAIL)) {
            $recipientEmail = $toRaw;
            $recipientName = explode('@', $toRaw)[0];
        } else {
            $recipientName = $this->decodeMimeStr($toRaw);
            $recipientEmail = $toRaw;
        }

        // Extract CC & BCC
        $cc = null;
        if (preg_match('/^Cc:\s*(.*)$/mi', $unfolded, $cm)) {
            $cc = $this->decodeMimeStr(trim($cm[1]));
        }

        $bcc = null;
        if (preg_match('/^Bcc:\s*(.*)$/mi', $unfolded, $bcm)) {
            $bcc = $this->decodeMimeStr(trim($bcm[1]));
        }

        // Extract Message-ID
        $messageId = md5($fromRaw . $toRaw . $subject . substr($rawEmail, 0, 500));
        if (preg_match('/^Message-ID:\s*<([^>]+)>/mi', $unfolded, $mim)) {
            $messageId = trim($mim[1]);
        }

        // Extract Date
        $dateStr = 'now';
        if (preg_match('/^Date:\s*(.*)$/mi', $unfolded, $dm)) {
            $dateStr = trim($dm[1]);
        }
        $date = @strtotime($dateStr) ?: time();

        // Extract Content-Type
        $contentType = 'text/plain';
        if (preg_match('/^Content-Type:\s*([^;\r\n]+)/mi', $unfolded, $ctm)) {
            $contentType = strtolower(trim($ctm[1]));
        }

        // Extract Body (supporting nested boundaries, quoted-printable, base64, and inline CID images)
        $bodyExtracted = $this->extractMimeBodies($rawEmail);

        return [
            'message_id' => $messageId,
            'sender_name' => $senderName ?: 'Pengirim',
            'sender_email' => $senderEmail,
            'recipient_name' => $recipientName ?: 'Penerima',
            'recipient_email' => $recipientEmail,
            'cc' => $cc,
            'bcc' => $bcc,
            'subject' => $subject,
            'body_text' => $bodyExtracted['body_text'],
            'body_html' => $bodyExtracted['body_html'],
            'timestamp' => $date,
            'has_attachment' => (stripos($contentType, 'multipart/mixed') !== false || stripos($rawEmail, 'filename=') !== false),
            'attachments' => [],
        ];
    }

    /**
     * Robust recursive MIME body extractor for nested boundaries, HTML, text, and inline CID images.
     */
    protected function extractMimeBodies($raw)
    {
        $bodyText = '';
        $bodyHtml = '';
        $cids = [];

        // Find all boundaries mentioned anywhere in headers or parts
        preg_match_all('/boundary="?([^";\r\n\s]+)"?/i', $raw, $boundaryMatches);
        $boundaries = array_unique($boundaryMatches[1] ?? []);

        if (!empty($boundaries)) {
            $parts = [$raw];
            foreach ($boundaries as $b) {
                $newParts = [];
                foreach ($parts as $p) {
                    $sub = explode('--' . $b, $p);
                    foreach ($sub as $s) {
                        if (trim($s) !== '' && trim($s) !== '--') {
                            $newParts[] = $s;
                        }
                    }
                }
                $parts = $newParts;
            }

            foreach ($parts as $part) {
                $sections = preg_split("/\r?\n\r?\n/", ltrim($part), 2);
                $pHeaders = $sections[0] ?? '';
                $pBody = $sections[1] ?? $part;

                // Check for inline Content-ID images (save to storage to keep database lightweight and prevent max_allowed_packet error)
                if (preg_match('/Content-ID:\s*<([^>]+)>/i', $pHeaders, $cidMatch) || preg_match('/Content-ID:\s*([^\r\n]+)/i', $pHeaders, $cidMatch)) {
                    $cid = trim(trim($cidMatch[1]), '<>');
                    $ext = 'png';
                    if (preg_match('/Content-Type:\s*image\/([a-zA-Z0-9_-]+)/i', $pHeaders, $ctMatch)) {
                        $ext = strtolower($ctMatch[1]);
                        if ($ext === 'jpeg') $ext = 'jpg';
                    }
                    $cleanBase64 = preg_replace('/\s+/', '', $pBody);
                    $decodedBinary = @base64_decode($cleanBase64);

                    if (!empty($decodedBinary)) {
                        $filename = 'inline_' . md5($cid . substr($cleanBase64, 0, 100)) . '.' . $ext;
                        $dir = storage_path('app/public/mailbox_inline');
                        if (!file_exists($dir)) {
                            @mkdir($dir, 0755, true);
                        }
                        @file_put_contents($dir . '/' . $filename, $decodedBinary);
                        $cids[$cid] = asset('storage/mailbox_inline/' . $filename);
                    }
                    continue;
                }

                $pEncoding = '7bit';
                if (preg_match('/Content-Transfer-Encoding:\s*([^\r\n]+)/i', $pHeaders, $em)) {
                    $pEncoding = strtolower(trim($em[1]));
                }

                if (preg_match('/filename="?([^";\r\n]+)"?/i', $pHeaders)) {
                    continue;
                }

                $decoded = $pBody;
                if ($pEncoding === 'quoted-printable') {
                    $decoded = quoted_printable_decode($pBody);
                } elseif ($pEncoding === 'base64') {
                    $decoded = @base64_decode(trim($pBody));
                }

                if (stripos($pHeaders, 'text/html') !== false) {
                    if (empty($bodyHtml) || strlen($decoded) > strlen($bodyHtml)) {
                        $bodyHtml = $decoded;
                    }
                } elseif (stripos($pHeaders, 'text/plain') !== false) {
                    if (empty($bodyText) || strlen($decoded) > strlen($bodyText)) {
                        $bodyText = $decoded;
                    }
                }
            }
        }

        // Fallback if no boundaries or text extracted
        if (empty($bodyText) && empty($bodyHtml)) {
            $sections = preg_split("/\r?\n\r?\n/", $raw, 2);
            $fallback = $sections[1] ?? $raw;
            $bodyText = strip_tags(quoted_printable_decode($fallback));
            $bodyHtml = nl2br(e($bodyText));
        }

        // Convert inline CID image references to base64 Data URIs
        if (!empty($bodyHtml) && !empty($cids)) {
            foreach ($cids as $cid => $dataUri) {
                $bodyHtml = str_ireplace('cid:' . $cid, $dataUri, $bodyHtml);
                $bodyHtml = str_ireplace('cid:<' . $cid . '>', $dataUri, $bodyHtml);
            }
        }

        if (empty($bodyText) && !empty($bodyHtml)) {
            $bodyText = trim(strip_tags($bodyHtml));
        }

        if (empty($bodyHtml) && !empty($bodyText)) {
            $bodyHtml = nl2br(e($bodyText));
        }

        return [
            'body_text' => trim($bodyText),
            'body_html' => trim($bodyHtml),
        ];
    }

    /**
     * Decode MIME header words.
     */
    protected function decodeMimeStr($string)
    {
        if (function_exists('iconv_mime_decode')) {
            $decoded = @iconv_mime_decode($string, 0, "UTF-8");
            if ($decoded !== false) return $decoded;
        }
        if (function_exists('mb_decode_mimeheader')) {
            return @mb_decode_mimeheader($string);
        }
        return $string;
    }

    /**
     * Send command to IMAP socket and read lines until tagged completion.
     */
    protected function sendCommand($command)
    {
        $tag = 'TAG' . ($this->tagCount++);
        $cmd = $tag . ' ' . $command . "\r\n";

        fwrite($this->socket, $cmd);

        $lines = [];
        while (!feof($this->socket)) {
            $line = $this->readLine();
            if ($line === false) break;
            $lines[] = $line;

            if (strpos($line, $tag . ' ') === 0) {
                break;
            }
        }

        return $lines;
    }

    /**
     * Read single line from socket using fgets.
     */
    protected function readLine()
    {
        if (!$this->socket || feof($this->socket)) {
            return false;
        }
        $line = fgets($this->socket, 4096);
        return $line !== false ? rtrim($line, "\r\n") : false;
    }

    /**
     * Check if tagged response is OK.
     */
    protected function isOk($lines)
    {
        $last = end($lines);
        return (bool) preg_match('/^TAG\d+\s+OK/i', $last);
    }

    /**
     * Extract response message from tagged line.
     */
    protected function getResponseMessage($lines)
    {
        $last = end($lines);
        return preg_replace('/^TAG\d+\s+(OK|NO|BAD)\s*/i', '', $last);
    }

    /**
     * Close connection.
     */
    public function disconnect()
    {
        if ($this->socket) {
            try {
                $this->sendCommand('LOGOUT');
            } catch (Exception $e) {}
            @fclose($this->socket);
            $this->socket = null;
        }
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
