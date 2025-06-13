<?php

namespace RealPHP\SwaggerPhpPlus\RouteMatching;

use PhpParser\Node\Expr\Closure;
use RealPHP\SwaggerPhpPlus\Contracts\RouteMatcherInterface;

class FastRouteMatcher implements RouteMatcherInterface
{
    public function __construct(private array $fastRoutes)
    {
    }

    /**
     * @param array $routeRules
     * @return array|RouteInfo[]
     */
    public function getRoutes(array $routeRules = []): array
    {
        $routeInfos = [];
        foreach ($this->fastRoutes[0] as $method => $fastRoutes) {
            foreach ($fastRoutes as $key => $fastRoute) {
                if ($fastRoute->getHandler() instanceof \Closure) {
                    continue;
                }
                [$controller, $method] = explode('@', $fastRoute->getHandler());
                $routeInfos[$key] = new RouteInfo(
                    $key,
                    [$fastRoute->getMethod()],
                    $controller,
                    $method
                );
            }
        }
        return $routeInfos;
    }


}
