<?php

namespace App\Util\net\exelearning\Util;

/**
 * Utility class for handling file permission checks that work reliably across platforms.
 */
class FilePermissionsUtil
{
    /**
     * A more reliable alternative to is_writable that works properly on Windows.
     *
     * @param string $path The file or directory path to check
     *
     * @return bool True if the path is writable, false otherwise
     */
    public static function isWritable(string $path): bool
    {
        // First try the native function directly
        if (is_writable($path)) {
            return true;
        }

        // For electron packaged app, we need a more reliable approach

        // If the path doesn't exist, check if we can create it
        if (!file_exists($path)) {
            // Check if the parent directory exists and is writable
            $parentDir = dirname($path);
            if (!is_dir($parentDir)) {
                return false;
            }

            return self::isWritable($parentDir);
        }

        // If it's a directory, try to create a temporary file inside it
        if (is_dir($path)) {
            $tempFile = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.uniqid('write_test_').'.tmp';

            try {
                $handle = @fopen($tempFile, 'w');
                if ($handle) {
                    fclose($handle);
                    unlink($tempFile);

                    return true;
                }

                return false;
            } catch (\Exception $e) {
                return false;
            }
        }

        // For files, try to open for writing (append mode to avoid data loss)
        try {
            $handle = @fopen($path, 'a');
            if ($handle) {
                fclose($handle);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Checks if a file or directory is readable.
     *
     * @param string $path The file or directory path to check
     *
     * @return bool True if the path is readable, false otherwise
     */
    public static function isReadable(string $path): bool
    {
        // Windows-specific handling could be added here if needed
        return is_readable($path);
    }
}
