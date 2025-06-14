<?php

namespace RealPHP\SwaggerPhpPlus;

use OpenApi\Generator;
use Psr\Log\LoggerInterface;
use RealPHP\SwaggerPhpPlus\Contracts\RouteMatcherInterface;
use RealPHP\SwaggerPhpPlus\Extracting\RouteExtractor;
use RealPHP\SwaggerPhpPlus\Processors\InjectRoutesProcessor;
use RealPHP\SwaggerPhpPlus\RouteMatching\RouteInfo;

class DocGenerator extends Generator
{
    private RouteMatcherInterface $routeMatcher;
    private bool $routesProcessed = false;

    public function __construct(
        RouteMatcherInterface $routeMatcher,
        ?LoggerInterface      $logger = null
    )
    {
        parent::__construct($logger);
        $this->routeMatcher = $routeMatcher;
        $this->routeExtractor = new RouteExtractor();

    }

    public function generate(iterable $sources, ?\OpenApi\Analysis $analysis = null, bool $validate = true): ?\OpenApi\Annotations\OpenApi
    {
        // 确保在生成前添加路由处理器
        if (!$this->routesProcessed) {
            $this->addRouteProcessor();
            $this->routesProcessed = true;
        }

        return parent::generate($sources, $analysis, $validate);
    }

    private function addRouteProcessor(): void
    {
        $routes = $this->extractRoutes();
        $pipeline = $this->getProcessorPipeline();
        // 创建路由处理器
        $routeProcessor = new InjectRoutesProcessor($routes);
        // 使用 insert 方法将处理器添加到最前面
        $pipeline->insert($routeProcessor, function(array $pipes) {
            // 始终插入到管道最前面
            return 0;
        });
    }
    /**
     * @return RouteInfo[]|array
     */
    /**
     * @return RouteInfo[]
     */
    private function extractRoutes(): array
    {
        $rawRoutes = $this->routeMatcher->getRoutes();
        $this->log(sprintf('Found %d raw routes from matcher', count($rawRoutes)));
        $processedRoutes = [];
        foreach ($rawRoutes as $route) {
            $processedRoute = $this->routeExtractor->processRoute($route);
            if ($processedRoute instanceof RouteInfo) {
                $processedRoutes[] = $processedRoute;
            } else {
                $this->log('Route extraction failed for: ' . get_class($route), 'warning');
            }
        }
        return $processedRoutes;
    }

    private function log(string $message, string $level = 'info'): void
    {
        if ($this->logger) {
            $this->logger->log($level, '[SwaggerPhpPlus] ' . $message);
        }
    }

}
