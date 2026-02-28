<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$title = 'Payment Verifications';
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

ob_start();
?>

<style>
    .verification-card {
        background: linear-gradient(135deg, var(--bg-2) 0%, var(--bg-3) 100%);
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 20px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .verification-card:hover {
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(255, 0, 102, 0.15);
    }

    .verification-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--accent);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .verification-card:hover::before {
        opacity: 1;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
        gap: 12px;
    }

    .customer-info {
        flex: 1;
    }

    .customer-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }

    .order-number {
        font-size: 0.85rem;
        color: var(--muted);
        font-family: 'Courier New', monospace;
    }

    .card-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin: 16px 0;
        padding: 16px;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 8px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .detail-label {
        font-size: 0.75rem;
        color: var(--muted);
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .detail-value {
        font-size: 0.95rem;
        color: var(--text);
        font-weight: 500;
    }

    .detail-value.highlight {
        color: var(--accent);
        font-weight: 700;
        font-size: 1.1rem;
    }

    .products-list {
        margin: 12px 0;
        padding: 12px;
        background: rgba(255, 0, 102, 0.05);
        border-left: 3px solid var(--accent);
        border-radius: 4px;
    }

    .products-title {
        font-size: 0.75rem;
        color: var(--muted);
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .products-value {
        font-size: 0.9rem;
        color: var(--text);
        line-height: 1.5;
    }

    .card-actions {
        display: flex;
        gap: 8px;
        margin-top: 16px;
        flex-wrap: wrap;
    }

    .btn-review {
        flex: 1;
        min-width: 120px;
        background: var(--accent);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
        text-decoration: none;
        display: inline-block;
    }

    .btn-review:hover {
        background: #ff1a75;
        transform: scale(1.02);
    }

    .btn-screenshot {
        background: rgba(58, 242, 255, 0.1);
        color: var(--accent);
        border: 1px solid var(--accent);
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-screenshot:hover {
        background: rgba(58, 242, 255, 0.2);
    }

    .btn-delete {
        background: rgba(255, 0, 0, 0.1);
        color: #ff4444;
        border: 1px solid #ff4444;
        padding: 10px 16px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-delete:hover {
        background: rgba(255, 0, 0, 0.2);
        border-color: #ff0000;
        color: #ff0000;
    }

    .payment-method-tag {
        display: inline-block;
        padding: 6px 12px;
        background: rgba(34, 255, 153, 0.1);
        color: #22ff99;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .filter-tabs {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .filter-btn {
        padding: 10px 20px;
        background: transparent;
        color: var(--muted);
        border: 1px solid var(--line);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
    }

    .filter-btn.active {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
    }

    .filter-btn:hover:not(.active) {
        border-color: var(--accent);
        color: var(--accent);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--muted);
    }

    .empty-state svg {
        width: 80px;
        height: 80px;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: linear-gradient(135deg, var(--bg-2) 0%, var(--bg-3) 100%);
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 16px;
        text-align: center;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--accent);
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--muted);
        text-transform: uppercase;
    }

    /* MODAL STYLES */
    .review-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(8px);
        z-index: 10000;
        overflow-y: auto;
        padding: 20px;
        animation: fadeIn 0.3s ease;
    }

    .review-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-container {
        background: linear-gradient(135deg, var(--bg-2) 0%, var(--bg-3) 100%);
        border: 2px solid var(--accent);
        border-radius: 16px;
        max-width: 1200px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        animation: slideUp 0.3s ease;
        box-shadow: 0 20px 60px rgba(255, 0, 102, 0.3);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-header {
        position: sticky;
        top: 0;
        background: var(--bg-2);
        border-bottom: 2px solid var(--accent);
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 10;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--accent);
        margin: 0;
    }

    .modal-close {
        background: rgba(255, 0, 102, 0.1);
        border: 1px solid var(--accent);
        color: var(--accent);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.5rem;
        font-weight: bold;
        transition: all 0.3s;
    }

    .modal-close:hover {
        background: var(--accent);
        color: white;
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 24px;
    }

    .modal-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .modal-section {
        background: rgba(0, 0, 0, 0.2);
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 20px;
    }

    .section-header {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--accent);
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--line);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-key {
        color: var(--muted);
        font-size: 0.85rem;
        font-weight: 600;
    }

    .info-val {
        color: var(--text);
        font-weight: 500;
        text-align: right;
    }

    .screenshot-preview {
        width: 100%;
        max-height: 400px;
        object-fit: contain;
        border: 2px solid var(--accent);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .screenshot-preview:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 24px rgba(255, 0, 102, 0.3);
    }

    .modal-actions {
        position: sticky;
        bottom: 0;
        background: var(--bg-2);
        border-top: 2px solid var(--accent);
        padding: 20px 24px;
        display: flex;
        gap: 12px;
        z-index: 10;
    }

    .modal-action-btn {
        flex: 1;
        padding: 14px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
        text-transform: uppercase;
        position: relative;
        overflow: hidden;
    }

    .modal-action-btn::before {
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

    .modal-action-btn:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-modal-approve {
        background: linear-gradient(135deg, #22ff99 0%, #11b981 100%);
        color: #000;
    }

    .btn-modal-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(34, 255, 153, 0.4);
    }

    .btn-modal-reject {
        background: linear-gradient(135deg, #ff0066 0%, #cc0052 100%);
        color: white;
    }

    .btn-modal-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(255, 0, 102, 0.4);
    }

    .modal-action-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .products-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }

    .product-badge {
        background: rgba(58, 242, 255, 0.1);
        border: 1px solid var(--accent);
        color: var(--accent);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* IMAGE LIGHTBOX */
    .image-lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        z-index: 20000;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fadeIn 0.2s ease;
    }

    .image-lightbox.active {
        display: flex;
    }

    .lightbox-image {
        max-width: 90%;
        max-height: 90vh;
        object-fit: contain;
        border: 3px solid var(--accent);
        border-radius: 8px;
        box-shadow: 0 0 50px rgba(255, 0, 102, 0.5);
        animation: zoomIn 0.3s ease;
    }

    @keyframes zoomIn {
        from {
            transform: scale(0.8);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 20px;
        background: var(--accent);
        color: white;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: bold;
        cursor: pointer;
        border: none;
        transition: all 0.3s;
        z-index: 20001;
    }

    .lightbox-close:hover {
        transform: rotate(90deg) scale(1.1);
        background: #ff1a75;
    }

    .screenshot-container {
        position: relative;
        cursor: zoom-in;
    }

    .screenshot-preview {
        width: 100%;
        max-height: 400px;
        object-fit: contain;
        border: 2px solid var(--accent);
        border-radius: 8px;
        transition: all 0.3s;
        display: block;
    }

    .screenshot-preview:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 24px rgba(255, 0, 102, 0.3);
    }

    .zoom-hint {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.8);
        color: var(--accent);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        pointer-events: none;
    }

    .modal-loading {
        text-align: center;
        padding: 60px 20px;
        color: var(--text);
    }

    .modal-loading .spinner {
        border: 4px solid rgba(255, 255, 255, 0.1);
        border-left-color: var(--accent);
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        .modal-grid {
            grid-template-columns: 1fr;
        }
        
        .modal-container {
            max-height: 95vh;
        }
        
        .modal-actions {
            flex-direction: column;
        }
    }
