<?php // Start PHP routes file; diri gi define ang web URLs.

// diri nimo i-register ang mga URL paths ug controllers nga mo-handle ana.
declare(strict_types=1); // Use strict types for route callback.

use App\Controllers\TaskController; // Controller that handles task pages/actions.
use Core\Http\Router; // Router class used to register routes.

return function (Router $router): void { // Return callback; Application calls this with router.
    // Home page route; 
    $router->get('/', [TaskController::class, 'index']); // uses TaskController index.

    // Task list route; 
    $router->get('/tasks', [TaskController::class, 'index']); //tasks shows all tasks.

    // Create form route; 
    $router->get('/tasks/create', [TaskController::class, 'create']); // GET create page.

    // Store route; 
    $router->post('/tasks', [TaskController::class, 'store']); // POST new task data.

    // Show route; 
    $router->get('/tasks/{id}', [TaskController::class, 'show']); // GET one task details.

    // Edit form route; 
    $router->get('/tasks/{id}/edit', [TaskController::class, 'edit']); // GET edit form.

    // Delete confirmation route; 
    $router->get('/tasks/{id}/delete', [TaskController::class, 'confirmDelete']); // GET delete confirmation.

    // Update route; 
    $router->post('/tasks/{id}/update', [TaskController::class, 'update']); // POST update task.

    // Destroy route; 
    $router->post('/tasks/{id}/delete', [TaskController::class, 'destroy']); // POST confirmed delete.

    // Complete route; 
    $router->post('/tasks/{id}/complete', [TaskController::class, 'complete']); // POST mark completed.
}; // End routes callback.
