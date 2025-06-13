<?php

namespace RealPHP\SwaggerPhpPlus\Exceptions;

use RealPHP\SwaggerPhpPlus\Exceptions\SwaggerPhpPlusException;
use Throwable;

class CouldntProcessValidationRule extends \RuntimeException implements SwaggerPhpPlusException
{
    public static function forParam(string $paramName, $rule, Throwable $innerException): CouldntProcessValidationRule
    {
        return new self(
            "Couldn't process the validation rule " . var_export($rule, true) . " for the param `$paramName`: {$innerException->getMessage()}",
            0, $innerException
        );
    }
}
