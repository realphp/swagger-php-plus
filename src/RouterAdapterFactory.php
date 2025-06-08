<?php

namespace RealPHP\SwaggerPhpPlus;

use RealPHP\SwaggerPhpPlus\RouterAdapter\LaravelRouteAdapter;
use RealPHP\SwaggerPhpPlus\RouterAdapter\QueryStringRouteAdapter;
use RealPHP\SwaggerPhpPlus\RouterAdapter\RouterAdapterInterface;
use RuntimeException;

class RouterAdapterFactory
{
    public static function create(array $config = []): RouterAdapterInterface
    {
        $framework = self::detectFramework();

        switch ($framework) {
            case 'laravel':
                return new LaravelRouteAdapter();
            case 'query_string':
                return new QueryStringRouteAdapter(
                    $config['entry_file'] ?? 'index.php',
                    $config['controller_param'] ?? 'c',
                    $config['action_param'] ?? 'a'
                );
            default:
                throw new RuntimeException("Unsupported framework: $framework");
        }
    }

    private static function detectFramework(): string
    {
        // 自动检测当前框架
        if (class_exists(\Illuminate\Foundation\Application::class)) {
            return 'laravel';
        }

        if (class_exists(\think\App::class)) {
            return 'thinkphp';
        }

        // 根据配置或约定使用查询字符串路由
        return 'query_string';
    }
}