<?php

namespace common\helpers;

/**
 * Class Pattern
 *
 * @package backend\helpers
 */
class Pattern
{
    /**
     * ReqExp pattern match for alias
     * Only latin letters, digits and "-" symbol
     *
     * @return string
     */
    public static function alias(): string
    {
        return '/^[a-z0-9-]+$/';
    }

    /**
     * ReqExp pattern match for strong password
     * Only latin letters. At least one uppercase letter. At least one lowercase letter. Al least one special symbol.
     * Password length controls with $min/$max params
     *
     * @param integer $min password length
     * @param integer $max password length
     * @return string
     */
    public static function password(int $min = 8, int $max = 14): string
    {
        return "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@\$.,:;!%\\-<>\\[\\]\\{\\}\/\\\_*#?&])[A-Za-z\\d@\$.,:;!%\\-<>\\[\\]\\{\\}\/\\\_*?#&]{{$min},{$max}}\$/";
    }
}
