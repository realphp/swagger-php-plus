<?php

namespace RealPHP\SwaggerPhpPlus\RouteMatching;
class RouteInfo
{
    public string $path;          // 实际路径 (如 '/index.php?c=user&a=profile')
    public array $httpMethod;     // HTTP方法 (GET/POST等)
    public string $controller;     // 控制器类名
    public string $method;         // 方法名
    public array $parameters = []; // 参数信息

    public array $headers = [];
    /**
     * @var array<string,\Knuckles\Camel\Extraction\Parameter>
     */
    public array $urlParameters = [];

    public function __construct(
        string $path,
        array  $httpMethod,
        string $controller,
        string $method,
        array  $parameters = []
    )
    {
        $this->path = $path;
        $this->httpMethod = $httpMethod;
        $this->controller = $controller;
        $this->method = $method;
        $this->parameters = $parameters;
    }
}
