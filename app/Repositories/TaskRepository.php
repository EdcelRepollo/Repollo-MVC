<?php // Start TaskRepository file.

declare(strict_types=1); // Strict types enabled.

// repository para sa task data operations, nag-bridge sa model ug controller.
namespace App\Repositories; // Namespace for repositories.

use App\Models\Task; // Task model used for database actions.

final readonly class TaskRepository implements TaskRepositoryInterface // Repository wrapper for task data.
{
    // Inject Task model; repository uses it for database actions.
    public function __construct(private Task $tasks) // Constructor stores Task model.
    {
    } // Constructor ends.

    public function all(): array // Get all tasks.
    {
        // Return all task rows.
        return $this->tasks->all(); // Ask model for every task row.
    }

    public function find(int $id): ?array // Find one task by id.
    {
        // Return one task or null kung wala.
        return $this->tasks->find($id); // Ask model for matching task.
    }

    public function create(array $data): array // Create a new task.
    {
        // Save new task using model.
        return $this->tasks->create($data); // Pass form data to model create.
    }

    public function update(int $id, array $data): bool // Update task by id.
    {
        // Update existing task details.
        return $this->tasks->update($id, $data); // Pass update data to model.
    }

    public function delete(int $id): bool // Delete task by id.
    {
        // Delete task by id.
        return $this->tasks->delete($id); // Ask model to delete row.
    }

    public function complete(int $id): bool // Mark one task completed.
    {
        // Set task status to completed.
        return $this->tasks->update($id, ['status' => 'completed']); // Update only status field.
    }
} // End TaskRepository class.
