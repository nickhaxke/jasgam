<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$title = 'Order Submitted - Jusgam';
include __DIR__ . '/../layouts/user-header.php';

$order_id = $_GET['order'] ?? null;
$user = $_SESSION['user'] ?? null;

if (!$user) {
    header('Location: /hasheem/login');
    exit;
}
?>

<style>
    :root {
        --neon-cyan: #3af2ff;
        --neon-green: #22ff99;
        --neon-pink: #ff4fd8;
        --bg-0: #060a12;
        --bg-1: #0f1419;
        --bg-2: #1a1f2e;
        --ink-0: #f2fbff;
        --ink-1: #b8c5d6;
        --ink-2: #7a8a9e;
    }

    .container {
        max-width: 600px;
        margin: 3rem auto;
        padding: 2rem;
    }

    .success-card {
        background: var(--bg-1);
        border: 2px solid var(--neon-green);
        border-radius: 12px;
        padding: 3rem;
        text-align: center;
    }

    .success-icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        animation: bounce 1s infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .success-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--neon-green);
        margin-bottom: 1rem;
        font-family: 'Rajdhani', monospace;
    }

    .success-message {
        color: var(--ink-1);
        font-size: 1.1rem;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .order-details {
        background: rgba(58, 242, 255, 0.05);
        border: 1px solid var(--neon-cyan);
        border-radius: 8px;
        padding: 1.5rem;
        margin: 2rem 0;
        text-align: left;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.8rem 0;
        border-bottom: 1px solid var(--bg-2);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: var(--ink-2);
        font-size: 0.9rem;
    }

    .detail-value {
        color: var(--ink-0);
        font-weight: 600;
    }

    .status-box {
        background: rgba(34, 255, 153, 0.1);
        border: 2px solid var(--neon-green);
        border-radius: 8px;
        padding: 1.5rem;
        margin: 2rem 0;
        text-align: center;
    }

    .status-label {
        color: var(--ink-2);
        font-size: 0.9rem;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .status-value {
        color: var(--neon-green);
        font-size: 1.2rem;
        font-weight: 700;
    }

    .steps-container {
        margin: 2rem 0;
        text-align: left;
    }

    .step {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .step-number {
        width: 40px;
        height: 40px;
        background: var(--neon-cyan);
        color: var(--bg-0);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }

    .step-content h3 {
        color: var(--ink-0);
        margin: 0 0 0.3rem;
        font-size: 1rem;
    }

    .step-content p {
        color: var(--ink-2);
        margin: 0;
        font-size: 0.9rem;
    }

    .button-group {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn {
        flex: 1;
        padding: 1rem;
        border: none;
        border-radius: 6px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.3s;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--neon-green) 0%, #11b981 100%);
        color: var(--bg-0);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(34, 255, 153, 0.3);
    }

    .btn-secondary {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
    }

    .btn-secondary:hover {
        background: rgba(58, 242, 255, 0.1);
    }

    @media (max-width: 768px) {
        .container {
            margin: 2rem 1rem;
        }

        .button-group {
            flex-direction: column;
        }

        .success-title {
            font-size: 1.5rem;
        }
    }
</style>

<div class="container">
    <div class="success-card">
        <div class="success-icon">📝</div>
        <h1 class="success-title">Order Submitted!</h1>
        
        <div class="success-message">
            Thank you! We have received your order request. <br>
            Your payment is now being reviewed...
        </div>

        <div class="order-details">
            <div class="detail-row">
                <span class="detail-label">Order Number</span>
                <span class="detail-value">#<?= htmlspecialchars($order_id) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value" style="color: var(--neon-orange);">⏳ Pending Review</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Next Step</span>
                <span class="detail-value">Admin Verification</span>
            </div>
        </div>

        <div class="status-box">
            <div class="status-label">🔐 Current Status</div>
            <div class="status-value">AWAITING ADMIN VERIFICATION</div>
        </div>

        <div class="steps-container">
            <h3 style="color: var(--neon-cyan); margin: 0 0 1.5rem;">📋 What Happens Next?</h3>
            
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3>Admin Reviews Payment</h3>
                    <p>Our team reviews your payment screenshot and confirms the transaction.</p>
                </div>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>Payment Approval</h3>
                    <p>If your payment is confirmed, your order will be updated to "PAID".</p>
                </div>
            </div>

            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3>Access Granted</h3>
                    <p>You will be able to log in and access your games immediately.</p>
                </div>
            </div>

            <div class="step">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h3>Download & Enjoy</h3>
                    <p>Your download access will be fully activated.</p>
                </div>
            </div>
        </div>

        <div style="background: rgba(255, 107, 53, 0.1); border: 2px solid var(--neon-orange); border-radius: 8px; padding: 1.5rem; margin: 2rem 0; text-align: left;">
            <p style="margin: 0; color: var(--neon-orange); font-weight: 600;">📞 CONTACT INFO</p>
            <p style="margin: 0.5rem 0 0; color: var(--ink-2);">If you have a question, use the contact form or call us:</p>
            <p style="margin: 0.5rem 0 0; color: var(--ink-0); font-weight: 600;">📧 support@jusgam.com</p>
            <p style="margin: 0.3rem 0 0; color: var(--ink-0); font-weight: 600;">📱 +255 XXX XXX XXX</p>
        </div>

        <div class="button-group">
            <a href="/hasheem/order/dashboard" class="btn btn-primary">
                👁️ CHECK ORDER STATUS
            </a>
            <a href="/hasheem/products" class="btn btn-secondary">
                🛒 CONTINUE SHOPPING
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/user-footer.php'; ?>
