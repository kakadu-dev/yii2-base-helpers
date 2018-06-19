<?php

namespace Kakadu\Yii2BaseHelpers;

/**
 * Class    AvatarHelper
 * @package Kakadu\Yii2BaseHelpers
 * @author  Yarmaliuk Mikhail
 * @author  Konstantin Timoshenko
 * @version 1.0
 */
class AvatarHelper
{
    const COLOR_HEX_C1 = '8870FF';
    const COLOR_HEX_C2 = '63B7E6';
    const COLOR_HEX_C3 = '597DF7';
    const COLOR_HEX_C4 = 'FFA338';
    const COLOR_HEX_C5 = '30D7BB';
    const COLOR_HEX_C6 = 'FF789E';
    const COLOR_HEX_C7 = 'FFC642';
    const COLOR_HEX_C8 = 'D2BDBE';

    /**
     * Get "random" class of color for avatar basing on name
     *
     * @param string $string
     *
     * @return string
     */
    public static function getAvatarClass(string $string): string
    {
        $string  = mb_strtolower(mb_substr($string, 0, 1));
        $number_letter = is_string($string) ? BaseHelper::getNumberPositionLetter($string) : (int) $string;
        $colors        = ['c1', 'c2', 'c3', 'c4', 'c5', 'c6', 'c7', 'c8'];

        if ($number_letter > 0) {
            $number_letter--;
        }

        return 'user__image__color__' . $colors[$number_letter % count($colors)];
    }

    /**
     * Get "random" hex of color for avatar basing on name
     *
     * @param string $string
     *
     * @return string
     */
    public static function getAvatarColorHex(string $string): string
    {
        $string  = mb_strtolower(mb_substr($string, 0, 1));
        $number_letter = is_string($string) ? BaseHelper::getNumberPositionLetter($string) : (int) $string;
        $colors        = [
            self::COLOR_HEX_C1,
            self::COLOR_HEX_C2,
            self::COLOR_HEX_C3,
            self::COLOR_HEX_C4,
            self::COLOR_HEX_C5,
            self::COLOR_HEX_C6,
            self::COLOR_HEX_C7,
            self::COLOR_HEX_C8,
        ];

        if ($number_letter > 0) {
            $number_letter--;
        }

        return $colors[$number_letter % count($colors)];
    }
}
