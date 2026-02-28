<?php
$title = 'My Account - Jusgam';
$user = $user ?? [];
$packages = $packages ?? [];
$pendingPackages = $pendingPackages ?? [];
$orders = $orders ?? [];
$packageCount = count($packages);

$additionalStyles = <<<'CSS'
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700;800&display=swap');

        body {
            font-family: 'Space Grotesk', 'Sora', sans-serif;
        }

        .account-wrap {
            background: radial-gradient(1200px 500px at 15% -10%, rgba(58, 242, 255, 0.12), transparent 60%),
                        radial-gradient(900px 400px at 90% 10%, rgba(34, 197, 94, 0.10), transparent 55%),
                        radial-gradient(800px 300px at 40% 80%, rgba(0, 217, 255, 0.12), transparent 60%);
            padding: 70px 0 80px;
        }

        .account-shell {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 28px;
        }

        .account-hero {
            border-radius: 18px;
            padding: 28px 32px;
            background: linear-gradient(135deg, rgba(15, 20, 25, 0.9), rgba(6, 10, 18, 0.6));
            border: 1px solid rgba(58, 242, 255, 0.15);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.25);
            margin-bottom: 26px;
            position: relative;
            overflow: hidden;
        }

        .account-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(58, 242, 255, 0.18), transparent 55%);
            opacity: 0.6;
            pointer-events: none;
        }

        .account-title {
            font-size: 2.6rem;
            font-weight: 800;
            color: var(--ink-0);
            margin-bottom: 6px;
            letter-spacing: 1px;
        }

        .account-subtitle {
            color: var(--ink-2);
            font-size: 1rem;
        }

        .account-card {
            background: linear-gradient(135deg, rgba(26, 31, 46, 0.95), rgba(10, 14, 25, 0.8));
            border: 1px solid rgba(58, 242, 255, 0.15);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.25);
        }

        .account-panels {
            display: grid;
            gap: 24px;
        }

        .panel {
            display: none;
        }

        .panel.active {
            display: block;
        }

        .profile-badge {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--accent), #00ffb2);
            display: grid;
            place-items: center;
            font-size: 2rem;
            color: var(--bg-0);
            margin-bottom: 16px;
            box-shadow: 0 12px 30px rgba(58, 242, 255, 0.35);
        }

        .profile-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--ink-0);
        }

        .profile-email {
            color: var(--ink-2);
            font-size: 0.9rem;
            margin-top: 4px;
        }

        .side-nav {
            margin-top: 18px;
            display: grid;
            gap: 10px;
        }

        .side-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            border-radius: 12px;
            color: var(--ink-1);
            text-decoration: none;
            background: rgba(26, 31, 46, 0.6);
            border: 1px solid transparent;
            transition: all 0.25s ease;
            font-weight: 600;
        }

        .side-link span {
            color: var(--accent);
            font-size: 0.85rem;
        }

        .side-link:hover {
            border-color: rgba(58, 242, 255, 0.4);
            transform: translateX(4px);
        }

        .side-link.active {
            background: linear-gradient(135deg, rgba(58, 242, 255, 0.2), rgba(0, 217, 255, 0.08));
            border-color: rgba(58, 242, 255, 0.45);
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }

        .stat-card {
            padding: 16px 18px;
            border-radius: 14px;
            background: rgba(6, 10, 18, 0.7);
            border: 1px solid rgba(58, 242, 255, 0.15);
        }

        .stat-card h4 {
            color: var(--ink-2);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .stat-card p {
            font-size: 1.4rem;
            color: var(--ink-0);
            font-weight: 700;
        }

        .section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .section-title h2 {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--ink-0);
        }

        .order-item {
            padding: 16px;
            border-radius: 12px;
            background: rgba(15, 20, 25, 0.7);
            border: 1px solid rgba(58, 242, 255, 0.2);
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .order-item:hover {
            border-color: rgba(58, 242, 255, 0.5);
            transform: translateY(-2px);
        }

        .order-item.active {
            border-color: rgba(34, 197, 94, 0.7);
            box-shadow: 0 10px 22px rgba(34, 197, 94, 0.2);
        }

        .order-info {
            flex: 1;
        }

        .order-id {
            font-size: 1rem;
            color: var(--ink-0);
            font-weight: 600;
            margin-bottom: 4px;
        }

        .order-status {
            font-size: 0.85rem;
            color: var(--ink-2);
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-pending {
            background: rgba(255, 107, 53, 0.2);
            color: #ff6b35;
            border: 1px solid #ff6b35;
        }

        .badge-approved {
            background: rgba(34, 255, 153, 0.2);
            color: #22ff99;
            border: 1px solid #22ff99;
        }

        .badge-paid {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid #22c55e;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .orders-layout {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 18px;
        }

        .order-detail {
            border-radius: 14px;
            background: rgba(6, 10, 18, 0.7);
            border: 1px solid rgba(58, 242, 255, 0.2);
            padding: 18px;
            min-height: 220px;
        }

        .detail-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--ink-0);
            margin-bottom: 10px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed rgba(58, 242, 255, 0.15);
            font-size: 0.9rem;
            color: var(--ink-1);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-message {
            margin-top: 12px;
            padding: 12px 14px;
            border-radius: 10px;
            background: rgba(34, 197, 94, 0.12);
            color: #22c55e;
            font-weight: 600;
        }

        .detail-message.pending {
            background: rgba(255, 107, 53, 0.12);
            color: #ff6b35;
        }

        .detail-message.rejected {
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
        }

        .whatsapp-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            padding: 10px 14px;
            border-radius: 10px;
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            text-decoration: none;
            font-weight: 600;
        }

        .package-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .package-item {
            padding: 16px;
            border-radius: 12px;
            background: rgba(15, 20, 25, 0.7);
            border: 1px solid rgba(0, 217, 255, 0.2);
        }

        .package-item h3 {
            font-size: 1.1rem;
            color: var(--accent);
            margin-bottom: 6px;
            letter-spacing: 0.6px;
        }

        .package-item p {
            color: var(--ink-2);
            font-size: 0.9rem;
        }

        .cta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 18px;
        }

        .cta-btn {
            padding: 12px 18px;
            border-radius: 10px;
            border: 1px solid rgba(58, 242, 255, 0.3);
            text-decoration: none;
            color: var(--ink-0);
            font-weight: 600;
            background: rgba(58, 242, 255, 0.1);
            transition: all 0.2s ease;
        }

        .cta-btn:hover {
            transform: translateY(-2px);
            background: rgba(58, 242, 255, 0.25);
        }

        .cta-btn.alt {
            border-color: rgba(34, 197, 94, 0.4);
            background: rgba(34, 197, 94, 0.15);
        }

        .cta-btn.danger {
            border-color: rgba(239, 68, 68, 0.4);
            background: rgba(239, 68, 68, 0.1);
        }

        @media (max-width: 960px) {
            .account-shell {
                grid-template-columns: 1fr;
            }

            .orders-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .account-hero {
                padding: 22px;
            }

            .account-title {
                font-size: 2rem;
            }
        }
