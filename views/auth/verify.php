<?php ob_start(); ?>
<h2>Email Verified</h2>
<p>Your email has been verified. You may now <a href="/login">login</a>.</p>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php';