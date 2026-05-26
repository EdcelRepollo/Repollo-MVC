<?php // Start Findable interface file.

declare(strict_types=1); // Strict typing enabled.

namespace Core\Database; // Namespace for database contracts.

interface Findable // Contract for models that can fetch records.
{
    /**
     * Get all records; kuha tanan rows sa table.
     *
     * @return list<array<string, mixed>>
     */
    public function all(): array; // Return all rows.

    /**
     * Find one record by id; returns null kung wala.
     *
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array; // Return one row by id or null.
} // End Findable interface.
