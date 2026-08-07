<?php

namespace App\Services;

class ToolAssetPath
{
    /**
     * Root folder to store tool-related uploads.
     * On the production server (non-Windows), the actual web root is the
     * sibling "public_html" folder, not Laravel's default "public" folder.
     */
    public static function base(): string
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && is_dir(base_path('../public_html'))) {
            return base_path('../public_html');
        }

        return public_path();
    }

    public static function to(string $relative): string
    {
        return self::base() . DIRECTORY_SEPARATOR . ltrim($relative, '/\\');
    }
}
