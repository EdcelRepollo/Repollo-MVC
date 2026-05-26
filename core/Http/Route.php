<?php // Start Route value object file.

declare(strict_types=1); // Strict typing enabled.

namespace Core\Http; // Namespace for HTTP classes.

final readonly class Route // Stores matched route information.
{
    /**
     * Route result object; stores controller, action, and URL params.
     *
     * @param class-string $controller
     * @param array<string, string> $params
     */
    public function __construct( // Constructor receives matched route data.
        public string $controller, // Controller class name to run.
        public string $action, // Controller method/action to call.
        public array $params = [] // Route params like id.
    ) {
    } // Constructor ends.
} // End Route class.