CSS;

include __DIR__ . '/../layouts/user-header.php';
?>

<main class="account-wrap">
    <div class="container">
        <div class="account-hero">
            <h1 class="account-title">My Account</h1>
            <p class="account-subtitle">Manage your access, downloads, and order history.</p>
        </div>

        <div class="account-shell">
            <aside class="account-card">
                <div class="profile-badge">🎮</div>
                <div class="profile-name"><?= htmlspecialchars($user['name'] ?? 'Player') ?></div>
                <div class="profile-email"><?= htmlspecialchars($user['email'] ?? 'account@jusgam.local') ?></div>

                <nav class="side-nav">
                    <a class="side-link active" href="#overview" data-panel="panel-overview">Overview <span>NEW</span></a>
                    <a class="side-link" href="#packages" data-panel="panel-packages">Packages</a>
                    <a class="side-link" href="#orders" data-panel="panel-orders">Orders</a>
                    <a class="side-link" href="/hasheem/products">Browse Store</a>
                    <a class="side-link" href="/hasheem/logout">Logout</a>
                </nav>
            </aside>

            <div class="account-panels">
                <section class="account-card panel active" id="panel-overview">
                    <div class="section-title">
                        <h2>Account Snapshot</h2>
                        <span class="account-subtitle">Your gaming status summary</span>
                    </div>

                    <div class="stat-grid">
                        <div class="stat-card">
                            <h4>Packages</h4>
                            <p><?= (int)$packageCount ?></p>
                        </div>
                        <div class="stat-card">
                            <h4>Access</h4>
                            <p><?= $packageCount > 0 ? 'Active' : 'Pending' ?></p>
                        </div>
                        <div class="stat-card">
                            <h4>Member</h4>
                            <p>Verified</p>
                        </div>
                    </div>

                    <div class="cta-row">
                        <a class="cta-btn" href="#orders" data-panel="panel-orders">View Orders</a>
                        <a class="cta-btn alt" href="/hasheem/products">Buy More Games</a>
                        <a class="cta-btn danger" href="/hasheem/logout">Logout</a>
                    </div>
                </section>

                <section class="account-card panel" id="panel-packages">
                    <div class="section-title">
                        <h2>My Packages</h2>
                        <span class="account-subtitle">Your unlocked games and accessories</span>
                    </div>

                    <?php if (!empty($packages)): ?>
                        <div class="package-grid">
                            <?php foreach ($packages as $package): ?>
                                <div class="package-item">
                                    <h3><?= htmlspecialchars($package['name'] ?? 'Product') ?></h3>
                                    <p>Unlocked: <?= date('d/m/Y', strtotime($package['unlocked_at'] ?? $package['created_at'])) ?></p>
                                    <p style="font-size: 0.85rem; color: var(--ink-2); margin-top: 0.5rem;">Type: <?= $package['product_type'] === 'game' ? '🎮 Game' : '🛍️ Item' ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="account-subtitle">No unlocked packages yet. Purchase a game to unlock access.</p>
                    <?php endif; ?>

                    <hr style="border: none; border-top: 1px solid rgba(58, 242, 255, 0.15); margin: 18px 0;">

                    <div class="section-title">
                        <h2>Pending Access</h2>
                        <span class="account-subtitle">Access waiting for admin approval</span>
                    </div>

                    <?php if (!empty($pendingPackages)): ?>
                        <div class="package-grid">
                            <?php foreach ($pendingPackages as $package): ?>
                                <div class="package-item">
                                    <h3><?= htmlspecialchars($package['name'] ?? 'Product') ?></h3>
                                    <p>Status: ⏳ Pending verification</p>
                                    <p style="font-size: 0.85rem; color: var(--ink-2); margin-top: 0.5rem;">Type: <?= $package['product_type'] === 'game' ? '🎮 Game' : '🛍️ Item' ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="account-subtitle">No pending access requests.</p>
                    <?php endif; ?>
                </section>

                <section class="account-card panel" id="panel-orders">
                    <div class="section-title">
                        <h2>Recent Orders</h2>
                        <span class="account-subtitle">Track your purchases and payment status</span>
                    </div>

                    <?php if (!empty($orders)): ?>
                        <div class="orders-layout">
                            <div>
                                <?php foreach ($orders as $order): ?>
                                    <?php
                                        $verificationStatus = $order['verification_status'] ?? '';
                                        $orderStatus = $order['status'] ?? 'pending';
                                    ?>
                                    <div class="order-item" data-order-id="<?= $order['id'] ?>"
                                         data-amount="<?= number_format($order['total_amount'] ?? 0, 2) ?>"
                                         data-created="<?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>"
                                         data-method="<?= htmlspecialchars($order['payment_method'] ?? 'Payment') ?>"
                                         data-status="<?= htmlspecialchars($orderStatus) ?>"
                                         data-verification="<?= htmlspecialchars($verificationStatus) ?>">
                                        <div class="order-info">
                                            <div class="order-id">Order #<?= $order['id'] ?></div>
                                            <div class="order-status" style="margin-top: 6px; font-size: 0.85rem;">
                                                <?= htmlspecialchars($order['payment_method'] ?? 'Payment') ?> • 
                                                <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                            </div>
                                        </div>
                                        <button class="cta-btn" type="button">View</button>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="order-detail" id="orderDetail">
                                <div class="detail-title">Order Summary</div>
                                <div class="detail-row">
                                    <span>Chagua order upande wa kushoto</span>
                                    <span>→</span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="account-subtitle">No orders yet. Your next purchase will appear here.</p>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../layouts/user-footer.php'; ?>

