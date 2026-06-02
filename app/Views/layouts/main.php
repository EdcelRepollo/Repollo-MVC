<?php
// This layout expects $content from the view; mao ni ang main page content.
/** @var string $content */
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Basic page settings; kani para sakto ang text encoding ug mobile view. -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Page title; if walay custom title, default siya to "Task Manager". -->
    <title><?= $this->e($title ?? 'Task Manager') ?></title>
</head>
<body>
    <!-- Header area; diri ang main navigation sa app. -->
    <header>
        <nav>
            <!-- Brand link; mo balik sa task list page. -->
            <a class="brand" href="/tasks">Task Manager</a>

        </nav>
    </header>

    <!-- Main area; diri i-display ang specific page view/content. -->
    <main>
        <?= $content ?>
    </main>
</body>
</html>
