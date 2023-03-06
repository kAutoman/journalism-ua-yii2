<?php
/**
 * Created by PhpStorm.
 * User: viktor
 * Date: 13.06.18
 * Time: 17:11
 */

namespace common\helpers;

use common\models\FpmFile;
use vova07\console\ConsoleRunner;
use metalguardian\fileProcessor\helpers\FPM;

/**
 * Class MediaHelper
 * @package common\helpers
 */
class MediaHelper extends FPM
{
    const THUMBNAIL_PATH = '/uploads/thumb';

    const POSTER_PATH = '/uploads/poster';

    const WEBM_PATH = '/uploads/webm';

    const SIZE_RETINA = 1;
    const SIZE_DESKTOP = 2;
    const SIZE_TABLET = 3;
    const SIZE_MOBILE = 4;

    const SIZE_STUB = 5;

    const STUB_SIZE = 100;

    const IMAGES_TYPES = ['png', 'jpg', 'jpeg'];
    const VIDEO_TYPES = ['mp4'];

    const MAX_IMAGE_SIZE = 2000;
    const MAX_VIDEO_SIZE = 25000;

    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;

    /**
     * @return array
     */
    public static function getSizesDictionary(): array
    {
        return [
            self::SIZE_MOBILE => [960, 960],
            self::SIZE_TABLET => [1000, 1000],
            self::SIZE_DESKTOP => [1280, 1280],
            self::SIZE_RETINA => [1920, 1920],
        ];
    }

    /**
     * @return array
     */
    public static function getBrakePoints()
    {
        return [
            self::SIZE_MOBILE => 540,
            self::SIZE_TABLET => 768,
            self::SIZE_DESKTOP => 1280,
            self::SIZE_RETINA => 1920,
        ];
    }

    /**
     * @param $id
     * @return null|string
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function originalSrc($id)
    {
        if (!(int)$id) {
            return null;
        }

        $file = FpmFile::findOne($id);

        if (!$file) {
            return null;
        }

        $model = FPM::transfer()->getData($id);
        $src = static::getOriginalDirectoryUrl($id)
            . rawurlencode(static::getThumbnailFileName($id, $model->base_name, $model->extension));

        return $src;
    }

    /**
     * @param FpmFile $file
     * @return null|string
     * @throws \yii\base\InvalidConfigException
     */
    public static function getOriginalByFile(FpmFile $file)
    {
        if (!$file) {
            return null;
        }

        $src = static::getOriginalDirectoryUrl($file->id)
            . rawurlencode(static::getThumbnailFileName($file->id, $file->base_name, $file->extension));

        return $src;
    }

    /**
     * @param $id
     */
    public static function optimize($id)
    {
        $file = FpmFile::findOne($id);

        $consoleRunner = new ConsoleRunner(['file' => getAlias('@root') . '/yii']);

        if (in_array($file->extension, self::VIDEO_TYPES)) {
            $consoleRunner->run('optimize/video ' . $file->id);
        }

        if (in_array($file->extension, self::IMAGES_TYPES)) {
            $consoleRunner->run('optimize/image ' . $file->id);
        }
    }

    /**
     * @param FpmFile $file
     * @param $extension
     * @return string
     */
    public static function getPosterName(FpmFile $file, $extension)
    {
        return $file->id . '-' . $file->base_name . '.' . $extension;
    }

    /**
     * @param FpmFile $file
     * @param $extension
     * @return string
     */
    public static function getPosterStub(FpmFile $file, $extension)
    {
        return $file->id . '-stub-' . $file->base_name . '.' . $extension;
    }

    /**
     * @param FpmFile $file
     * @return string
     */
    public static function getVideoName(FpmFile $file)
    {
        return $file->id . '-' . $file->base_name . '.webm';
    }

    /**
     * @param FpmFile $file
     * @param int $sizeType
     * @param string $extension
     * @return string
     */
    public static function getThumbnailName(FpmFile $file, int $sizeType, string $extension): string
    {
        return $file->id . '-' . $sizeType . '-' . $file->base_name . '.' . $extension;
    }

