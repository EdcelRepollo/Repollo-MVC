<?php
// Task details view; expects one $task record.
/** @var array<string, mixed> $task */
?>
<article class="panel">
    <!-- Back link; mo balik sa task list. -->
    <p><a href="/tasks">&larr; Back to tasks</a></p>

    <!-- Task title. -->
    <h1><?= $this->e($task['title']) ?></h1>

    <!-- Task metadata: status, priority, and due date kung naa. -->
    <p class="muted">
        <span class="badge"><?= $this->e(str_replace('_', ' ', $task['status'])) ?></span>
        Priority: <?= $this->e($task['priority']) ?>
        <?php if (! empty($task['due_date'])): ?>
            · Due: <?= $this->e($task['due_date']) ?>
        <?php endif; ?>
    </p>

    <!-- Description section; fallback if walay description. -->
    <?php if (! empty($task['description'])): ?>
        <p><?= nl2br($this->e($task['description'])) ?></p>
    <?php else: ?>
        <p class="muted">No description provided.</p>
    <?php endif; ?>

    <!-- Action buttons for this task. -->
    <div class="actions">
        <!-- Edit action; opens edit form. -->
        <form class="inline" method="get" action="/tasks/<?= $this->e($task['id']) ?>/edit">
            <button type="submit">Edit</button>
        </form>
        <!-- Complete action; only show if task is not yet completed. -->
        <?php if ($task['status'] !== 'completed'): ?>
            <form class="inline" method="post" action="/tasks/<?= $this->e($task['id']) ?>/complete">
                <button type="submit">Complete</button>
            </form>
        <?php endif; ?>
        <!-- Delete action; opens confirmation page. -->
        <form class="inline" method="get" action="/tasks/<?= $this->e($task['id']) ?>/delete">
            <button type="submit">Delete</button>
        </form>
    </div>
</article>
