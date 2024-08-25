<?php
namespace hformat\tests\common;



class ImgFormator
{

    /**
     * @param string $value
     * @return string
     */
    public static function resFormator(string $value):string
    {
        return 'http://www.hehex.cn/' . $value;
    }

}
