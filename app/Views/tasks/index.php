<?php
// Task list view; expects $tasks from TaskController@index.
/** @var list<array<string, mixed>> $tasks */
?>
<h1>Task Manager</h1>
<p>Track small project work using PHP MVC framework.</p>

<!-- Create button; mo adto sa create task form. -->
<form method="get" action="/tasks/create">
    <button type="submit">Create Task</button>
</form>

<hr>

<!-- If walay tasks, show empty message; otherwise show table. -->
<?php if ($tasks === []): ?>
    <h2>No tasks yet</h2>
    <p>Create your first task!</p>
<?php else: ?>
    <!-- Tasks table; each row is one task record. -->
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Loop all tasks and print their details safely. -->
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td>
                        <!-- Title link; mo open sa task details page. -->
                        <a href="/tasks/<?= $this->e($task['id']) ?>">
                            <?= $this->e($task['title']) ?>
                        </a>
                    </td>
                    <td>
                        <!-- Show description if naa; otherwise fallback text. -->
                        <?php if (! empty($task['description'])): ?>
                            <?= nl2br($this->e($task['description'])) ?>
                        <?php else: ?>
                            No description
                        <?php endif; ?>
                    </td>
                    <td><?= $this->e(str_replace('_', ' ', $task['status'])) ?></td>
                    <td><?= $this->e($task['priority']) ?></td>
                    <td><?= $this->e($task['due_date'] ?: 'No due date') ?></td>
                    <td>
                        <!-- Complete button only appears if task is not completed. -->
                        <?php if ($task['status'] !== 'completed'): ?>
                            <form method="post" action="/tasks/<?= $this->e($task['id']) ?>/complete">
                                <button type="submit">Complete</button>
                            </form>
                        <?php endif; ?>

                        <!-- Edit button; mo adto sa edit form. -->
                        <form method="get" action="/tasks/<?= $this->e($task['id']) ?>/edit">
                            <button type="submit">Edit</button>
                        </form>

                        <!-- Delete button; mo adto sa confirmation page. -->
                        <form method="get" action="/tasks/<?= $this->e($task['id']) ?>/delete">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
