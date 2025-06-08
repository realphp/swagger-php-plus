<?php

namespace RealPHP\SwaggerPhpPlus;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class ControllerScanner
{
    private $controllerDir;

    public function __construct(string $controllerDir)
    {
        $this->controllerDir = $controllerDir;
    }

    public function scan(): array
    {
        $routes = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->controllerDir));

        foreach ($iterator as $file) {
            if ($file->isDir() || $file->getExtension() !== 'php') continue;

            $className = $this->fileToClass($file->getPathname());

            try {
                $refClass = new ReflectionClass($className);

                foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                    if ($method->isConstructor() || $method->isDestructor()) continue;
                    if (preg_match('/^__/', $method->getName())) continue; // 跳过魔术方法

                    $routes[] = new RouteInfo(
                        path: '', // 将在适配器中填充
                        httpMethod: $this->detectHttpMethod($method),
                        controller: $className,
                        action: $method->getName(),
                        parameters: $this->getMethodParams($method)
                    );
                }
            } catch (ReflectionException $e) {
                // 记录错误，继续处理
            }
        }

        return $routes;
    }

    private function fileToClass(string $filePath): string
    {
        // 实现文件路径到类名的转换逻辑
        // 例如: app/Controllers/UserController.php → App\Controllers\UserController
        $basePath = str_replace('/', '\\', rtrim($this->controllerDir, '/'));
        $relativePath = str_replace($this->controllerDir, '', $filePath);
        $className = str_replace('/', '\\', substr($relativePath, 0, -4));
        return $basePath . $className;
    }

    private function detectHttpMethod(ReflectionMethod $method): string
    {
        // 根据方法名或注解推断HTTP方法
        $methodName = strtolower($method->getName());

        if (str_starts_with($methodName, 'get')) return 'GET';
        if (str_starts_with($methodName, 'post')) return 'POST';
        if (str_starts_with($methodName, 'put')) return 'PUT';
        if (str_starts_with($methodName, 'delete')) return 'DELETE';

        return 'GET'; // 默认
    }

    private function getMethodParams(ReflectionMethod $method): array
    {
        $params = [];
        foreach ($method->getParameters() as $param) {
            $params[$param->getName()] = [
                'type' => $param->getType()?->getName() ?? 'string',
                'position' => $param->getPosition()
            ];
        }
        return $params;
    }
}