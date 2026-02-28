<?php
ob_start();
$totalSalesToday = $todaySales ?? 0;
$totalRevenueGames = $gameRevenue ?? 0;
$totalRevenueProducts = $productRevenue ?? 0;
$activeUsers = $totalUsers ?? 0;
$blockedUsersCount = $blockedUsers ?? 0;
?>

<div class="grid">
    <div class="kpi-grid">
        <div class="glass kpi">
            <div class="kpi-label">Total Sales Today</div>
            <div class="kpi-value"><?= (int)$totalSalesToday ?></div>
            <div class="kpi-trend">+12% vs yesterday</div>
        </div>
        <div class="glass kpi">
            <div class="kpi-label">Revenue (Games)</div>
            <div class="kpi-value">TZS <?= number_format($totalRevenueGames, 0) ?></div>
            <div class="kpi-trend">Core digital sales</div>
        </div>
        <div class="glass kpi">
            <div class="kpi-label">Revenue (Products)</div>
            <div class="kpi-value">TZS <?= number_format($totalRevenueProducts, 0) ?></div>
            <div class="kpi-trend">Physical inventory</div>
        </div>
        <div class="glass kpi">
            <div class="kpi-label">Pending Payments</div>
            <div class="kpi-value"><?= (int)($pendingVerifications ?? 0) ?></div>
            <div class="kpi-trend">Needs review</div>
        </div>
        <div class="glass kpi">
            <div class="kpi-label">Active Users</div>
            <div class="kpi-value"><?= (int)$activeUsers ?></div>
            <div class="kpi-trend">Engaged accounts</div>
        </div>
        <div class="glass kpi">
            <div class="kpi-label">Blocked Users</div>
            <div class="kpi-value"><?= (int)$blockedUsersCount ?></div>
            <div class="kpi-trend">Restricted access</div>
        </div>
    </div>

    <div class="grid" style="grid-template-columns: 1.6fr 1fr;">
        <div class="glass" style="padding: 18px;">
            <div class="section-title">
                <h3>Sales Trend</h3>
                <div>
                    <button class="btn btn-outline">Daily</button>
                    <button class="btn btn-outline">Weekly</button>
                    <button class="btn btn-primary">Monthly</button>
                </div>
            </div>
            <div class="chart">
                <div class="chart-line"></div>
            </div>
        </div>
        <div class="grid">
            <div class="glass" style="padding: 18px;">
                <div class="section-title">
                    <h3>Games vs Products</h3>
                </div>
                <div class="chart">
                    <div class="chart-bars">
                        <div class="bar" style="height: 40%;"></div>
                        <div class="bar" style="height: 70%;"></div>
                        <div class="bar" style="height: 55%;"></div>
                        <div class="bar" style="height: 85%;"></div>
                        <div class="bar" style="height: 60%;"></div>
                        <div class="bar" style="height: 75%;"></div>
                    </div>
                </div>
            </div>
            <div class="glass" style="padding: 18px;">
                <div class="section-title">
                    <h3>Top Selling Items</h3>
                    <a class="btn btn-outline" href="/hasheem/admin/products">View All</a>
                </div>
                <div style="display: grid; gap: 10px;">
                    <?php foreach (($recentProducts ?? []) as $product): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; border: 1px solid var(--line); border-radius: 12px;">
                            <div>
                                <div style="font-weight: 600;"><?= htmlspecialchars($product['name']) ?></div>
                                <div style="color: var(--muted); font-size: 0.85rem;"><?= htmlspecialchars($product['product_type'] ?? 'item') ?></div>
                            </div>
                            <span class="badge badge-success"><?= (($product['product_type'] ?? 'item') === 'game') ? 'AD UNLOCK' : 'TZS ' . number_format($product['price'] ?? 0, 0) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="glass" style="padding: 18px;">
        <div class="section-title">
            <h3>Recent Transactions</h3>
            <a class="btn btn-outline" href="<?= $basePath ?>/admin/orders">View Orders</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentTransactions)): ?>
                    <?php foreach ($recentTransactions as $transaction): ?>
                        <?php
                        $statusClass = 'badge-warning';
                        if ($transaction['status'] === 'paid') $statusClass = 'badge-success';
                        if ($transaction['status'] === 'rejected') $statusClass = 'badge-danger';
                        ?>
                        <tr>
                            <td>#<?= (int)$transaction['id'] ?></td>
                            <td><?= htmlspecialchars($transaction['name'] ?? $transaction['email'] ?? 'Unknown') ?></td>
                            <td>TZS <?= number_format($transaction['total_amount'] ?? 0, 0) ?></td>
                            <td><span class="badge <?= $statusClass ?>"><?= ucfirst($transaction['status']) ?></span></td>
                            <td><?= date('d M, Y', strtotime($transaction['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline" onclick="viewPaymentDetails(<?= $transaction['id'] ?>)">
                                    View Details
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="color: var(--muted);">No transactions yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Payment Details Modal -->
<div id="paymentModal" class="modal" style="display: none;">
    <div class="modal-overlay" onclick="closePaymentModal()"></div>
    <div class="modal-content" style="max-width: 800px; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Payment Details</h2>
            <button class="btn btn-outline" onclick="closePaymentModal()">✕</button>
        </div>
        <div id="paymentDetailsContent">
            <div style="text-align: center; padding: 40px; color: var(--muted);">
                Loading...
            </div>
        </div>
    </div>
</div>

<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(4px);
}

.modal-content {
    position: relative;
    background: var(--glass-bg, rgba(255, 255, 255, 0.1));
    border: 1px solid var(--line, rgba(255, 255, 255, 0.2));
    border-radius: 20px;
    padding: 30px;
    margin: 50px auto;
    max-width: 600px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.875rem;
}

.badge-danger {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

/* Error Modal Styles */
.error-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    animation: fadeIn 0.2s ease-out;
}

.error-modal {
    background: linear-gradient(135deg, #1a1f36 0%, #0f1419 100%);
    border: 2px solid rgba(255, 107, 107, 0.3);
    border-radius: 20px;
    padding: 40px;
    max-width: 500px;
    width: 90%;
    text-align: center;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5), 
                0 0 30px rgba(255, 107, 107, 0.1);
    animation: slideUp 0.3s ease-out;
}

.error-modal-icon {
    font-size: 64px;
    margin-bottom: 20px;
    animation: pulse 2s infinite;
}

.error-modal-title {
    color: #ff6b6b;
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 15px 0;
    text-shadow: 0 2px 10px rgba(255, 107, 107, 0.3);
}

.error-modal-message {
    color: #e6edf6;
    font-size: 16px;
    line-height: 1.6;
    margin: 0 0 30px 0;
    opacity: 0.9;
}

.error-modal-btn {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
    border: none;
    padding: 14px 40px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
}

.error-modal-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
}

.error-modal-btn:active {
    transform: translateY(0);
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

/* Success Modal Styles */
.success-modal {
    background: linear-gradient(135deg, #1a2f36 0%, #0f1a19 100%);
    border: 2px solid rgba(34, 197, 94, 0.4);
    border-radius: 20px;
    padding: 40px;
    max-width: 500px;
    width: 90%;
    text-align: center;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5), 
                0 0 30px rgba(34, 197, 94, 0.15);
    animation: slideUp 0.3s ease-out;
}

.success-modal-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: bold;
    color: white;
    margin: 0 auto 25px;
    box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
    animation: scaleIn 0.5s ease-out;
}

.success-modal-title {
    color: #22c55e;
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 15px 0;
    text-shadow: 0 2px 10px rgba(34, 197, 94, 0.3);
}

.success-modal-message {
    color: #e6edf6;
    font-size: 16px;
    line-height: 1.6;
    margin: 0 0 30px 0;
    opacity: 0.9;
}

.success-modal-btn {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    border: none;
    padding: 14px 40px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
}

.success-modal-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4);
}

