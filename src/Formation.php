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
 * @method int int($value = '',$params = [])
 * @method float float($value = '',$params = [])
 * @method array jsonDecode($value = '',$params = [])
 * @method stringt jsonEncode(array $data)
 * @method array toArr($value = '',$params = [])
 * @method string trim($value = '',$params = [])
 * @method string|DictFormator dict($value = '',$params = [])
 * @method string date(string $value,string $format = '')
 */
class Formation
{

    /**
     * 自定义格式器
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
        'int'=>['class'=>'CommonFormator@@intFormator'],
        'float'=>['class'=>'CommonFormator@@floatFormator'],
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
     * @param array $config 属性
     */
    public function __construct(array $config = [])
    {
        // 注入属性
        if (!empty($config)) {
            foreach ($config as $key => $value) {
                $this->$key = $value;
            }
        }

        // 注册自定义格式器
        static::addFormatCollectors(...$this->formatCollectors);
    }

    /**
     * 添加格式器
     *<B>说明：</B>
     *<pre>
     * 略
     *</pre>
     * @param string|array $formator 格式函数
     * @param string $alias 格式器别名
     * @return void
     */
    public static function addFormator($formator,string $alias = ''):void
    {
        if ($alias !== '') {
            static::$formators[$alias] = $formator;
        } else {
            static::$formators = array_merge(static::$formators,Utils::buildFormator($formator));
        }
    }

    /**
     * 添加格式化集合
     * @param array $formatCollectors
     * @return void
     */
    public static function addFormatCollectors(...$formatCollectors):void
    {
        foreach ($formatCollectors as $formator) {
            static::$formators = array_merge(static::$formators,Utils::buildFormator($formator));
        }
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
    public function createFormator(string $alias,array $config = []):?Formator
    {
        $formatorPropertys = static::buildFormatorPropertys($alias,$config);
        $formatorClass = $formatorPropertys['class'];
        unset($formatorPropertys['class']);
        return new $formatorClass($formatorPropertys);
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
    public static function makeFormator(string $alias,array $config = []):?Formator
    {
        $formatorPropertys = static::buildFormatorPropertys($alias,$config);
        $formatorClass = $formatorPropertys['class'];
        unset($formatorPropertys['class']);
        return new $formatorClass($formatorPropertys);
    }

    /**
     * 构建格式器属性
     * @param string $alias
     * @param array $config
     * @return array
     * @throws \Exception
     */
    protected static function buildFormatorPropertys(string $alias,array $config = []):array
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
    protected function buildFormatRules(array $rules,array $datas):array
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
     * @param array $rules 格式化规则集合
     * @param array $customFormatItems 自定义格式项['hits','order_num']
     * @return array
     */
    public function doFormat(array $datas = [],array $rules = [],array $customFormatItems = []):array
    {
        if (!empty($customFormatItems)) {
            $rules = $this->filterRules($rules,$customFormatItems);
        }

        $formatRules = $this->buildFormatRules($rules,$datas);
        foreach ($datas as $index=>$data) {
            foreach ($formatRules as $formatRule) {
                $dataid = $formatRule->getDataId();
                if (isset($data[$dataid])) {
                    $value = $formatRule->getValue($data[$dataid]);
                    $data[$formatRule->getAlias()] = $value === null ? $formatRule->getDefautValue() : $value;
                }
            }

            $datas[$index] = $data;
        }

        return $datas;
    }

    protected function filterRules(array $rules,array $customFormatItems = []):array
    {
        // 允许规则名称集合
        $allowRuleNames = [];
        $allowRuleConfig = [];
        if (!empty($customFormatItems)) {
            foreach ($customFormatItems as $name=>$ruleConfig) {
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
        if (!isset(self::$formators[$alias])) {
            throw new \Exception('method not exists:' . __CLASS__ . '->' . $alias);
        }

        if (!empty($params)) {
            return $this->createFormator($alias)->getValue(...$params);
        } else {
            return $this->createFormator($alias);
        }
    }

    /**
     * 格式器快捷方式
     *<B>说明：</B>
     *<pre>
     *　一般用于单个值格式化
     *</pre>
     * @param string $alias  方法名
     * @param array $params 方法参数
     * @return boolean
     * @throws \Exception
     */
    public static function __callStatic($alias, $params)
    {
        if (!isset(self::$formators[$alias])) {
            throw new \Exception('method not exists:' . __CLASS__ . '->' . $alias);
        }

        if (!empty($params)) {
            return static::makeFormator($alias)->getValue(...$params);
        } else {
            return static::makeFormator($alias);
        }
    }

    /**
     * 安装格式器
     * @param string|array $targetNamespace 目标类命名空间路径
     * @param bool $onlyTargetClass 是否只安装目标类,true 只安装目标类,false 安装目标类位置的所有类
     * @param bool $subDir 是否搜索子目录
     * @param string $suffix 安装名称后缀
     */
    public static function install(string $targetNamespace,bool $onlyTargetClass = true,bool $subDir = false,string $suffix = 'Formator')
    {
        $suffix_preg = '/(.+)' . $suffix .'.php$/';

        // 判断是命令空间还是路径
        if (is_array($targetNamespace)) {
            $onlyTargetClass = false;
            list($base_path,$base_class_namespace) = $targetNamespace;
        } else {
            $reflectionClass = new \ReflectionClass($targetNamespace);
            $base_path = dirname($reflectionClass->getFileName());
            $base_class_namespace = str_replace(DIRECTORY_SEPARATOR,'\\',dirname(str_replace('\\',DIRECTORY_SEPARATOR,$targetNamespace)));
        }

        $find_class = function($base_class_namespace,$base_path,$suffix_preg,$is_read_dir) use(&$find_class) {
            $fileList = scandir($base_path);
            $classList = [];
            foreach ($fileList as $filename) {
                if ($filename === '.' || $filename === '..'){
                    continue;
                }

                $filePath = $base_path . DIRECTORY_SEPARATOR . $filename;
                if (is_dir($filePath)) {
                    if ($is_read_dir) {
                        $read_dir = false;
                        $dir_class_list = $find_class($base_class_namespace . '\\' . $filename,$filePath,$suffix_preg,$read_dir);
                        $classList = array_merge($dir_class_list,$classList);
                    } else {
                        continue;
                    }
                } else {
                    // 判断后缀
                    if (preg_match($suffix_preg, $filename, $matches)) {
                        $classNamespace = $base_class_namespace . '\\' . basename($filename,".php");
                        if (class_exists($classNamespace) || interface_exists($classNamespace)) {
                            $classList[] = $classNamespace;
                        }
                    }
                }
            }

            return $classList;
        };
        $classList = [];
        if ($onlyTargetClass) {
            $classList[] = $targetNamespace;
        } else {
            $classList = $find_class($base_class_namespace,$base_path,$suffix_preg,$subDir);
        }

        static::addFormatCollectors(...$classList);
    }
}
