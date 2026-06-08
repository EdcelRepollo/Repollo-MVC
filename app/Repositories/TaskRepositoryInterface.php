<?php // Start TaskRepositoryInterface file.

declare(strict_types=1); // Strict typing enabled.

// kontrata para sa task repository, klaro unsa ang methods nga dapat naa.
namespace App\Repositories; // Namespace for repository contracts.

interface TaskRepositoryInterface // Contract for task repository behavior.
{
    /**
     * Get all tasks.
     *
     * @return list<array<string, mixed>>
     */
    public function all(): array; // Return all tasks.

    /**
     * Find one task by id.
     *
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array; // Return task by id or null.

    /**
     * Create new task.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function create(array $data): array; // Create task and return it.

    /**
     * Update existing task.
     *
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool; // Update task and return success.

    // Delete task by id.
    public function delete(int $id): bool; // Delete task and return success.

    // Mark task as completed.
    public function complete(int $id): bool; // Complete task and return success.
} // End TaskRepositoryInterface.
