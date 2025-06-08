<?php

namespace RealPHP\SwaggerPhpPlus\ActionFilters;

use ReflectionMethod;

class ActionAttributeFilter implements ActionFilterInterface
{
    public function shouldGenerate(string $controller, string $action, ReflectionMethod $method): bool
    {
        // 检查方法或类是否有特定注解
        if ($this->hasAnnotation($method, 'OA\Generate')) {
            return true;
        }

        if ($this->hasAnnotation($method, 'OA\Ignore')) {
            return false;
        }

        // 检查类级别注解
        $class = $method->getDeclaringClass();
        if ($this->hasAnnotation($class, 'OA\Generate')) {
            return true;
        }

        return !$this->hasAnnotation($class, 'OA\Ignore');
    }

    private function hasAnnotation($reflector, string $annotation): bool
    {
        $docComment = $reflector->getDocComment();
        return $docComment && str_contains($docComment, $annotation);
    }
}