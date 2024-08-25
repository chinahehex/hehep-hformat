# hehep-hformat

## 介绍
- hehep-hformat 是一个PHP 格式器工具组件,对数据列的集中处理,减少foreach 循环的编写,可通过自定义方法,满足不同场景以及业务需求,比如状态id转对应名称,http 的转换,统计量,json 字符串转数组等等,重用代码，并节省大量遍历处理的时间
- 支持多个格式器
- 支持直接使用格式器
- 支持添加自定义格式器

## 安装
- 直接下载:
```

```
- 命令安装：
```
composer require hehep-hformat
```

## 组件配置
**格式器规则:**
```
['名称',[['格式器1','格式器1属性1'=>'','格式1属性2'=>''],['格式器2','格式器2属性1'=>'','格式器2属性2'=>''] ],'格式规则属性1'=>'','格式规则属性2'=>''],
```

**格式器规则参数:**
```
name:规则key
alias:别名,即新的键名,指定新键名,"status_xxxx_name",":_text2" 冒号表示原始键名,比如"status_text2"
def_val:如格式器无对应值,则为默认值
col_id:需要格式化对应的键名,如未设置,则默认读取name
```
**普通格式器参数:**
```
func:格式单值对应的函数
- 类静态方法方式:common\extend\formats\CustomFormats@@res
- 对象方法方式:common\extend\formats\CustomFormats@res
- 数组函数方式:['对象','方法名']
```
**字典格式器参数:**
```
func:参考"普通格式器参数"
dic_id:字典数据id键名
dic_name:字典数据名称键名
key:字典数据缓存key,如未填,则默认为"规则"名称(name)
```


## 基本示例

**快速使用**
```php
use hehe\core\hformat\Validation;
$hformat = new Formation();

$adminUserList = $this->adminUserService->findByIds([1]);
$data = $hformat->doFormat($adminUserList,[
    // 状态数值转状态文本
    ['status',[['cdict','func'=>'showStatus']], 'alias'=>':_text' ],
    
    // 日期转换
    ['ctime',[['date','format'=>'Y年m月d日 H:i']] ],
    
    // 头像短地址转长地址(http)
    ['headPortrait',[['trim'],['res']], 'alias'=>':_url' ],
    // 获取此id对应访问量
    ['id',[['dict','dic_name'=>'num','key'=>'totalAdminNewsNum','func'=>[[\he::$ctx->newsService, 'totalAdminNewsNum']]]],
        'alias'=>'hit_num' ],
    // 获取此id对应订单量
    ['id',[['dict','dic_name'=>'buy_num','key'=>'totalAdminNewsNum','func'=>[[\he::$ctx->newsService, 'totalAdminNewsNum']]]],
        'alias'=>'buy_num' ],
    // 角色ID值转角色名称
    ['roleId',[['dict','dic_name'=>'roleName','func'=>[[\he::$ctx->adminUserRoleService, 'findForFormat']]]],
        'alias'=>'roleName_text']
]);

// $data 格式化后如下:
array(16) {
      ["id"]=>
      string(1) "1"
      ["headPortrait"]=>
      string(43) "res/ad\headimg\2021/08/01\610606abb1058.jpg"
      ["ctime"]=>
      string(23) "2021年03月02日 20:29"
      ["status"]=>
      string(1) "1"
      ["roleId"]=>
      string(1) "1"
      ["status_text"]=>
      string(6) "显示"
      ["headPortrait_url"]=>
      string(63) "http://res.hehex.cn/res/ad\headimg\2021/08/01\610606abb1058.jpg"
      ["hit_num"]=>
      string(1) "2"
      ["buy_num"]=>
      string(1) "2"
      ["roleName_text"]=>
      string(15) "超级管理员"
    }

```

**格式器调用**
```php
use hehe\core\hformat\Validation;
$hformat = new Formation();

// 地址转换
$url = $hformat->res('res/ad\headimg\2021/08/01\610606abb1058.jpg');
// $url http://res.hehex.cn/res/ad\headimg\2021/08/01\610606abb1058.jpg

// 状态值转换
$status_text = $hformat->cdict(0,['func'=>'showStatus']);
// $status_text 启用

// json 转换
$str_json = $hformat->json(["id"=>1,'name'=>'hehe']);

```

**自定义格式器**
```php
namespace common\extend\formats;

class CustomFormats
{
    public static function handle()
    {
        return [
            'res'=>['func'=>'common\extend\formats\CustomFormats@@res'],
            'cdict'=>['class'=>'common\extend\formats\CdictFormator'],
        ];
    }
    // http 地址转换
    public static function res($value)
    {
        return \he::$ctx->home->toResUrl($value);
    }
}


namespace common\extend\formats;


use hehe\core\hformat\formators\DictFormator;

class CdictFormator extends DictFormator
{
    // 静态字典
    public function getDicts($rule,&$datas)
    {
        if (is_null($this->data)) {
            $this->data = call_user_func([\he::$ctx->commonDict,$this->func]);
        }

        return $this->data;
    }
}


use hehe\core\hformat\Validation;

//  注入自定义格式器
$hformat = new Formation([
  'customFormators'=>[
     'common\extend\formats\CustomFormats'
  ]
]);

```



## 默认格式器
格式器 | 说明 | 规则示例
----------|-------------|------------
`jsonEncode`  | 数组json | `['fieldname', ['json'] ]`
`jsonDecode`  |json字符串转数组 | `['fieldname', ['json_decode'] ]`
`date`  | 日期格式 | `['fieldname', ['date','format'=>'日期格式'] ]`
`toArr`  | 字符串转数组 | `['fieldname', ['to_arr'] ]`
`trim`  | 字符串去掉两边空格 | `['fieldname', ['trim'] ]`
`dict`  | 数组列值转换 | `['fieldname', ['dict','dic_id'=>'','dic_name'=>'']' ]`
