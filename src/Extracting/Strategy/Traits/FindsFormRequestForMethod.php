<?php

namespace RealPHP\SwaggerPhpPlus\Extracting\Strategy\Traits;

//use Illuminate\Foundation\Http\FormRequest;
use Framework\Model\RequestForm\RequestForm;
use ReflectionClass;
use ReflectionException;
use ReflectionFunctionAbstract;
use ReflectionUnionType;

trait FindsFormRequestForMethod
{
    protected function getFormRequestReflectionClass(ReflectionFunctionAbstract $method): ?ReflectionClass
    {
        foreach ($method->getParameters() as $argument) {
            $argType = $argument->getType();
            if ($argType === null || $argType instanceof ReflectionUnionType) continue;

            $argumentClassName = $argType->getName();

            if (!class_exists($argumentClassName)) continue;

            try {
                $argumentClass = new ReflectionClass($argumentClassName);
            } catch (ReflectionException $e) {
                continue;
            }
            if ($argumentClass->getName() === \Framework\Http\Request::class || $argumentClass->isSubclassOf(\Framework\Http\Request::class)) {
                return $argumentClass;
            }
        }

        return null;
    }
}
