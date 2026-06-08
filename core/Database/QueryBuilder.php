<?php // Start QueryBuilder file.

declare(strict_types=1); // Strict typing enabled.

// simple query builder para maghimo ug SQL statements sa model.
namespace Core\Database; // Namespace for database layer.

use PDO; // PDO used to prepare/execute SQL.

final class QueryBuilder // Small helper for building simple SQL queries.
{
    /**
     * WHERE conditions collected before query runs.
     *
     * @var list<array{column: string, operator: string, value: mixed}>
     */
    private array $wheres = []; // Stored WHERE clauses.

    // Store PDO connection and table name for this query.
    public function __construct(private readonly PDO $pdo, private readonly string $table) // Constructor stores PDO and table.
    {
    } // Constructor ends.

    public function where(string $column, mixed $value, string $operator = '='): self // Add WHERE condition.
    {
        // Add one WHERE condition and return same builder for chaining.
        $this->wheres[] = compact('column', 'operator', 'value'); // Save condition parts.

        return $this; // Return same builder for chaining.
    }

    /**
     * Get many rows ordered by latest id by default.
     *
     * @return list<array<string, mixed>>
     */
    public function get(string $orderBy = 'id DESC'): array // Run SELECT and return many rows.
    {
        // Build SELECT query with optional WHERE clauses.
        $sql = "SELECT * FROM {$this->table}" . $this->whereSql() . " ORDER BY {$orderBy}"; // Build SQL string.
        $statement = $this->pdo->prepare($sql); // Prepare SQL statement.
        $statement->execute($this->bindings()); // Execute with WHERE bindings.

        return $statement->fetchAll(); // Return all rows.
    }

    /**
     * Get first matching row or null.
     *
     * @return array<string, mixed>|null
     */
    public function first(): ?array // Run SELECT and return first row.
    {
        // LIMIT 1 kay one row ra needed.
        $sql = "SELECT * FROM {$this->table}" . $this->whereSql() . ' LIMIT 1'; // Build single-row SQL.
        $statement = $this->pdo->prepare($sql); // Prepare SQL statement.
        $statement->execute($this->bindings()); // Execute with WHERE bindings.
        $row = $statement->fetch(); // Fetch one row.

        return $row === false ? null : $row; // Convert false to null.
    }

    /**
     * Insert new row and return inserted id.
     *
     * @param array<string, mixed> $attributes
     */
    public function insert(array $attributes): int // Insert row and return new id.
    {
        // Prepare column names and named placeholders.
        $columns = array_keys($attributes); // Get columns from data keys.
        $placeholders = array_map(fn (string $column): string => ":{$column}", $columns); // Build named placeholders.

        // Build INSERT statement safely using placeholders.
        $sql = sprintf( // Format INSERT SQL.
            'INSERT INTO %s (%s) VALUES (%s)', // SQL template.
            $this->table, // Table name.
            implode(', ', $columns), // Comma-separated columns.
            implode(', ', $placeholders) // Comma-separated placeholders.
        ); // Final insert SQL.

        $this->pdo->prepare($sql)->execute($attributes); // Execute insert using attributes as bindings.

        return (int) $this->pdo->lastInsertId(); // Return new row id.
    }

    /**
     * Update rows matching current WHERE clauses.
     *
     * @param array<string, mixed> $attributes
     */
    public function update(array $attributes): bool // Update rows with current WHERE clauses.
    {
        // Create SET clauses like title = :title.
        $sets = array_map(fn (string $column): string => "{$column} = :{$column}", array_keys($attributes)); // Build SET parts.
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . $this->whereSql(); // Build update SQL.

        return $this->pdo->prepare($sql)->execute(array_merge($attributes, $this->bindings())); // Execute update with data and where bindings.
    }

    public function delete(): bool // Delete rows with current WHERE clauses.
    {
        // Delete rows matching current WHERE clauses.
        $statement = $this->pdo->prepare("DELETE FROM {$this->table}" . $this->whereSql()); // Prepare delete SQL.

        return $statement->execute($this->bindings()); // Execute delete with WHERE bindings.
    }

    private function whereSql(): string // Convert stored wheres into SQL text.
    {
        // No WHERE clauses means query applies to all rows.
        if ($this->wheres === []) { // If no conditions...
            return ''; // No WHERE SQL needed.
        } // End no-where check.

        // Convert saved wheres into SQL conditions with unique placeholders.
        $clauses = array_map( // Build each WHERE clause.
            fn (array $where, int $index): string => "{$where['column']} {$where['operator']} :where_{$index}", // One condition.
            $this->wheres, // Saved WHERE data.
            array_keys($this->wheres) // Indexes for unique placeholders.
        ); // Final clauses array.

        return ' WHERE ' . implode(' AND ', $clauses); // Join clauses with AND.
    }

    /**
     * Create placeholder bindings for WHERE values.
     *
     * @return array<string, mixed>
     */
    private function bindings(): array // Build values for WHERE placeholders.
    {
        // Example result: ['where_0' => 5].
        $bindings = []; // Start empty bindings array.
        foreach ($this->wheres as $index => $where) { // Loop saved WHERE clauses.
            $bindings["where_{$index}"] = $where['value']; // Bind value to matching placeholder.
        } // End bindings loop.

        return $bindings; // Return final WHERE bindings.
    }
} // End QueryBuilder class.
