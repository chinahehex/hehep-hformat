<?php
namespace hehe\core\hformat\base;

use hehe\core\hformat\FormatManager;
use hehe\core\hformat\formators\DictFormator;

/**
 * 格式规则类
 */
class Rule
{
    /**
     * 规则名称
     * @var string
     */
    protected $name;

    /**
     * 格式器列表
     * @var Formator[]|DictFormator[]
     */
    protected $formators = [];

    /**
     * 键名别名或新名称
     * @var string
     */
    protected $alias = '';

    /**
     * 默认值
     * @var string
     */
    protected $defval = '';

    /**
     * 数据键名
     * @var string
     */
    protected $dataid = '';

    public function __construct(array $ruleConfig = [])
    {
        $this->formators = $ruleConfig[1];
        $this->name = $ruleConfig[0];

        $attrs = array_slice($ruleConfig, 2);
        if (!empty($attrs)) {
            foreach ($attrs as $name => $value) {
                $this->{$name} = $value;
            }
        }

        $this->buildFormator();
    }


    public function getFormators():array
    {
        return $this->formators;
    }

    public function getName():string
    {
        return $this->name;
    }

    public function getDataId():string
    {
        return $this->dataid !== '' ? $this->dataid : $this->name;
    }

    /**
     * 获取新字段名称
     * @return string
     */
    public function getAlias():string
    {
        if (!empty($this->alias)) {
            if (substr($this->alias,0,1) == ':') {
                $key = $this->name . substr($this->alias,1);
            } else {
                $key = $this->alias;
            }
        } else {
            $key = $this->name;
        }

        return $key;
    }

    public function getDefautValue()
    {
        return $this->defval;
    }

    protected function buildFormator()
    {
        $formators =  [];
        foreach ($this->formators as $formatorArr) {
            $formatorAlias = $formatorArr[0];
            $formatorConfig = array_slice($formatorArr, 1);
            $formator = FormatManager::createFormator($formatorAlias,$formatorConfig);
            $formators[] = $formator;
        }

        $this->formators = $formators;
    }

    /**
     * 构建字典数据
     * @param array $datas
     * @param array $dictFormatorData
     */
    public function buildFormatorData(array $datas,&$dictFormatorData)
    {
        foreach ($this->formators as $formator) {
            if ($formator->isDict()) {
                // 获取字典数据
                if ($formator->hasCache()) {
                    $cacheKey = $formator->getCache();
                    if (isset($dictFormatorData[$cacheKey])) {
                        $formator->setData($dictFormatorData[$cacheKey]);
                    } else {
                        $dictFormatorData[$cacheKey] = $formator->buildData($this,$datas);
                    }
                } else {
                    $formator->buildData($this,$datas);
                }
            }
        }
    }

    /**
     * 获取格式化后的值
     * @param $value
     * @return false|mixed
     */
    public function getValue($value)
    {
        foreach ($this->formators as $formator) {
            $value = $formator->getValue($value);
        }

        return $value;
    }

}
