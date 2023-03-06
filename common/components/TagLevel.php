<?php

namespace common\components;

use yii\helpers\Json;

/**
 * Class TagLevel
 *
 * @package common\components
 * @deprecated
 */
class TagLevel
{
    CONST TAG_H1 = 1;
    CONST TAG_H2 = 2;
    CONST TAG_H3 = 3;
    CONST TAG_H4 = 4;
    CONST TAG_H5 = 5;

    public static function getTags()
    {
        return [

            self::TAG_H1 => 'H1',
            self::TAG_H2 => 'H2',
            self::TAG_H3 => 'H3',
            self::TAG_H4 => 'H4',
            self::TAG_H5 => 'H5',
        ];
    }

    public static function getText(string $text)
    {
        $text = is_array($text) ? $text : (Json::decode($text) ?? []);
        return [
            'text' => $text[0] ?? '',
            'level' => (int)($text[1] ?? 2)
        ];
    }
}
