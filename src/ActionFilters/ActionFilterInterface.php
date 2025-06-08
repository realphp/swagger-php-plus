<?php

namespace RealPHP\SwaggerPhpPlus\ActionFilters;

use ReflectionMethod;

interface ActionFilterInterface
{
    public function shouldGenerate(string $controller, string $action, ReflectionMethod $method): bool;
}