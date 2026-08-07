<?php

namespace App\Services;

class QuotationDescriptionResolver
{
    public static function resolve($title, $subtitles = [], $details = []): string
    {
        $cleanTitle = trim((string) ($title ?? ''));
        if ($cleanTitle !== '') {
            return $cleanTitle;
        }

        $parts = [];

        foreach ($subtitles as $subtitle) {
            $subtitleText = trim((string) data_get($subtitle, 'subtitle', ''));
            if ($subtitleText !== '') {
                $parts[] = $subtitleText;
            }

            foreach (data_get($subtitle, 'detail', []) as $detail) {
                $detailText = trim((string) data_get($detail, 'detail', ''));
                if ($detailText !== '') {
                    $parts[] = $detailText;
                }
            }
        }

        foreach ($details as $detail) {
            $detailText = trim((string) data_get($detail, 'detail', ''));
            if ($detailText !== '') {
                $parts[] = $detailText;
            }
        }

        $parts = array_values(array_filter(array_unique($parts), fn ($value) => $value !== ''));

        return implode(' | ', array_slice($parts, 0, 4));
    }
}