.success-modal-btn:active {
    transform: translateY(0);
}

@keyframes scaleIn {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>

<script>
function viewPaymentDetails(orderId) {
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('paymentDetailsContent');
    
    modal.style.display = 'block';
    content.innerHTML = '<div style="text-align: center; padding: 40px; color: var(--muted);">Loading...</div>';
    
    // Fetch payment details via AJAX
    fetch('<?= $basePath ?>/admin/orders/payment-details/' + orderId)
        .then(response => response.text())
        .then(html => {
            content.innerHTML = html;
            attachFormHandlers();
        })
        .catch(error => {
            content.innerHTML = '<div style="text-align: center; padding: 40px; color: #ef4444;">Error loading payment details.</div>';
        });
}

function attachFormHandlers() {
    const forms = document.querySelectorAll('#paymentDetailsContent form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const action = this.action;
            const isApprove = action.includes('approve');
            
            if (!confirm(isApprove ? 'Approve this payment?' : 'Reject this payment?')) {
                return;
            }
            
            console.log('Submitting to:', action);
            console.log('Form data:', Object.fromEntries(formData));
            
            fetch(action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers.get('content-type'));
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // Not JSON, get text to see what's wrong
                    return response.text().then(text => {
                        console.error('Non-JSON response:', text);
                        
                        // Check if it's a token expired error (419)
                        if (response.status === 419 || text.includes('Token Expired') || text.includes('419')) {
                            showErrorModal('Session Expired', 'Your session has expired. The page will reload to refresh your session.', function() {
                                location.reload();
                            });
                            throw new Error('Session expired');
                        }
                        
                        showErrorModal('Server Error', 'The server returned an unexpected response. Please try again.', null);
                        throw new Error('Server returned non-JSON response');
                    });
                }
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    showSuccessModal('Payment Approved', data.message || 'Payment status updated successfully', function() {
                        closePaymentModal();
                        location.reload();
                    });
                } else {
                    showErrorModal('Approval Failed', data.message || 'Failed to update payment status', null);
                    if (data.debug) {
                        console.error('Debug info:', data);
                    }
                }
            })
            .catch(error => {
                console.error('Full error:', error);
                if (!error.message.includes('Session expired')) {
                    showErrorModal('Request Failed', error.message || 'An unexpected error occurred. Please try again.', null);
                }
            });
        });
    });
}

