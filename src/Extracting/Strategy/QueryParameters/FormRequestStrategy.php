<?php

namespace RealPHP\SwaggerPhpPlus\Extracting\Strategy\QueryParameters;

use RealPHP\SwaggerPhpPlus\Extracting\Strategy\FormRequestStrategyBase;

class FormRequestStrategy extends FormRequestStrategyBase
{
    protected string $customParameterDataMethodName = 'queryParameters';
}
