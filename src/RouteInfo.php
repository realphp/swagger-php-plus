<?php

namespace RealPHP\SwaggerPhpPlus;
class RouteInfo
{
    public string $path;          // 实际路径 (如 '/index.php?c=user&a=profile')
    public string $httpMethod;     // HTTP方法 (GET/POST等)
    public string $controller;     // 控制器类名
    public string $action;         // 方法名
    public array $parameters = []; // 参数信息

    public function __construct(
        string $path,
        string $httpMethod,
        string $controller,
        string $action,
        array  $parameters = []
    )
    {
        $this->path = $path;
        $this->httpMethod = $httpMethod;
        $this->controller = $controller;
        $this->action = $action;
        $this->parameters = $parameters;
    }
}