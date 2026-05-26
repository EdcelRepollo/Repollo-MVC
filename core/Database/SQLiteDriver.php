<?php // Start SQLite driver file.

declare(strict_types=1); // Strict typing enabled.

namespace Core\Database; // Namespace for database layer.

use PDO; // PDO class for database connection.

final class SQLiteDriver implements DatabaseDriver // SQLite implementation of DatabaseDriver.
{
    /**
     * Create SQLite PDO connection.
     *
     * @param array<string, mixed> $config
     */
    public function connect(array $config): PDO // Connect to SQLite database file.
    {
        // Open SQLite database file from config.
        $pdo = new PDO('sqlite:' . $config['database']); // Create PDO using sqlite DSN.

        // Throw exceptions on database errors and return rows as arrays.
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Use exceptions for errors.
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Fetch rows as arrays.

        return $pdo; // Return ready PDO connection.
    }
} // End SQLiteDriver class.
