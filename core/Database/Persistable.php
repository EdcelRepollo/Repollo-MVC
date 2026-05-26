<?php // Start Persistable interface file.

declare(strict_types=1); // Strict typing enabled.

namespace Core\Database; // Namespace for database contracts.

interface Persistable // Contract for models that can save changes.
{
    /**
     * Create a new record using allowed attributes.
     *
     * @param array<string, mixed> $attributes
     * @return array<string, mixed>
     */
    public function create(array $attributes): array; // Insert and return created row.

    /**
     * Update existing record by id.
     *
     * @param array<string, mixed> $attributes
     */
    public function update(int $id, array $attributes): bool; // Update row by id.

    // Delete existing record by id.
    public function delete(int $id): bool; // Delete row by id.
} // End Persistable interface.
