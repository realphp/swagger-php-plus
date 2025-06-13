<?php

namespace RealPHP\SwaggerPhpPlus\RouteMatching;

use RealPHP\SwaggerPhpPlus\Contracts\RouteMatcherInterface;

class LaravelRouteMatcher implements RouteMatcherInterface
{
    /**
     * @param array $routeRules
     * @return array|RouteInfo[]
     */
    public function getRoutes(array $routeRules = []): array
    {
        return [];
    }

}
