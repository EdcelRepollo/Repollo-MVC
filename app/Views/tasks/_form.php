<?php
/**
 * Shared task form; gigamit sa create ug edit pages.
 *
 * @var array<string, mixed> $old
 * @var array<string, list<string>> $errors
 * @var string|null $formError
 * @var string $action
 * @var string $submitLabel
 */
?>
<!-- Task form; action changes depending if create or edit. -->
<form class="panel" method="post" action="<?= $this->e($action) ?>">
    <!-- General form error; example when important fields are empty. -->
    <?php if (! empty($formError)): ?>
        <p><?= $this->e($formError) ?></p>
    <?php endif; ?>

    <!-- Title input; old value is kept after validation error. -->
    <label for="title">Title</label>
    <input id="title" name="title" value="<?= $this->e($old['title'] ?? '') ?>" maxlength="100">
    <?php foreach ($errors['title'] ?? [] as $error): ?>
        <div class="error"><?= $this->e($error) ?></div>
    <?php endforeach; ?>

    <!-- Description textarea; supports longer task details. -->
    <label for="description">Description</label>
    <textarea id="description" name="description" maxlength="500"><?= $this->e($old['description'] ?? '') ?></textarea>
    <?php foreach ($errors['description'] ?? [] as $error): ?>
        <div class="error"><?= $this->e($error) ?></div>
    <?php endforeach; ?>

    <!-- Status dropdown; selected value follows old/current task data. -->
    <label for="status">Status</label>
    <select id="status" name="status">
        <?php foreach (['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $value => $label): ?>
            <option value="<?= $value ?>" <?= ($old['status'] ?? 'pending') === $value ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
    </select>
    <?php foreach ($errors['status'] ?? [] as $error): ?>
        <div class="error"><?= $this->e($error) ?></div>
    <?php endforeach; ?>

    <!-- Priority dropdown; lets user choose low, normal, or high. -->
    <label for="priority">Priority</label>
    <select id="priority" name="priority">
        <?php foreach (['low' => 'Low', 'normal' => 'Normal', 'high' => 'High'] as $value => $label): ?>
            <option value="<?= $value ?>" <?= ($old['priority'] ?? 'normal') === $value ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
    </select>
    <?php foreach ($errors['priority'] ?? [] as $error): ?>
        <div class="error"><?= $this->e($error) ?></div>
    <?php endforeach; ?>

    <!-- Due date input; browser shows date picker. -->
    <label for="due_date">Due date</label>
    <input id="due_date" type="date" name="due_date" value="<?= $this->e($old['due_date'] ?? '') ?>">
    <?php foreach ($errors['due_date'] ?? [] as $error): ?>
        <div class="error"><?= $this->e($error) ?></div>
    <?php endforeach; ?>

    <!-- Submit button label changes for create/edit. -->
    <p class="actions">
        <button type="submit"><?= $this->e($submitLabel) ?></button>
    </p>
</form>

<!-- Cancel button; mo balik sa task list without saving. -->
<form method="get" action="/tasks">
    <button type="submit">Cancel</button>
</form>
