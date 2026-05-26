<?php // Start Router class file.

declare(strict_types=1); // Strict typing enabled.

namespace Core\Http; // Namespace for HTTP classes.

use RuntimeException; // Used for 404 route errors.

final class Router // Registers and resolves application routes.
{
    /**
     * Route table grouped by HTTP method.
     *
     * @var array<string, array<int, array{uri: string, action: array{0: class-string, 1: string}}>>
     */
    private array $routes = []; // Stored routes grouped by HTTP method.

    /**
     * Register GET route; usually for showing pages.
     *
     * @param array{0: class-string, 1: string} $action
     */
    public function get(string $uri, array $action): void // Register a GET route.
    {
        $this->register('GET', $uri, $action); // Delegate to shared register helper.
    }

    /**
     * Register POST route; usually for forms/actions that change data.
     *
     * @param array{0: class-string, 1: string} $action
     */
    public function post(string $uri, array $action): void // Register a POST route.
    {
        $this->register('POST', $uri, $action); // Delegate to shared register helper.
    }

    public function resolve(Request $request): Route // Find route matching current request.
    {
        // Check routes for current method and find one matching the request path.
        foreach ($this->routes[$request->method()] ?? [] as $route) { // Loop routes for request method.
            $params = $this->match($route['uri'], $request->path()); // Try matching route URI to request path.

            if ($params !== null) { // If route matched...
                return new Route($route['action'][0], $route['action'][1], $params); // Return controller/action/params.
            } // End match check.
        } // End route loop.

        // No matching route means page not found.
        throw new RuntimeException('Page not found.', 404); // Throw 404 when no route matches.
    }

    /**
     * Save route in route table.
     *
     * @param array{0: class-string, 1: string} $action
     */
    private function register(string $method, string $uri, array $action): void // Store one route.
    {
        $this->routes[$method][] = [ // Add route under HTTP method.
            'uri' => '/' . trim($uri, '/'), // Normalize URI with leading slash.
            'action' => $action, // Store controller and method pair.
        ]; // End route array.
    }

    /**
     * Match route URI with request path and return params like id.
     *
     * @return array<string, string>|null
     */
    private function match(string $routeUri, string $requestPath): ?array // Match route pattern to request path.
    {
        // Convert /tasks/{id} into regex and remember param names.
        $paramNames = []; // Store parameter names like id.
        $pattern = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)}/', function (array $matches) use (&$paramNames): string { // Replace {param} with regex.
            $paramNames[] = $matches[1]; // Remember param name.

            return '([^/]+)'; // Match one URL segment.
        }, $routeUri); // Build final regex pattern.

        if (! is_string($pattern)) { // If regex conversion failed...
            return null; // Treat as no match.
        } // End pattern check.

        // If path does not match, this route is skipped.
        if (! preg_match('#^' . $pattern . '$#', $requestPath, $matches)) { // Test request path.
            return null; // No match for this route.
        } // End regex match.

        // Remove full match and pair remaining values with param names.
        array_shift($matches); // Remove whole matched path.

        return array_combine($paramNames, $matches) ?: []; // Return params like ['id' => '1'].
    }
} // End Router class.
