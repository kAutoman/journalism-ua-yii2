<?php

namespace backend\modules\imagesUpload\helpers;

use common\components\model\Helper;

class ImageUploadUrlHelper
{
    use Helper;

    public static function getMetaDataFormGenerateUrl(array $params): string
    {
        return self::createUrl('/imagesUpload/meta-data/generate-form', $params);
    }

    public static function getMetaDataFormSaveUrl(array $params): string
    {
        return self::createUrl('/imagesUpload/meta-data/save-form', $params);
    }
}