<?php
namespace hformat\tests\units;
use hehe\core\hformat\base\Formator;
use hehe\core\hformat\formators\CommonFormator;
use hehe\core\hformat\formators\DateFormator;
use hformat\tests\common\User;
use hformat\tests\common\UserFormat;
use hformat\tests\common\UserFormator;
use hformat\tests\TestCase;

class ExampleTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    // 单个测试之后(每个测试方法之后调用)
    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testJson()
    {
        $str_json = $this->hformat->jsonEncode(["id"=>1,'name'=>'hehe']);
        $this->assertTrue(is_string($str_json));

        $arr = $this->hformat->jsonDecode($str_json);

        $this->assertTrue(is_array($arr));
    }

    public function testTrim()
    {
        $value = $this->hformat->trim('  hehex  ');

        $this->assertSame('hehex',$value);
    }

    public function testDate()
    {
        $value = $this->hformat->date('2000-01-01');
        $this->assertSame('2000-01-01',$value);

        $value = $this->hformat->date('2000-01-01','Y');
        $this->assertSame('2000',$value);

        $value = $this->hformat->date('2000-01-01','Y年m月d日');
        $this->assertSame('2000年01月01日',$value);
    }


    public function testDict()
    {
        $data = [
            ['id'=>1,'name'=>'hehe1'],
            ['id'=>2,'name'=>'hehe2'],
            ['id'=>3,'name'=>'hehe3'],
        ];

        $dict = $this->hformat->createFormator('dict',['dict'=>['id'=>'id','name'=>'name']]);
        $dict->setData($data);
        $name = $dict->getValue(1);

        $this->assertSame('hehe1',$name);
    }

    public function testDict1()
    {
        $data = [
            ['id'=>1,'name'=>'hehe1'],
            ['id'=>2,'name'=>'hehe2'],
            ['id'=>3,'name'=>'hehe3'],
        ];

        $dict = $this->hformat->dict();
        $dict->setData($data);
        $name = $dict->getValue(2);

        $this->assertSame('hehe2',$name);
    }

    public function testDict2()
    {
        $data = [
            ['id'=>1,'name'=>'hehe1'],
            ['id'=>2,'name'=>'hehe2'],
            ['id'=>3,'name'=>'hehe3'],
        ];

        $dict = $this->hformat->dict()->setData($data);
        $name = $dict->getValue(3);
        $this->assertSame('hehe3',$name);
    }

    public function testDoFormat()
    {

        $users = [
            ['id'=>1,'name'=>'hehe1','status'=>1,'ctime'=>'2018-01-01 12:00:00','roleId'=>1,'headPortrait'=>'/a/b/c1.jpg'],
            ['id'=>2,'name'=>'hehe2','status'=>2,'ctime'=>'2018-01-01 12:00:00','roleId'=>2,'headPortrait'=>'/a/b/c2.jpg'],
            ['id'=>3,'name'=>'hehe3','status'=>3,'ctime'=>'2018-01-01 12:00:00','roleId'=>3,'headPortrait'=>'/a/b/c3.jpg'],
        ];

        $user = new User();

        $data = $this->hformat->doFormat($users,[
            // 状态数值转状态文本
//            ['status',[['cdict','func'=>'showStatus']], 'alias'=>':_text' ],

            // 日期转换
            ['ctime',[['date','format'=>'Y年m月d日 H:i']] ],

            // 头像短地址转长地址(http)
//            ['headPortrait',[['trim'],['res']], 'alias'=>':_url' ],
            // 获取此id对应访问量
            ['id',[['dict','name'=>'hit_num','data'=>[[$user, 'totalAdminNewsNum']]]],'alias'=>'hit_num'],
            ['id',[['dict','name'=>'buy_num','data'=>[[$user, 'totalAdminNewsNum']]]],'alias'=>'buy_num'],
            // 角色ID值转角色名称
            ['roleId',[['dict','name'=>'roleName','data'=>[[$user, 'getRoles']]]], 'alias'=>'roleName_text']
        ]);

        //var_dump($data);
    }

    public function testFormat()
    {

        $users = [
            ['id'=>1,'name'=>'hehe1','status'=>1,'ctime'=>'2018-01-01 12:00:00','roleId'=>1,'headPortrait'=>'/a/b/c1.jpg'],
            ['id'=>2,'name'=>'hehe2','status'=>2,'ctime'=>'2018-01-01 12:00:00','roleId'=>2,'headPortrait'=>'/a/b/c2.jpg'],
            ['id'=>3,'name'=>'hehe3','status'=>3,'ctime'=>'2018-01-01 12:00:00','roleId'=>3,'headPortrait'=>'/a/b/c3.jpg'],
        ];

        $data = $this->hformat->format($users,[UserFormat::defaultFormat(),['hit_num','buy_num']]);

        var_dump($data);
    }
}
