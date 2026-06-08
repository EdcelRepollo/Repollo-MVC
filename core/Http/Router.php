<?php

declare(strict_types=1);

namespace Core\Http;

use RuntimeException;

// router mo-match sa request path ngadto sa sakto nga controller action.
final class Router
{
    private array $routes = [];

    public function get(string $uri, array $action): void
    {
        $this->register('GET', $uri, $action);
    }

    public function post(string $uri, array $action): void
    {
        $this->register('POST', $uri, $action);
    }

    public function resolve(Request $request): Route
    {
        foreach ($this->routes[$request->method()] ?? [] as $route) {
            $params = $this->match($route['uri'], $request->path());

            if ($params !== null) {
                return new Route($route['action'][0], $route['action'][1], $params);
            }
        }

        throw new RuntimeException('Page not found.', 404);
    }

    private function register(string $method, string $uri, array $action): void
    {
        $this->routes[$method][] = [
            'uri' => '/' . trim($uri, '/'),
            'action' => $action,
        ];
    }

    private function match(string $routeUri, string $requestPath): ?array
    {
        $paramNames = [];
        $pattern = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)}/', function (array $matches) use (&$paramNames): string {
            $paramNames[] = $matches[1];

            return '([^/]+)';
        }, $routeUri);

        if (! is_string($pattern)) {
            return null;
        }

        if (! preg_match('#^' . $pattern . '$#', $requestPath, $matches)) {
            return null;
        }

        array_shift($matches);

        return array_combine($paramNames, $matches) ?: [];
    }
}
