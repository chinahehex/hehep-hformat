<?php
namespace hformat\tests\common;

/**
 */
class UserFormat
{

    public static function defaultFormat()
    {
        $user = new User();
        return [
            // 状态值转换
            ['status',[ ['dict','data'=>[[UserDict::class,'showStatus']] ]],'isdef'=>true,'alias'=>':_text'],
            ['ctime',[['date','params'=>['Y年m月d日 H:i']]],'isdef'=>true ],
            // 头像图片http 转换
            ['headPortrait',[ ['res'] ],'isdef'=>true,'alias'=>'headPortraitUrl'],

            ['hit_num',[['dict','name'=>'hit_num','data'=>[[$user, 'totalAdminNewsNum']]]],'dataid'=>'id'],
            ['buy_num',[['dict','name'=>'buy_num','data'=>[[$user, 'totalAdminNewsNum']]]],'dataid'=>'id'],

            // 角色数值转角色名称
            ['roleId',[ ['dict','name'=>'roleName','data'=>[[$user, 'getRoles']]] ],'isdef'=>true,'alias'=>'roleName_text']
        ];
    }

}
