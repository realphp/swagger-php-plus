<?php

namespace RealPHP\SwaggerPhpPlus\RouteMatching;
class MatchedRoute implements \ArrayAccess
{
    protected RouteInfo $route;

    protected array $rules;

    public function __construct(RouteInfo $route)
    {
        $this->route = $route;
    }

    public function getRoute(): RouteInfo
    {
        return $this->route;
    }

    public function offsetExists($offset): bool
    {
        return is_callable([$this, 'get' . ucfirst($offset)]);
    }

    public function offsetGet($offset): mixed
    {
        return call_user_func([$this, 'get' . ucfirst($offset)]);
    }

    public function offsetSet($offset, $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset($offset): void
    {
        $this->$offset = null;
    }
}
