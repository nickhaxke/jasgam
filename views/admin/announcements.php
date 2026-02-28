<?php ob_start(); ?>

<div style="padding: 20px;">
    <h2>📣 Announcements</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.35); padding: 12px; border-radius: 6px; margin-bottom: 15px; color: #22c55e;">
            ✅ <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.35); padding: 12px; border-radius: 6px; margin-bottom: 15px; color: #ef4444;">
            ❌ <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <div style="margin-bottom: 20px;">
        <a href="<?= htmlspecialchars($basePath ?? '/hasheem') ?>/admin/announcements/create" style="background: linear-gradient(135deg, var(--accent), #667eea); color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block;">
            + New Announcement
        </a>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
        <div style="background: var(--bg-2); padding: 15px; border-radius: 8px; border: 1px solid var(--line);">
            <div style="font-size: 0.85rem; color: var(--muted); margin-bottom: 5px;">Total</div>
            <div style="font-size: 1.8rem; font-weight: bold; color: var(--text);"><?= $stats['total'] ?? 0 ?></div>
        </div>
        <div style="background: var(--bg-2); padding: 15px; border-radius: 8px; border: 1px solid var(--line);">
            <div style="font-size: 0.85rem; color: var(--muted); margin-bottom: 5px;">Active</div>
            <div style="font-size: 1.8rem; font-weight: bold; color: var(--text);"><?= $stats['active'] ?? 0 ?></div>
        </div>
        <div style="background: var(--bg-2); padding: 15px; border-radius: 8px; border: 1px solid var(--line);">
            <div style="font-size: 0.85rem; color: var(--muted); margin-bottom: 5px;">Views</div>
            <div style="font-size: 1.8rem; font-weight: bold; color: var(--text);"><?= $stats['total_views'] ?? 0 ?></div>
        </div>
        <div style="background: var(--bg-2); padding: 15px; border-radius: 8px; border: 1px solid var(--line);">
            <div style="font-size: 0.85rem; color: var(--muted); margin-bottom: 5px;">Dismissals</div>
            <div style="font-size: 1.8rem; font-weight: bold; color: var(--text);"><?= $stats['total_dismissals'] ?? 0 ?></div>
        </div>
    </div>
    
    <?php if (!empty($announcements)): ?>
        <table style="width: 100%; border-collapse: collapse; background: var(--bg-2); border-radius: 8px; overflow: hidden; border: 1px solid var(--line);">
            <thead style="background: var(--bg-3); border-bottom: 1px solid var(--line);">
                <tr>
                    <th style="padding: 12px; text-align: left; color: var(--text); font-weight: 600;">Title</th>
                    <th style="padding: 12px; text-align: left; color: var(--text); font-weight: 600;">Status</th>
                    <th style="padding: 12px; text-align: left; color: var(--text); font-weight: 600;">Views</th>
                    <th style="padding: 12px; text-align: center; color: var(--text); font-weight: 600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($announcements as $announcement): ?>
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 12px; color: var(--text);"><?= htmlspecialchars($announcement['title'] ?? '') ?></td>
                        <td style="padding: 12px;">
                            <span style="background: <?= ($announcement['active'] ?? false) ? 'rgba(34, 197, 94, 0.15)' : 'rgba(156, 163, 175, 0.15)' ?>; color: <?= ($announcement['active'] ?? false) ? '#22c55e' : '#9ca3af' ?>; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">
                                <?= ($announcement['active'] ?? false) ? '● Active' : '○ Inactive' ?>
                            </span>
                        </td>
                        <td style="padding: 12px; color: var(--text);"><?= $announcement['view_count'] ?? 0 ?></td>
                        <td style="padding: 12px; text-align: center;">
                            <a href="<?= htmlspecialchars($basePath ?? '/hasheem') ?>/admin/announcements/edit/<?= intval($announcement['id'] ?? 0) ?>" style="color: var(--accent); text-decoration: none; margin: 0 5px;">✏️ Edit</a>
                            <form method="POST" action="<?= htmlspecialchars($basePath ?? '/hasheem') ?>/admin/announcements/delete/<?= intval($announcement['id'] ?? 0) ?>" style="display: inline;" onsubmit="return confirm('Delete?');">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 0 5px;">🗑️ Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; color: var(--muted);">
            <div style="font-size: 3rem; margin-bottom: 15px;">📣</div>
            <h3 style="color: var(--text); margin-bottom: 10px;">No Announcements</h3>
            <p>Create your first announcement to notify users</p>
            <a href="<?= htmlspecialchars($basePath ?? '/hasheem') ?>/admin/announcements/create" style="background: linear-gradient(135deg, var(--accent), #667eea); color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; margin-top: 15px;">
                + Create Announcement
            </a>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php'; ?>
