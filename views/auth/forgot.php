<?php /** @var array $success */ ?>
<?php ob_start(); ?>
<h2>Forgot Password</h2>
<?php if (!empty($success)): ?>
    <div class="success"> <?= htmlspecialchars($success) ?> </div>
<?php endif; ?>
<form method="post" action="/hasheem/forgot">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    <label>Email: <input type="email" name="email" required></label><br>
    <button type="submit">Send Reset Link</button>
</form>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php';