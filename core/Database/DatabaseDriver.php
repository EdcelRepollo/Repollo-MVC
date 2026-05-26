<?php // Start database driver interface file.

declare(strict_types=1); // Strict typing enabled.

namespace Core\Database; // Namespace for database layer.

use PDO; // PDO return type for driver connection.

interface DatabaseDriver // Contract for any database driver.
{
    /**
     * Connect to database using config and return PDO instance.
     *
     * @param array<string, mixed> $config
     */
    public function connect(array $config): PDO; // Connect and return PDO.
} // End DatabaseDriver interface.
