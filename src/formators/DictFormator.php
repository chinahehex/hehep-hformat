<?php
namespace hehe\core\hformat\formators;

use hehe\core\hformat\base\Formator;
use hehe\core\hformat\base\Rule;
use hehe\core\hformat\Utils;

class DictFormator extends Formator
{
    /**
     * 字典数据id键名
     * @var string
     */
    protected $id = 'id';

    /**
     * 字典数据名称键名
     * @var string
     */
    protected $name = 'name';

    /**
     * 字典数据缓存key,如未填,则默认为"规则"名称
     * @var string
     */
    protected $cache = '';

    /**
     * 获取字典数据方法
     * @var array
     */
    protected $data;

    protected $_data;

    protected $args = [];

    public function isDict():bool
    {
        // 获取方法参数
        return true;
    }

    public function getCache():string
    {
        return $this->cache ?? $this->name;
    }

    public function hasCache():bool
    {
        if ($this->cache !== '') {
            return true;
        } else {
            return false;
        }
    }

    protected function parseMethod($dataMethod)
    {
        $call = [];
        if (!empty($dataMethod)) {
            if (is_string($dataMethod)) {
                if (substr($dataMethod,0,1) === ':') {
                    $call = substr($dataMethod,1);
                } else {
                    $call = Utils::buildFormatorFunc($dataMethod);
                }
            }  else if (is_array($dataMethod)) {
                $call = $dataMethod;
            } else if ($dataMethod instanceof \Closure) {
                $call = $dataMethod;
            }
        }

        return $call;
    }

    public function buildData(Rule $rule,array $datas):?array
    {
        if (is_null($this->_data)) {
            $dataIds  = Utils::getColumn($datas,$rule->getDataId());
            $args = $this->args;
            // 插入函数参数
            array_unshift($args,$dataIds);
            $this->setData(call_user_func_array($this->data,$args));
        }

        return $this->_data;
    }

    public function setData(array $data):self
    {
        $this->_data = Utils::index($data,$this->id);

        return $this;
    }

    public function getData():?array
    {
        return $this->_data;
    }

    public function formatValue($idValue)
    {
        if (isset($this->_data[$idValue][$this->name])) {
            return $this->_data[$idValue][$this->name];
        } else {
            return null;
        }
    }


}
