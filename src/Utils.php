<?php
namespace hehe\core\hformat;

class Utils
{
    /**
     * 读取数组key对应的值
     *<B>说明：</B>
     *<pre>
     * 略
     *</pre>
     *<B>示例：</B>
     *<pre>
     * $array = ['id' => '123', 'data' => 'abc','age'=>null,'user'=>['user_name'=>'admin_ok']];
     *
     * $result = ArrayUtil::getValue($array,'id');
     * the result is:
     * 123
     *
     * 默认值
     * $result = ArrayUtil::getValue($array,'age',1);
     * the result is:
     * 1
     *
     * 读取复合列
     * $result = ArrayUtil::getValue($array,'user.user_name');
     * the result is:
     * admin_ok
     *
     *</pre>
     * @param array $array 数据
     * @param string $key 读取的键名
     * @param string $default 默认值
     * @return string
     */
    public static function getValue($array, string $key, $default = null)
    {

        if (isset($array[$key])) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (isset($array[$key])) {
            return $array[$key];
        } else {
            return $default;
        }
    }

    /**
     * 根据指定的key 读取对应的值
     *<B>说明：</B>
     *<pre>
     * 略
     *</pre>
     *<B>示例：</B>
     *<pre>
     * $array = [
     *     ['id' => '123', 'data' => 'abc','class'=>'x'],
     *     ['id' => '345', 'data' => 'def','class'=>'x'],
     *     ['id' => '346', 'data' => 'dec','class'=>'y'],
     * ];
     * $result = ArrayUtil::getColumn($array, 'id');
     * 结果:
     * ['123', '345']
     *
     * $result = ArrayUtil::getColumn($array, 'id','class');
     * 结果:
     * [
     *     'x' => ['123','345'],
     *     'y' => ['346'],
     * ]
     *</pre>
     * @param array $array 数据
     * @param string $name 获取列的名称
     * @param string $group_key 分租key
     * @return array
     */
    public static function getColumn($array, $name,$group_key = null)
    {
        $result = [];
        if ($group_key !== null) {
            foreach ($array as $k => $element) {
                $group_val = static::getValue($element, $group_key);
                $result[$group_val][] = static::getValue($element, $name);
            }
        } else {
            foreach ($array as $k => $element) {
                $result[] = static::getValue($element, $name);
            }
        }

        return $result;
    }

    /**
     * 根据指定的键组合成索引数组
     *<B>说明：</B>
     *<pre>
     * 略
     *</pre>
     *<B>示例：</B>
     *<pre>
     * $array = [
     *     ['id' => '123','role_id'=>1, 'data' => 'abc'],
     *     ['id' => '345','role_id'=>2,, 'data' => 'def'],
     * ];
     *
     * $result = ArrayUtil::index($array, 'id');
     *  the result is:
     *  [
     *     '123' => ['id' => '123','role_id'=>1,'data' => 'abc'],
     *     '345' => ['id' => '345','role_id'=>2,'data' => 'def'],
     * ]
     * // $key 第一元素必须为分隔符
     * $result = ArrayUtil::index($array, ['_','id,'role_id']);
     *  the result is:
     *  [
     *     '123_1' => ['id' => '123','role_id'=>1,'data' => 'abc'],
     *     '345_2' => ['id' => '345','role_id'=>2,'data' => 'def'],
     * ]
     *
     * $result = ArrayUtil::index($array, 'id',['role_id','id']);
     *  [
     *     '123' => ['id' => '123','role_id'=>1],
     *     '345' => ['id' => '345','role_id'=>2],
     * ]
     *</pre>
     * @param array $array 数据
     * @param string|array $key 索引键名
     * @param array $fields 读取的键名
     * @return array
     */
    public static function index($array, $key,$fields = [])
    {
        $result = [];
        $separator = null;
        if (is_array($key)) {
            // 第一元素为分隔符
            $separator = array_shift($key);
        }

        foreach ($array as $element) {
            if ($separator !== null) {
                $key_values = static::getField($element,$key);
                $index_key = implode($separator,$key_values);
            } else {
                $index_key = static::getValue($element, $key);
            }

            if (!empty($fields)) {
                $result[$index_key] = static::getField($element,$fields);
            } else {
                $result[$index_key] = $element;
            }
        }

        return $result;
    }

    /**
     * 根据指定的key 读取一维数组对应的值
     *<B>说明：</B>
     *<pre>
     * 略
     *</pre>
     *<B>示例：</B>
     *<pre>
     * $array = ['id' => '123', 'data' => 'abc','name'=>'admin'];
     * $result = ArrayUtil::getField($array, ['id','name']);
     * the result is:
     * ['id'=>'123','name'=>'admin']
     *</pre>
     * @param array $data 数据
     * @param array $fields 数组键
     * @return array
     */
    public static function getField(array $data,array $fields = [])
    {
        $result = [];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $result[$field] = $data[$field];
            }
        }

        return $result;
    }

    public static function buildFormatorFunc(string $func):array
    {
        $newClass = true;
        if (strpos($func,"@@") !== false) {
            list($class,$method) = explode('@@',$func);
        }  else if (strpos($func,"@") !== false) {
            list($class,$method) = explode('@',$func);
            $newClass = true;
        } else {
            $class = $func;
            $method = 'format';
            $newClass = true;
        }

        if (strpos($class,'\\') === false) {
            $class = implode('\\',explode('\\',static::class,-1)) . '\\formators\\' . ucfirst($class);
        }

        if ($newClass) {
            return [new $class(),$method];
        } else {
            return [$class,$method];
        }
    }

    /**
     * 注解构造参数转关联数组
     * @param array $args 构造参数
     * @param string $firstArgName 第一个构造参数对应的属性名
     * @return array
     * @throws \ReflectionException
     */
    public static function argToDict(string $class,array $args = [],string $firstArgName = ''):array
    {
        // php 注解
        $values = [];
        if (!empty($args)) {
            if (is_string($args[0]) || is_null($args[0])) {
                $arg_params = (new \ReflectionClass(get_class($class)))->getConstructor()->getParameters();
                foreach ($arg_params as $index => $param) {
                    $name = $param->getName();
                    $value = null;
                    if (isset($args[$index])) {
                        $value = $args[$index];
                    } else {
                        if ($param->isDefaultValueAvailable()) {
                            $value = $param->getDefaultValue();
                        }
                    }

                    if (!is_null($value)) {
                        $values[$name] = $value;
                    }
                }
            } else if (is_array($args[0])) {
                $values = $args[0];
            }
        }

        $value_dict = [];
        foreach ($values as $name => $value) {
            if (is_null($value)) {
                continue;
            }

            if ($name == 'value' && $firstArgName != '') {
                $value_dict[$firstArgName] = $value;
            } else {
                $value_dict[$name] = $value;
            }
        }


        return $value_dict;
    }

    /**
     * 构造参数注入注解属性
     * @param array $annotation 注解类
     * @param array $args 构造参数
     * @param string $firstArgName 第一个构造参数对应的属性名
     */
    public static function argInjectProperty($annotation,array $args = [],string $firstArgName = ''):void
    {
        $values = self::argToDict(get_class($annotation),$args,$firstArgName);

        foreach ($values as $name=>$value) {
            $annotation->{$name} = $value;
        }
    }

}
