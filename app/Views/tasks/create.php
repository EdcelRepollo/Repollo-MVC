<?php
// Create page; setup action and button label for shared form.
/** @var array<string, mixed> $old */
/** @var array<string, list<string>> $errors */
$action = '/tasks';
$submitLabel = 'Create Task';
?>
<h1>Create Task</h1>
<!-- Include shared form; pareho nga form used by create and edit. -->
<?php include __DIR__ . '/_form.php'; ?>
