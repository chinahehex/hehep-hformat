<?php
namespace hformat\tests\common;

use hehe\core\hformat\annotation\Formator;

class ImgFormator
{

    /**
     * @param string $value
     * @return string
     */
    public static function resFormator(string $value):string
    {
        return 'http://www.hehex.cn' . $value;
    }

}
