<?php

namespace App\Core;

class Router
{
    private $routes = [];

    function addRoute(string $method, string $route, string $handler)
    {
        $this->routes[$method][$route] = $handler;
    }

    function dispatch(string $method, string $uri)
    {
        $uri = trim(parse_url($uri, PHP_URL_PATH), '/');

        if ($uri === 'favicon.ico') return;

        $segments = explode('/', $uri);

        if (isset($this->routes[$method]['/' . $uri])) {
            $handler = $this->routes[$method]['/' . $uri];
            [$controllerName, $methodName] = explode('@', $handler);
        } else {
            $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) . 'Controller' : null;
            $methodName = $segments[1] ?? null;
        }

        $controllerPath = implode(
            DIRECTORY_SEPARATOR,
            [dirname(__DIR__), 'Logic', 'Controller', $controllerName . '.php']
        );

        if (!file_exists($controllerPath)) throw new \Error('UNSUPPORTED!');

        require_once $controllerPath;

        $controllerClass = 'App\\Logic\\Controller\\' . $controllerName;
        $controller = new $controllerClass();

        if (!method_exists($controller, $methodName)) throw new \Error('UNSUPPORTED!');

        call_user_func_array([$controller, $methodName], array_slice($segments, 2));
    }
}
