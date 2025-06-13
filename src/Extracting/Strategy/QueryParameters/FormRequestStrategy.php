<?php

namespace RealPHP\SwaggerPhpPlus\Extracting\Strategy\QueryParameters;

use RealPHP\SwaggerPhpPlus\Extracting\Strategy\FormRequestBaseStrategy;

class FormRequestStrategy extends FormRequestBaseStrategy
{
    protected string $customParameterDataMethodName = 'queryParameters';
}
