<?php
// Admin Payment Verification Detail - View full screenshot
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Verification Details</title>
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
        }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--bg-0);
            color: var(--ink-0);
            padding: 2rem;
        }

        .container {
            max-width: 1000px;
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
            font-size: 1.8rem;
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

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .card {
            background: var(--bg-1);
            border: 1px solid var(--bg-2);
            border-radius: 8px;
            padding: 1.5rem;
        }

        .card h2 {
            color: var(--accent);
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .info-group {
            margin-bottom: 1.5rem;
        }

        .info-label {
            font-size: 0.85rem;
            color: var(--ink-2);
            text-transform: uppercase;
            margin-bottom: 0.3rem;
        }

        .info-value {
            font-size: 1rem;
            color: var(--ink-0);
            font-weight: 500;
        }

        .screenshot-container {
            grid-column: 1 / -1;
            background: var(--bg-1);
            border: 1px solid var(--bg-2);
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
        }

        .screenshot-container h2 {
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .screenshot-image {
            max-width: 100%;
            max-height: 500px;
            border-radius: 6px;
            border: 2px solid var(--bg-2);
            margin-bottom: 1rem;
        }

        .no-screenshot {
            padding: 2rem;
            color: var(--ink-2);
            font-style: italic;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .status-badge.pending {
            background: rgba(255, 165, 0, 0.2);
            color: #ffa500;
            border: 1px solid #ffa500;
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

        .actions {
            grid-column: 1 / -1;
            display: flex;
            gap: 1rem;
        }

        .btn {
            flex: 1;
            padding: 1rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-approve {
            background: var(--success);
            color: white;
        }

        .btn-approve:hover {
            background: #16a34a;
            transform: translateY(-2px);
        }

        .btn-reject {
            background: var(--danger);
            color: white;
        }

        .btn-reject:hover {
            background: #cc0000;
            transform: translateY(-2px);
        }

        .note-section {
            margin-top: 1rem;
        }

        .note-section textarea {
            width: 100%;
            padding: 0.75rem;
            background: var(--bg-2);
            border: 1px solid var(--bg-2);
            border-radius: 4px;
            color: var(--ink-0);
            font-family: 'Sora', sans-serif;
            resize: vertical;
            min-height: 80px;
        }

        .note-section textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 10px rgba(58, 242, 255, 0.2);
        }

        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Verification Details</h1>
            <a href="/hasheem/admin/verifications" class="back-link">← Back</a>
        </div>

        <div class="status-badge <?= strtolower($verification['status']) ?>">
            <?= strtoupper($verification['status']) ?>
        </div>

        <div class="content-grid">
            <div class="card">
                <h2>👤 User Information</h2>
                <div class="info-group">
                    <div class="info-label">Name</div>
                    <div class="info-value"><?= htmlspecialchars($verification['user_name']) ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= htmlspecialchars($verification['user_email']) ?></div>
                </div>
            </div>

            <div class="card">
                <h2>� Payment Amount</h2>
                <div class="info-group">
                    <div class="info-label">Verification ID</div>
                    <div class="info-value">#<?= $verification['id'] ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Amount Declared</div>
                    <div class="info-value">TZS <?= number_format($verification['amount'], 0) ?></div>
                </div>
            </div>

            <div class="card">
                <h2>💳 Payment Information</h2>
                <div class="info-group">
                    <div class="info-label">Product</div>
                    <div class="info-value"><?= htmlspecialchars($verification['product_name']) ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Product Type</div>
                    <div class="info-value"><?= ucfirst($verification['product_type']) ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Payment Method</div>
                    <div class="info-value"><?= htmlspecialchars($verification['payment_method'] ?? 'Not specified') ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Access Level</div>
                    <div class="info-value"><?= ucfirst($verification['access_level'] ?? 'full') ?></div>
                </div>
            </div>

            <div class="card">
                <h2>📅 Submission Details</h2>
                <div class="info-group">
                    <div class="info-label">Submitted</div>
                    <div class="info-value"><?= date('M d, Y @ H:i', strtotime($verification['created_at'])) ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Status</div>
                    <div class="info-value" style="color: <?= $verification['status'] === 'approved' ? '#22c55e' : ($verification['status'] === 'pending' ? '#fbbf24' : '#ff4444') ?>; font-weight: 600;">
                        <?= ucfirst($verification['status']) ?>
                    </div>
                </div>
            </div>

            <div class="screenshot-container">
                <h2>📸 Payment Screenshot</h2>
                <?php if ($verification['screenshot_path']): ?>
                    <img src="<?= htmlspecialchars($verification['screenshot_path']) ?>" alt="Payment Screenshot" class="screenshot-image">
                <?php else: ?>
                    <div class="no-screenshot">
                        No screenshot available
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($verification['status'] === 'pending'): ?>
            <div class="actions">
                <form method="post" action="/hasheem/admin/verifications/approve/<?= $verification['id'] ?>" style="flex: 1;">
                    <input type="hidden" name="_token" value="<?php echo \Core\Security\CSRF::getToken(); ?>">
                    <div class="note-section">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--ink-2); font-size: 0.85rem;">Admin Notes (Optional)</label>
                        <textarea name="reason" placeholder="Add notes about this approval..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-approve" style="margin-top: 1rem;" onclick="return confirm('Approve this payment? The game will be unlocked for the user.')">
                        ✅ Approve Payment
                    </button>
                </form>
            </div>

            <div class="actions">
                <form method="post" action="/hasheem/admin/verifications/reject/<?= $verification['id'] ?>" style="flex: 1;">
                    <input type="hidden" name="_token" value="<?php echo \Core\Security\CSRF::getToken(); ?>">
                    <div class="note-section">
                        <label style="display: block; margin-bottom: 0.5rem; color: var(--ink-2); font-size: 0.85rem;">Rejection Reason (Required)</label>
                        <textarea name="reason" placeholder="Why are you rejecting this payment?" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-reject" style="margin-top: 1rem;" onclick="return confirm('Reject this payment? A notification will be sent to the user.')">
                        ❌ Reject Payment
                    </button>
                </form>
            </div>
            <?php endif; ?>

            <?php if ($verification['status'] === 'approved'): ?>
            <div class="card" style="grid-column: 1 / -1; background: rgba(34, 197, 94, 0.1); border-color: var(--success);">
                <h2 style="color: var(--success);">✅ Payment Approved</h2>
                <div class="info-group">
                    <div class="info-label">Admin Notes</div>
                    <div class="info-value"><?= htmlspecialchars($verification['admin_notes'] ?? 'No notes') ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
ob_end_flush();
?>
