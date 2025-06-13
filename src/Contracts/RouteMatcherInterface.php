<?php

namespace RealPHP\SwaggerPhpPlus\Contracts;
interface RouteMatcherInterface
{
    /**
     * @param array $routeRules
     * @return array|RouteInfo[]
     */
    public function getRoutes(array $routeRules = []): array;
}
