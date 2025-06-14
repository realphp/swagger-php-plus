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
        foreach ($this->routes as $route) {
            $pathItem = $this->createPathItem($route);
            $analysis->addAnnotation($pathItem, $pathItem->_context);
        }
    }

    private function logDuplicatePath(Analysis $analysis, string $path): void
    {
        $context = $analysis->_context;
        if ($context && $context->logger) {
            $context->logger->warning(sprintf(
                '[InjectRoutes] Skipping duplicate path: %s',
                $path
            ));
        }
    }

    protected function createPathItem(RouteInfo $route): \OpenApi\Attributes\PathItem
    {
        $pathItem = new \OpenApi\Attributes\PathItem(path: $route->uri);
        foreach ($route->httpMethod as $httpMethod) {
            $method = strtolower($httpMethod);
            if (!in_array($method, ['get', 'post', 'put', 'delete', 'patch', 'head', 'options', 'trace'])) {
                continue;
            }
            $operation = $this->createOperation($route, $method);
            $pathItem->{$method} = $operation;
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
        // 1. 创建查询参数
        $queryParameters = $this->createQueryParameters($route);
        // 2. 创建操作对象
        return new $operationClass(
            operationId: $this->generateOperationId($route, $method),
            parameters: $queryParameters,
            responses: [
                '200' => new \OpenApi\Attributes\Response(description: 'OK'),
//                '400' => new \OpenApi\Attributes\Response(description: 'Bad Request'),
//                '404' => new \OpenApi\Attributes\Response(description: 'Not Found'),
//                '500' => new \OpenApi\Attributes\Response(description: 'Server Error'),
            ],
        // 可添加更多通用参数
        // description: $route->description,
        // summary: $route->summary,
        );
    }

    /**
     * 根据路由信息创建查询参数数组
     */
    protected function createQueryParameters(RouteInfo $route): array
    {
        $parameters = [];

        foreach ($route->queryParameters as $paramName => $paramData) {
            // 创建参数模式
            $schema = new \OpenApi\Attributes\Schema(
                type: $paramData['type'],
                nullable: $paramData['nullable'],
                example: $paramData['example'] ?? null
            );

            // 创建参数对象
            $parameter = new \OpenApi\Attributes\Parameter(
                name: $paramName,
                in: 'query',
                required: $paramData['required'],
                description: $paramData['description'] ?: "Query parameter: $paramName",
                schema: $schema
            );

            $parameters[] = $parameter;
        }

        return $parameters;
    }


    protected function generateOperationId(RouteInfo $route, string $method): string
    {
        // 生成更规范的operationId
        $path = trim($route->uri, '/');
        $path = str_replace(['/', '{', '}'], ['-', '', ''], $path);
        return md5($path . '-' . $method);
    }
}