</style>

<div class="grid">
    <div class="glass" style="padding: 24px;">
        <div class="section-title" style="margin-bottom: 24px;">
            <h3>💳 Payment Verification Hub</h3>
            <div style="display: flex; gap: 10px;">
                <input class="search" id="searchInput" placeholder="Search customer, order, product..." onkeyup="filterVerifications()">
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= count($pendingVerifications) ?></div>
                <div class="stat-label">⏳ Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= count($approvedVerifications) ?></div>
                <div class="stat-label">✅ Approved</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">TZS <?= number_format(array_sum(array_column($pendingVerifications, 'payment_amount')), 0) ?></div>
                <div class="stat-label">💰 Pending Amount</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-tabs">
            <button class="filter-btn active" onclick="filterByType('all')">All (<?= count($pendingVerifications) ?>)</button>
            <button class="filter-btn" onclick="filterByType('game')">🎮 Games (<?= count($pendingGameVerifications) ?>)</button>
            <button class="filter-btn" onclick="filterByType('product')">🛍️ Products (<?= count($pendingProductVerifications) ?>)</button>
        </div>

        <!-- Pending Verifications Grid -->
        <div id="verificationsGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 20px;">
            <?php if (!empty($pendingVerifications)): ?>
                <?php foreach ($pendingVerifications as $verification): 
                    $phone = $verification['order_phone'] ?? 'N/A';
                    $productType = !empty($verification['has_game']) ? 'game' : 'product';
                ?>
                    <div class="verification-card" data-type="<?= $productType ?>" data-search="<?= strtolower($verification['first_name'] . ' ' . $verification['last_name'] . ' ' . $verification['order_id'] . ' ' . $verification['product_names']) ?>">
                        <div class="card-header">
                            <div class="customer-info">
                                <div class="customer-name">
                                    👤 <?= htmlspecialchars($verification['first_name'] ?? $verification['name']) ?> <?= htmlspecialchars($verification['last_name'] ?? '') ?>
                                </div>
                                <div class="order-number">Order #<?= $verification['order_id'] ?> • <?= date('d M Y, H:i', strtotime($verification['created_at'])) ?></div>
                            </div>
                            <span class="badge badge-warning">⏳ Pending</span>
                        </div>

                        <div class="card-details">
                            <div class="detail-item">
                                <span class="detail-label">📞 Phone</span>
                                <span class="detail-value"><?= htmlspecialchars($phone) ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">💳 Payment Method</span>
                                <span class="detail-value">
                                    <span class="payment-method-tag"><?= formatPaymentMethod($verification['payment_method']) ?></span>
                                </span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">💰 Amount</span>
                                <span class="detail-value highlight">TZS <?= number_format($verification['payment_amount'], 0) ?></span>
                            </div>
                        </div>

                        <?php if (!empty($verification['product_names'])): ?>
                        <div class="products-list">
                            <div class="products-title">
                                <?php if (!empty($verification['has_game'])): ?>
                                    🎮 Game
                                <?php else: ?>
                                    🛒 Product
                                <?php endif; ?>
                            </div>
                            <div class="products-value"><?= htmlspecialchars($verification['product_names']) ?></div>
                        </div>
                        <?php endif; ?>

                        <div class="card-actions">
                            <button class="btn-review" onclick="openReviewModal(<?= $verification['id'] ?>)">
                                👁️ Review Details
                            </button>
                            <?php if (!empty($verification['screenshot_path'])): ?>
                            <a class="btn-screenshot" href="<?= htmlspecialchars($verification['screenshot_path']) ?>" target="_blank">
                                📸 Screenshot
                            </a>
                            <?php endif; ?>
                            <button class="btn-delete" onclick="deleteVerification(<?= $verification['id'] ?>)">
                                🗑️
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state" style="grid-column: 1 / -1;">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"/></svg>
                    <h3 style="margin-bottom: 8px;">No Pending Verifications</h3>
                    <p>All payments have been processed!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- IMAGE LIGHTBOX -->
    <div id="imageLightbox" class="image-lightbox" onclick="closeImageLightbox()">
        <button class="lightbox-close" onclick="closeImageLightbox()">×</button>
        <img id="lightbox-img" src="" alt="Screenshot" class="lightbox-image">
    </div>

    <!-- REVIEW MODAL -->
    <div id="reviewModal" class="review-modal">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title">💳 Payment Verification Details</h2>
                <button class="modal-close" onclick="closeReviewModal()">×</button>
            </div>

            <div class="modal-body">
                <div class="modal-grid">
                    <!-- Customer Information -->
                    <div class="modal-section">
                        <div class="section-header">👤 Customer Information</div>
                        <div class="info-row">
                            <span class="info-key">Name:</span>
                            <span class="info-val" id="modal-customer-name">-</span>
                        </div>
                        <div class="info-row">
                            <span class="info-key">Email:</span>
                            <span class="info-val" id="modal-customer-email">-</span>
                        </div>
                        <div class="info-row">
                            <span class="info-key">Phone:</span>
                            <span class="info-val" id="modal-customer-phone">-</span>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="modal-section">
                        <div class="section-header">💰 Payment Details</div>
                        <div class="info-row">
                            <span class="info-key">Order #:</span>
                            <span class="info-val" id="modal-order-id">-</span>
                        </div>
                        <div class="info-row">
                            <span class="info-key">Amount:</span>
                            <span class="info-val" id="modal-amount">-</span>
                        </div>
                        <div class="info-row">
                            <span class="info-key">Payment Method:</span>
                            <span class="info-val" id="modal-payment-method">-</span>
                        </div>
                        <div class="info-row">
                            <span class="info-key">Submitted:</span>
                            <span class="info-val" id="modal-submitted-date">-</span>
                        </div>
                    </div>

                    <!-- Payment Screenshot -->
                    <div class="modal-section" style="grid-column: 1 / -1;">
                        <div class="section-header">📸 Payment Screenshot</div>
                        <div class="screenshot-container" onclick="openImageLightbox(event)">
                            <img id="modal-screenshot" src="" alt="Payment Screenshot" class="screenshot-preview">
                            <div class="zoom-hint">🔍 Click to zoom</div>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="modal-section" id="modal-delivery-section" style="display: none;">
                        <div class="section-header">📍 Delivery Address</div>
                        <div id="modal-delivery-content"></div>
                    </div>

                    <!-- Order Items -->
                    <div class="modal-section" id="modal-items-section">
                        <div class="section-header">🎮 Ordered Items</div>
                        <div id="modal-items-list" class="products-badges"></div>
                    </div>
                </div>
            </div>

            <div class="modal-actions">
                <button class="modal-action-btn btn-modal-approve" id="modal-approve-btn" onclick="approvePaymentFromModal()">
                    ✅ Approve Payment
                </button>
                <button class="modal-action-btn btn-modal-reject" onclick="rejectPaymentFromModal()">
                    ❌ Reject Payment
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentVerificationId = null;

