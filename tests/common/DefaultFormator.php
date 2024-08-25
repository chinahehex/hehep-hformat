<?php
namespace hformat\tests\common;

use hehe\core\hformat\annotation\Formator;

class DefaultFormator
{

    /**
     * @param string $value
     * @return string
     * @Formator()
     */
    public static function resFormator(string $value):string
    {
        return '1' . $value;
    }

}
