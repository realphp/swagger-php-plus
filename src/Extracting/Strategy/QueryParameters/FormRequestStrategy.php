<?php

namespace RealPHP\SwaggerPhpPlus\Extracting\Strategy\QueryParameters;

use RealPHP\SwaggerPhpPlus\Extracting\Strategy\FormRequestBaseStrategy;

class FormRequestStrategy extends FormRequestBaseStrategy
{
    protected string $customParameterDataMethodName = 'queryParameters';

    protected function isFormRequestMeantForThisStrategy(\ReflectionClass $formRequestReflectionClass): bool
    {
        // Only use this FormRequest for body params if there's no "Query parameters" in the docblock
        // Or there's a bodyParameters() method
        $formRequestDocBlock = $formRequestReflectionClass->getDocComment();
        if (strpos(strtolower($formRequestDocBlock), "query parameters") !== false
            || $formRequestReflectionClass->hasMethod('queryParameters')) {
            return false;
        }

        return true;
    }
}
