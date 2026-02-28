<?php
ob_start();
?>

<div class="glass" style="padding: 18px;">
    <div class="section-title">
        <h3>User Management & Access Control</h3>
        <div style="display: flex; gap: 10px;">
            <input class="search" placeholder="Search name, email, role...">
            <button class="btn btn-outline">Export CSV</button>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" style="color: var(--muted);">No users found.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($users as $user): ?>
                <?php $role = $user['role'] ?? 'customer'; ?>
                <tr>
                    <td><?= htmlspecialchars($user['name'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($user['email'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                    <td><span class="badge badge-success"><?= ucfirst($role) ?></span></td>
                    <td>
                        <?php if (!($user['blocked'] ?? false)): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Blocked</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <?php if ($user['blocked'] ?? false): ?>
                                <form method="post" action="/hasheem/admin/users/unblock/<?= $user['id'] ?>" onsubmit="return confirm('Unblock this user?')">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                    <button type="submit" class="btn btn-outline">Unblock</button>
                                </form>
                            <?php else: ?>
                                <form method="post" action="/hasheem/admin/users/block/<?= $user['id'] ?>" onsubmit="return confirm('Block this user?')">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                    <button type="submit" class="btn btn-danger">Block</button>
                                </form>
                            <?php endif; ?>
                            <form method="post" action="/hasheem/admin/users/delete/<?= $user['id'] ?>" onsubmit="return confirm('Delete this user permanently?')">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                <button type="submit" class="btn btn-outline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php'; ?>
