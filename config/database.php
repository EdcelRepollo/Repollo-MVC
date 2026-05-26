<?php // Start database config file.

declare(strict_types=1); // Strict types for config file.

// Database settings; diri gi specify unsa nga database driver ug file gamiton.
return [ // Return database configuration array.
    // SQLite driver; simple file-based database siya.
    'driver' => 'sqlite', // Database driver name.

    // Database file path; diri ma store ang task records.
    'database' => dirname(__DIR__) . '/database/task_manager.sqlite', // Full SQLite database file path.
]; // End database config.
