<?php /** @var string $token */ ?>
<?php ob_start(); ?>
<h2>Reset Password</h2>
<form method="post" action="/hasheem/reset/<?= htmlspecialchars($token) ?>">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    <label>New Password: <input type="password" name="password" required></label><br>
    <button type="submit">Reset Password</button>
</form>
<?php if (!empty($error)): ?>
    <div class="error"> <?= htmlspecialchars($error) ?> </div>
<?php endif; ?>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php';