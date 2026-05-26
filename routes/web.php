<?php // Start PHP routes file; diri gi define ang web URLs.

declare(strict_types=1); // Use strict types for route callback.

use App\Controllers\TaskController; // Controller that handles task pages/actions.
use Core\Http\Router; // Router class used to register routes.

return function (Router $router): void { // Return callback; Application calls this with router.
    // Home page route; kung mo visit sa "/", ipakita ang task list.
    $router->get('/', [TaskController::class, 'index']); // GET / uses TaskController index.

    // Task list route; mao ni ang main page sa tanan tasks.
    $router->get('/tasks', [TaskController::class, 'index']); // GET /tasks shows all tasks.

    // Create form route; mo display sa form para mag add ug new task.
    $router->get('/tasks/create', [TaskController::class, 'create']); // GET create page.

    // Store route; mo save sa new task gikan sa create form.
    $router->post('/tasks', [TaskController::class, 'store']); // POST new task data.

    // Show route; mo display sa details sa usa ka task based sa id.
    $router->get('/tasks/{id}', [TaskController::class, 'show']); // GET one task details.

    // Edit form route; mo display sa form para ma update ang existing task.
    $router->get('/tasks/{id}/edit', [TaskController::class, 'edit']); // GET edit form.

    // Delete confirmation route; mangutana una before i-delete ang task.
    $router->get('/tasks/{id}/delete', [TaskController::class, 'confirmDelete']); // GET delete confirmation.

    // Update route; mo save sa changes gikan sa edit form.
    $router->post('/tasks/{id}/update', [TaskController::class, 'update']); // POST update task.

    // Destroy route; mo delete sa selected task after confirmation.
    $router->post('/tasks/{id}/delete', [TaskController::class, 'destroy']); // POST confirmed delete.

    // Complete route; mo mark sa selected task as completed.
    $router->post('/tasks/{id}/complete', [TaskController::class, 'complete']); // POST mark completed.
}; // End routes callback.
