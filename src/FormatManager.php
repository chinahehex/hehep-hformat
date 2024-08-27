<?php
namespace hehe\core\hformat;

use hehe\core\hformat\base\Formator;
use hehe\core\hformat\base\Rule;
use hehe\core\hformat\formators\CommonFormator;
use hehe\core\hformat\formators\DictFormator;


/**
 * 格式器组件类
 *<B>说明：</B>
 *<pre>
 * 略
 * </pre>
 * @method string jsonEncode($value = '',$params = [])
 * @method array jsonDecode($value = '',$params = [])
 * @method array toArr($value = '',$params = [])
 * @method string trim($value = '',$params = [])
 * @method string|DictFormator dict($value = '',$params = [])
 * @method string date(string $value,string $format = '')
 */
class FormatManager
{
    protected $bind = true;

    /**
     * 格式化集合器
     * @var array
     */
    protected $formatCollectors  = [];

    /**
     * 格式器列表
     *<B>说明：</B>
     *<pre>
     *　略
     *</pre>
     * @param array
     */
    protected static $formators = [
        'jsonEncode'=>['class'=>'CommonFormator@@jsonEncodeFormator'],
        'jsonDecode'=>['class'=>'CommonFormator@@jsonDecodeFormator'],
        'toArr'=>['class'=>'CommonFormator@@toArrFormator'],
        'trim'=>['class'=>'CommonFormator@@trimFormator'],
        'date'=>['class'=>'CommonFormator@@dateFormator'],
        'dict'=>['class'=>'DictFormator'],
    ];

    /**
     * 构造方法
     *<B>说明：</B>
     *<pre>
     * 略
     *</pre>
     * @param array $attrs 属性
     */
    public function __construct(array $attrs = [])
    {
        // 注入属性
        if (!empty($attrs)) {
            foreach ($attrs as $name => $value) {
                $this->{$name} = $value;
            }
        }

        static::addFormatCollectors($this->formatCollectors);

        if ($this->bind) {
            Format::$formatManager = $this;
        }
    }

    public static function make(array $attrs = [])
    {
        return new static($attrs);
    }

    /**
     * 添加格式化集合器
     * @param string $formatCollector
     * @return void
     */
    public static function addFormatCollector(string $formatCollector):void
    {
        static::$formators = array_merge(static::$formators,Utils::buildFormatCollector($formatCollector));
    }

    /**
     * 添加格式化集合器
     * @param array $formatorClasss
     * @return void
     */
    public static function addFormatCollectors(array $formatCollectors):void
    {
        foreach ($formatCollectors as $formatCollector) {
            static::addFormatCollector($formatCollector);
        }
    }

    /**
     * 添加格式器
     *<B>说明：</B>
     *<pre>
     * 略
     *</pre>
     * @param string $alias 格式器别名
     * @param string|array $func 格式函数
     * @return void
     */
    public static function addFormator(string $alias,$func):void
    {
        static::$formators[$alias] = $func;
    }

    public static function addFormators(array $formators):void
    {
        static::$formators = array_merge(static::$formators,$formators);
    }


    /**
     * 创建格式器对象
     *<B>说明：</B>
     *<pre>
     *　略
     *</pre>
     * @param string $alias 格式器别名
     * @param array $config 格式器配置
     * @return Formator|null
     */
    public static function createFormator(string $alias,array $config = [])
    {
        $formatorPropertys = static::buildFormatorPropertys($alias,$config);
        $formatorClass = $formatorPropertys['class'];
        unset($formatorPropertys['class']);
        $formator = new $formatorClass($formatorPropertys);

        return $formator;
    }

