<?php // Start PHP file; diri magsugod ang Request class code.

declare(strict_types=1); // Enforce strict types; para klaro ang data types.

namespace Core\Http; // Namespace location; belongs to Core HTTP layer.

final class Request // Final class; dili na siya intended i-extend.
{
    /**
     * Request data holder; keeps query, post, server, and route params.
     * Function flow: controller/router can read method, path, inputs, and route params from here.
     *
     * @param array<string, mixed> $query
     * @param array<string, mixed> $post
     * @param array<string, mixed> $server
     * @param array<string, string> $routeParams
     */
    public function __construct( // Constructor; mo store sa request data.
        private readonly array $query, // GET data; mga query string values.
        private readonly array $post, // POST data; mga form submit values.
        private readonly array $server, // SERVER data; contains method, URI, etc.
        private array $routeParams = [] // Route params; example id from /tasks/{id}.
    ) {
    } // Constructor ends; data is now stored in object.

    public static function capture(): self // Factory function; creates Request from PHP superglobals.
    {
        // Capture PHP superglobals into one Request object.
        return new self($_GET, $_POST, $_SERVER); // Return Request using GET, POST, and SERVER.
    }

    public function method(): string // Get HTTP method; tells if request is GET or POST.
    {
        // Return HTTP method like GET or POST.
        return strtoupper((string) ($this->server['REQUEST_METHOD'] ?? 'GET')); // Default to GET if missing.
    }

    public function path(): string // Get clean URL path; used by router to match routes.
    {
        // Extract clean URL path without query string.
        $uri = (string) ($this->server['REQUEST_URI'] ?? '/'); // Read full URI from server.
        $path = parse_url($uri, PHP_URL_PATH) ?: '/'; // Remove query string and keep path only.

        return '/' . trim($path, '/'); // Normalize path so it always starts with slash.
    }

    /**
     * Return input data depending on request method.
     * Function flow: GET returns query data, other methods return post data.
     *
     * @return array<string, mixed>
     */
    public function all(): array // Get all request inputs; query or post.
    {
        return $this->method() === 'GET' ? $this->query : $this->post; // Use GET data for GET, POST data otherwise.
    }

    public function input(string $key, mixed $default = null): mixed // Get one form/query value by key.
    {
        // Get form/query value; if missing, use default.
        return $this->all()[$key] ?? $default; // Return value if exists, else default.
    }

    public function param(string $key, mixed $default = null): mixed // Get route parameter by key.
    {
        // Get route parameter, example: {id}.
        return $this->routeParams[$key] ?? $default; // Return route param if exists, else default.
    }

    /**
     * Set params from matched route.
     * Function flow: router resolves URL, then Application stores params here.
     *
     * @param array<string, string> $params
     */
    public function setRouteParams(array $params): void // Save route params after route match.
    {
        $this->routeParams = $params; // Replace current route params with matched params.
    }
} // End Request class.
