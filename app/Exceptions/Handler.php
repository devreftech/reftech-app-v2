<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use App\Models\ActivityLog;
use App\Models\HelpdeskTicket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // Ignore validation exceptions or 404 HTTP exceptions from creating error tickets
            if ($e instanceof \Illuminate\Validation\ValidationException || $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return;
            }

            try {
                $user = Auth::user();
                $url = Request::fullUrl();
                $method = Request::method();
                $exceptionClass = get_class($e);
                $file = $e->getFile();
                $line = $e->getLine();
                $message = $e->getMessage();

                // 1. Create Error Activity Log
                ActivityLog::create([
                    'user_id'     => $user ? $user->id : null,
                    'type'        => 'error',
                    'action'      => 'error_500',
                    'description' => "Terjadi System Error 500: {$message} pada {$file}:{$line}",
                    'properties'  => [
                        'exception'   => $exceptionClass,
                        'message'     => $message,
                        'file'        => $file,
                        'line'        => $line,
                        'url'         => $url,
                        'method'      => $method,
                        'user_name'   => $user ? $user->name : 'Guest',
                        'user_role'   => $user ? $user->role : 'N/A',
                        'payload'     => Request::except(['password', 'password_confirmation', '_token']),
                    ],
                    'ip_address'  => Request::ip(),
                    'user_agent'  => Request::userAgent(),
                ]);

                // 2. Create Automatic System Error Helpdesk Ticket
                $year = now()->format('Y');
                $month = now()->format('m');
                $prefix = "ERR/{$year}/{$month}/";
                $last = HelpdeskTicket::where('no_ticket', 'like', $prefix . '%')->orderByDesc('no_ticket')->value('no_ticket');
                $lastSeq = $last ? (int) substr($last, -3) : 0;
                $nextSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);

                HelpdeskTicket::create([
                    'no_ticket'       => $prefix . $nextSeq,
                    'id_user'         => $user ? $user->id : null,
                    'category'        => 'system_error',
                    'title'           => "[System Error] " . class_basename($e) . " pada " . Request::path(),
                    'description'     => "Temuan Error Otomatis System:\nMessage: {$message}\nFile: {$file} (Line {$line})\nURL: [{$method}] {$url}\nUser: " . ($user ? $user->name . " ({$user->role})" : "Guest"),
                    'error_file'      => $file,
                    'error_line'      => $line,
                    'error_exception' => $exceptionClass,
                    'url_accessed'    => $url,
                    'http_method'     => $method,
                    'status'          => 'Open',
                ]);
            } catch (\Throwable $loggingException) {
                \Log::error('Failed to log system error ticket: ' . $loggingException->getMessage());
            }
        });
    }
}
