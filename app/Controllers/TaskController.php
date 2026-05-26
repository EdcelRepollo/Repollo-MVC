<?php // Start TaskController file.

declare(strict_types=1); // Strict typing enabled.

namespace App\Controllers; // Namespace for app controllers.

use App\Repositories\TaskRepositoryInterface; // Contract for task data operations.
use Core\Http\Request; // Request object for input/params.
use Core\Http\Response; // Response object for browser output.
use Core\Validation\Validator; // Form validation helper.
use Core\View\Engine; // View rendering engine.

final readonly class TaskController // Handles task pages and actions.
{
    // Inject repository for task data and view engine for rendering pages.
    public function __construct( // Constructor receives dependencies.
        private TaskRepositoryInterface $tasks, // Repository for task records.
        private Engine $view // View engine for rendering PHP templates.
    ) {
    } // Constructor ends.

    public function index(): Response // Show task list page.
    {
        // Show task list page with all tasks.
        return new Response($this->view->render('tasks/index', [ // Render index view inside response.
            'title' => 'Task Manager', // Page title.
            'tasks' => $this->tasks->all(), // All task records.
        ])); // Return response.
    }

    public function create(): Response // Show create task form.
    {
        // Show create form with empty default values.
        return new Response($this->view->render('tasks/create', [ // Render create view.
            'title' => 'Create Task', // Page title.
            'errors' => [], // No errors on first load.
            'old' => $this->defaults(), // Empty/default form values.
        ])); // Return response.
    }

    public function store(Request $request): Response // Save new task from form.
    {
        // Get form data and validate before saving.
        $data = $this->taskData($request); // Normalize submitted form data.
        $validator = new Validator(); // Create validator.

        if (! $validator->validate($data, $this->rules())) { // If validation fails...
            // If invalid, show create form again with errors and old input.
            return new Response($this->view->render('tasks/create', [ // Re-render create form.
                'title' => 'Create Task', // Page title.
                'errors' => $validator->errors(), // Field errors.
                'formError' => $this->emptyFieldsMessage($data), // General empty-fields message.
                'old' => $data, // Keep user input.
            ]), 422); // Use validation error status.
        } // End validation failure.

        // Save task then redirect to task list.
        $this->tasks->create($data); // Save new task.

        return Response::redirect('/tasks'); // Redirect to task list.
    }

    public function show(Request $request): Response // Show one task details page.
    {
        // Find task by route id then show details page.
        $task = $this->findTask((int) $request->param('id')); // Find task from route id.

        return new Response($this->view->render('tasks/show', [ // Render show view.
            'title' => $task['title'], // Page title from task title.
            'task' => $task, // Task data for view.
        ])); // Return response.
    }

    public function edit(Request $request): Response // Show edit form.
    {
        // Find task and show edit form with current values.
        $task = $this->findTask((int) $request->param('id')); // Find existing task.

        return new Response($this->view->render('tasks/edit', [ // Render edit view.
            'title' => 'Edit Task', // Page title.
            'task' => $task, // Task for id/action.
            'errors' => [], // No errors on first load.
            'old' => $task, // Current task values fill the form.
        ])); // Return response.
    }

    public function update(Request $request): Response // Save edited task.
    {
        // Check task exists first, then validate updated form data.
        $id = (int) $request->param('id'); // Get route id.
        $this->findTask($id); // Ensure task exists.
        $data = $this->taskData($request); // Normalize form data.
        $validator = new Validator(); // Create validator.

        if (! $validator->validate($data, $this->rules())) { // If validation fails...
            // If invalid, return edit form with errors.
            return new Response($this->view->render('tasks/edit', [ // Re-render edit form.
                'title' => 'Edit Task', // Page title.
                'task' => ['id' => $id], // Keep id for form action.
                'errors' => $validator->errors(), // Field errors.
                'formError' => $this->emptyFieldsMessage($data), // General empty-fields message.
                'old' => $data, // Keep submitted data.
            ]), 422); // Validation error status.
        } // End validation failure.

        // Save changes then redirect to task details.
        $this->tasks->update($id, $data); // Save updates.

        return Response::redirect('/tasks/' . $id); // Redirect to updated task.
    }

    public function destroy(Request $request): Response // Delete task after confirmation.
    {
        // Find task and require confirm_delete=yes before deleting.
        $id = (int) $request->param('id'); // Get route id.
        $task = $this->findTask($id); // Find task or 404.

        if ($request->input('confirm_delete') !== 'yes') { // Require hidden confirmation value.
            // If not confirmed, show delete page again with message.
            return new Response($this->view->render('tasks/delete', [ // Re-render delete view.
                'title' => 'Delete Task', // Page title.
                'task' => $task, // Task being deleted.
                'alertMessage' => 'delete permanently?', // Warning message.
                'error' => 'Please confirm before deleting.', // Confirmation error.
            ]), 422); // Validation-like status.
        } // End confirmation check.

        // Delete confirmed task then go back to list.
        $this->tasks->delete($id); // Delete task.

        return Response::redirect('/tasks'); // Redirect back to list.
    }

    public function confirmDelete(Request $request): Response // Show delete confirmation page.
    {
        // Show confirmation page before permanent delete.
        $task = $this->findTask((int) $request->param('id')); // Find task to confirm delete.

        return new Response($this->view->render('tasks/delete', [ // Render delete view.
            'title' => 'Delete Task', // Page title.
            'task' => $task, // Task being deleted.
            'alertMessage' => 'delete permanently?', // Warning message.
            'error' => null, // No error on first load.
        ])); // Return response.
    }

    public function complete(Request $request): Response // Mark task completed.
    {
        // Mark task as completed then redirect to list.
        $this->tasks->complete((int) $request->param('id')); // Update status to completed.

        return Response::redirect('/tasks'); // Redirect to list.
    }

    /**
     * Collect task fields from request and normalize values.
     *
     * @return array<string, string>
     */
    private function taskData(Request $request): array // Collect and clean task form data.
    {
        return [ // Return normalized task data array.
            'title' => trim((string) $request->input('title', '')), // Clean title.
            'description' => trim((string) $request->input('description', '')), // Clean description.
            'status' => (string) $request->input('status', 'pending'), // Status default pending.
            'priority' => (string) $request->input('priority', 'normal'), // Priority default normal.
            'due_date' => trim((string) $request->input('due_date', '')), // Clean due date.
        ]; // End task data.
    }

    /**
     * Validation rules for create/update task form.
     *
     * @return array<string, list<string>>
     */
    private function rules(): array // Return validation rules for task form.
    {
        return [ // Rules keyed by field name.
            'title' => ['required', 'min:3', 'max:100'], // Title required length 3-100.
            'description' => ['required', 'max:500'], // Description required max 500.
            'status' => ['required', 'in:pending,in_progress,completed'], // Status must be allowed value.
            'priority' => ['required', 'in:low,normal,high'], // Priority must be allowed value.
            'due_date' => ['required', 'date'], // Due date required and valid date.
        ]; // End rules.
    }

    /**
     * Show special message when main required fields are all empty.
     *
     * @param array<string, string> $data
     */
    private function emptyFieldsMessage(array $data): ?string // Return message if major fields empty.
    {
        return $data['title'] === '' && $data['description'] === '' && $data['due_date'] === '' // Check main required fields.
            ? 'Fields are empty' // Message if all empty.
            : null; // No general message otherwise.
    }

    /**
     * Default form values for create page.
     *
     * @return array<string, string>
     */
    private function defaults(): array // Default values for new task form.
    {
        return [ // Return blank/default form data.
            'title' => '', // Empty title.
            'description' => '', // Empty description.
            'status' => 'pending', // Default status.
            'priority' => 'normal', // Default priority.
            'due_date' => '', // Empty due date.
        ]; // End defaults.
    }

    /**
     * Find task or throw 404 error when not found.
     *
     * @return array<string, mixed>
     */
    private function findTask(int $id): array // Find task or throw 404.
    {
        $task = $this->tasks->find($id); // Ask repository for task.

        if ($task === null) { // If no task found...
            throw new \RuntimeException('Task not found.', 404); // Stop with 404.
        } // End not found check.

        return $task; // Return found task.
    }
} // End TaskController class.
