<?php
namespace hehe\core\hformat\formators;

use hehe\core\hformat\base\Formator;

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


}
