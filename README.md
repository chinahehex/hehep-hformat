# hehep-hformat

## 介绍
- hehep-hformat 是一个PHP 格式器工具组件,对数据列的集中处理,减少foreach 循环的编写,可通过自定义方法,满足不同场景以及业务需求,比如状态id转对应名称,http 的转换,统计量,json 字符串转数组等等,重用代码，并节省大量遍历处理的时间
- 支持多个格式器
- 支持直接使用格式器
- 支持添加自定义格式器

## 安装
- **gitee下载**:
```
git clone git@gitee.com:chinahehex/hehep-hformat.git
```

- **github下载**:
```
git clone git@github.com:chinahehex/hehep-hformat.git
```
- 命令安装：
```
composer require hehex/hehep-hformat
```

## 组件配置
**格式器规则:**
```
['规则名称',[['格式器1','格式器1属性1'=>'','格式1属性2'=>''],['格式器2','格式器2属性1'=>'','格式器2属性2'=>''] ],'格式规则属性1'=>'','格式规则属性2'=>''],
```

**格式规则参数:**
```
name:规则名称
alias:别名,即新的键名,指定新键名,"status_xxxx_name",":_text2" 冒号表示原始键名,比如"status_text2"
defval:如格式器无对应值,则为默认值
dataid:数据id键名,如未设置,则默认读取name
```
**普通格式器参数:**
```
func:格式单值对应的函数
- 类静态方法方式:common\extend\formats\CustomFormats@@res
- 对象方法方式:common\extend\formats\CustomFormats@res
- 数组函数方式:['对象','方法名']
- 闭包方式:function($value){return $value;}
```
**字典格式器参数:**
```
func:参考"普通格式器参数"
id:字典数据id键名,如未设置,则默认"id"
name:字典数据名称键名,如未设置,则默认"name"
cache:缓存key,如果两个规则设置了相同的缓存key,则只读取一次字典数据,避免重复读取
data:字典数据来源,格式:[['类名获对象','方法'],'方法参数1','方法参数2','方法参数3']
```

## 常规格式化示例
- 定义数据源
```php
// 数据源定义
class UserData
{
    public static function showStatus():array
    {
        return [
            ['id'=>1,'name'=>'正常'],
            ['id'=>2,'name'=>'禁用'],
            ['id'=>3,'name'=>'注销'],
        ];
    }

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
```

- 常规格式化规则示例
```php
use hehe\core\hformat\FormatManager;
use UserData;
// 数据定义,一般从数据库获取
$users = [
    ['id'=>1,'name'=>'hehe1','status'=>1,'ctime'=>'2018-01-01 12:00:00','roleId'=>1,'headPortrait'=>'a/b/c1.jpg'],
    ['id'=>2,'name'=>'hehe2','status'=>2,'ctime'=>'2018-01-01 12:00:00','roleId'=>2,'headPortrait'=>'a/b/c2.jpg'],
];

$userData = new UserData();

$hformat = new FormatManager();
// 格式化"$users"数据
$data = $hformat->doFormat($users,[
    // 状态数值转状态文本
    ['status',[['dict','data'=> [[UserData::class,'showStatus']] ]], 'alias'=>':_text' ],
    // 日期转换
    ['ctime',[['date','format'=>'Y年m月d日 H:i']] ],
    // 头像短地址转长地址(http)
    ['headPortrait',[['trim'],['res']], 'alias'=>':_url' ],
    // 统计访问量,统计购买量
    ['hit_num',[['dict','name'=>'hit_num','data'=>[[$userData, 'totalAdminNewsNum']]]],'dataid'=>'id','alias'=>'hit_num'],
    ['buy_num',[['dict','name'=>'buy_num','data'=>[[$userData, 'totalAdminNewsNum']]]],'dataid'=>'id','alias'=>'buy_num'],
    // 角色ID值转角色名称
    ['roleId',[['dict','name'=>'roleName','data'=>[[$userData, 'getRoles']]]], 'alias'=>'roleName_text']
]);


// 输出数据
$data = [
    ['id'=>1,'name'=>'hehe1','status'=>1,'ctime'=>'2018年01月01日 12:00','roleId'=>1,'headPortrait'=>'a/b/c1.jpg',
    'status_text'=>'正常','roleName_text'=>'超级管理员','headPortrait_url'=>'http://www.hehex.com/a/b/c1.jpg','hit_num'=>10,'buy_num'=>20],
    
    ['id'=>2,'name'=>'hehe2','status'=>1,'ctime'=>'2018年01月01日 12:00','roleId'=>2,'headPortrait'=>'a/b/c1.jpg',
    'status_text'=>'禁用','roleName_text'=>'管理员','headPortrait_url'=>'http://www.hehex.com/a/b/c2.jpg','hit_num'=>10,'buy_num'=>21],
];

```

