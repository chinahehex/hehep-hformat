<?php
namespace hehe\core\hformat\formators;

use hehe\core\hformat\base\Formator;

// 日期格式器
class DateFormator extends Formator
{

    protected $format = 'Y-m-d';

    public function getValue(...$params)
    {
        return $this->formatDate(...$params);
    }

    protected function formatDate(string $value,string $format = '')
    {
        if ($format === '') {
            $format = $this->format;
        }

        return date($format,strtotime($value));
    }
}
