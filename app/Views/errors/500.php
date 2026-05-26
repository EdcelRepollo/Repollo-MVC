<?php
// Error view; shows failure message from Application catch block.
/** @var string $message */
?>
<section class="panel">
    <!-- Error title. -->
    <h1>Request failed</h1>

    <!-- Escaped error message; debug controls what message is shown. -->
    <p class="muted"><?= $this->e($message) ?></p>

    <!-- Recovery link; user can go back to task list. -->
    <p><a href="/tasks">Return to task list</a></p>
</section>
