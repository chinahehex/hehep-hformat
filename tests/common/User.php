<?php


namespace hformat\tests\common;

class User
{

    public function totalAdminNewsNum(array $ids)
    {
        return [
            ['id'=>1,'hit_num'=>10,'buy_num'=>20],
            ['id'=>2,'hit_num'=>10,'buy_num'=>25],
            ['id'=>3,'hit_num'=>11,'buy_num'=>21],
        ];
    }

    public function getRoles(array $ids)
    {
        return [
            ['id'=>1,'roleName'=>'超级管理员'],
            ['id'=>2,'roleName'=>'管理员'],
            ['id'=>3,'roleName'=>'普通用户'],
        ];
    }

}
