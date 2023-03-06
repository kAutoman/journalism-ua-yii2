<?php

namespace backend\components;

use metalguardian\fileProcessor\helpers\FPM;
use metalguardian\fileProcessor\models\File;
use yii\helpers\Html;

/**
 * Class Formatter
 */
class Formatter extends \yii\i18n\Formatter
{
    /**
     * Formats the value as an link tag using FPM module.
     * @param mixed $value the value to be formatted
     * @param array $options
     * @return string the formatted result
     */
    public function asFile($value, $options = [])
    {
        if (!$value) {
            return $this->nullDisplay;
        }
        $file = FPM::transfer()->getData($value);

        return static::getFileLink($file);
    }

    /**
     * @param $file File
     * @return string
     */
    public static function getFileLink($file)
    {
        switch (true) {
            case in_array($file->extension, ['jpg', 'png', 'gif', 'tif', 'bmp']):
                $linkLabel = FPM::image($file->id, 'admin', 'file');
                break;
            case $file->extension == 'ico':
                $linkLabel = Html::img(FPM::originalSrc($file->id));
                break;
            case $file->extension == 'svg':
                $linkLabel = Html::img(FPM::originalSrc($file->id), ['class' => 'svg']);
                break;
            default:
                $linkLabel = FPM::getOriginalFileName($file->id, $file->base_name, $file->extension);
        }

        return Html::a(
            $linkLabel,
            FPM::originalSrc($file->id),
            ['target' => '_blank']
        );
    }
}