## 预定义格式化示例
- 预定义格式化规则
```php
class UserFormat
{
    public static function defaultFormat()
    {
        // 字典数据来源
        $user = new UserData();
        return [
            // 状态值转换
            ['status',[ ['dict','data'=>[[UserData::class,'showStatus']] ]],'isdef'=>true,'alias'=>':Text'],
            // 头像图片http 转换
            ['headPortrait',[ ['res'] ],'isdef'=>true,'alias'=>'headPortraitUrl'],
            // 统计访问量,统计购买量
            ['hit_num',[['dict','name'=>'hit_num','data'=>[[$user, 'totalAdminNewsNum']]]],'dataid'=>'id'],
            ['buy_num',[['dict','name'=>'buy_num','data'=>[[$user, 'totalAdminNewsNum']]]],'dataid'=>'id'],

            // 角色数值转角色名称
            ['roleId',[ ['dict','name'=>'roleName','data'=>[[$user, 'getRoles']]] ],'isdef'=>true,'alias'=>'roleName_text']
        ];
    }

}
```
- 预定义格式化规则示例
```php
$users = [
    ['id'=>1,'name'=>'hehe1','status'=>1,'ctime'=>'2018-01-01 12:00:00','roleId'=>1,'headPortrait'=>'/a/b/c1.jpg'],
    ['id'=>2,'name'=>'hehe2','status'=>2,'ctime'=>'2018-01-01 12:00:00','roleId'=>2,'headPortrait'=>'/a/b/c2.jpg'],
];

$hformat = new FormatManager();
$data = $hformat->format($users,[UserFormat::defaultFormat(),['hit_num']]);

// 输出数据
$data = [
    ['id'=>1,'name'=>'hehe1','status'=>1,'ctime'=>'2018年01月01日 12:00','roleId'=>1,'headPortrait'=>'a/b/c1.jpg',
    'status_text'=>'正常','roleName_text'=>'超级管理员','headPortrait_url'=>'http://www.hehex.com/a/b/c1.jpg','hit_num'=>10],
    
    ['id'=>2,'name'=>'hehe2','status'=>1,'ctime'=>'2018年01月01日 12:00','roleId'=>2,'headPortrait'=>'a/b/c1.jpg',
    'status_text'=>'禁用','roleName_text'=>'管理员','headPortrait_url'=>'http://www.hehex.com/a/b/c2.jpg','hit_num'=>10],
];
```

## 格式器
- 格式器定义
```php
namespace hehe\core\hformat\formators;

use hehe\core\hformat\base\Formator;

// 日期格式器
class DateFormator extends Formator
{

    protected $format = 'Y-m-d';
    
    // 格式化数据入口
    public function getValue(...$params)
    {
        return $this->formatDate(...$params);
    }

    protected function formatDate(string $value,string $format = '')
    {
        if ($format === '') {
            $format = $this->format;
        }

        return date($format,strtotime($value));
    }
}

```

