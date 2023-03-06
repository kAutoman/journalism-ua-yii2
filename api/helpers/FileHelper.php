<?php


namespace api\helpers;

use common\models\File;
use common\models\FileMetaData;
use Exception;
use metalguardian\fileProcessor\helpers\FPM;

/**
 * Class FileHelper
 * @package api\helpers
 * @author Andrew P. Kontseba <andjey.skinwalker@gmail.com>
 *
 * @deprecated
 */
class FileHelper
{
    /**
     * @param int $fileId
     * @param bool $meta
     * @return mixed
     * @throws Exception
     */
    public static function getFile(?int $fileId, bool $meta = false)
    {
        if ($fileId === null) {
            return $meta ? [
                'alt' => '',
                'title' => '',
                'src' => '',
                'webp' => '',
            ] : null;
        }
        if ($meta) {
            $image = [];
            $meta = FileMetaData::getMetaByFileId($fileId);

            $image['alt'] = obtain('alt', $meta, '');
            $image['title'] = obtain('title', $meta, '');
            $image['src'] = FPM::originalSrc($fileId);
//            $image['webp'] = Webp::originalSrc($fileId);
            return $image;
        } else {
            return FPM::originalSrc($fileId);
        }
    }

    public static function getFiles($fileIds, bool $meta = true)
    {
        if($fileIds == null){
            return [];
        }
        $files = explode(',', $fileIds);
        $filesModels = File::find()->andWhere(['id'=>$files])->orderBy(['position'=>SORT_DESC])->all();
        $data = [];
        foreach ($filesModels as $file) {
            $data[] = self::getFile((int)trim($file->id), $meta);
        }

        return $data;
    }

}
