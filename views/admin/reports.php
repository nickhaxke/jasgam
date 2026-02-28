<?php ob_start(); ?>

<style>
.reports-header {
    margin-bottom: 2rem;
}

.reports-header h2 {
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, var(--accent), #667eea);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.filter-card {
    background: var(--bg-1);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--ink-2);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-input {
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-2);
    color: var(--ink-1);
    font-size: 0.95rem;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.btn-filter {
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, var(--accent), #667eea);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.btn-export {
    padding: 0.75rem 1.5rem;
    background: var(--bg-2);
    color: var(--ink-1);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-export:hover {
    background: #22c55e;
    color: white;
    border-color: #22c55e;
}

.stats-grid {
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
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    border-color: var(--accent);
}

.stat-icon {
    font-size: 2.5rem;
    opacity: 0.15;
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
}

.stat-label {
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
    position: relative;
    z-index: 1;
}

.stat-subtext {
    font-size: 0.875rem;
    color: var(--ink-2);
    margin-top: 0.5rem;
}

.chart-card {
    background: var(--bg-1);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.chart-card h3 {
    font-size: 1.25rem;
    margin-bottom: 1.5rem;
    color: var(--ink-1);
}

.chart-bar {
    display: grid;
    grid-template-columns: 120px 1fr auto;
    gap: 1rem;
    align-items: center;
    margin-bottom: 1rem;
}

.chart-label {
    color: var(--ink-1);
    font-weight: 500;
    font-size: 0.9rem;
}

.chart-progress {
    background: rgba(99, 102, 241, 0.1);
    border-radius: 8px;
    height: 32px;
    position: relative;
    overflow: hidden;
}

.chart-fill {
    background: linear-gradient(90deg, var(--accent), #667eea);
    height: 100%;
    border-radius: 8px;
    transition: width 0.5s ease;
}

.chart-value {
    color: var(--ink-1);
    font-weight: 700;
    min-width: 100px;
    text-align: right;
    font-size: 1.05rem;
}

.table-card {
    background: var(--bg-1);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid var(--border-color);
    margin-bottom: 2rem;
}

.table-card h3 {
    padding: 1.5rem;
    font-size: 1.25rem;
    background: var(--bg-2);
    border-bottom: 2px solid var(--border-color);
    margin: 0;
    color: var(--ink-1);
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    background: var(--bg-2);
    border-bottom: 2px solid var(--border-color);
}

.data-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--ink-1);
    font-size: 0.875rem;
    text-transform: uppercase;
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    color: var(--ink-1);
}

.data-table tbody tr:hover {
    background: var(--bg-2);
}

.rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent), #667eea);
    color: white;
    font-weight: 700;
    font-size: 0.875rem;
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

.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.status-item {
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: var(--bg-2);
}

.status-item-name {
    font-size: 0.875rem;
    color: var(--ink-2);
    margin-bottom: 0.5rem;
    text-transform: capitalize;
}

.status-item-count {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--ink-1);
}

.status-item-revenue {
    font-size: 0.9rem;
    color: var(--ink-2);
    margin-top: 0.25rem;
}
</style>

<div class="reports-header">
    <h2>📊 Sales Reports</h2>
    <p style="color: var(--ink-2); margin-top: 0.5rem;">Comprehensive sales analytics and insights</p>
</div>

<!-- Date Filters -->
<div class="filter-card">
    <form method="GET" action="<?= htmlspecialchars($basePath ?? '') ?>/admin/reports" class="filter-form">
        <div class="form-group">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($startDate ?? date('Y-m-01')) ?>" class="form-input">
        </div>
        <div class="form-group">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" value="<?= htmlspecialchars($endDate ?? date('Y-m-d')) ?>" class="form-input">
        </div>
        <div class="form-group">
            <button type="submit" class="btn-filter">Apply Filters</button>
        </div>
        <div class="form-group">
            <a href="<?= htmlspecialchars($basePath ?? '') ?>/admin/reports/export?start_date=<?= urlencode($startDate ?? '') ?>&end_date=<?= urlencode($endDate ?? '') ?>" class="btn-export">
                📥 Export CSV
            </a>
        </div>
    </form>
</div>

