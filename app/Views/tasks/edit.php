<?php
// Edit page; setup action and button label for shared form.
/** @var array<string, mixed> $task */
/** @var array<string, mixed> $old */
/** @var array<string, list<string>> $errors */
$action = '/tasks/' . $task['id'] . '/update';
$submitLabel = 'Save Changes';
?>
<h1>Edit Task</h1>
<!-- Include shared form with current task values. -->
<?php include __DIR__ . '/_form.php'; ?>
