<?php
namespace hehe\core\hformat;


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
 * @method array doFormat(array $datas = [],array $rules = [])
 * @method array doCustomFormat(array $datas = [],array ...$formatRules)
 * @method void addFormator(string $alias,$func)
 * @method void addFormators(array $formators)
 * @method void addFormatCollector(string $formatCollector)
 * @method void addFormatCollectors(array $formatCollectors):void
 */
class Format
{
    /**
     * @var FormatManager
     */
    public static $formatManager;

    public static function __callStatic($method, $params)
    {
        if (is_null(static::$formatManager)) {
            static::$formatManager = FormatManager::make();
        }

        return call_user_func_array([static::$formatManager,$method],$params);
    }
}
