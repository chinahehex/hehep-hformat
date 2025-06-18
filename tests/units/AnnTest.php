<?php
namespace hformat\tests\units;
use hehe\core\hcontainer\ContainerManager;
use hehe\core\hformat\Formation;
use hformat\tests\TestCase;
use hformat\tests\common\DefaultFormator;

class AnnTest extends TestCase
{
    /**
     * @var \hehe\core\hcontainer\ContainerManager
     */
    protected $hcontainer;

    protected function setUp():void
    {
        parent::setUp();
        $this->hcontainer = new ContainerManager();
        $this->hcontainer->addScanRule(DefaultFormator::class,Formation::class)
            ->startScan();
    }

    // 单个测试之后(每个测试方法之后调用)
    protected function tearDown():void
    {
        parent::tearDown();
    }

    public function testRes()
    {
        $img_url = $this->hformat->res('a/b/a.jpg');
        $this->assertSame('http://www.hehex.cn/a/b/a.jpg',$img_url);
    }

    public function testUrl()
    {
        $img_url = $this->hformat->url('a/b/a.jpg');
        $this->assertSame('2a/b/a.jpg',$img_url);
    }

    public function testUri()
    {
        $img_url = $this->hformat->uri('a/b/a.jpg');
        $this->assertSame('3a/b/a.jpg',$img_url);
    }




}
