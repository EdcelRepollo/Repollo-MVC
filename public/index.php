<?php // Start PHP front controller; tanan browser requests mo agi diri.

declare(strict_types=1); // Enforce strict typing for safer PHP code.

// Load Composer autoload; kani ang mo load sa classes automatically.
require dirname(__DIR__) . '/vendor/autoload.php'; // Include Composer autoloader.

// Base path sa project; gamiton para dali ma locate ang config, routes, ug views.
$basePath = dirname(__DIR__); // Project root path, one level above public.

// Create the main application; diri gi pass ang app ug database settings.
$app = new Core\Application($basePath, [ // Build Application with base path and configs.
    'app' => require $basePath . '/config/app.php', // Load app config file.
    'database' => require $basePath . '/config/database.php', // Load database config file.
]); // Application is ready after services/database are prepared.

// Load web routes; kani ang mo register sa URLs sa router.
(require $basePath . '/routes/web.php')($app->router()); // Pass router to route registration file.

// Run the app; mo receive request, process route, then send response.
$app->run(); // Start request handling and output response.