    protected static function buildFormatorPropertys(string $alias,array $config = [])
    {
        if (!isset(self::$formators[$alias])) {
            throw new \Exception('invalid format alias:' . $alias);
        }

        $formator = self::$formators[$alias];

        if (is_array($formator)) {
            if (isset($formator['class'])) {
                $class = $formator['class'];
                unset($formator['class']);
            } else {
                $class =  Formator::class;
            }
            $config = array_merge($formator,$config);
        } else {
            $class = $formator;
        }

        if (strpos($class,"@") !== false) {
            $config['func'] = $class;
            $class = Formator::class;
        }

        if (strpos($class,'\\') === false) {
            $class = __NAMESPACE__ . '\\formators\\' . ucfirst($class);
        }

        $config['alias'] = $alias;
        $config['class'] = $class;

        return $config;
    }


    /**
     * 用户规则转为格式规则对象
     * @param array $rules
     * @param array $datas
     * @return Rule[]
     */
    protected function rulesToFormatRule(array $rules,array $datas):array
    {
        $formatRules = [];
        $dictFormatorData = [];
        foreach ($rules as $ruleConfig) {
            if ($ruleConfig instanceof Rule) {
                $rule = $ruleConfig;
            } else if (is_array($ruleConfig) && isset($ruleConfig[0], $ruleConfig[1])) {
                $rule = new Rule($ruleConfig);
            }

            $rule->buildFormatorData($datas,$dictFormatorData);
            $formatRules[] = $rule;
        }

        return $formatRules;
    }

    /**
     * 格式化数据
     *<B>说明：</B>
     *<pre>
     *　略
     *</pre>
     * @param array $datas 格式化数据
     * @param array $rules 格式化规则
     * @return array
     */
    public function doFormat(array $datas = [],array $rules = [])
    {
        $formatRules = $this->rulesToFormatRule($rules,$datas);
        foreach ($datas as $index=>$attrs) {
            foreach ($formatRules as $formatRule) {
                $dataid = $formatRule->getDataId();
                if (isset($attrs[$dataid])) {
                    $value = $formatRule->getValue($attrs[$dataid]);
                    $attrs[$formatRule->getAlias()] = $value === null ? $formatRule->getDefautValue() : $value;
                }
            }

            $datas[$index] = $attrs;
        }

        return $datas;
    }

    /**
     * 格式化数据
     * @param array $datas
     * @param array ...$formatRules <格式化规则,格式化允许名称集合>
     * @return array
     */
    public function doCustomformat(array $datas = [],array ...$formatRules):array
    {
        $ruleList = [];
        foreach ($formatRules as $formatRule) {
            list($rules,$allowNames) = $formatRule;
            if (empty($allowNames)) {
                $allowNames = [];
            }

            $ruleList = array_merge($ruleList,$this->filterRules($rules,$allowNames));
        }

        return $this->doFormat($datas,$ruleList);
    }

    protected function filterRules(array $rules,array $allowNames = []):array
    {
        // 允许规则名称集合
        $allowRuleNames = [];
        $allowRuleConfig = [];
        if (!empty($allowNames)) {
            foreach ($allowNames as $name=>$ruleConfig) {
                if (is_string($ruleConfig)) {
                    $allowRuleNames[] = $ruleConfig;
                } else {
                    $allowRuleNames[] = $name;
                    $allowRuleConfig[$name] = $ruleConfig;
                }
            }
        }

        $validRules = [];
        foreach ($rules as $ruleConfig) {
            $name = $ruleConfig[0];
            if (isset($allowRuleConfig[$name])) {
                $ruleConfig = array_merge($ruleConfig,$allowRuleConfig[$name]);
            }

            $rule = new Rule($ruleConfig);
            if (!empty($allowRuleNames) && in_array($name,$allowRuleNames)) {
                $validRules[] = $rule;
                continue;
            }

            if ($rule->isDefault()) {
                $validRules[] = $rule;
                continue;
            }
        }

        return $validRules;
    }


    /**
     * 格式器快捷方式
     *<B>说明：</B>
     *<pre>
     *　略
     *</pre>
     * @param string $alias 格式器别名
     * @param array $params 格式器方法参数
     * @return mixed|Formator|DictFormator
     */
    public function __call(string $alias, array $params)
    {
        $formator = static::createFormator($alias);

        if (!empty($params)) {
            return $formator->getValue(...$params);
        } else {
            return $formator;
        }
    }
}
