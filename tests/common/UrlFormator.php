<?php
namespace hformat\tests\common;

use hehe\core\hformat\annotation\Formator;

/**
 * Class UrlFormator
 * @package hformat\tests\common
 * @Formator()
 */
class UrlFormator
{

    /**
     * @param string $value
     * @return string
     */
    public static function url(string $value):string
    {
        return '2' . $value;
    }

    /**
     * @param string $value
     * @return string
     */
    public static function uriFormator(string $value):string
    {
        return '3' . $value;
    }

}