function filterByType(type) {
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    // Filter cards
    const cards = document.querySelectorAll('.verification-card');
    cards.forEach(card => {
        if (type === 'all' || card.dataset.type === type) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function filterVerifications() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const cards = document.querySelectorAll('.verification-card');
    
    cards.forEach(card => {
        const searchData = card.dataset.search;
        if (searchData.includes(searchTerm)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function deleteVerification(id) {
    if (!confirm('Are you sure you want to DELETE this payment verification? This action cannot be undone!')) {
        return;
    }

    fetch('/hasheem/admin/payment-delete/' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= $csrf_token ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting');
    });
}

// MODAL FUNCTIONS
function openReviewModal(id) {
    currentVerificationId = id;
    const modal = document.getElementById('reviewModal');
    const modalBody = modal.querySelector('.modal-body');
    
    // Show modal with loading state
    modal.classList.add('active');
    modalBody.innerHTML = '<div class="modal-loading"><div class="spinner"></div><p>Loading payment details...</p></div>';
    
    // Fetch verification details
    fetch('/hasheem/admin/payment-details/' + id)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load data');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Restore original modal structure
                modalBody.innerHTML = `
                    <div class="modal-grid">
                        <!-- Customer Information -->
                        <div class="modal-section">
                            <div class="section-header">👤 Customer Information</div>
                            <div class="info-row">
                                <span class="info-key">Name:</span>
                                <span class="info-val" id="modal-customer-name">-</span>
                            </div>
                            <div class="info-row">
                                <span class="info-key">Email:</span>
                                <span class="info-val" id="modal-customer-email">-</span>
                            </div>
                            <div class="info-row">
                                <span class="info-key">Phone:</span>
                                <span class="info-val" id="modal-customer-phone">-</span>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div class="modal-section">
                            <div class="section-header">💰 Payment Details</div>
                            <div class="info-row">
                                <span class="info-key">Order #:</span>
                                <span class="info-val" id="modal-order-id">-</span>
                            </div>
                            <div class="info-row">
                                <span class="info-key">Amount:</span>
                                <span class="info-val" id="modal-amount">-</span>
                            </div>
                            <div class="info-row">
                                <span class="info-key">Payment Method:</span>
                                <span class="info-val" id="modal-payment-method">-</span>
                            </div>
                            <div class="info-row">
                                <span class="info-key">Submitted:</span>
                                <span class="info-val" id="modal-submitted-date">-</span>
                            </div>
                        </div>

                        <!-- Payment Screenshot -->
                        <div class="modal-section" style="grid-column: 1 / -1;">
                            <div class="section-header">📸 Payment Screenshot</div>
                            <div class="screenshot-container" onclick="openImageLightbox(event)">
                                <img id="modal-screenshot" src="" alt="Payment Screenshot" class="screenshot-preview">
                                <div class="zoom-hint">🔍 Click to zoom</div>
                            </div>
                        </div>

                        <!-- Delivery Address -->
                        <div class="modal-section" id="modal-delivery-section" style="display: none;">
                            <div class="section-header">📍 Delivery Address</div>
                            <div id="modal-delivery-content"></div>
                        </div>

                        <!-- Order Items -->
                        <div class="modal-section" id="modal-items-section">
                            <div class="section-header">🎮 Ordered Items</div>
                            <div id="modal-items-list" class="products-badges"></div>
                        </div>
                    </div>
                `;
                
                populateModal(data.verification, data.orderItems);
            } else {
                modalBody.innerHTML = '<div class="modal-loading"><p>❌ Error: ' + (data.message || 'Failed to load verification details') + '</p></div>';
                setTimeout(() => closeReviewModal(), 2000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = '<div class="modal-loading"><p>❌ Failed to load verification details. Please try again.</p></div>';
            setTimeout(() => closeReviewModal(), 2000);
        });
}

function closeReviewModal() {
    const modal = document.getElementById('reviewModal');
    modal.classList.remove('active');
    currentVerificationId = null;
}

function populateModal(verification, orderItems) {
    // Debug logging
    console.log('Verification data:', verification);
    console.log('Payment method raw:', verification.payment_method);
    
    // Format payment method
    function formatPaymentMethod(method) {
        if (!method) return 'N/A';
        
        // Clean the input
        method = String(method).toLowerCase().trim();
        console.log('Formatting payment method:', method);
        
        const methods = {
            'tigo_pesa': 'TIGO PESA',
            'tigopesa': 'TIGO PESA',
            'tigo': 'TIGO PESA',
            'tigo pesa': 'TIGO PESA',
            'airtel_money': 'AIRTEL MONEY',
            'airtelmoney': 'AIRTEL MONEY',
            'airtel': 'AIRTEL MONEY',
            'airtel money': 'AIRTEL MONEY',
            'm_pesa': 'M-PESA',
            'mpesa': 'M-PESA',
            'm-pesa': 'M-PESA',
            'halopesa': 'HALOPESA',
            'halo_pesa': 'HALOPESA',
            'halo pesa': 'HALOPESA',
            'bank_transfer': 'BANK TRANSFER',
            'banktransfer': 'BANK TRANSFER',
            'bank transfer': 'BANK TRANSFER',
            'bank': 'BANK TRANSFER'
        };
        
        const formatted = methods[method] || method.toUpperCase().replace(/_/g, ' ');
        console.log('Formatted to:', formatted);
        return formatted;
    }
    
    // Customer Information
    const customerName = verification.first_name && verification.last_name 
        ? verification.first_name + ' ' + verification.last_name 
        : verification.name || 'N/A';
    document.getElementById('modal-customer-name').textContent = customerName;
    document.getElementById('modal-customer-email').textContent = verification.order_email || verification.email || 'N/A';
    document.getElementById('modal-customer-phone').textContent = verification.phone || 'N/A';
    
    // Payment Details
    document.getElementById('modal-order-id').textContent = '#' + verification.order_id;
    document.getElementById('modal-amount').textContent = 'TZS ' + parseFloat(verification.payment_amount || verification.total_amount || 0).toLocaleString();
    document.getElementById('modal-payment-method').textContent = formatPaymentMethod(verification.payment_method);
    document.getElementById('modal-submitted-date').textContent = new Date(verification.created_at).toLocaleString();
    
    // Screenshot
    let screenshotPath = verification.screenshot_path || '';
    
    // Handle various path formats
    if (screenshotPath) {
        // If it's already a full URL or starts with /, use as is
        if (screenshotPath.startsWith('http') || screenshotPath.startsWith('/hasheem/')) {
            // Use as is
        } else if (screenshotPath.startsWith('/')) {
            // Starts with / but not /hasheem
            screenshotPath = screenshotPath;
        } else if (screenshotPath.startsWith('uploads/')) {
            // Relative path starting with uploads/
            screenshotPath = '/hasheem/' + screenshotPath;
        } else {
            // Assume it needs /hasheem/ prefix
            screenshotPath = '/hasheem/' + screenshotPath;
        }
        
        document.getElementById('modal-screenshot').src = screenshotPath;
        document.getElementById('modal-screenshot').style.display = 'block';
    } else {
        document.getElementById('modal-screenshot').style.display = 'none';
        document.getElementById('modal-screenshot').parentElement.innerHTML = 
            '<div class="info-val" style="text-align: center; padding: 40px; color: var(--muted);">❌ No screenshot uploaded</div>';
    }
    
    // Delivery Address (construct from fields)
    const addressParts = [
        verification.street,
        verification.city,
        verification.state,
        verification.zip,
        verification.country
    ].filter(function(part) { return part; });  // Remove empty values
    
    if (addressParts.length > 0) {
        document.getElementById('modal-delivery-section').style.display = 'block';
        document.getElementById('modal-delivery-content').innerHTML = 
            '<div class="info-val">' + addressParts.join(', ') + '</div>';
    } else {
        document.getElementById('modal-delivery-section').style.display = 'none';
    }
    
    // Order Items
    const itemsList = document.getElementById('modal-items-list');
    itemsList.innerHTML = '';
    
    if (orderItems && orderItems.length > 0) {
        orderItems.forEach(function(item) {
            const badge = document.createElement('span');
            badge.className = 'product-badge';
            const itemType = item.type || 'Product';
            const itemName = item.name || 'Unknown ' + itemType;
            badge.textContent = itemName + ' (' + itemType + ') ×' + (item.quantity || 1);
            itemsList.appendChild(badge);
        });
    } else {
        itemsList.innerHTML = '<span class="info-val">No items found</span>';
    }
}

function approvePaymentFromModal() {
    if (!currentVerificationId) {
        alert('Error: No verification selected');
        return;
    }
    
    if (!confirm('Are you sure you want to APPROVE this payment?')) {
        return;
    }
    
    const btn = document.getElementById('modal-approve-btn');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = '⏳ Processing...';
    
    fetch('/hasheem/admin/payment-approve/' + currentVerificationId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= $csrf_token ?>'
        },
        body: JSON.stringify({})
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            btn.textContent = '✅ Approved!';
            btn.style.background = 'linear-gradient(135deg, #22ff99 0%, #11b981 100%)';
            
            setTimeout(() => {
                closeReviewModal();
                location.reload();
            }, 1500);
        } else {
            alert('Error: ' + (data.message || 'Failed to approve payment'));
            btn.disabled = false;
            btn.textContent = originalText;
            console.error('Approval failed:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while approving payment. Please check console for details.');
        btn.disabled = false;
        btn.textContent = originalText;
    });
}

function rejectPaymentFromModal() {
    if (!currentVerificationId) {
        alert('Error: No verification selected');
        return;
    }
    
    const reason = prompt('Please enter rejection reason:');
    if (!reason || reason.trim() === '') {
        alert('Rejection reason is required');
        return;
    }
    
    fetch('/hasheem/admin/payment-reject/' + currentVerificationId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= $csrf_token ?>'
        },
        body: JSON.stringify({ reason: reason.trim() })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('✅ Payment rejected successfully');
            closeReviewModal();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to reject payment'));
            console.error('Rejection failed:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while rejecting payment. Please check console for details.');
    });
}

// Close modal on backdrop click
document.addEventListener('click', function(e) {
    const modal = document.getElementById('reviewModal');
    if (e.target === modal) {
        closeReviewModal();
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeReviewModal();
        closeImageLightbox();
    }
});

// IMAGE LIGHTBOX FUNCTIONS
function openImageLightbox(event) {
    const screenshotSrc = document.getElementById('modal-screenshot').src;
    document.getElementById('lightbox-img').src = screenshotSrc;
    document.getElementById('imageLightbox').classList.add('active');
    if (event && event.stopPropagation) {
        event.stopPropagation(); // Prevent modal close
    }
}

function closeImageLightbox() {
    document.getElementById('imageLightbox').classList.remove('active');
}
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php'; ?>
