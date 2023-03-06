<?php

namespace common\modules\fileManager\components;

use common\helpers\FileHelper;
use trntv\filekit\File;
use trntv\filekit\Storage as BaseStorage;
use Yii;
use yii\web\UploadedFile;

/**
 * Class Storage
 *
 * @package common\modules\fileManager\components
 */
class Storage extends BaseStorage
{
    public $maxDirFiles = 3000;

    public function save($file, $preserveFileName = false, $overwrite = false, $config = [], $pathPrefix = '')
    {
        $pathPrefix = FileHelper::normalizePath($pathPrefix);
        $fileObj = File::create($file);
        $dirIndex = $this->getDirIndex($pathPrefix);
        if ($preserveFileName === false) {
            do {
                $filename = $this->generateRandomName($fileObj);
                $path = implode(DIRECTORY_SEPARATOR, [$pathPrefix, $dirIndex, $filename]);
            } while ($this->getFilesystem()->has($path));
        } else {
            $path = $this->generateUniqueName($file, $pathPrefix);
        }

        $this->beforeSave($fileObj->getPath(), $this->getFilesystem());

        $stream = fopen($fileObj->getPath(), 'rb+');

        $config = array_merge(['ContentType' => $fileObj->getMimeType()], $config);
        if ($overwrite) {
            $success = $this->getFilesystem()->putStream($path, $stream, $config);
        } else {
            $success = $this->getFilesystem()->writeStream($path, $stream, $config);
        }

        if (is_resource($stream)) {
            fclose($stream);
        }

        if ($success) {
            $this->afterSave($path, $this->getFilesystem());
            return $path;
        }

        return false;
    }

    /**
     * @param File $fileObj
     * @return string
     */
    protected function generateRandomName(File $fileObj): string
    {
        return implode('.', [
            Yii::$app->security->generateRandomString(),
            $fileObj->getExtension()
        ]);
    }

    /**
     * @param UploadedFile $file
     * @param string $pathPrefix
     * @param int $index
     * @return string
     */
    protected function generateUniqueName(UploadedFile $file, string $pathPrefix, $index = 0): string
    {
        $filename = $index . '-' . $file->name;
        $path = implode(DIRECTORY_SEPARATOR, [$pathPrefix, $this->getDirIndex($pathPrefix), $filename]);
        if ($this->getFilesystem()->has($path)) {
            return $this->generateUniqueName($file, $pathPrefix, ++$index);
        }
        return $path;
    }
}