- 定义类格式器
```php
class CommonFormator
{
    // 方法名后缀为"Formator"作为格式器,默认别名为:trim
    public static function trimFormator($value)
    {
        return trim($value);
    }
    
    // 方法名后缀为"Formator"作为格式器,默认别名为:toArr
    public static function toArrFormator($value)
    {
        if (!empty($value) && is_string($value)) {
            $value = explode(',',$value);
        }

        return $value;
    }
    
    // 方法名后缀为"Formator"作为格式器,默认别名为:jsonDecode
    public static function jsonDecodeFormator($value)
    {
        return json_decode($value,true);
    }
    
    // 方法名后缀为"Formator"作为格式器,默认别名为:jsonEncode
    public static function jsonEncodeFormator($value)
    {
        return json_encode($value,true);
    }
}
```
- 注册格式器
```php
use hehe\core\hformat\FormatManager;

// 注册日期格式器,别名为 date
FormatManager::addFormator('date',DateFormator::class);

// 注册类格式器
FormatManager::addBatchFormator(CommonFormator::class);

```

## 格式器直接使用
```php
use hehe\core\hformat\FormatManager;
$hformat = new FormatManager();
$str_json = $hformat->jsonEncode(["id"=>1,'name'=>'hehe']);
$arr = $hformat->jsonDecode($str_json);
$value = $hformat->trim('  hehex  ');
$date = $hformat->date('2000-01-01','Y年m月d日');

// 字典
$data = [
    ['id'=>1,'name'=>'hehe1'],
    ['id'=>2,'name'=>'hehe2'],
    ['id'=>3,'name'=>'hehe3'],
];

$name = $hformat->createFormator('dict')->setData($data)->getValue(1);
// $name = 'hehe1'

$name = $this->hformat->dict()->setData($data)->getValue(2);
// $name = 'hehe2'
```

## 格式器注解
- 说明
```
类名: `hehe\core\hformat\annotation\AnnFormator`
注解: `@AnnFormator`, `@AnnFormator("别名")`
```

- 注解常规格式器
```php
use hehe\core\hformat\annotation\AnnFormator;
use \hehe\core\hformat\base\Formator;
/**
 * @AnnFormator("uri")
 */
class UrlFormator extends Formator
{

    public function getValue(...$params)
    {
        return $this->formatUri(...$params);
    }

    protected function formatUri(string $url)
    {
        // 生成URL地址
        return $url;
    }

}
```
- 注解类格式器
```php
use hehe\core\hformat\annotation\AnnFormator;
/**
* @AnnFormator()
 */
class CommonFormator
{
    // 方法名后缀为"Formator"作为格式器,默认别名为:toArr
    public static function toArrFormator($value)
    {
        if (!empty($value) && is_string($value)) {
            $value = explode(',',$value);
        }

        return $value;
    }
}
```

- 注解方法格式器
```php
use hehe\core\hformat\annotation\AnnFormator;
class CommonFormator
{
    /**
     * @AnnFormator("toArr")
     */
    public static function toArrFormator($value)
    {
        if (!empty($value) && is_string($value)) {
            $value = explode(',',$value);
        }

        return $value;
    }
    
    /**
     * @AnnFormator()
     */
    public static function uriFormator(string $url)
    {
        // URL生成

        return $url;
    }
    
}
```


## 默认格式器
格式器 | 说明 | 规则示例
----------|-------------|------------
`jsonEncode`  | 数组json | `['fieldname', ['jsonEncode'] ]`
`jsonDecode`  |json字符串转数组 | `['fieldname', ['jsonDecode'] ]`
`date`  | 日期格式 | `['fieldname', ['date','format'=>'日期格式'] ]`
`toArr`  | 字符串转数组 | `['fieldname', ['toArr'] ]`
`trim`  | 字符串去掉两边空格 | `['fieldname', ['trim'] ]`
`dict`  | 数组列值转换 | `['fieldname', ['dict','id'=>'','name'=>''] ]`
