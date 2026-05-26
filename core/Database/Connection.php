<?php // Start database connection file.

declare(strict_types=1); // Strict typing enabled.

namespace Core\Database; // Namespace for database layer.

use PDO; // PDO class for database connection.

final class Connection // Manages one lazy PDO connection.
{
    // Cached PDO connection; null until first use.
    private ?PDO $pdo = null; // Stores connected PDO object after first call.

    /**
     * Store database driver and config; connection is lazy.
     *
     * @param array<string, mixed> $config
     */
    public function __construct( // Constructor stores driver and config.
        private readonly DatabaseDriver $driver, // Database driver, example SQLiteDriver.
        private readonly array $config // Database config array.
    ) {
    } // Constructor ends.

    public function pdo(): PDO // Return PDO connection; create if needed.
    {
        // Connect only once; sunod calls reuse same PDO.
        if ($this->pdo === null) { // Check if connection not created yet.
            $this->pdo = $this->driver->connect($this->config); // Create PDO using configured driver.
        } // End lazy connection check.

        return $this->pdo; // Return cached PDO connection.
    }
} // End Connection class.
