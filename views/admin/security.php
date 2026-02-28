<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$title = 'Security & Access Control';
ob_start();
?>

<div class="grid" style="gap: 20px;">
    <div class="section-title">
        <h3>🔒 Security & Access Control</h3>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.35); border-radius: 12px; padding: 14px;">
            <span class="badge badge-success"><?= htmlspecialchars($_SESSION['success']) ?></span>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <!-- Security Overview -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="kpi">
            <div class="kpi-label">🔒 Blocked Users</div>
            <div class="kpi-value"><?= $blockedUsersCount ?></div>
            <div class="kpi-change" style="color: var(--danger);">Currently restricted</div>
        </div>
        
        <div class="kpi">
            <div class="kpi-label">📊 Total Audit Logs</div>
            <div class="kpi-value"><?= number_format($totalLogs) ?></div>
            <div class="kpi-change" style="color: var(--accent);">All recorded actions</div>
        </div>
        
        <div class="kpi">
            <div class="kpi-label">⚠️ Failed Logins</div>
            <div class="kpi-value"><?= $failedLoginCount ?></div>
            <div class="kpi-change" style="color: var(--warning);">Last 24 hours</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="glass" style="padding: 20px;">
        <form method="GET" action="/hasheem/admin/security" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
            <div>
                <label style="color: var(--muted); font-size: 0.85rem; display: block; margin-bottom: 6px;">Action Filter</label>
                <select name="action" class="search" style="padding: 10px; width: 100%;">
                    <option value="all" <?= $selectedAction === 'all' ? 'selected' : '' ?>>All Actions</option>
                    <?php foreach ($actionTypes as $action): ?>
                        <option value="<?= htmlspecialchars($action) ?>" <?= $selectedAction === $action ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $action))) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label style="color: var(--muted); font-size: 0.85rem; display: block; margin-bottom: 6px;">Records Per Page</label>
                <select name="limit" class="search" style="padding: 10px; width: 100%;">
                    <option value="50" <?= $limit === 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= $limit === 100 ? 'selected' : '' ?>>100</option>
                    <option value="200" <?= $limit === 200 ? 'selected' : '' ?>>200</option>
                </select>
            </div>
            
            <div style="display: flex; align-items: flex-end; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Apply</button>
                <a href="/hasheem/admin/security/export?action=<?= urlencode($selectedAction) ?>" class="btn btn-outline" title="Export CSV">📥</a>
            </div>
        </form>
    </div>

    <!-- Recent Security Events -->
    <div class="glass" style="padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h4 style="color: var(--text);">🔐 Recent Security Events</h4>
            <form method="POST" action="/hasheem/admin/security/clear-logs" onsubmit="return confirm('Clear logs older than 90 days?');" style="display: inline;">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="days" value="90">
                <button type="submit" class="btn btn-sm btn-outline">🗑️ Clear Old Logs</button>
            </form>
        </div>
        
        <?php if (!empty($securityEvents)): ?>
            <div style="max-height: 400px; overflow-y: auto;">
                <?php foreach (array_slice($securityEvents, 0, 20) as $event): ?>
                    <div style="background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(65, 209, 255, 0.15); border-radius: 10px; padding: 12px; margin-bottom: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                                    <span class="badge badge-<?= 
                                        strpos($event['action'], 'approve') !== false ? 'success' : 
                                        (strpos($event['action'], 'reject') !== false || strpos($event['action'], 'block') !== false || strpos($event['action'], 'delete') !== false ? 'danger' : 'info') 
                                    ?>">
                                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $event['action']))) ?>
                                    </span>
                                    <span style="color: var(--muted); font-size: 0.85rem;">
                                        by <strong><?= htmlspecialchars($event['user_name'] ?? 'System') ?></strong>
                                    </span>
                                </div>
                                <div style="color: var(--text); font-size: 0.9rem; margin-bottom: 4px;">
                                    <?= htmlspecialchars($event['details'] ?? 'No details') ?>
                                </div>
                                <div style="color: var(--muted); font-size: 0.8rem;">
                                    📍 <?= htmlspecialchars($event['ip_address'] ?? 'N/A') ?> 
                                    • 🕒 <?= date('M d, Y H:i:s', strtotime($event['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; color: var(--muted); padding: 40px;">No security events recorded</div>
        <?php endif; ?>
    </div>

    <!-- Full Audit Log -->
    <div class="glass" style="padding: 20px;">
        <h4 style="color: var(--text); margin-bottom: 16px;">📜 Complete Audit Trail</h4>
        
        <?php if (!empty($logs)): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Target</th>
                            <th>IP Address</th>
                            <th>Details</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td style="color: var(--muted); font-size: 0.85rem;">#<?= $log['id'] ?></td>
                                <td>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($log['user_name'] ?? 'System') ?></div>
                                    <?php if ($log['user_email']): ?>
                                        <div style="color: var(--muted); font-size: 0.8rem;"><?= htmlspecialchars($log['user_email']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= 
                                        strpos($log['action'], 'approve') !== false ? 'success' : 
                                        (strpos($log['action'], 'reject') !== false || strpos($log['action'], 'block') !== false || strpos($log['action'], 'delete') !== false ? 'danger' : 'info') 
                                    ?>">
                                        <?= htmlspecialchars(str_replace('_', ' ', $log['action'])) ?>
                                    </span>
                                </td>
                                <td style="font-size: 0.85rem;">
                                    <?php if ($log['target_type']): ?>
                                        <?= htmlspecialchars($log['target_type']) ?> #<?= $log['target_id'] ?>
                                    <?php else: ?>
                                        <span style="color: var(--muted);">—</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--muted);"><?= htmlspecialchars($log['ip_address'] ?? 'N/A') ?></td>
                                <td style="max-width: 300px; font-size: 0.85rem;"><?= htmlspecialchars($log['details'] ?? '—') ?></td>
                                <td style="font-size: 0.85rem; color: var(--muted); white-space: nowrap;">
                                    <?= date('M d, Y', strtotime($log['created_at'])) ?><br>
                                    <?= date('H:i:s', strtotime($log['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div style="display: flex; justify-content: center; gap: 8px; margin-top: 20px;">
                    <?php for ($i = 1; $i <= min($totalPages, 10); $i++): ?>
                        <a href="?page=<?= $i ?>&action=<?= urlencode($selectedAction) ?>&limit=<?= $limit ?>" 
                           class="btn btn-sm <?= $i === $currentPage ? 'btn-primary' : 'btn-outline' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    <?php if ($totalPages > 10): ?>
                        <span style="color: var(--muted); padding: 8px;">...</span>
                        <a href="?page=<?= $totalPages ?>&action=<?= urlencode($selectedAction) ?>&limit=<?= $limit ?>" class="btn btn-sm btn-outline">
                            <?= $totalPages ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div style="text-align: center; color: var(--muted); padding: 40px;">No audit logs found</div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php'; ?>
