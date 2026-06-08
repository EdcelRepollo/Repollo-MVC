<?php // Start sa TaskController file.

declare(strict_types=1); // Gi-enable ang strict typing.

// controller nga nag-handle sa task pages, forms, ug actions.
namespace App\Controllers; // Namespace para sa app controllers.

use App\Repositories\TaskRepositoryInterface; // Contract para sa task data operations.
use Core\Http\Request; // Request object para sa input/params.
use Core\Http\Response; // Response object para sa browser output.
use Core\Validation\Validator; // Helper para sa form validation.
use Core\View\Engine; // View engine para sa page rendering.

final readonly class TaskController // Mo-handle sa task pages ug actions.
{
    // I-inject ang repository para sa task data ug view engine para sa pages.
    public function __construct( // Constructor nga mo-receive sa dependencies.
        private TaskRepositoryInterface $tasks, // Repository para sa task records.
        private Engine $view // View engine para sa PHP templates.
    ) {
    } // End sa constructor.

    public function index(): Response // Ipakita ang task list page.
    {
        // Ipakita ang task list page with all tasks.
        return new Response($this->view->render('tasks/index', [ // I-render ang index view inside response.
            'title' => 'Task Manager', // Title sa page.
            'tasks' => $this->tasks->all(), // Tanan task records.
        ])); // I-return ang response.
    }

    public function create(): Response // Ipakita ang create task form.
    {
        // Ipakita ang create form with empty default values.
        return new Response($this->view->render('tasks/create', [ // I-render ang create view.
            'title' => 'Create Task', // Title sa page.
            'errors' => [], // Walay errors sa first load.
            'old' => $this->defaults(), // Empty/default form values.
        ])); // I-return ang response.
    }

    public function store(Request $request): Response // I-save ang new task gikan sa form.
    {
        // Kuhaon ang form data ug i-validate before saving.
        $data = $this->taskData($request); // I-normalize ang submitted form data.
        $validator = new Validator(); // Mag-create ug validator.

        if (! $validator->validate($data, $this->rules())) { // Kung failed ang validation...
            $formError = $this->emptyFieldsMessage($data); // General empty-fields message.

            // Kung invalid, ipakita balik ang create form with errors ug old input.
            return new Response($this->view->render('tasks/create', [ // I-render balik ang create form.
                'title' => 'Create Task', // Title sa page.
                'errors' => $formError === null ? $validator->errors() : [], // I-hide ang field errors kung all required fields are empty.
                'formError' => $formError, // General empty-fields message.
                'old' => $data, // I-keep ang user input.
            ]), 422); // Gamiton ang validation error status.
        } // End sa validation failure.

        // I-save ang task then redirect to task list.
        $this->tasks->create($data); // I-save ang new task.

        return Response::redirect('/tasks'); // Redirect padulong sa task list.
    }

    public function show(Request $request): Response // Ipakita ang one task details page.
    {
        // Pangitaon ang task by route id then ipakita ang details page.
        $task = $this->findTask((int) $request->param('id')); // Pangitaon ang task from route id.

        return new Response($this->view->render('tasks/show', [ // I-render ang show view.
            'title' => $task['title'], // Title sa page gikan sa task title.
            'task' => $task, // Task data para sa view.
        ])); // I-return ang response.
    }

    public function edit(Request $request): Response // Ipakita ang edit form.
    {
        // Pangitaon ang task ug ipakita ang edit form with current values.
        $task = $this->findTask((int) $request->param('id')); // Pangitaon ang existing task.

        return new Response($this->view->render('tasks/edit', [ // I-render ang edit view.
            'title' => 'Edit Task', // Title sa page.
            'task' => $task, // Task para sa id/action.
            'errors' => [], // Walay errors sa first load.
            'old' => $task, // Current task values ang mo-fill sa form.
        ])); // I-return ang response.
    }

    public function update(Request $request): Response // I-save ang edited task.
    {
        // I-check first kung task exists, then i-validate ang updated form data.
        $id = (int) $request->param('id'); // Kuhaon ang route id.
        $this->findTask($id); // Siguraduhon nga existing ang task.
        $data = $this->taskData($request); // I-normalize ang form data.
        $validator = new Validator(); // Mag-create ug validator.

        if (! $validator->validate($data, $this->rules())) { // Kung failed ang validation...
            $formError = $this->emptyFieldsMessage($data); // General empty-fields message.

            // Kung invalid, ibalik ang edit form with errors.
            return new Response($this->view->render('tasks/edit', [ // I-render balik ang edit form.
                'title' => 'Edit Task', // Title sa page.
                'task' => ['id' => $id], // I-keep ang id para sa form action.
                'errors' => $formError === null ? $validator->errors() : [], // I-hide ang field errors kung all required fields are empty.
                'formError' => $formError, // General empty-fields message.
                'old' => $data, // I-keep ang submitted data.
            ]), 422); // Validation error status.
        } // End sa validation failure.

        // I-save ang changes then redirect to task details.
        $this->tasks->update($id, $data); // I-save ang updates.

        return Response::redirect('/tasks/' . $id); // Redirect padulong sa updated task.
    }

    public function destroy(Request $request): Response // I-delete ang task after confirmation.
    {
        // Pangitaon ang task ug i-require ang confirm_delete=yes before deleting.
        $id = (int) $request->param('id'); // Kuhaon ang route id.
        $task = $this->findTask($id); // Pangitaon ang task or 404.

        if ($request->input('confirm_delete') !== 'yes') { // I-require ang hidden confirmation value.
            // Kung not confirmed, ipakita balik ang delete page with message.
            return new Response($this->view->render('tasks/delete', [ // I-render balik ang delete view.
                'title' => 'Delete Task', // Title sa page.
                'task' => $task, // Task nga ide-delete.
                'alertMessage' => 'delete permanently?', // Warning message para sa user.
                'error' => 'Please confirm before deleting.', // Confirmation error message.
            ]), 422); // Validation-like status.
        } // End sa confirmation check.

        // I-delete ang confirmed task then balik sa list.
        $this->tasks->delete($id); // I-delete ang task.

        return Response::redirect('/tasks'); // Redirect balik sa list.
    }

    public function confirmDelete(Request $request): Response // Ipakita ang delete confirmation page.
    {
        // Ipakita ang confirmation page before permanent delete.
        $task = $this->findTask((int) $request->param('id')); // Pangitaon ang task to confirm delete.

        return new Response($this->view->render('tasks/delete', [ // I-render ang delete view.
            'title' => 'Delete Task', // Title sa page.
            'task' => $task, // Task nga ide-delete.
            'alertMessage' => 'delete permanently?', // Warning message para sa user.
            'error' => null, // Walay error sa first load.
        ])); // I-return ang response.
    }

    public function complete(Request $request): Response // I-mark ang task as completed.
    {
        // I-mark ang task as completed then redirect to list.
        $this->tasks->complete((int) $request->param('id')); // I-update ang status to completed.

        return Response::redirect('/tasks'); // Redirect padulong sa list.
    }

    /**
     * Kuhaon ang task fields from request ug i-normalize ang values.
     *
     * @return array<string, string>
     */
    private function taskData(Request $request): array // Kuhaon ug limpyohan ang task form data.
    {
        return [ // I-return ang normalized task data array.
            'title' => trim((string) $request->input('title', '')), // Limpyohan ang title.
            'description' => trim((string) $request->input('description', '')), // Limpyohan ang description.
            'status' => (string) $request->input('status', 'pending'), // Default status kay pending.
            'priority' => (string) $request->input('priority', 'normal'), // Default priority kay normal.
            'due_date' => trim((string) $request->input('due_date', '')), // Limpyohan ang due date.
        ]; // End sa task data.
    }

    /**
     * Validation rules para sa create/update task form.
     *
     * @return array<string, list<string>>
     */
    private function rules(): array // I-return ang validation rules para sa task form.
    {
        return [ // Rules nga keyed by field name.
            'title' => ['required', 'min:3', 'max:100'], // Title required, length 3-100.
            'description' => ['required', 'max:500'], // Description required, max 500.
            'status' => ['required', 'in:pending,in_progress,completed'], // Status dapat allowed value.
            'priority' => ['required', 'in:low,normal,high'], // Priority dapat allowed value.
            'due_date' => ['required', 'date'], // Due date required ug valid date.
        ]; // End sa rules.
    }

    /**
     * Ipakita ang special message kung main required fields kay empty tanan.
     *
     * @param array<string, string> $data
     */
    private function emptyFieldsMessage(array $data): ?string // I-return ang message kung major fields empty.
    {
        return $data['title'] === '' && $data['description'] === '' && $data['due_date'] === '' // I-check ang main required fields.
            ? 'Fields are empty' // Message kung empty tanan.
            : null; // Walay general message otherwise.
    }

    /**
     * Default form values para sa create page.
     *
     * @return array<string, string>
     */
    private function defaults(): array // Default values para sa new task form.
    {
        return [ // I-return ang blank/default form data.
            'title' => '', // Empty title value.
            'description' => '', // Empty description value.
            'status' => 'pending', // Default status value.
            'priority' => 'normal', // Default priority value.
            'due_date' => '', // Empty due date value.
        ]; // End sa defaults.
    }

    /**
     * Pangitaon ang task or mo-throw ug 404 error kung not found.
     *
     * @return array<string, mixed>
     */
    private function findTask(int $id): array // Pangitaon ang task or throw 404.
    {
        $task = $this->tasks->find($id); // Mangayo sa repository ug task.

        if ($task === null) { // Kung walay task nga nakita...
            throw new \RuntimeException('Task not found.', 404); // Mo-stop with 404.
        } // End sa not found check.

        return $task; // I-return ang found task.
    }
} // End sa TaskController class.
