<?php ob_start(); ?>

<?php
// Get status filter from URL
$status_filter = $_GET['status'] ?? 'pending';

// Initialize variables from controller
$verifications = $verifications ?? [];
$statusCounts = $statusCounts ?? [];
$status = $status ?? 'pending';

// Create $counts array with default values
$counts = [
    'pending' => $statusCounts['pending'] ?? 0,
    'approved' => $statusCounts['approved'] ?? 0,
    'rejected' => $statusCounts['rejected'] ?? 0
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Payment Verifications - Admin</title>
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
            --danger: #ef4444;
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
            margin-bottom: 2rem;
        }

        .header h1 {
            color: var(--accent);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .filter-btn {
            padding: 0.7rem 1.5rem;
            background: var(--bg-2);
            border: 2px solid transparent;
            color: var(--ink-1);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--accent);
            color: var(--bg-0);
            border-color: var(--accent);
        }

        .verifications-grid {
            display: grid;
            gap: 1.5rem;
        }

        .verification-card {
            background: var(--bg-1);
            border: 1px solid var(--bg-2);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
        }

        .verification-card:hover {
            border-color: var(--accent);
            box-shadow: 0 0 20px rgba(58, 242, 255, 0.2);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--bg-2);
        }

        .card-user {
            flex: 1;
        }

        .card-user h3 {
            color: var(--accent);
            margin-bottom: 0.3rem;
        }

        .card-user p {
            color: var(--ink-2);
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .status-pending {
            background: rgba(255, 165, 0, 0.2);
            color: #ffa500;
        }

        .status-approved {
            background: rgba(34, 197, 94, 0.2);
            color: var(--success);
        }

        .status-rejected {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }

        .card-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .detail-item {
            background: var(--bg-2);
            padding: 1rem;
            border-radius: 8px;
        }

        .detail-label {
            color: var(--ink-2);
            font-size: 0.85rem;
            text-transform: uppercase;
            margin-bottom: 0.3rem;
        }

        .detail-value {
            color: var(--accent);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .screenshot-container {
            margin: 1rem 0;
            text-align: center;
        }

        .screenshot-img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
            border: 2px solid var(--accent);
            cursor: pointer;
        }

        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn {
            flex: 1;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-approve {
            background: var(--success);
            color: #000;
        }

        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }

        .btn-reject {
            background: var(--danger);
            color: #fff;
        }

        .btn-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .empty {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--ink-2);
        }

        .empty h3 {
            color: var(--accent);
            margin-bottom: 0.5rem;
        }

        a {
            color: var(--accent);
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin-bottom: 2rem;
            background: var(--bg-2);
            border-radius: 6px;
            transition: all 0.3s;
        }

        .back-link:hover {
            background: var(--accent);
            color: var(--bg-0);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/hasheem/admin" class="back-link">← Back to Admin</a>

        <div class="header">
            <h1>💳 Game Payment Verifications</h1>
        </div>

        <div class="filters">
            <button class="filter-btn <?= $status_filter === 'pending' ? 'active' : '' ?>" onclick="window.location='?status=pending'">
                ⏳ Pending (<?= $counts['pending'] ?>)
            </button>
            <button class="filter-btn <?= $status_filter === 'approved' ? 'active' : '' ?>" onclick="window.location='?status=approved'">
                ✅ Approved (<?= $counts['approved'] ?>)
            </button>
            <button class="filter-btn <?= $status_filter === 'rejected' ? 'active' : '' ?>" onclick="window.location='?status=rejected'">
                ❌ Rejected (<?= $counts['rejected'] ?>)
            </button>
            <button class="filter-btn <?= $status_filter === 'all' ? 'active' : '' ?>" onclick="window.location='?status=all'">
                📋 All
            </button>
        </div>

        <?php if (empty($verifications)): ?>
            <div class="empty">
                <h3>✨ No verifications found</h3>
                <p>All payment verifications have been processed!</p>
            </div>
        <?php else: ?>
            <div class="verifications-grid">
                <?php foreach ($verifications as $v): ?>
                    <div class="verification-card">
                        <div class="card-header">
                            <div class="card-user">
                                <h3><?= htmlspecialchars($v['user_name']) ?></h3>
                                <p>📧 <?= htmlspecialchars($v['email']) ?></p>
                                <p>🎮 <?= htmlspecialchars($v['product_name']) ?></p>
                            </div>
                            <span class="status-badge status-<?= $v['status'] ?>">
                                <?= strtoupper($v['status']) ?>
                            </span>
                        </div>

                        <div class="card-details">
                            <div class="detail-item">
                                <div class="detail-label">Amount</div>
                                <div class="detail-value">TZS <?= number_format($v['amount'], 0) ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Access Level</div>
                                <div class="detail-value"><?= ucfirst($v['access_level']) ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Payment Method</div>
                                <div class="detail-value"><?= ucfirst($v['payment_method']) ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Submitted</div>
                                <div class="detail-value" style="font-size: 0.9rem;"><?= date('M d, H:i', strtotime($v['created_at'])) ?></div>
                            </div>
                        </div>

                        <?php if (!empty($v['screenshot_path'])): ?>
                            <div class="screenshot-container">
                                <a href="<?= htmlspecialchars($v['screenshot_path']) ?>" target="_blank">
                                    <img src="<?= htmlspecialchars($v['screenshot_path']) ?>" alt="Payment screenshot" class="screenshot-img">
                                </a>
                                <p style="color: var(--ink-2); font-size: 0.85rem; margin-top: 0.5rem;">Click to view full screenshot</p>
                            </div>
                        <?php endif; ?>

                        <?php if ($v['status'] === 'pending'): ?>
                            <div class="actions">
                                <form method="POST" action="/hasheem/admin/verifications/approve/<?= $v['id'] ?>" style="flex: 1;">
                                    <input type="hidden" name="_token" value="<?php echo \Core\Security\CSRF::getToken(); ?>">
                                    <button type="submit" class="btn btn-approve">✅ Approve & Give Access</button>
                                </form>
                                <form method="POST" action="/hasheem/admin/verifications/reject/<?= $v['id'] ?>" style="flex: 1;" onsubmit="return confirm('Reject this payment?');">
                                    <input type="hidden" name="_token" value="<?php echo \Core\Security\CSRF::getToken(); ?>">
                                    <button type="submit" class="btn btn-reject">❌ Reject</button>
                                </form>
                            </div>
                        <?php elseif ($v['status'] === 'approved'): ?>
                            <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid var(--success); border-radius: 8px; padding: 1rem; margin-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                                <p style="color: var(--success); margin: 0;"><strong>✅ Payment approved!</strong> User has access to this game.</p>
                                <form method="POST" action="/hasheem/admin/verifications/grant-access/<?= $v['id'] ?>" style="margin: 0;">
                                    <input type="hidden" name="_token" value="<?php echo \Core\Security\CSRF::getToken(); ?>">
                                    <button type="submit" class="btn btn-approve" style="padding: 0.5rem 1rem; font-size: 0.9rem;">🔓 Re-grant Access</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--danger); border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                                <p style="color: var(--danger); margin: 0;"><strong>❌ Payment rejected</strong></p>
                                <?php if (!empty($v['approval_notes'])): ?>
                                    <p style="color: var(--ink-2); font-size: 0.9rem; margin: 0.5rem 0 0 0;"><?= htmlspecialchars($v['approval_notes']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
