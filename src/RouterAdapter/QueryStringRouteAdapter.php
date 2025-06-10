<?php

namespace RealPHP\SwaggerPhpPlus\RouterAdapter;

class QueryStringRouteAdapter implements RouterAdapterInterface
{
    private $entryFile;
    private $controllerParam;
    private $actionParam;

    public function __construct(
        string $entryFile = 'index.php',
        string $controllerParam = 'c',
        string $actionParam = 'a'
    )
    {
        $this->entryFile = $entryFile;
        $this->controllerParam = $controllerParam;
        $this->actionParam = $actionParam;
    }

    public function getRoutes(): array
    {

        $routes[] = new RouteInfo(
            path: '', // 将在适配器中填充
            httpMethod: 'get',
            controller: 'IndexController',
            method: 'index',
            parameters: []
        );
        $routes[] = new RouteInfo(
            path: '', // 将在适配器中填充
            httpMethod: 'post',
            controller: 'IndexController2xx',
            method: 'save',
            parameters: []
        );
        return $routes;
    }

    public function convertPath(string $logicalPath, string $controller, string $action): string
    {
        $queryParams = [
            $this->controllerParam => $controller,
            $this->actionParam => $action
        ];

        return '/' . $this->entryFile . '?' . http_build_query($queryParams);
    }

    public function getCoreParamNames(): array
    {
        return [$this->controllerParam, $this->actionParam];
    }
}