function showErrorModal(title, message, callback) {
    const modal = document.createElement('div');
    modal.className = 'error-modal-overlay';
    modal.innerHTML = `
        <div class="error-modal">
            <div class="error-modal-icon">⚠️</div>
            <h2 class="error-modal-title">${title}</h2>
            <p class="error-modal-message">${message}</p>
            <button class="error-modal-btn" onclick="this.closest('.error-modal-overlay').remove()">OK</button>
        </div>
    `;
    document.body.appendChild(modal);
    
    if (callback) {
        modal.querySelector('.error-modal-btn').onclick = function() {
            modal.remove();
            callback();
        };
    } else {
        modal.querySelector('.error-modal-btn').onclick = function() {
            modal.remove();
        };
    }
}

function showSuccessModal(title, message, callback) {
    const modal = document.createElement('div');
    modal.className = 'error-modal-overlay';
    modal.innerHTML = `
        <div class="success-modal">
            <div class="success-modal-icon">✓</div>
            <h2 class="success-modal-title">${title}</h2>
            <p class="success-modal-message">${message}</p>
            <button class="success-modal-btn">OK</button>
        </div>
    `;
    document.body.appendChild(modal);
    
    if (callback) {
        modal.querySelector('.success-modal-btn').onclick = function() {
            modal.remove();
            callback();
        };
    } else {
        modal.querySelector('.success-modal-btn').onclick = function() {
            modal.remove();
        };
    }
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePaymentModal();
    }
});
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php'; ?>
