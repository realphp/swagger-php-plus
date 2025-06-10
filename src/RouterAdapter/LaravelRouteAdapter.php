<?php

namespace RealPHP\SwaggerPhpPlus\RouterAdapter;

class LaravelRouteAdapter implements RouterAdapterInterface
{
    public function getRoutes(): array
    {
        $routes = [];
        $router = app('router');

        foreach ($router->getRoutes() as $route) {
            $action = $route->getAction();

            if (isset($action['controller'])) {
                [$controller, $action] = explode('@', $action['controller']);

                $routes[] = new RouteInfo(
                    path: $route->uri(),
                    httpMethod: implode('|', $route->methods()),
                    controller: $controller,
                    method: $action,
                    parameters: $route->wheres
                );
            }
        }

        return $routes;
    }

    public function convertPath(string $logicalPath, string $controller, string $action): string
    {
        return $logicalPath;
    }

    public function getCoreParamNames(): array
    {
        return []; // Laravel 不需要核心参数
    }
}
