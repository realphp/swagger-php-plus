<?php

namespace RealPHP\SwaggerPhpPlus\Exceptions;

use Throwable;

class ProblemParsingValidationRules extends \RuntimeException implements SwaggerPhpPlusException
{
    public static function forParam(string $paramName,  Throwable $innerException): ProblemParsingValidationRules
    {
        return new self(
            "Problem processing validation rules for the param `$paramName`: {$innerException->getMessage()}",
            0, $innerException);
    }
}
