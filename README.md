# Repollo MVC Task Manager

A pure PHP 8.3+ final project that implements a lightweight MVC framework and a working Task Manager MVP on top of it. The project uses Composer PSR-4 autoloading, a custom router, controllers, PHP views, a small DI container, a PDO SQLite database layer, a simple ORM-style model, and a custom validator.

## Requirements

- PHP 8.3 or higher
- Composer
- PDO SQLite extension enabled

## Setup

Install Composer autoload files:

```bash
composer dump-autoload
```

Run the app with PHP's built-in server:

```bash
php -S localhost:8000 -t public
```

Open:

```txt
http://localhost:8000/tasks
```

The SQLite database file is created automatically at `database/task_manager.sqlite`. The schema is stored in `database/schema.sql`.

## Routes

| Method | URI | Action |
| --- | --- | --- |
| GET | `/` | Task list |
| GET | `/tasks` | Task list |
| GET | `/tasks/create` | Create form |
| POST | `/tasks` | Store task |
| GET | `/tasks/{id}` | Task detail |
| GET | `/tasks/{id}/edit` | Edit form |
| POST | `/tasks/{id}/update` | Update task |
| POST | `/tasks/{id}/delete` | Delete task |
| POST | `/tasks/{id}/complete` | Mark completed |

## Framework Design

- `Core\Http\Router` maps HTTP methods and URI patterns to controller actions, including route parameters like `/tasks/{id}`.
- `Core\Http\Request` wraps request input, route parameters, method, and path.
- `Core\Http\Response` wraps status, headers, redirects, and body output.
- `Core\Container\Container` resolves dependencies and binds abstractions to implementations.
- `Core\View\Engine` renders PHP views and layouts with escaped output helpers.
- `Core\Database\Connection`, `DatabaseDriver`, `SQLiteDriver`, `QueryBuilder`, and `Model` provide database access and simple ORM-style CRUD.
- `Core\Validation\Validator` provides custom form validation without external packages.

## MVP

The MVP is a Task Manager. It supports creating, viewing, editing, completing, and deleting tasks. Each task has a title, description, status, priority, and optional due date.

Validation rules:

- `title`: required, minimum 3 characters, maximum 100 characters
- `description`: maximum 500 characters
- `status`: pending, in progress, or completed
- `priority`: low, normal, or high
- `due_date`: optional valid `YYYY-MM-DD` date

## Project Structure

```txt
app/
  Controllers/
  Models/
  Repositories/
  Views/
  Middleware/
core/
  Container/
  Database/
  Http/
  Validation/
  View/
config/
database/
public/
routes/
```

Only `public/index.php` manually requires Composer's autoloader. Application and framework classes are loaded through PSR-4 namespaces:

- `Core\\` => `core/`
- `App\\` => `app/`
