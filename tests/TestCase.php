<?php
namespace hformat\tests;

use hehe\core\hformat\Formation;
use hformat\tests\common\DefaultFormator;
use hformat\tests\common\ImgFormator;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Formation
     */
    protected $hformat;
    // 单个测试之前(每个测试方法之前调用)
    protected function setUp():void
    {
        $this->hformat = new Formation();

        $this->hformat->addFormatCollectors(ImgFormator::class);
    }

    // 单个测试之后(每个测试方法之后调用)
    protected function tearDown():void
    {

    }

    // 整个测试类之前
    public static function setUpBeforeClass():void
    {

    }

    // 整个测试类之前
    public static function tearDownAfterClass():void
    {

    }


}
