<?php

namespace common\helpers;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use yii\helpers\FileHelper as BaseFileHelper;
use ZipArchive;

/**
 * Class FileHelper
 *
 * @package common\helpers
 */
class FileHelper extends BaseFileHelper
{
    /**
     * Creates ZIP archive recursively from source into destination
     * Requires php-zip extension
     *
     * @param string $source Path to file|directory
     * @param string $destination Path to store the created archive
     * @return bool
     */
    public static function zip(string $source, string $destination): bool
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }
        $zip = new ZipArchive();
        if (!$zip->open($destination, ZipArchive::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));
        if (is_dir($source)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);
                // Ignore "." and ".." folders
                if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                    continue;
                }
                $file = realpath($file);
                if (is_dir($file)) {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                } elseif (is_file($file)) {
                    $zip->addFile($file, str_replace($source . '/', '', $file));
                }
            }
        } elseif (is_file($source)) {
            $zip->addFile($source, basename($source));
        }

        return $zip->close();
    }
}