<!-- Revenue Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-icon">💰</span>
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value">TZS <?= number_format($revenueStats['total_revenue'] ?? 0) ?></div>
        <div class="stat-subtext"><?= number_format($revenueStats['total_orders'] ?? 0) ?> completed orders</div>
    </div>
    
    <div class="stat-card">
        <span class="stat-icon">📦</span>
        <div class="stat-label">Total Orders</div>
        <div class="stat-value"><?= number_format($revenueStats['total_orders'] ?? 0) ?></div>
        <div class="stat-subtext"><?= number_format($revenueStats['pending_orders'] ?? 0) ?> pending</div>
    </div>
    
    <div class="stat-card">
        <span class="stat-icon">📊</span>
        <div class="stat-label">Avg Order Value</div>
        <div class="stat-value">TZS <?= number_format($revenueStats['avg_order_value'] ?? 0) ?></div>
        <div class="stat-subtext">per transaction</div>
    </div>
</div>

<!-- Daily Revenue Chart -->
<div class="chart-card">
    <h3>📈 Daily Revenue Trend (Last 30 Days)</h3>
    
    <?php if (!empty($dailyRevenue)): ?>
        <div>
            <?php 
            $maxRevenue = max(array_column($dailyRevenue, 'revenue'));
            foreach ($dailyRevenue as $day): 
                $percentage = $maxRevenue > 0 ? ($day['revenue'] / $maxRevenue) * 100 : 0;
            ?>
                <div class="chart-bar">
                    <div class="chart-label"><?= date('M d', strtotime($day['date'])) ?></div>
                    <div class="chart-progress">
                        <div class="chart-fill" style="width: <?= $percentage ?>%;"></div>
                    </div>
                    <div class="chart-value">TZS <?= number_format($day['revenue']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">📊</div>
            <p>No sales data for this period</p>
        </div>
    <?php endif; ?>
</div>

<!-- Top Selling Items -->
<div class="table-card">
    <h3>🏆 Top Selling Products</h3>
    
    <?php if (!empty($topItems)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Product Name</th>
                    <th>Sales Count</th>
                    <th>Total Quantity</th>
                    <th>Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topItems as $index => $item): ?>
                    <tr>
                        <td>
                            <span class="rank-badge">#<?= $index + 1 ?></span>
                        </td>
                        <td><?= htmlspecialchars($item['name'] ?? 'Unknown') ?></td>
                        <td><?= number_format($item['sales_count'] ?? 0) ?> orders</td>
                        <td><?= number_format($item['total_quantity'] ?? 0) ?> units</td>
                        <td><strong style="color: #22c55e;">TZS <?= number_format($item['total_revenue'] ?? 0, 2) ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">🏆</div>
            <p>No sales data available</p>
        </div>
    <?php endif; ?>
</div>

<!-- Order Status Breakdown -->
<?php if (!empty($statusBreakdown)): ?>
<div class="chart-card">
    <h3>📋 Order Status Breakdown</h3>
    <div class="status-grid">
        <?php foreach ($statusBreakdown as $status): ?>
            <div class="status-item">
                <div class="status-item-name">
                    <?php
                        $icon = '⏳';
                        if ($status['status'] === 'completed') $icon = '✅';
                        elseif ($status['status'] === 'shipped') $icon = '🚚';
                        elseif ($status['status'] === 'cancelled') $icon = '❌';
                    ?>
                    <?= $icon ?> <?= ucfirst($status['status'] ?? 'pending') ?>
                </div>
                <div class="status-item-count"><?= number_format($status['count'] ?? 0) ?></div>
                <div class="status-item-revenue">TZS <?= number_format($status['revenue'] ?? 0) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Payment Method Breakdown -->
<?php if (!empty($paymentBreakdown)): ?>
<div class="chart-card">
    <h3>💳 Payment Methods</h3>
    <?php 
    $totalPaymentRevenue = array_sum(array_column($paymentBreakdown, 'revenue'));
    foreach ($paymentBreakdown as $payment): 
        $percentage = $totalPaymentRevenue > 0 ? ($payment['revenue'] / $totalPaymentRevenue) * 100 : 0;
    ?>
        <div class="chart-bar">
            <div class="chart-label"><?= ucfirst($payment['payment_method'] ?? 'Unknown') ?></div>
            <div class="chart-progress">
                <div class="chart-fill" style="width: <?= $percentage ?>%;"></div>
            </div>
            <div class="chart-value">TZS <?= number_format($payment['revenue'] ?? 0) ?></div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php'; ?>
