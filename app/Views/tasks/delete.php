<?php
/**
 * Delete confirmation view; asks user before deleting task.
 *
 * @var array<string, mixed> $task
 * @var string $alertMessage
 * @var string|null $error
 */
?>
<h1>Delete Task</h1>

<!-- Alert message; explains deletion action. -->
<p><?= $this->e($alertMessage) ?></p>

<!-- Error message appears when delete was submitted without confirmation. -->
<?php if (! empty($error)): ?>
    <p><?= $this->e($error) ?></p>
<?php endif; ?>

<!-- Task title being deleted. -->
<p><strong><?= $this->e($task['title']) ?></strong></p>

<!-- Confirm delete form; sends hidden yes value. -->
<form method="post" action="/tasks/<?= $this->e($task['id']) ?>/delete">
    <input type="hidden" name="confirm_delete" value="yes">
    <button type="submit">Yes, delete</button>
</form>

<!-- Cancel form; mo balik sa task list. -->
<form method="get" action="/tasks">
    <button type="submit">Cancel</button>
</form>
