<?php
namespace hehe\core\hformat\formators;

use hehe\core\hformat\base\Formator;

/**
 * 通用格式化集合器
 */
class CommonFormator
{

    public static function trimFormator($value)
    {
        return trim($value);
    }

    public static function toArrFormator($value)
    {
        if (!empty($value) && is_string($value)) {
            $value = explode(',',$value);
        }

        return $value;
    }

    public static function jsonDecodeFormator($value)
    {
        return json_decode($value,true);
    }

    public static function jsonEncodeFormator($value)
    {
        return json_encode($value,true);
    }

    public static function dateFormator(string $value,string $format = 'Y-m-d')
    {
        return date($format,strtotime($value));
    }


}
