<?php // Start base Model file.

declare(strict_types=1); // Strict typing enabled.

// base model nga mo-provide ug common CRUD behavior sa tanan models.
namespace Core\Database; // Namespace for database layer.

use PDO; // PDO used for database queries.

abstract class Model implements Findable, Persistable // Base model with common CRUD.
{
    // Table name for the model; child class must set this.
    protected string $table; // Child model sets database table name.

    // Primary key column; default is id.
    protected string $primaryKey = 'id'; // Default primary key column.

    /**
     * Fields allowed for create/update; protection against unwanted columns.
     *
     * @var list<string>
     */
    protected array $fillable = []; // Allowed fields for save/update.

    // PDO is injected so model can run database queries.
    public function __construct(protected readonly PDO $pdo) // Constructor stores PDO.
    {
    } // Constructor ends.

    /**
     * Get all records from model table.
     *
     * @return list<array<string, mixed>>
     */
    public function all(): array // Return all records from table.
    {
        return $this->query()->get(); // Use query builder get.
    }

    /**
     * Find one record using primary key.
     *
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array // Find one record by id.
    {
        return $this->query()->where($this->primaryKey, $id)->first(); // Add primary key where and get first row.
    }

    /**
     * Create record and return newly created row.
     *
     * @param array<string, mixed> $attributes
     * @return array<string, mixed>
     */
    public function create(array $attributes): array // Create new database record.
    {
        // Add timestamps and keep only fillable fields.
        $now = date('Y-m-d H:i:s'); // Current timestamp.
        $data = array_merge($this->filterFillable($attributes), [ // Merge safe fields with timestamps.
            'created_at' => $now, // Created time.
            'updated_at' => $now, // Updated time.
        ]); // Final data for insert.

        return $this->find($this->query()->insert($data)) ?? []; // Insert then fetch created row.
    }

    /**
     * Update record by id and refresh updated_at timestamp.
     *
     * @param array<string, mixed> $attributes
     */
    public function update(int $id, array $attributes): bool // Update existing record.
    {
        $data = array_merge($this->filterFillable($attributes), [ // Keep fillable fields and update timestamp.
            'updated_at' => date('Y-m-d H:i:s'), // New updated time.
        ]); // Final data for update.

        return $this->query()->where($this->primaryKey, $id)->update($data); // Update row matching id.
    }

    public function delete(int $id): bool // Delete record by id.
    {
        // Delete row matching the primary key.
        return $this->query()->where($this->primaryKey, $id)->delete(); // Delete row matching primary key.
    }

    protected function query(): QueryBuilder // Create query builder for this model.
    {
        // Create query builder for this model table.
        return new QueryBuilder($this->pdo, $this->table); // Return builder with PDO and table.
    }

    /**
     * Remove fields not listed in fillable; para safe ang mass assignment.
     *
     * @param array<string, mixed> $attributes
     * @return array<string, mixed>
     */
    private function filterFillable(array $attributes): array // Remove fields not allowed for save.
    {
        return array_intersect_key($attributes, array_flip($this->fillable)); // Keep only fillable keys.
    }
} // End Model class.
