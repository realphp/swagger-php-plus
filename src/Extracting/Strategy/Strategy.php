<?php

namespace RealPHP\SwaggerPhpPlus\Extracting\Strategy;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use RealPHP\SwaggerPhpPlus\RouteMatching\RouteInfo;

abstract class Strategy
{
    public ?RouteInfo $routeInfo;

    abstract public function __invoke(RouteInfo $routeInfo, array $settings = []): ?array;

}