    /**
     * @param string $path
     */
    public static function createDir(string $path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    /**
     * @param $id
     * @return array|null|string
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     *
     * @deprecated
     * @todo transfer to Formatter component
     */
    public static function getSourceSet($id)
    {
        /** @var FpmFile $file */
        $file = FpmFile::findOne($id);

        if (!$file) {
            return null;
        }

        if (in_array($file->extension, self::VIDEO_TYPES)) {
            $default = self::POSTER_PATH . '/' . self::getPosterName($file, 'jpg');

            $stub = self::POSTER_PATH . '/' . self::getPosterStub($file, 'jpg');

            $webp = self::POSTER_PATH . '/' . self::getPosterName($file, 'webp');

            $webm = self::WEBM_PATH . '/' . self::getVideoName($file);

            return [
                'type' => self::TYPE_VIDEO,
                'src' => [
                    'mp4' => self::originalSrc($id ?? false),
                    'webm' => file_exists(getAlias('@webroot') . $webm) ? $webm : null,
                ],
                'poster' => [
                    'default' => file_exists(getAlias('@webroot') . $default) ? $default : null,
                    'webp' => file_exists(getAlias('@webroot') . $webp) ? $webp : null,
                    'stub' => file_exists(getAlias('@webroot') . $stub) ? $stub : null,
                ]
            ];
        }

        if (in_array($file->extension, self::IMAGES_TYPES)) {
            $default = [];
            $webp = [];
            foreach (self::getSizesDictionary() as $type => $size) {
                $defaultPath = self::THUMBNAIL_PATH . '/' . $id . '-' . $type . '-' . $file->base_name . '.' . $file->extension;

                $webpPath = self::THUMBNAIL_PATH . '/' . $id . '-' . $type . '-' . $file->base_name . '.' . 'webp';

                $default[self::getBrakePoints()[$type] ?? null] = file_exists(getAlias('@webroot') . $defaultPath) ? $defaultPath : null;

                $webp[self::getBrakePoints()[$type] ?? null] = file_exists(getAlias('@webroot') . $webpPath) ? $webpPath : null;
            }

            $stubPath = self::THUMBNAIL_PATH . '/' . $id . '-' . self::SIZE_STUB . '-' . $file->base_name . '.' . $file->extension;

            $stub = file_exists(getAlias('@webroot') . $stubPath) ? $stubPath : null;

            return [
                'type' => self::TYPE_IMAGE,
                'src' => [
                    'default' => $default,
                    'webp' => $webp,
                    'stub' => $stub
                ]
            ];
        }

        return null;
    }

    /**
     * @param int $id
     * @return bool
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function delete(int $id)
    {
        $file = FpmFile::findOne($id);

        if (in_array($file->extension, self::VIDEO_TYPES)) {

            $default = self::POSTER_PATH . '/' . self::getPosterName($file, 'jpg');
            $stub = self::POSTER_PATH . '/' . self::getPosterStub($file, 'jpg');
            $webp = self::POSTER_PATH . '/' . self::getPosterName($file, 'webp');
            $webm = self::WEBM_PATH . '/' . self::getVideoName($file);

            if (file_exists(getAlias('@webroot') . $default) && $default !== null) {
                unlink(getAlias('@webroot') . $default);
            }

            if (file_exists(getAlias('@webroot') . $webp) && $webp !== null) {
                unlink(getAlias('@webroot') . $webp);
            }

            if (file_exists(getAlias('@webroot') . $webm) && $webm !== null) {
                unlink(getAlias('@webroot') . $webm);
            }

            if (file_exists(getAlias('@webroot') . $stub) && $stub !== null) {
                unlink(getAlias('@webroot') . $stub);
            }

            return true;
        }

        if (in_array($file->extension, self::IMAGES_TYPES)) {
            $sourceSet = self::getSourceSet($id)['src'] ?? false;

            if ($sourceSet) {
                foreach ($sourceSet['default'] as $image) {
                    if ($image) {
                        if (file_exists(getAlias('@webroot') . $image) && $image !== null) {
                            unlink(getAlias('@webroot') . $image);
                        }
                    }
                }

                foreach ($sourceSet['webp'] as $image) {
                    if ($image) {
                        if (file_exists(getAlias('@webroot') . $image) && $image !== null) {
                            unlink(getAlias('@webroot') . $image);
                        }
                    }
                }

                if (file_exists(getAlias('@webroot') . $sourceSet['stub']) && $sourceSet['stub'] !== null) {
                    unlink(getAlias('@webroot') . $sourceSet['stub']);
                }
            }
        }
    }
}
