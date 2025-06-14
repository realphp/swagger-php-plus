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

        return new $operationClass(
            operationId: $this->generateOperationId($route, $method),
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

    protected function generateOperationId(RouteInfo $route, string $method): string
    {
        // 生成更规范的operationId
        $path = trim($route->uri, '/');
        $path = str_replace(['/', '{', '}'], ['-', '', ''], $path);
        return md5($path . '-' . $method);
    }
}
