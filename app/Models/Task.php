<?php // Start Task model file.

declare(strict_types=1); // Strict type checking.

// model para sa tasks table, nag-specify unsa nga columns ang pwede gamiton.
namespace App\Models; // Namespace for app models.

use Core\Database\Model; // Base model with CRUD helpers.

final class Task extends Model // Task model maps to tasks table.
{
    // Database table used by this model.
    protected string $table = 'tasks'; // Table name for task records.

    /**
     * Fields allowed when creating/updating tasks.
     *
     * @var list<string>
     */
    protected array $fillable = [ // List of columns allowed for mass assignment.
        'title', // Task title column.
        'description', // Task description column.
        'status', // Task status column.
        'priority', // Task priority column.
        'due_date', // Task due date column.
    ]; // End fillable list.
} // End Task model.
