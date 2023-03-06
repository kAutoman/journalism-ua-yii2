<?php

namespace common\modules\fileManager\helpers;

/**
 * Class FileHelper
 *
 * @package common\modules\fileManager\helpers
 */
class FileHelper
{
    /**
     * @param array|null $attribute
     * @return string|null
     */
    public static function src(?array $attribute): ?array
    {
        $result = [];
        if (empty($attribute)) {
            return [];
        }
        foreach ($attribute as $one) {
            $item = [];
            $item['src'] = obtain('base_url', $one) ? $one['base_url'] . DIRECTORY_SEPARATOR . $one['path'] : null;
            $item['alt'] = obtain('img_alt', $one);
            $item['title'] = obtain('img_title', $one);

            $result[] = $item;
        }
        if (count($result) == 1) {
            return $result[0];
        }
        return $result;
    }
}
