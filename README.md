# Repollo MVC Task Manager

Repollo MVC Task Manager is a simple PHP MVC project for managing tasks. It uses a custom mini-framework built with plain PHP, Composer autoloading, routing, controllers, views, repositories, models, validation, and SQLite database storage.

The code also includes simple English-Bisaya comments to explain the function and flow of each part.

## Features

- View all tasks
- Create a new task
- View task details
- Edit an existing task
- Mark a task as completed
- Delete a task with confirmation
- Validate task form inputs
- Store data using SQLite

## Requirements

- PHP 8.3 or higher
- Composer
- PDO SQLite extension enabled

## Installation

1. Open the project folder:

```bash
cd Repollo-MVC
```

2. Install or refresh Composer autoload files:

```bash
composer dump-autoload
```

3. Run the PHP development server:

```bash
php -S localhost:8000 -t public
```

4. Open the app in your browser:

```txt
http://localhost:8000/tasks
```

The SQLite database file will be created automatically at:

```txt
database/task_manager.sqlite
```

The table structure is defined in:

```txt
database/schema.sql
```

## Default App Flow

1. The browser opens a URL like `/tasks`.
2. `public/index.php` starts the application.
3. `routes/web.php` registers all routes.
4. `Core\Http\Router` matches the URL to a controller action.
5. `App\Controllers\TaskController` handles the request.
6. `TaskRepository` and `Task` model get or save data.
7. `Core\View\Engine` renders the PHP view.
8. `Core\Http\Response` sends the final output to the browser.

## Routes

| Method | URI | Description |
| --- | --- | --- |
| GET | `/` | Show task list |
| GET | `/tasks` | Show task list |
| GET | `/tasks/create` | Show create task form |
| POST | `/tasks` | Save new task |
| GET | `/tasks/{id}` | Show one task |
| GET | `/tasks/{id}/edit` | Show edit task form |
| POST | `/tasks/{id}/update` | Save task changes |
| GET | `/tasks/{id}/delete` | Show delete confirmation |
| POST | `/tasks/{id}/delete` | Delete confirmed task |
| POST | `/tasks/{id}/complete` | Mark task as completed |

## Validation Rules

| Field | Rules |
| --- | --- |
| `title` | required, minimum 3 characters, maximum 100 characters |
| `description` | required, maximum 500 characters |
| `status` | required, must be `pending`, `in_progress`, or `completed` |
| `priority` | required, must be `low`, `normal`, or `high` |
| `due_date` | required, valid date format |

## Project Structure

```txt
app/
  Controllers/
    TaskController.php
  Models/
    Task.php
  Repositories/
    TaskRepository.php
    TaskRepositoryInterface.php
  Views/
    errors/
    layouts/
    tasks/

config/
  app.php
  database.php

core/
  Container/
  Database/
  Http/
  Validation/
  View/

database/
  schema.sql
  task_manager.sqlite

public/
  index.php

routes/
  web.php
```

## Main Files

- `public/index.php` is the entry point of the app.
- `routes/web.php` contains all app routes.
- `app/Controllers/TaskController.php` handles task actions.
- `app/Models/Task.php` represents the `tasks` table.
- `app/Repositories/TaskRepository.php` handles task data operations.
- `core/Http/Request.php` handles request data.
- `core/Http/Response.php` sends browser responses.
- `core/Http/Router.php` matches URLs to controller methods.
- `core/View/Engine.php` renders PHP view files.
- `core/Validation/Validator.php` checks form inputs.
- `database/schema.sql` creates the tasks table.

## Database Table

The `tasks` table contains:

| Column | Purpose |
| --- | --- |
| `id` | Unique task ID |
| `title` | Task title |
| `description` | Task details |
| `status` | Current task status |
| `priority` | Task priority |
| `due_date` | Task due date |
| `created_at` | Date/time created |
| `updated_at` | Date/time updated |

## Notes

- This project uses a custom MVC structure, not Laravel.
- The project is written in plain PHP for learning purposes.
- The comments in the code explain the function and flow in simple English-Bisaya.
- Do not add comments inside `composer.json` or lock files because JSON does not support comments.

## Author

Created by Repollo for a PHP MVC Task Manager project.
