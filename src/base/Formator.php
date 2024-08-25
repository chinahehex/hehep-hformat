<?php
namespace hehe\core\hformat\base;

use hehe\core\hformat\FormatManager;
use hehe\core\hformat\Utils;

/**
 * 格式器基类
 */
class Formator
{
    /**
     * 格式器别名
     * @var string
     */
    protected $alias;

    /**
     * 格式器函数
     * @var array|string|null
     */
    protected $func;

    public function __construct(array $attrs = [])
    {
        if (!empty($attrs)) {
            foreach ($attrs as $name => $value) {
                $this->{$name} = $value;
            }
        }

        $this->parseFunc();
    }

    /**
     * 给对象属性赋值
     *<B>说明：</B>
     *<pre>
     * 比较适合用于创建业务类
     *</pre>
     */
    protected function parseFunc():void
    {
        $call = [];
        if (!empty($this->func)) {
            if (is_string($this->func)) {
                $call = Utils::buildFormatorFunc($this->func);
            }  else if (is_array($this->func)) {
                $call = $this->func;
            } else if ($this->func instanceof \Closure) {
                $call = $this->func;
            }
        } else {
            $call = [$this,'formatValue'];
        }

        $this->func = $call;
    }

    /**
     * 格式化值
     * @param mixed $value
     */
    public function formatValue($value)
    {
        return $value;
    }

    /**
     * 格式化数据
     * @param $params
     * @return false|mixed
     */
    public function getValue(...$params)
    {
        $value = call_user_func_array($this->func,$params);

        return $value;
    }

    /**
     * 是否字典格式器
     * @return boolean
     */
    public function isDict():bool
    {
        return false;
    }
}
