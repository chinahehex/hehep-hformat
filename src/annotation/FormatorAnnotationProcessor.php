<?php
namespace hehe\core\hformat\annotation;

use hehe\core\hcontainer\ann\base\AnnotationProcessor;
use hehe\core\hformat\base\Formator;
use hehe\core\hformat\FormatManager;

/**
 * 格式器注解处理器
 * 用于收集格式器注解的信息
 */
class FormatorAnnotationProcessor extends AnnotationProcessor
{
    protected $batchFormators  = [];

    protected $formators = [];

    public function handleAnnotationClass($annotation,string $class):void
    {
        if (is_subclass_of($class,Formator::class)) {
            if (!empty($annotation->alias)) {
                $this->formators[$annotation->alias] = $class;
            } else {
                $this->formators[lcfirst(substr((new \ReflectionClass($class))->getShortName(),0,-8))] = $class;
            }
        } else {
            $this->batchFormators[] = $class;
        }
    }

    public function handleAnnotationMethod($annotation,string $class,string $method):void
    {
        $reflectionMethod = new \ReflectionMethod($class,$method);
        $func = $class . ($reflectionMethod->isStatic() ?  '@@' . $method : '@' . $method);

        if (!empty($annotation->alias)) {
            $this->formators[$annotation->alias] = $func;
        } else {
            if (substr($method,-8) === 'Formator') {
                $method_alias = substr($method,0,-8);
            } else {
                $method_alias = $method;
            }
            $this->formators[$method_alias] = $func;
        }
    }

    public function handleProcessorFinish()
    {
        FormatManager::addBatchFormators($this->batchFormators);
        FormatManager::addFormators($this->formators);
    }

}
