<?php
// Admin Payment Verifications - View all pending screenshots and approve/reject
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Verifications - Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --accent: #3af2ff;
            --bg-0: #060a12;
            --bg-1: #0f1419;
            --bg-2: #1a1f2e;
            --ink-0: #f2fbff;
            --ink-1: #b8c5d6;
            --ink-2: #7a8a9e;
            --success: #22c55e;
            --danger: #ff4444;
            --warning: #ffa500;
        }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--bg-0);
            color: var(--ink-0);
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--bg-2);
        }

        .header h1 {
            font-size: 2rem;
            color: var(--accent);
        }

        .back-link {
            color: var(--accent);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border: 1px solid var(--accent);
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            background: var(--accent);
            color: var(--bg-0);
        }

        .status-filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.7rem 1.5rem;
            background: var(--bg-2);
            border: 2px solid var(--bg-2);
            color: var(--ink-1);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--accent);
            border-color: var(--accent);
            color: var(--bg-0);
        }

        .verifications-list {
            display: grid;
            gap: 1.5rem;
        }

        .verification-card {
            background: var(--bg-1);
            border: 1px solid var(--bg-2);
            border-radius: 8px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .verification-card:hover {
            border-color: var(--accent);
            box-shadow: 0 0 20px rgba(58, 242, 255, 0.2);
        }

        .verification-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .verification-user {
            flex: 1;
        }

        .verification-user h3 {
            color: var(--accent);
            margin-bottom: 0.5rem;
        }

        .verification-user p {
            color: var(--ink-2);
            font-size: 0.9rem;
            margin: 0.3rem 0;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .status-badge.pending {
            background: rgba(255, 165, 0, 0.2);
            color: var(--warning);
            border: 1px solid var(--warning);
        }

        .status-badge.approved {
            background: rgba(34, 197, 94, 0.2);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .status-badge.rejected {
            background: rgba(255, 68, 68, 0.2);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .verification-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
            padding: 1rem;
            background: var(--bg-2);
            border-radius: 6px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.85rem;
            color: var(--ink-2);
            text-transform: uppercase;
            margin-bottom: 0.3rem;
        }

        .detail-value {
            font-weight: 600;
            color: var(--ink-0);
        }

        .verification-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--bg-2);
        }

        .view-btn {
            flex: 1;
            padding: 0.8rem;
            background: var(--accent);
            color: var(--bg-0);
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .view-btn:hover {
            background: #2dd4ed;
            transform: translateY(-2px);
        }

        .approve-btn {
            flex: 1;
            padding: 0.8rem;
            background: var(--success);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .approve-btn:hover {
            background: #16a34a;
            transform: translateY(-2px);
        }

        .reject-btn {
            flex: 1;
            padding: 0.8rem;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .reject-btn:hover {
            background: #cc0000;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--ink-2);
        }

        .empty-state h3 {
            color: var(--ink-1);
            margin-bottom: 0.5rem;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-1);
            border: 1px solid var(--bg-2);
            border-radius: 6px;
            padding: 1.5rem;
            text-align: center;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 0.3rem;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--ink-2);
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💰 Payment Verifications</h1>
            <a href="/hasheem/admin" class="back-link">← Back to Dashboard</a>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= $statusCounts['pending'] ?? 0 ?></div>
                <div class="stat-label">⏳ Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $statusCounts['approved'] ?? 0 ?></div>
                <div class="stat-label">✅ Approved</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $statusCounts['rejected'] ?? 0 ?></div>
                <div class="stat-label">❌ Rejected</div>
            </div>
        </div>

        <?php if (empty($verifications)): ?>
        <div class="empty-state">
            <h3>No payment verifications yet</h3>
            <p>Users will submit payment screenshots here when they buy games</p>
        </div>
        <?php else: ?>

        <div class="verifications-list">
            <?php foreach ($verifications as $v): ?>
            <div class="verification-card">
                <div class="verification-header">
                    <div class="verification-user">
                        <h3><?= htmlspecialchars($v['user_name']) ?></h3>
                        <p>📧 <?= htmlspecialchars($v['user_email']) ?></p>
                        <p>🛒 Order #<?= $v['order_id'] ?></p>
                        <p>🎮 <?= htmlspecialchars($v['product_name']) ?></p>
                    </div>
                    <span class="status-badge <?= strtolower($v['status']) ?>">
                        <?= strtoupper($v['status']) ?>
                    </span>
                </div>

                <div class="verification-details">
                    <div class="detail-item">
                        <span class="detail-label">Amount</span>
                        <span class="detail-value">TZS <?= number_format($v['payment_amount'] ?? 0, 0) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Payment Method</span>
                        <span class="detail-value"><?= htmlspecialchars($v['payment_method'] ?? 'N/A') ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Submitted</span>
                        <span class="detail-value"><?= date('M d, Y H:i', strtotime($v['created_at'])) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Type</span>
                        <span class="detail-value"><?= ucfirst($v['product_type']) ?></span>
                    </div>
                </div>

                <div class="verification-actions">
                    <a href="/hasheem/admin/verifications/<?= $v['id'] ?>" class="view-btn">
                        👁️ View Screenshot
                    </a>
                    <?php if ($v['status'] === 'pending'): ?>
                    <form method="post" action="/hasheem/admin/verifications/approve/<?= $v['id'] ?>" style="flex: 1;">
                        <button type="submit" class="approve-btn" onclick="return confirm('Approve this payment?')">
                            ✅ Approve
                        </button>
                    </form>
                    <form method="post" action="/hasheem/admin/verifications/reject/<?= $v['id'] ?>" style="flex: 1;">
                        <button type="submit" class="reject-btn" onclick="return confirm('Reject this payment?')">
                            ❌ Reject
                        </button>
                    </form>
                    <?php endif; ?>
                </div>

                <?php if ($v['status'] === 'approved'): ?>
                <div style="margin-top: 1rem; padding: 1rem; background: rgba(34, 197, 94, 0.1); border-left: 4px solid var(--success); border-radius: 4px;">
                    <p style="color: var(--success); font-weight: 600;">✅ Approved by Admin</p>
                    <p style="color: var(--ink-2); font-size: 0.9rem; margin-top: 0.5rem;">
                        <?= htmlspecialchars($v['admin_notes'] ?? '') ?>
                    </p>
                </div>
                <?php elseif ($v['status'] === 'rejected'): ?>
                <div style="margin-top: 1rem; padding: 1rem; background: rgba(255, 68, 68, 0.1); border-left: 4px solid var(--danger); border-radius: 4px;">
                    <p style="color: var(--danger); font-weight: 600;">❌ Rejected</p>
                    <p style="color: var(--ink-2); font-size: 0.9rem; margin-top: 0.5rem;">
                        Reason: <?= htmlspecialchars($v['rejected_reason'] ?? 'No reason provided') ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>
    </div>
</body>
</html>
<?php
ob_end_flush();
?>
