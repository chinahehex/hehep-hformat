<?php


namespace hformat\tests\common;

class UserDict
{
    public static function showStatus():array
    {
        return [
            ['id'=>1,'name'=>'正常'],
            ['id'=>2,'name'=>'禁用'],
            ['id'=>3,'name'=>'注销'],
        ];
    }
}
