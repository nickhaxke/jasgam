<?php ob_start(); ?>

<style>
.orders-header {
    margin-bottom: 2rem;
}

.orders-header h2 {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, var(--accent), #667eea);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.orders-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: linear-gradient(135deg, var(--bg-2) 0%, var(--bg-1) 100%);
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    border-color: var(--accent);
}

.stat-card h3 {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--ink-2);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--ink-1);
    margin-bottom: 0.25rem;
}

.stat-icon {
    font-size: 2.5rem;
    opacity: 0.2;
    float: right;
}

.orders-toolbar {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    align-items: center;
}

.search-box {
    flex: 1;
    min-width: 250px;
}

.search-box input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-1);
    color: var(--ink-1);
    font-size: 0.95rem;
    transition: all 0.2s;
}

.search-box input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.filter-group {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 0.75rem 1.25rem;
    border: 1px solid var(--border-color);
    background: var(--bg-1);
    color: var(--ink-1);
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s;
}

.filter-btn:hover {
    background: var(--bg-2);
    border-color: var(--accent);
}

.filter-btn.active {
    background: var(--accent);
    color: white;
    border-color: var(--accent);
}

.modern-table {
    background: var(--bg-1);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid var(--border-color);
}

.modern-table table {
    width: 100%;
    border-collapse: collapse;
}

.modern-table thead {
    background: linear-gradient(135deg, var(--bg-2) 0%, var(--bg-1) 100%);
    border-bottom: 2px solid var(--border-color);
}

.modern-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--ink-1);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.modern-table tbody tr {
    border-bottom: 1px solid var(--border-color);
    transition: all 0.2s;
}

.modern-table tbody tr:hover {
    background: var(--bg-2);
    transform: scale(1.01);
}

.modern-table td {
    padding: 1rem;
    color: var(--ink-1);
}

.order-id {
    font-weight: 700;
    color: var(--accent);
    font-size: 1.05rem;
}

.customer-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.customer-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent), #667eea);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
}

.status-badge {
    display: inline-block;
    padding: 0.4rem 0.9rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: capitalize;
}

.status-pending {
    background: rgba(251, 191, 36, 0.15);
    color: #f59e0b;
}

.status-completed {
    background: rgba(34, 197, 94, 0.15);
    color: #22c55e;
}

.status-cancelled {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}

.status-shipped {
    background: rgba(99, 102, 241, 0.15);
    color: var(--accent);
}

.amount-display {
    font-weight: 700;
    font-size: 1.05rem;
    color: #22c55e;
}

.action-btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
}

.btn-view {
    background: linear-gradient(135deg, var(--accent), #667eea);
    color: white;
}

.btn-view:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--ink-2);
}

.empty-state-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}
</style>

<div class="orders-header">
    <h2>📋 Order Management</h2>
    <p style="color: var(--ink-2); margin-top: 0.5rem;">Manage and track all customer orders</p>
</div>

<!-- Stats Cards -->
<div class="orders-stats">
    <div class="stat-card">
        <span class="stat-icon">📦</span>
        <h3>Total Orders</h3>
        <div class="stat-value"><?= number_format($stats['total'] ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <span class="stat-icon">⏳</span>
        <h3>Pending Orders</h3>
        <div class="stat-value"><?= number_format($stats['pending'] ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <span class="stat-icon">✅</span>
        <h3>Completed</h3>
        <div class="stat-value"><?= number_format($stats['completed'] ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <span class="stat-icon">💰</span>
        <h3>Total Revenue</h3>
        <div class="stat-value">TZS <?= number_format($stats['revenue'] ?? 0) ?></div>
    </div>
</div>

<!-- Toolbar -->
<div class="orders-toolbar">
    <div class="search-box">
        <input type="text" id="orderSearch" placeholder="🔍 Search by Order ID, Customer name..." />
    </div>
    <div class="filter-group">
        <button class="filter-btn active" data-status="all">All Orders</button>
        <button class="filter-btn" data-status="pending">Pending</button>
        <button class="filter-btn" data-status="completed">Completed</button>
        <button class="filter-btn" data-status="shipped">Shipped</button>
        <button class="filter-btn" data-status="cancelled">Cancelled</button>
    </div>
</div>

<!-- Orders Table -->
<div class="modern-table">
    <table id="ordersTable">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!isset($orders) || empty($orders)): ?>
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-state-icon">📦</div>
                            <h3>No orders yet</h3>
                            <p>Orders will appear here once customers make purchases</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <tr data-status="<?= htmlspecialchars($order['status'] ?? 'pending') ?>">
                        <td>
                            <span class="order-id">#<?= htmlspecialchars($order['id'] ?? 'N/A') ?></span>
                        </td>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar">
                                    <?= strtoupper(substr($order['user_name'] ?? 'G', 0, 1)) ?>
                                </div>
                                <span><?= htmlspecialchars($order['user_name'] ?? 'Guest') ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($order['item_count'] ?? '0') ?> items</td>
                        <td>
                            <span class="amount-display">TZS <?= number_format($order['total'] ?? 0, 2) ?></span>
                        </td>
                        <td>
                            <?php 
                                $status = $order['status'] ?? 'pending';
                                $statusClass = 'status-' . $status;
                            ?>
                            <span class="status-badge <?= $statusClass ?>">
                                <?php
                                    if ($status === 'pending') echo '⏳ ';
                                    elseif ($status === 'completed') echo '✅ ';
                                    elseif ($status === 'shipped') echo '🚚 ';
                                    elseif ($status === 'cancelled') echo '❌ ';
                                ?>
                                <?= ucfirst($status) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($order['created_at'] ? date('M d, Y - H:i', strtotime($order['created_at'])) : 'N/A') ?></td>
                        <td>
                            <a href="<?= htmlspecialchars($basePath ?? '') ?>/admin/orders/view/<?= htmlspecialchars($order['id'] ?? '') ?>" 
                               class="action-btn btn-view">
                                👁️ View Details
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// Search functionality
document.getElementById('orderSearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#ordersTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Filter functionality
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Update active state
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const status = this.dataset.status;
        const rows = document.querySelectorAll('#ordersTable tbody tr');
        
        rows.forEach(row => {
            if (status === 'all' || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php';