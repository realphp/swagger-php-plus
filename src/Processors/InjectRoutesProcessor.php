<?php

namespace RealPHP\SwaggerPhpPlus\Processors;

use OpenApi\Analysis;
use OpenApi\Context;
use RealPHP\SwaggerPhpPlus\RouteMatching\RouteInfo;

class InjectRoutesProcessor
{
    /**
     * @param RouteInfo[] $routes
     */
    public function __construct(private array $routes)
    {
    }

    public function __invoke(Analysis $analysis): void
    {
        // 为每个路径创建PathItem
        foreach ($this->routes as $path => $route) {
            $pathItem = $this->createPathItem($route);
            $analysis->addAnnotation($pathItem, $analysis->context);
        }
    }

    protected function createPathItem(RouteInfo $route): \OpenApi\Attributes\PathItem
    {
        $pathItem = new \OpenApi\Attributes\PathItem(path: $route->uri);
        $method = strtolower($route->httpMethod[0]);
        $operation = $this->createOperation($route, $method);
        // 使用反射动态设置属性
        $reflection = new \ReflectionClass($pathItem);
        if ($reflection->hasProperty($method)) {
            $property = $reflection->getProperty($method);
            $property->setAccessible(true);
            $property->setValue($pathItem, $operation);
        }
        return $pathItem;
    }

    protected function createOperation(RouteInfo $route, string $method): object
    {
        $operationClass = match ($method) {
            'get' => \OpenApi\Attributes\Get::class,
            'post' => \OpenApi\Attributes\Post::class,
            'put' => \OpenApi\Attributes\Put::class,
            'delete' => \OpenApi\Attributes\Delete::class,
            'patch' => \OpenApi\Attributes\Patch::class,
            'head' => \OpenApi\Attributes\Head::class,
            'options' => \OpenApi\Attributes\Options::class,
            default => \OpenApi\Attributes\Trace::class,
        };

        return new $operationClass(
            operationId: $route->uri . '-' . $method,
            responses: [
                '200' => new \OpenApi\Attributes\Response(description: 'OK'),
                '400' => new \OpenApi\Attributes\Response(description: 'Bad Request'),
                '404' => new \OpenApi\Attributes\Response(description: 'Not Found'),
                '500' => new \OpenApi\Attributes\Response(description: 'Server Error'),
            ],
        // 可添加更多通用参数
        // description: $route->description,
        // summary: $route->summary,
        );
    }
}