<script>
    const orders = document.querySelectorAll('.order-item');
    const detail = document.getElementById('orderDetail');
    const adminWhatsapp = <?= json_encode($appSettings['whatsapp_number'] ?? '255621215237') ?>;
    const panelLinks = document.querySelectorAll('[data-panel]');
    const panels = document.querySelectorAll('.panel');
    const sideLinks = document.querySelectorAll('.side-link[data-panel]');

    function showPanel(panelId) {
        panels.forEach(panel => panel.classList.remove('active'));
        const activePanel = document.getElementById(panelId);
        if (activePanel) {
            activePanel.classList.add('active');
        }
        sideLinks.forEach(link => link.classList.remove('active'));
        sideLinks.forEach(link => {
            if (link.dataset.panel === panelId) {
                link.classList.add('active');
            }
        });
    }

    panelLinks.forEach(link => {
        link.addEventListener('click', (event) => {
            const panelId = link.dataset.panel;
            if (panelId) {
                event.preventDefault();
                showPanel(panelId);
            }
        });
    });

    function statusBadge(status, verification) {
        if (verification === 'approved') {
            return '<span class="badge badge-approved">✅ Approved</span>';
        }
        if (verification === 'rejected') {
            return '<span class="badge badge-pending">❌ Rejected</span>';
        }
        if (verification === 'pending' || verification === 'pending_admin_review') {
            return '<span class="badge badge-pending">⏳ Awaiting Review</span>';
        }
        if (status === 'paid') {
            return '<span class="badge badge-paid">💚 Paid</span>';
        }
        return '<span class="badge badge-pending">⏳ ' + status.toUpperCase() + '</span>';
    }

    function statusMessage(status, verification) {
        if (verification === 'approved' || status === 'paid') {
            return {
                text: 'Admin will review within 30 minutes. Or check WhatsApp.',
                className: 'detail-message'
            };
        }
        if (verification === 'rejected') {
            return {
                text: 'Payment was rejected. Please resubmit a valid confirmation.',
                className: 'detail-message rejected'
            };
        }
        return {
            text: 'Your payment is still under review.',
            className: 'detail-message pending'
        };
    }

    orders.forEach(order => {
        order.addEventListener('click', () => {
            orders.forEach(item => item.classList.remove('active'));
            order.classList.add('active');

            const orderId = order.dataset.orderId;
            const amount = order.dataset.amount;
            const created = order.dataset.created;
            const method = order.dataset.method;
            const status = order.dataset.status;
            const verification = order.dataset.verification;
            const badge = statusBadge(status, verification);
            const message = statusMessage(status, verification);

            detail.innerHTML = `
                <div class="detail-title">Order #${orderId}</div>
                <div class="detail-row"><span>Amount</span><span>TZS ${amount}</span></div>
                <div class="detail-row"><span>Method</span><span>${method}</span></div>
                <div class="detail-row"><span>Date</span><span>${created}</span></div>
                <div class="detail-row"><span>Status</span><span>${badge}</span></div>
                <div class="${message.className}">${message.text}</div>
                ${verification === 'approved' || status === 'paid' ? `
                    <a class="whatsapp-link" href="https://wa.me/${adminWhatsapp}" target="_blank">💬 WhatsApp Admin (${adminWhatsapp})</a>
                ` : ''}
            `;
        });
    });
</script>
