<?php

namespace RealPHP\SwaggerPhpPlus\RouteMatching;
class RouteInfo
{
    public string $uri;          // 实际路径 (如 '/index.php?c=user&a=profile')
    public array $httpMethod;     // HTTP方法 (GET/POST等)
    public string $controller;     // 控制器类名
    public string $method;         // 方法名
    public array $parameters = []; // 参数信息

    public array $headers = [];
    public array $queryParameters = [];

    public function __construct(
        string $uri,
        array  $httpMethod,
        string $controller,
        string $method,
        array  $parameters = []
    )
    {
        $this->uri = $uri;
        $this->httpMethod = $httpMethod;
        $this->controller = $controller;
        $this->method = $method;
        $this->parameters = $parameters;
    }
}
