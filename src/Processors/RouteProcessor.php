<?php

namespace RealPHP\SwaggerPhpPlus\Processors;

use RealPHP\SwaggerPhpPlus\RouteMatching\RouteInfo;

class RouteProcessor
{
    public function process(RouteInfo $route, array $routeRules = []): array
    {
        $operation = [
            'summary' => $this->inferSummary($route->handler),
            'parameters' => [],
            'responses' => [],
        ];

        // 1. 处理继承的文档覆盖
        $inheritedDocs = $this->getInheritedDocs($route->handler);
        $this->applyInheritedDocs($operation, $inheritedDocs);

        // 2. 解析参数（URL/Query/Body）
        $operation['parameters'] = $this->fetchParameters($route, $routeRules);

        // 3. 处理请求头
        $operation['consumes'] = $this->determineContentType(
            $operation['parameters']['body'] ?? []
        );

        // 4. 推断响应
//        $operation['responses'] = $this->fetchResponses($route->handler);

        // 5. 返回 OpenAPI 结构
        return [
            'path' => $route->uri,
            'operations' => [
                strtolower($route->methods[0]) => $operation
            ]
        ];
    }

    private function getInheritedDocs(callable $handler): ?array
    {
        if (is_array($handler) && method_exists($handler[0], 'inheritedDocsOverrides')) {
            $class = new \ReflectionClass($handler[0]);
            return $class->getMethod('inheritedDocsOverrides')->invoke(null);
        }
        return null;
    }

    private function determineContentType(array $bodyParams): array
    {
        if (empty($bodyParams)) return [];

        return isset($bodyParams['files'])
            ? ['multipart/form-data']
            : ['application/json'];
    }

    private function fetchParameters(RouteInfo $route): array
    {
    }

    private function getInheritedDocs(callable $handler): ?array
    { /* ... */
    }
}
