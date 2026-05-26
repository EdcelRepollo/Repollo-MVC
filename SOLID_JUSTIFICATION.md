# SOLID Design Justification

This project separates a small PHP MVC framework from the Task Manager application that runs on top of it. The framework is organized around focused classes and interfaces so the architecture demonstrates all five SOLID principles.

## Single Responsibility Principle

Each framework class has one main reason to change. `Core\Http\Router` only registers and resolves routes. It does not instantiate controllers, connect to the database, or render views. `Core\Http\Request` only wraps HTTP input, request method, URI path, and route parameters. `Core\Http\Response` only stores status, headers, redirects, and body output. `Core\View\Engine` only renders PHP view files and escapes output. The `App\Controllers\TaskController` coordinates the task workflow, but it does not write SQL directly; persistence is delegated to `TaskRepositoryInterface`.

## Open/Closed Principle

The database connection is open for extension through `Core\Database\DatabaseDriver`. `Core\Database\Connection` depends on the driver interface, so a new driver such as `MySQLDriver` can be added without rewriting `Connection`. The router is also extensible by registering more routes in `routes/web.php` without modifying the router's matching algorithm.

## Liskov Substitution Principle

`Core\Database\SQLiteDriver` implements `DatabaseDriver` and returns a configured `PDO` connection from the same `connect(array $config): PDO` contract expected by `Connection`. Any future driver that follows the same contract can replace it without surprising callers. Application code asks for repositories and framework services through stable method signatures, so implementations can be substituted safely.

## Interface Segregation Principle

The database model behavior is split into `Core\Database\Findable` and `Core\Database\Persistable`. A read-only model or repository can depend only on `Findable` and is not forced to implement write operations. The app-level `App\Repositories\TaskRepositoryInterface` exposes only the operations the task controller needs: list, find, create, update, delete, and complete.

## Dependency Inversion Principle

High-level code depends on abstractions. `TaskController` receives `TaskRepositoryInterface`, not a concrete `TaskRepository`. `Core\Application` binds `TaskRepositoryInterface` to `TaskRepository` in the DI container at runtime. The same pattern appears in the database layer: `Connection` receives `DatabaseDriver`, and the container chooses `SQLiteDriver`. This keeps controllers and framework services decoupled from infrastructure details.

## Result

The framework and MVP are intentionally small, but the separation is visible: routing, request handling, responses, views, validation, database access, models, repositories, and controllers each have clear responsibilities. The Task Manager proves the framework can support a real CRUD application while staying pure PHP and PSR-4 compliant.
