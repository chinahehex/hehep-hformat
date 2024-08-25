<?php
namespace hehe\core\hformat\annotation;
use hehe\core\hcontainer\ann\base\Annotation;
use Attribute;
use hehe\core\hformat\Utils;

/**
 * @Annotation("hehe\core\hformat\annotation\FormatorAnnotationProcessor")
 */
#[Annotation("hehe\core\hformat\annotation\FormatorAnnotationProcessor")]
#[Attribute]
class AnnFormator
{
    /**
     * 事件名称
     * @var string
     */
    public $alias;

    /**
     * 构造方法
     *<B>说明：</B>
     *<pre>
     *  略
     *</pre>
     */
    public function __construct($value = null,string $alias = null)
    {
        Utils::argInjectProperty($this,func_get_args(),'alias');
    }

}
