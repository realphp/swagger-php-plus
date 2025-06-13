<?php

namespace RealPHP\SwaggerPhpPlus\Processors;

use OpenApi\Analysis;
use RealPHP\SwaggerPhpPlus\RouteMatching\RouteInfo;

class InjectRoutesProcessor
{
    private Analysis $analysis;

    /**
     * @param RouteInfo[] $routes
     */
    public function __construct(private array $routes)
    {

    }

    public function __invoke(Analysis $analysis): void
    {
        foreach ($this->routes as $route) {
            $pathItem = $this->routeToPathItem($route);
            $analysis->addAnnotation($pathItem, null);
        }
    }

    protected static function routeToPathItem(RouteInfo $route): OA\PathItem
    {
        $method = strtolower($route->methods()[0]); // GET, POST, etc.
        $path = $route->uri();

        // 动态生成 Operation（如 @OA\Get, @OA\Post）
        $operation = new OA\Operation([
            'operationId' => $route->getName() ?: $path . '-' . $method,
            'tags' => self::extractTags($route),
            'responses' => [
                '200' => new OA\Response(['description' => 'OK']),
            ],
        ]);

        // 返回 PathItem（如 @OA\PathItem）
        return new OA\PathItem([
            'path' => $path,
            $method => $operation, // 动态绑定 GET/POST/PUT...
        ]);
    }


}
