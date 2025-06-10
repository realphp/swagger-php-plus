<?php

namespace RealPHP\SwaggerPhpPlus\Processors;

use Knuckles\Scribe\Tools\ConsoleOutputUtils as c;
use OpenApi\Analysis;
use OpenApi\Annotations\Operation;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\PathItem;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;
use RealPHP\SwaggerPhpPlus\RouterAdapter\RouteInfo;
use RealPHP\SwaggerPhpPlus\RouterAdapter\RouterAdapterInterface;
use RealPHP\SwaggerPhpPlus\RouterAdapterFactory;
use ReflectionClass;

class UniversalRouteProcessor
{
    private ?RouterAdapterInterface $adapter;

    public function __construct(?RouterAdapterInterface $adapter = null)
    {
        $this->adapter = $adapter ?? RouterAdapterFactory::create();
    }

    public function __invoke(Analysis $analysis): void
    {
        // 1. 处理手动声明的路由注解
//        $this->processManualAnnotations($analysis);

        // 2. 自动添加未声明的路由
        $this->addMissingRoutes($analysis);
    }

    private function processManualAnnotations(Analysis $analysis): void
    {
        foreach ($analysis->annotations as $annotation) {
            if ($annotation instanceof PathItem) {
                $this->processPathItem($annotation, $analysis);
            }
        }
    }

    private function processPathItem(PathItem $pathItem, Analysis $analysis): void
    {
        foreach ($pathItem->operations() as $operation) {
            $context = $operation->_context;

            // 获取控制器和方法名
            $controller = $context->class;
            $action = $context->method;

            // 转换为实际路径
            $actualPath = $this->adapter->convertPath(
                $pathItem->path,
                $this->getControllerKey($controller),
                $action
            );

            // 更新路径
            $pathItem->path = $actualPath;

            // 确保核心参数存在
            $this->ensureCoreParams($operation, $controller, $action);
        }
    }

    private function ensureCoreParams(Operation $operation, string $controller, string $action): void
    {
        $coreParams = $this->adapter->getCoreParamNames();

        foreach ($coreParams as $paramName) {
            $exists = false;

            // 检查是否已存在
            foreach ($operation->parameters as $param) {
                if ($param->name === $paramName) {
                    $exists = true;
                    break;
                }
            }

            // 不存在则添加
            if (!$exists) {
                $paramValue = ($paramName === $this->adapter->getCoreParamNames()[0])
                    ? $this->getControllerKey($controller)
                    : $action;
                $operation->parameters[] = new Parameter(name: $paramName, in: 'query', required: true, schema: new Schema(type: 'string', enum: [$paramValue]));
            }
        }
    }

    private function getControllerKey(string $className): string
    {
        // 例如: App\Controllers\UserController → user
        $baseName = basename(str_replace('\\', '/', $className));
        return strtolower(preg_replace('/Controller$/', '', $baseName));
    }

    private function addMissingRoutes(Analysis $analysis): void
    {
        $existingRoutes = $this->getExistingRoutes($analysis);
        $allRoutes = $this->adapter->getRoutes();

        foreach ($allRoutes as $route) {
            $routeKey = "{$route->controller}::{$route->method}";

            if ($this->analysisRoute($route)) {
                $this->addRouteToAnalysis($route, $analysis);
            }
        }
    }

    private function analysisRoute(RouteInfo $route): bool
    {
        if (!$this->doesControllerMethodExist($route)) {
            return false;
        }
    }


    private function addRouteToAnalysis(RouteInfo $route, Analysis $analysis)
    {
        $logicalPath = "/{$this->getControllerKey($route->controller)}/{$route->action}";
        $actualPath = $this->adapter->convertPath($logicalPath, $route->controller, $route->action);

        $pathItem = new PathItem(path: $actualPath);

        $operation = new Get(
            operationId: "{$route->controller}_{$route->action}",
            parameters: $this->buildParameters($route->parameters),
            responses: ['200' => new Response(description: 'Success')]
        );
        $pathItem->get = $operation;
        $analysis->addAnnotation($pathItem);
        $analysis->addAnnotation($operation);
    }

    private function buildParameters(array $params): array
    {
        $oaParams = [];
        $coreParams = $this->adapter->getCoreParamNames();

        foreach ($params as $name => $meta) {
            // 排除核心参数
            if (in_array($name, $coreParams)) continue;
            $oaParams[] = new Parameter(name: $name, in: 'query', schema: new  Schema(type: $this->mapType($meta['type'] ?? 'string')));
        }
        return $oaParams;
    }

    private function mapType(string $type): string
    {
        return match (strtolower($type)) {
            'int', 'integer' => 'integer',
            'float', 'double' => 'number',
            'bool', 'boolean' => 'boolean',
            'array' => 'array',
            default => 'string'
        };
    }

    private function doesControllerMethodExist(RouteInfo $routeInfo)
    {
        $reflection = new ReflectionClass($routeInfo->controller);

        if ($reflection->hasMethod($routeInfo->method)) {
            return true;
        }
        return false;
    }
}
