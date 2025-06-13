<?php

use Knuckles\Camel\Extraction\Parameter;
use Psr\Log\LoggerInterface;
use RealPHP\SwaggerPhpPlus\Contracts\RouteMatcherInterface;
use RealPHP\SwaggerPhpPlus\Processors\InjectRoutesProcessor;
use RealPHP\SwaggerPhpPlus\Processors\RouteProcessor;
use OpenApi\Generator;
use RealPHP\SwaggerPhpPlus\RouteMatching\RouteInfo;

class DocGenerator extends Generator
{
    private RouteMatcherInterface $routeMatcher;

    public function __construct(
        ?LoggerInterface      $logger = null,
        RouteMatcherInterface $routeMatcher
    )
    {
        parent::__construct($logger);
        $this->routeMatcher = $routeMatcher;
        $this->addRouteProcessor();
    }

    private function addRouteProcessor()
    {
        $routes = $this->extractRoutes();
        $this->getProcessorPipeline()
            ->add(new InjectRoutesProcessor($routes));
    }

    /**
     * @return RouteInfo[]|array
     */
    private function extractRoutes()
    {
        $routes = $this->routeMatcher->getRoutes();
        foreach ($routes as $route) {
            $this->processRoute($route);
        }
    }

    private function processRoute(RouteInfo $route)
    {
        $this->fetchQueryParameters($route);
    }

    private function fetchQueryParameters(RouteInfo $route)
    {
        $this->iterateThroughStrategies('queryParameters', $route, function ($results) use ($route) {
            foreach ($results as $key => $item) {
                if (empty($item['name'])) {
                    $item['name'] = $key;
                }
                $route->queryParameters[$key] = Parameter::create($item, $route->queryParameters[$key] ?? []);
            }
        });
    }


}
