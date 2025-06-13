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

    public function __construct(
        RouteMatcherInterface $routeMatcher,
        ?LoggerInterface      $logger = null
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
        $routeExtractor = new RouteExtractor();
        foreach ($routes as $route) {
            $routeExtractor->processRoute($route);
        }
    }


}
