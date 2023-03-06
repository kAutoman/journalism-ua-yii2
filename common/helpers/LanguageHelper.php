<?php

namespace common\helpers;

use Yii;
use common\models\Language;

/**
 * Class LanguageHelper
 * @package common\helpers
 */
class LanguageHelper
{
    /**
     * @var array
     */
    private static $models = [];
    /**
     * @var Language Default language object
     */
    private static $default;

    /**
     * @return Language
     */
    public static function getDefaultLanguage()
    {
        if (static::$default === null) {
            static::$default = Language::find()->where(['is_default' => 1])->one();
        }
        return static::$default;
    }

    /**
     * @return Language[]
     */
    public static function getLanguageModels()
    {
        if (empty(static::$models)) {
            $models = Yii::$app->cacheLang->get('languages');
            if (!$models) {
                $models = Language::find()
                    ->isPublished()
                    ->orderBy(['is_default' => SORT_DESC, 'position' => SORT_ASC])
                    ->all();
                Yii::$app->cacheLang->set('languages', $models, 60 * 60 * 24);
            }

            static::$models = $models;
        }

        return static::$models;
    }

    /**
     * @param bool $exceptDefault
     *
     * @return array
     */
    public static function getApplicationLanguages($exceptDefault = false): array
    {
        $models = static::getLanguageModels();
        $languages = [];
        foreach ($models as $model) {
            $languages[$model->id] = $model->code;
        }
        if ($exceptDefault) {
            unset($languages[self::getDefaultLanguage()->id]);
        }

        return $languages;
    }

    /**
     * @return array
     */
    public static function getEditableLanguages(): array
    {
        $models = static::getLanguageModels();
        $languages = [];
        foreach ($models as $model) {
            $languages[$model->code] = $model->code;
        }

        return $languages;
    }

    /**
     * @return string
     */
    public static function getEditLanguage(): string
    {
        return request()->get(
            urlManager()->langParam,
            self::getDefaultLanguage()->code
        );
    }
}
