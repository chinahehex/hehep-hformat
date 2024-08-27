<?php
namespace hformat\tests;

use hehe\core\hformat\FormatManager;
use hformat\tests\common\DefaultFormator;
use hformat\tests\common\ImgFormator;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FormatManager
     */
    protected $hformat;
    // 单个测试之前(每个测试方法之前调用)
    protected function setUp()
    {
        $this->hformat = new FormatManager();

        FormatManager::addFormatCollector(ImgFormator::class);
    }

    // 单个测试之后(每个测试方法之后调用)
    protected function tearDown()
    {

    }

    // 整个测试类之前
    public static function setUpBeforeClass()
    {

    }

    // 整个测试类之前
    public static function tearDownAfterClass()
    {

    }


}
