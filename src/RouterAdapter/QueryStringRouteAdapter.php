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
        // 对于查询字符串路由，通常没有集中注册，需要扫描控制器
        $controllerDir = config('swagger.controller_dir', 'app/Controllers');
        $scanner = new ControllerScanner($controllerDir);
        return $scanner->scan();
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