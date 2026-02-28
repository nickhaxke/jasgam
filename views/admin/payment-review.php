<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$title = 'Review Payment - Jusgam Admin';
$csrf_token = \Core\Security\CSRF::getToken();

// Payment method formatter
function formatPaymentMethod($method) {
    $methods = [
        'tigo_pesa' => 'TIGO PESA',
        'tigopesa' => 'TIGO PESA',
        'tigo' => 'TIGO PESA',
        'airtel_money' => 'AIRTEL MONEY',
        'airtelmoney' => 'AIRTEL MONEY',
        'airtel' => 'AIRTEL MONEY',
        'm_pesa' => 'M-PESA',
        'mpesa' => 'M-PESA',
        'halopesa' => 'HALOPESA',
        'halo_pesa' => 'HALOPESA',
        'bank_transfer' => 'BANK TRANSFER',
        'banktransfer' => 'BANK TRANSFER',
    ];
    
    $method = strtolower(trim($method));
    
    if (isset($methods[$method])) {
        return $methods[$method];
    }
    
    // Fallback: capitalize each word
    return strtoupper(str_replace('_', ' ', $method));
}

include __DIR__ . '/../layouts/admin.php';
?>

<style>
    :root {
        --neon-cyan: #3af2ff;
        --neon-green: #22ff99;
        --neon-pink: #ff4fd8;
        --neon-orange: #ff6b35;
        --bg-0: #060a12;
        --bg-1: #0f1419;
        --bg-2: #1a1f2e;
        --ink-0: #f2fbff;
        --ink-1: #b8c5d6;
        --ink-2: #7a8a9e;
    }

    .review-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--neon-cyan);
        text-transform: uppercase;
        font-family: 'Rajdhani', monospace;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.2rem;
        background: transparent;
        color: var(--neon-cyan);
        border: 2px solid var(--neon-cyan);
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .back-btn:hover {
        background: rgba(58, 242, 255, 0.1);
    }

    .review-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    .review-section {
        background: var(--bg-1);
        border: 2px solid var(--bg-2);
        border-radius: 8px;
        padding: 2rem;
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--neon-cyan);
        margin-bottom: 1.5rem;
        text-transform: uppercase;
        font-family: 'Rajdhani', monospace;
    }

    .info-group {
        margin-bottom: 1.5rem;
    }

    .info-label {
        font-size: 0.85rem;
        color: var(--ink-2);
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .info-value {
        font-size: 1rem;
        color: var(--ink-0);
        font-weight: 600;
    }

    .payment-method-badge {
        display: inline-block;
        padding: 0.4rem 1rem;
        background: rgba(58, 242, 255, 0.1);
        color: var(--neon-cyan);
        border: 1px solid var(--neon-cyan);
        border-radius: 4px;
        font-weight: 600;
    }

    .screenshot-container {
        margin: 1.5rem 0;
    }

    .screenshot-title {
        font-size: 0.9rem;
        color: var(--ink-1);
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .screenshot-image {
        width: 100%;
        border: 2px solid var(--neon-cyan);
        border-radius: 8px;
        max-height: 400px;
        object-fit: cover;
    }

    .screenshot-link {
        display: inline-block;
        margin-top: 0.5rem;
        color: var(--neon-cyan);
        text-decoration: none;
        font-size: 0.9rem;
    }

    .order-items {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--bg-2);
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.8rem;
        background: rgba(58, 242, 255, 0.05);
        border-radius: 4px;
        margin-bottom: 0.8rem;
    }

    .item-name {
        color: var(--ink-0);
        font-weight: 600;
    }

    .item-price {
        color: var(--neon-green);
        font-weight: 700;
    }

    .action-buttons {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .btn-action {
        padding: 1rem;
        border: none;
        border-radius: 6px;
        font-weight: 700;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        transition: all 0.3s;
        font-size: 0.95rem;
    }

    .btn-approve {
        background: linear-gradient(135deg, var(--neon-green) 0%, #11b981 100%);
        color: var(--bg-0);
        position: relative;
        overflow: hidden;
    }

    .btn-approve::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-approve:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(34, 255, 153, 0.3);
    }

    .btn-approve:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(34, 255, 153, 0.2);
    }

    .btn-approve.loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .btn-approve.loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid transparent;
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .btn-reject {
        background: linear-gradient(135deg, var(--neon-pink) 0%, #ec4899 100%);
        color: var(--bg-0);
    }

    .btn-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 79, 216, 0.3);
    }

    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: var(--bg-1);
        border: 2px solid var(--neon-pink);
        border-radius: 8px;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-title {
        color: var(--neon-pink);
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    .modal-form-group {
        margin-bottom: 1.5rem;
    }

    .modal-label {
        display: block;
        color: var(--ink-1);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .modal-textarea {
        width: 100%;
        padding: 0.8rem;
        background: var(--bg-2);
        border: 1px solid var(--bg-2);
        border-radius: 6px;
        color: var(--ink-0);
        font-family: 'Sora', sans-serif;
        resize: vertical;
        min-height: 100px;
    }

    .modal-textarea:focus {
        outline: none;
        border-color: var(--neon-pink);
        background: rgba(255, 79, 216, 0.05);
    }

    .modal-buttons {
        display: flex;
        gap: 1rem;
    }

    .modal-btn {
        flex: 1;
        padding: 0.8rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        text-transform: uppercase;
        transition: all 0.3s;
    }

    .modal-btn-confirm {
        background: var(--neon-pink);
        color: var(--bg-0);
    }

    .modal-btn-cancel {
        background: transparent;
        color: var(--neon-pink);
        border: 2px solid var(--neon-pink);
    }

    @media (max-width: 768px) {
        .review-grid {
            grid-template-columns: 1fr;
        }

        .review-header {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>

<div class="review-container">
    <div class="review-header">
        <h1 class="page-title">👁️ Review Payment</h1>
        <a href="/hasheem/admin/payments" class="back-btn">← Back</a>
    </div>

    <div class="review-grid">
        <!-- LEFT: Payment & Order Details -->
        <div class="review-section">
            <h2 class="section-title">📋 Payment Info</h2>

            <div class="info-group">
                <span class="info-label">Order #</span>
                <span class="info-value">#<?= $verification['order_id'] ?></span>
            </div>

            <div class="info-group">
                <span class="info-label">Payment Method</span>
                <span class="payment-method-badge"><?= formatPaymentMethod($verification['payment_method']) ?></span>
            </div>

            <div class="info-group">
                <span class="info-label">Amount</span>
                <span class="info-value" style="color: var(--neon-green); font-size: 1.3rem;">TZS <?= number_format($verification['payment_amount'], 2) ?></span>
            </div>

            <div class="info-group">
                <span class="info-label">Date Submitted</span>
                <span class="info-value"><?= date('d/m/Y H:i', strtotime($verification['created_at'])) ?></span>
            </div>

            <hr style="border: none; border-top: 1px solid var(--bg-2); margin: 1.5rem 0;">

            <h2 class="section-title">👤 Customer Info</h2>

            <div class="info-group">
                <span class="info-label">Name</span>
                <span class="info-value"><?= htmlspecialchars($verification['first_name'] ?? $verification['name']) ?> <?= htmlspecialchars($verification['last_name'] ?? '') ?></span>
            </div>

            <div class="info-group">
                <span class="info-label">Email</span>
                <span class="info-value"><?= htmlspecialchars($verification['email']) ?></span>
            </div>

            <div class="info-group">
                <span class="info-label">Phone</span>
                <span class="info-value"><?= !empty($verification['phone']) ? htmlspecialchars($verification['phone']) : 'N/A' ?></span>
            </div>

            <hr style="border: none; border-top: 1px solid var(--bg-2); margin: 1.5rem 0;">

            <h2 class="section-title">📦 Delivery Address</h2>

            <div class="info-group">
                <span class="info-label">Street</span>
                <span class="info-value"><?= htmlspecialchars($verification['street']) ?></span>
            </div>

            <div class="info-group">
                <span class="info-label">City</span>
                <span class="info-value"><?= htmlspecialchars($verification['city']) ?></span>
            </div>

            <div class="info-group">
                <span class="info-label">State / Province</span>
                <span class="info-value"><?= htmlspecialchars($verification['state']) ?></span>
            </div>

            <div class="info-group">
                <span class="info-label">ZIP / Postal Code</span>
                <span class="info-value"><?= htmlspecialchars($verification['zip']) ?></span>
            </div>

            <div class="info-group">
                <span class="info-label">Country</span>
                <span class="info-value"><?= htmlspecialchars($verification['country']) ?></span>
            </div>
        </div>

        <!-- RIGHT: Screenshot & Order Items -->
        <div class="review-section">
            <h2 class="section-title">📸 Payment Proof</h2>

            <?php if (!empty($verification['screenshot_path'])): ?>
                <div class="screenshot-container">
                    <div class="screenshot-title">Customer submitted the following proof of payment:</div>
                    <img src="<?= htmlspecialchars($verification['screenshot_path']) ?>" alt="Payment proof" class="screenshot-image">
                    <a href="<?= htmlspecialchars($verification['screenshot_path']) ?>" target="_blank" class="screenshot-link">🔗 Open in new tab</a>
                </div>
            <?php else: ?>
                <div style="padding: 2rem; text-align: center; color: var(--ink-2); background: rgba(58, 242, 255, 0.05); border-radius: 6px;">
                    <p>❌ No screenshot provided</p>
                </div>
            <?php endif; ?>

            <hr style="border: none; border-top: 1px solid var(--bg-2); margin: 1.5rem 0;">

            <h2 class="section-title">🛒 Order Items</h2>

            <div class="order-items">
                <?php if (!empty($orderItems)): ?>
                    <?php foreach ($orderItems as $item): ?>
                        <div class="order-item">
                            <div>
                                <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                                <div style="color: var(--ink-2); font-size: 0.9rem;">Qty: <?= $item['quantity'] ?></div>
                            </div>
                            <div class="item-price">TZS <?= number_format($item['unit_price'] * $item['quantity'], 2) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <hr style="border: none; border-top: 1px solid var(--bg-2); margin: 1.5rem 0;">

            <h2 class="section-title">✅ Actions</h2>

            <div class="action-buttons">
                <button class="btn-action btn-approve" onclick="approvePayment(<?= $verification['id'] ?>)">
                    ✅ APPROVE & UNLOCK ACCESS
                </button>
                <button class="btn-action btn-reject" onclick="openRejectModal()">
                    ❌ REJECT PAYMENT
                </button>
            </div>
        </div>
    </div>
</div>

<!-- REJECT MODAL -->
<div id="rejectModal" class="modal-overlay">
    <div class="modal-content">
        <h2 class="modal-title">❌ Reject Payment</h2>
        
        <div class="modal-form-group">
            <label class="modal-label">Reason for rejection:</label>
            <textarea id="rejectionReason" class="modal-textarea" placeholder="e.g., Payment amount doesn't match, screenshot not clear, etc."></textarea>
        </div>

        <div class="modal-buttons">
            <button class="modal-btn modal-btn-confirm" onclick="confirmReject(<?= $verification['id'] ?>)">CONFIRM REJECT</button>
            <button class="modal-btn modal-btn-cancel" onclick="closeRejectModal()">CANCEL</button>
        </div>
    </div>
</div>

<script>
    const csrfToken = <?= json_encode($csrf_token ?? '') ?>;

    function approvePayment(id) {
        if (!confirm('✅ Confirm approval? User akan kupata access mara moja.')) {
            return;
        }

        // Add loading animation
        const approveBtn = event.target;
        const originalText = approveBtn.innerHTML;
        approveBtn.classList.add('loading');
        approveBtn.innerHTML = 'Processing...';
        approveBtn.disabled = true;

        const formData = new FormData();
        formData.append('_token', csrfToken);

        fetch('/hasheem/admin/payment-approve/' + id, {
            method: 'POST',
            body: formData
        })
        .then(async response => {
            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error(text || 'Unexpected response from server');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                approveBtn.innerHTML = '✅ Approved!';
                approveBtn.style.background = 'linear-gradient(135deg, #22ff99 0%, #00cc66 100%)';
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 800);
            } else {
                approveBtn.classList.remove('loading');
                approveBtn.innerHTML = originalText;
                approveBtn.disabled = false;
                alert('❌ Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            approveBtn.classList.remove('loading');
            approveBtn.innerHTML = originalText;
            approveBtn.disabled = false;
            alert('❌ Network error. Please try again.');
        });
    }

    function openRejectModal() {
        document.getElementById('rejectModal').classList.add('active');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.remove('active');
        document.getElementById('rejectionReason').value = '';
    }

    function confirmReject(id) {
        const reason = document.getElementById('rejectionReason').value.trim();
        
        if (!reason) {
            alert('⚠️ Please enter a rejection reason.');
            return;
        }

        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('reason', reason);

        fetch('/hasheem/admin/payment-reject/' + id, {
            method: 'POST',
            body: formData
        })
        .then(async response => {
            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error(text || 'Unexpected response from server');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('❌ Payment rejected. User will be notified to resubmit.');
                window.location.href = data.redirect;
            } else {
                alert('❌ Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Network error. Please try again.');
        });
    }

    // Close modal when clicking outside
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
</script>
