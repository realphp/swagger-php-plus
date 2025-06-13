<?php

namespace RealPHP\SwaggerPhpPlus\Config;

use RealPHP\SwaggerPhpPlus\Extracting\Strategy\QueryParameters\FormRequestStrategy;

class Defaults
{
    public const QUERY_PARAMETERS_STRATEGIES = [
        FormRequestStrategy::class,
    ];

}
