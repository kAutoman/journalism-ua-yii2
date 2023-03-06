<?php

namespace console\controllers;

use FFMpeg\FFMpeg;
use Imagine\Image\Box;
use Imagine\Gd\Imagine;
use common\models\FpmFile;
use yii\console\Controller;
use FFMpeg\Format\Video\WebM;
use FFMpeg\Coordinate\TimeCode;
use common\helpers\MediaHelper;

class OptimizeController extends Controller
{
    const WEBP_QUALITY = 80;
    const IMAGES_QUALITY = 80;
    const STUB_QUALITY = 50;

    /**
     * @param $id
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionImage($id)
    {

        /** @var FpmFile $file */
        $file = FpmFile::findOne($id);

        $path = getAlias('@web') . MediaHelper::THUMBNAIL_PATH;

        MediaHelper::createDir($path);

        $imagine = new Imagine();

        $imageUrl = MediaHelper::originalSrc($id);

        foreach (MediaHelper::getSizesDictionary() as $sizeType => $size) {


            $src = $path . '/' . MediaHelper::getThumbnailName($file, $sizeType, $file->extension);
            $imagine->open($imageUrl)->thumbnail(new Box(array_shift($size), array_pop($size)))
                ->save($src, ['quality' => self::IMAGES_QUALITY]);
            $webp = $path . '/' . MediaHelper::getThumbnailName($file, $sizeType, 'webp');
            exec("cwebp -q " . self::WEBP_QUALITY . " " . $src . " -o " . $webp . " ");
        }

        $stubSrc = $path . '/' . MediaHelper::getThumbnailName($file, MediaHelper::SIZE_STUB, $file->extension);

        $imagine->open($imageUrl)->thumbnail(new Box(50, 50))
            ->save($stubSrc, ['quality' => self::STUB_QUALITY]);

    }

    /**
     * @param $id
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionVideo($id)
    {
        /** @var FpmFile $file */
        $file = FpmFile::findOne($id);

        $posterPath = getAlias('@web') . MediaHelper::POSTER_PATH;

        $webmPath = getAlias('@web') . MediaHelper::WEBM_PATH;

        $imagine = new Imagine();

        MediaHelper::createDir($posterPath);

        MediaHelper::createDir($webmPath);

        $transformer = FFMpeg::create([
            'ffmpeg.binaries' => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe'
        ]);

        $src = MediaHelper::originalSrc($file->id ?? false);

        $video = $transformer->open($src);

        $savePath = $posterPath . '/' . MediaHelper::getPosterName($file, 'jpg');

        $video->frame(TimeCode::fromSeconds(0))
            ->save($savePath);

        foreach (MediaHelper::getSizesDictionary() as $sizeType => $size) {


            $src = $posterPath . '/' . MediaHelper::getThumbnailName($file, $sizeType, 'jpg');
            $imagine->open($savePath)->thumbnail(new Box(array_shift($size), array_pop($size)))
                ->save($src, ['quality' => self::IMAGES_QUALITY]);
            $webp = $posterPath . '/' . MediaHelper::getThumbnailName($file, $sizeType, 'webp');
            exec("cwebp -q " . self::WEBP_QUALITY . " " . $src . " -o " . $webp . " ");
        }

        $stubSrc = $posterPath . '/' . MediaHelper::getThumbnailName($file, MediaHelper::SIZE_STUB, 'jpg');

        $imagine->open($savePath)->thumbnail(new Box(50, 50))
            ->save($stubSrc, ['quality' => self::STUB_QUALITY]);

        $video->save(new WebM(), $webmPath . '/' . MediaHelper::getVideoName($file));
    }
}
