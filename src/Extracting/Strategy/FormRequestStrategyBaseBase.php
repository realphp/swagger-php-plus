<?php

namespace RealPHP\SwaggerPhpPlus\Extracting\Strategy;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Foundation\Http\FormRequest;
use Knuckles\Scribe\Tools\ConsoleOutputUtils as c;
use RealPHP\SwaggerPhpPlus\Extracting\Strategy\Traits\FindsFormRequestForMethod;
use RealPHP\SwaggerPhpPlus\Extracting\Strategy\Traits\ParsesValidationRules;
use RealPHP\SwaggerPhpPlus\RouteMatching\RouteInfo;

class FormRequestStrategyBase extends Strategy
{
    use ParsesValidationRules, FindsFormRequestForMethod;

    public function __invoke(RouteInfo $routeInfo, array $routeRules = []): ?array
    {
        return $this->getParametersFromFormRequest($routeInfo->method, $routeInfo->route);
    }

    public function getParametersFromFormRequest(ReflectionFunctionAbstract $method, Route $route): array
    {
        if (!$formRequestReflectionClass = $this->getFormRequestReflectionClass($method)) {
            return [];
        }
        if (!$this->isFormRequestMeantForThisStrategy($formRequestReflectionClass)) {
            return [];
        }
        $className = $formRequestReflectionClass->getName();
        $formRequest = new $className;
        $formRequest->setRouteResolver(function () use ($formRequest, $route) {
            // Also need to bind the request to the route in case their code tries to inspect current request
            return $route->bind($formRequest);
        });
        $formRequest->server->set('REQUEST_METHOD', $route->methods()[0]);
        $parametersFromFormRequest = $this->getParametersFromValidationRules(
            $this->getRouteValidationRules($formRequest),
            $this->getCustomParameterData($formRequest)
        );

    }

    protected function getRouteValidationRules(FormRequest $formRequest)
    {
        if (method_exists($formRequest, 'validator')) {
            $validationFactory = app(ValidationFactory::class);

            // @phpstan-ignore-next-line
            return app()->call([$formRequest, 'validator'], [$validationFactory])
                ->getRules();
        } elseif (method_exists($formRequest, 'rules')) {
            return app()->call([$formRequest, 'rules']);
        }

        return [];
    }

    protected function getCustomParameterData(FormRequest $formRequest)
    {
        if (method_exists($formRequest, $this->customParameterDataMethodName)) {
            return call_user_func_array([$formRequest, $this->customParameterDataMethodName], []);
        }

        c::warn("No {$this->customParameterDataMethodName}() method found in " . get_class($formRequest) . ". Scribe will only be able to extract basic information from the rules() method.");

        return [];
    }


    protected function isFormRequestMeantForThisStrategy(ReflectionClass $formRequestReflectionClass): bool
    {
        return $formRequestReflectionClass->hasMethod($this->customParameterDataMethodName);
    }

}
