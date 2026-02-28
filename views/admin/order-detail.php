<?php ob_start(); ?>

<style>
.order-detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.order-detail-header h2 {
    font-size: 1.75rem;
    background: linear-gradient(135deg, var(--accent), #667eea);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.back-btn {
    padding: 0.75rem 1.5rem;
    background: var(--bg-2);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--ink-1);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}

.back-btn:hover {
    background: var(--bg-1);
    border-color: var(--accent);
    transform: translateX(-4px);
}

.order-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.detail-card {
    background: var(--bg-1);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 2rem;
}

.detail-card h3 {
    color: var(--accent);
    margin-bottom: 1.5rem;
    font-size: 1.25rem;
}

.info-group {
    margin-bottom: 1.5rem;
}

.info-label {
    display: block;
    color: var(--ink-2);
    font-size: 0.85rem;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
    letter-spacing: 0.05em;
    font-weight: 600;
}

.info-value {
    font-size: 1.1rem;
    color: var(--ink-1);
}

.total-amount {
    font-size: 1.75rem;
    color: #22c55e;
    font-weight: 700;
}

.status-form select {
    width: 100%;
    padding: 0.9rem;
    background: var(--bg-2);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--ink-1);
    font-size: 0.95rem;
    margin-bottom: 1rem;
}

.status-form button {
    width: 100%;
    padding: 0.9rem;
    background: linear-gradient(135deg, var(--accent), #667eea);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.status-form button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.current-status {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color);
}

.status-badge-large {
    display: inline-block;
    margin-top: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: var(--bg-2);
    border-radius: 8px;
    font-weight: 700;
    font-size: 1.1rem;
}

.items-section {
    grid-column: 1 / -1;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.items-table thead {
    background: var(--bg-2);
    border-bottom: 2px solid var(--border-color);
}

.items-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--ink-1);
    font-size: 0.875rem;
    text-transform: uppercase;
}

.items-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    color: var(--ink-1);
}

.items-table tbody tr:hover {
    background: var(--bg-2);
}

.product-cell {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.product-image {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    object-fit: cover;
    background: var(--bg-2);
}

.divider {
    padding-top: 1rem;
    margin-top: 1rem;
    border-top: 1px solid var(--border-color);
}
</style>

<div class="order-detail-header">
    <h2>📋 Order #<?= htmlspecialchars($order['id'] ?? 'N/A') ?></h2>
    <a href="<?= htmlspecialchars($basePath ?? '') ?>/admin/orders" class="back-btn">← Back to Orders</a>
</div>

<div class="order-grid">
    <!-- Order Details -->
    <div class="detail-card">
        <h3>Order Information</h3>
        
        <div class="info-group">
            <label class="info-label">Customer</label>
            <p class="info-value"><?= htmlspecialchars($order['user_name'] ?? 'Guest') ?></p>
        </div>

        <div class="info-group">
            <label class="info-label">Order Date</label>
            <p class="info-value"><?= htmlspecialchars($order['created_at'] ? date('M d, Y - H:i', strtotime($order['created_at'])) : 'N/A') ?></p>
        </div>

        <div class="info-group">
            <label class="info-label">Items</label>
            <p class="info-value"><?= htmlspecialchars($order['item_count'] ?? 0) ?> items</p>
        </div>

        <div class="info-group">
            <label class="info-label">Payment Method</label>
            <p class="info-value"><?= htmlspecialchars(ucfirst($order['payment_method'] ?? 'N/A')) ?></p>
        </div>

        <div class="info-group divider">
            <label class="info-label">Total Amount</label>
            <p class="total-amount">TZS <?= number_format($order['total'] ?? 0, 2) ?></p>
        </div>
    </div>

    <!-- Status Update -->
    <div class="detail-card">
        <h3>Update Status</h3>
        
        <form method="post" action="<?= htmlspecialchars($basePath ?? '') ?>/admin/orders/update-status/<?= htmlspecialchars($order['id'] ?? '') ?>" class="status-form">
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

            <div class="info-group">
                <label class="info-label">New Status</label>
                <select name="status">
                    <option value="pending" <?= ($order['status'] ?? '') == 'pending' ? 'selected' : '' ?>>⏳ Pending</option>
                    <option value="shipped" <?= ($order['status'] ?? '') == 'shipped' ? 'selected' : '' ?>>🚚 Shipped</option>
                    <option value="completed" <?= ($order['status'] ?? '') == 'completed' ? 'selected' : '' ?>>✅ Completed</option>
                    <option value="cancelled" <?= ($order['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>❌ Cancelled</option>
                </select>
            </div>

            <button type="submit">Update Status</button>
        </form>

        <div class="current-status">
            <label class="info-label">Current Status</label>
            <?php 
                $status = $order['status'] ?? 'pending';
                $statusColor = '#f59e0b';
                $statusIcon = '⏳';
                if ($status === 'completed') {
                    $statusColor = '#22c55e';
                    $statusIcon = '✅';
                } elseif ($status === 'shipped') {
                    $statusColor = 'var(--accent)';
                    $statusIcon = '🚚';
                } elseif ($status === 'cancelled') {
                    $statusColor = '#ef4444';
                    $statusIcon = '❌';
                }
            ?>
            <span class="status-badge-large" style="color: <?= $statusColor ?>;">
                <?= $statusIcon ?> <?= ucfirst($status) ?>
            </span>
        </div>
    </div>

    <!-- Order Items -->
    <?php if (!empty($order['items'])): ?>
    <div class="detail-card items-section">
        <h3>Order Items</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td>
                        <div class="product-cell">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?= htmlspecialchars($basePath ?? '') ?>/<?= htmlspecialchars($item['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($item['product_name'] ?? 'Product') ?>" 
                                     class="product-image">
                            <?php else: ?>
                                <div class="product-image" style="display: flex; align-items: center; justify-content: center; color: var(--ink-2);">🎮</div>
                            <?php endif; ?>
                            <span><?= htmlspecialchars($item['product_name'] ?? 'Unknown Product') ?></span>
                        </div>
                    </td>
                    <td>TZS <?= number_format($item['unit_price'] ?? 0, 2) ?></td>
                    <td><?= htmlspecialchars($item['quantity'] ?? 1) ?></td>
                    <td><strong>TZS <?= number_format(($item['unit_price'] ?? 0) * ($item['quantity'] ?? 1), 2) ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php'; ?>
