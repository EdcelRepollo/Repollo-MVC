-- Tasks table schema; creates table if wala pa siya.
CREATE TABLE IF NOT EXISTS tasks ( -- Start tasks table; only create if missing.
    -- Primary id; auto increment every new task.
    id INTEGER PRIMARY KEY AUTOINCREMENT, -- Unique task id; auto adds number.

    -- Task title; required field.
    title TEXT NOT NULL, -- Required task title.

    -- Task description; optional details about the work.
    description TEXT, -- Optional task description.

    -- Task status; default pending kung walay gi set.
    status TEXT NOT NULL DEFAULT 'pending', -- Required status with default pending.

    -- Task priority; default normal.
    priority TEXT NOT NULL DEFAULT 'normal', -- Required priority with default normal.

    -- Due date saved as text in Y-m-d format.
    due_date TEXT, -- Optional due date text.

    -- Created timestamp.
    created_at TEXT NOT NULL, -- Required created timestamp.

    -- Updated timestamp.
    updated_at TEXT NOT NULL -- Required last updated timestamp.
); -- End tasks table definition.
