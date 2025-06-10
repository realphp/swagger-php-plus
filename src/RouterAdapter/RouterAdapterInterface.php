<?php

namespace RealPHP\SwaggerPhpPlus\RouterAdapter;

interface RouterAdapterInterface
{
    /**
     * 获取所有路由信息
     * @return RouteInfo[]
     */
    public function getRoutes(): array;

    /**
     * 将逻辑路径转换为实际路径
     */
    public function convertPath(string $logicalPath, string $controller, string $action): string;

    /**
     * 获取核心参数名
     */
    public function getCoreParamNames(): array;
}
