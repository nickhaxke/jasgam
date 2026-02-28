<?php ob_start(); ?>
<h2>Categories</h2>
<ul>
    <?php foreach ($categories as $category): ?>
        <li><?= htmlspecialchars($category['name']) ?></li>
    <?php endforeach; ?>
</ul>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php';