<?php // Start main Application class file.

declare(strict_types=1); // Strict typing enabled.

namespace Core; // Core namespace for main app class.

use App\Models\Task; // Task model binding.
use App\Repositories\TaskRepository; // Concrete task repository.
use App\Repositories\TaskRepositoryInterface; // Repository contract.
use Core\Container\Container; // Dependency injection container.
use Core\Database\Connection; // Database connection wrapper.
use Core\Database\DatabaseDriver; // Database driver contract.
use Core\Database\SQLiteDriver; // SQLite driver implementation.
use Core\Http\Request; // Request object.
use Core\Http\Response; // Response object.
use Core\Http\Router; // Router object.
use Core\View\Engine; // View renderer.
use PDO; // Database connection type.
use Throwable; // Catches any error/exception.

final class Application // Main app coordinator; handles boot, routing, response.
{
    // Service container; mao ni ang mo create ug store sa needed classes.
    private Container $container; // Stores and resolves services.

    // Router; mao ni ang mo match sa URL to controller action.
    private Router $router; // Stores route definitions.

    /**
     * App configuration; contains app settings and database settings.
     *
     * @var array{app: array<string, mixed>, database: array<string, mixed>}
     */
    private array $config; // App and database config.

    /**
     * Start the application object; gi prepare ang services ug database.
     *
     * @param array{app: array<string, mixed>, database: array<string, mixed>} $config
     */
    public function __construct(private readonly string $basePath, array $config) // Create app with path and config.
    {
        $this->config = $config; // Save config for later use.
        $this->container = new Container(); // Create service container.
        $this->router = new Router(); // Create router.

        $this->registerServices(); // Register dependencies.
        $this->prepareDatabase(); // Prepare database/schema.
    }

    public function router(): Router // Expose router so routes can be registered.
    {
        // Return router para routes/web.php maka register ug routes.
        return $this->router; // Return router instance.
    }

    public function run(): void // Main request flow; capture, route, run controller, send response.
    {
        // Capture current browser request; kuhaon ang URL, method, ug form data.
        $request = Request::capture(); // Build Request from PHP globals.

        try { // Try normal request flow.
            // Find matching route, then attach route params like {id}.
            $route = $this->router->resolve($request); // Match route.
            $request->setRouteParams($route->params); // Put route params into request.

            // Resolve controller from container and call the route action.
            $controller = $this->container->resolve($route->controller); // Create controller with dependencies.
            $result = $controller->{$route->action}($request); // Call controller action.

            // If action returned plain text, wrap it as a Response.
            $response = $result instanceof Response // Check if controller returned Response.
                ? $result // Use Response directly.
                : new Response((string) $result); // Wrap plain output in Response.
        } catch (Throwable $exception) { // If any error happens, handle it here.
            // If naay error, show error page with proper status code.
            $status = (int) $exception->getCode(); // Get exception code as HTTP status candidate.
            $response = new Response( // Build error response.
                body: $this->container->resolve(Engine::class)->render('errors/500', [ // Render error view.
                    'message' => $this->config['app']['debug'] ? $exception->getMessage() : 'Something went wrong.', // Show real error only in debug.
                ]), // End error view data.
                statusCode: $status >= 400 && $status < 600 ? $status : 500 // Use valid HTTP error code or 500.
            ); // End error response.
        }

        $response->send(); // Send final response to browser.
    }

    private function registerServices(): void // Register all services in container.
    {
        // Register already-created core objects.
        $this->container->instance(Application::class, $this); // Store current app instance.
        $this->container->instance(Router::class, $this->router); // Store router instance.
        $this->container->instance(Engine::class, new Engine($this->basePath . '/app/Views')); // Store view engine.

        // Bind database driver and connection; kani para reusable ang PDO connection.
        $this->container->bind(DatabaseDriver::class, SQLiteDriver::class); // Use SQLite as database driver.
        $this->container->singleton(Connection::class, function (Container $container): Connection { // Register shared Connection.
            return new Connection( // Create Connection object.
                driver: $container->resolve(DatabaseDriver::class), // Resolve database driver.
                config: $this->config['database'] // Pass database config.
            ); // Return Connection.
        }); // End Connection singleton.
        $this->container->singleton(PDO::class, fn (Container $container): PDO => $container->resolve(Connection::class)->pdo()); // Shared PDO.

        // Bind app classes; repository interface points to concrete TaskRepository.
        $this->container->bind(Task::class, Task::class); // Bind Task model.
        $this->container->bind(TaskRepositoryInterface::class, TaskRepository::class); // Interface uses TaskRepository.
    }

    private function prepareDatabase(): void // Ensure database folder/table exists.
    {
        // Make sure database folder exists before SQLite creates/uses the file.
        $databasePath = $this->config['database']['database'] ?? null; // Read database path from config.
        if (is_string($databasePath)) { // Continue only if path is valid string.
            $directory = dirname($databasePath); // Get database folder.
            if (! is_dir($directory)) { // If folder does not exist...
                mkdir($directory, 0777, true); // Create folder recursively.
            } // End folder check.
        } // End database path check.

        // Load schema.sql if it exists; creates tables automatically.
        $schema = $this->basePath . '/database/schema.sql'; // Build schema file path.
        if (file_exists($schema)) { // Only run schema if file exists.
            $this->container->resolve(PDO::class)->exec((string) file_get_contents($schema)); // Execute schema SQL.
        } // End schema check.
    }
} // End Application class.
