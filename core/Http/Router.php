<?php // Start sa Router class file.

declare(strict_types=1); // Gi-enable ang strict typing para klaro ang expected types.

namespace Core\Http; // Namespace ni para sa HTTP-related classes.

use RuntimeException; // Gamiton ni kung walay route nga makita, like 404 error.

final class Router // Mo-register ug mo-resolve sa application routes.
{
    /**
     * Route table nga gi-group by HTTP method, like GET ug POST.
     * Diri ibutang tanan registered routes para dali pangitaon later.
     *
     * @var array<string, array<int, array{uri: string, action: array{0: class-string, 1: string}}>>
     */
    private array $routes = []; // Storage sa routes nga naka-group per HTTP method.

    /**
     * Mo-register ug GET route.
     * Kasagaran gamit ani kay pagpakita ug pages or pagkuha ug data.
     *
     * @param array{0: class-string, 1: string} $action
     */
    public function get(string $uri, array $action): void // Mo-register ug usa ka GET route.
    {
        $this->register('GET', $uri, $action); // Ipaagi sa shared register helper para reusable ang logic.
    }

    /**
     * Mo-register ug POST route.
     * Kasagaran gamit ani kay forms or actions nga mo-change ug data.
     *
     * @param array{0: class-string, 1: string} $action
     */
    public function post(string $uri, array $action): void // Mo-register ug usa ka POST route.
    {
        $this->register('POST', $uri, $action); // Ipaagi sa shared register helper para pareho ra ang storage process.
    }

    public function resolve(Request $request): Route // Pangitaon ang route nga match sa current request.
    {
        // I-check tanan routes para sa current HTTP method, then pangitaon ang matching path.
        foreach ($this->routes[$request->method()] ?? [] as $route) { // Loop sa routes under sa request method.
            $params = $this->match($route['uri'], $request->path()); // Sulayan ug match ang route URI sa actual request path.

            if ($params !== null) { // Kung naay matching route...
                return new Route($route['action'][0], $route['action'][1], $params); // Ibalik ang controller, method/action, ug route params.
            } // Human sa route match check.
        } // Human sa route loop.

        // Kung walay matching route, meaning page not found.
        throw new RuntimeException('Page not found.', 404); // Mo-throw ug 404 kung walay route nga ni-match.
    }

    /**
     * I-save ang route sa route table.
     * Ang method kay GET/POST, ang URI kay path, ug ang action kay controller + method.
     *
     * @param array{0: class-string, 1: string} $action
     */
    private function register(string $method, string $uri, array $action): void // I-store ang usa ka route.
    {
        $this->routes[$method][] = [ // Idugang ang route under sa iyang HTTP method.
            'uri' => '/' . trim($uri, '/'), // I-normalize ang URI aron naa pirmi leading slash.
            'action' => $action, // I-store ang controller class ug method name pair.
        ]; // End sa route array.
    }

    /**
     * I-match ang route URI sa request path.
     * Kung naay dynamic segments like /tasks/{id}, ibalik ang params like ['id' => '1'].
     *
     * @return array<string, string>|null
     */
    private function match(string $routeUri, string $requestPath): ?array // I-match ang route pattern sa request path.
    {
        // I-convert ang /tasks/{id} into regex, then i-remember ang param names.
        $paramNames = []; // Storage sa parameter names, like id.
        $pattern = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)}/', function (array $matches) use (&$paramNames): string { // Ilisan ang {param} ug regex.
            $paramNames[] = $matches[1]; // I-remember ang param name.

            return '([^/]+)'; // Mo-match ug usa ka URL segment only.
        }, $routeUri); // Himoon ang final regex pattern.

        if (! is_string($pattern)) { // Kung failed ang regex conversion...
            return null; // Treat as no match aron dili mo-crash ang router.
        } // End sa pattern check.

        // Kung dili mo-match ang path, i-skip ni nga route.
        if (! preg_match('#^' . $pattern . '$#', $requestPath, $matches)) { // I-test ang request path against sa regex.
            return null; // Walay match para ani nga route.
        } // End sa regex match.

        // Tangtangon ang full match, then i-pair ang remaining values sa param names.
        array_shift($matches); // Tangtangon ang whole matched path.

        return array_combine($paramNames, $matches) ?: []; // Ibalik ang params like ['id' => '1']; empty array kung walay params.
    }
} // End sa Router class.
