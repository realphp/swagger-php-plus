<?php

namespace RealPHP\SwaggerPhpPlus\Extracting;

use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Knuckles\Camel\Extraction\Parameter;
use Knuckles\Scribe\Extracting\Strategies\StaticData;
use RealPHP\SwaggerPhpPlus\Config\Defaults;
use RealPHP\SwaggerPhpPlus\RouteMatching\RouteInfo;

class RouteExtractor
{
    public function processRoute(RouteInfo $route, array $routeRules = []): RouteInfo
    {
        $this->fetchQueryParameters($route, $routeRules);
        return $route;
    }

    protected function fetchQueryParameters(RouteInfo $routeInfo, array $rulesToApply): void
    {
        $this->iterateThroughStrategies('queryParameters', $routeInfo, $rulesToApply, function ($results) use ($routeInfo) {
            foreach ($results as $key => $item) {
                if (empty($item['name'])) {
                    $item['name'] = $key;
                }
                $routeInfo->queryParameters[$key] = $item;
            }
        });
    }

    /**
     * Iterate through all defined strategies for this stage.
     * A strategy may return an array of attributes
     * to be added to that stage data, or it may modify the stage data directly.
     *
     * @param string $stage
     * @param RouteInfo $routeInfo
     * @param array $rulesToApply Deprecated. Use strategy config instead.
     * @param callable $handler Function to run after each strategy returns its results (an array).
     *
     */
    protected function iterateThroughStrategies(string $stage, RouteInfo $routeInfo, array $rulesToApply, callable $handler): void
    {
        //TODO
//        $strategies = $this->config->get("strategies.$stage", []);
        $strategies = Defaults::QUERY_PARAMETERS_STRATEGIES;
        foreach ($strategies as $strategyClassOrTuple) {
            if (is_array($strategyClassOrTuple)) {
                [$strategyClass, &$settings] = $strategyClassOrTuple;
            } else {
                $strategyClass = $strategyClassOrTuple;
                $settings = [];
            }

//            $routesToExclude = Arr::wrap($settings["except"] ?? []);
//            $routesToInclude = Arr::wrap($settings["only"] ?? []);

//            if ($this->shouldSkipRoute($routeInfo->route, $routesToExclude, $routesToInclude)) {
//                continue;
//            }
//TODO $this->>config
            $strategy = new $strategyClass([]);
            $results = $strategy($routeInfo, $settings);
            if (is_array($results)) {
                $handler($results);
            }
        }
    }
}
