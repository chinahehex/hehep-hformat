<?php
namespace hformat\tests\common;

use hehe\core\hformat\annotation\AnnFormator;

class DefaultFormator
{

    /**
     * @param string $value
     * @return string
     * @AnnFormator()
     */
    public static function resFormator(string $value):string
    {
        return 'http://www.hehex.cn/' . $value;
    }

}
