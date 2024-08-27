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

    public function isDict():bool
    {
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

    public function buildData(Rule $rule,array $datas):?array
    {
        if (is_null($this->_data)) {
            $column_values  = Utils::getColumn($datas,$rule->getDataId());
            $data_func_params = array_slice($this->data, 1);
            // 插入函数参数
            array_unshift($data_func_params,$column_values);
            $this->setData(call_user_func_array($this->data[0],$data_func_params));
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
