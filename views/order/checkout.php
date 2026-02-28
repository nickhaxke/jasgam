<?php
$totalAmount = $summary['total'] ?? 0;
$userEmail = $user['email'] ?? '';
require_once __DIR__ . '/../../core/Settings.php';
$settings = \Core\Settings::all();
$whatsappNumber = $settings['whatsapp_number'] ?? '255621215237';
$paymentMethods = [];
if (!empty($settings['payment_methods'])) {
    $decoded = json_decode($settings['payment_methods'], true);
    if (is_array($decoded)) {
        $paymentMethods = $decoded;
    }
}
if (empty($paymentMethods)) {
    $paymentMethods = [
        ['name' => 'M-Pesa', 'receiver' => 'Jusgam', 'number' => '255755900101'],
        ['name' => 'Tigo Pesa', 'receiver' => 'Jusgam', 'number' => '255711558202'],
        ['name' => 'Halopesa', 'receiver' => 'Jusgam', 'number' => '255744112233'],
        ['name' => 'Bank Transfer', 'receiver' => 'Jusgam', 'number' => '01522499844']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Jusgam</title>
    <link href="/hasheem/assets/css/home.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f3f5f9;
            --card: #ffffff;
            --ink: #0b1220;
            --muted: #62718a;
            --line: #e7ecf3;
            --primary: #2f6bff;
            --primary-2: #49a1ff;
            --primary-soft: rgba(47, 107, 255, 0.12);
            --success: #18b873;
            --warning: #f3a115;
            --shadow: 0 18px 40px rgba(16, 24, 40, 0.08);
            --radius-lg: 18px;
            --radius-md: 14px;
            --radius-sm: 10px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Plus Jakarta Sans", sans-serif;
            background: radial-gradient(1200px 800px at 10% -10%, #eaf1ff 0%, transparent 60%),
                        radial-gradient(800px 600px at 110% 10%, #f1f6ff 0%, transparent 55%),
                        var(--bg);
            color: var(--ink);
            min-height: 100vh;
            overflow: hidden;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(11, 18, 32, 0.35);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            z-index: 999;
        }

        .modal-shell {
            width: min(1180px, 100%);
            max-height: 92vh;
            overflow-y: auto;
            border-radius: 22px;
            box-shadow: 0 30px 70px rgba(11, 18, 32, 0.25);
            background: transparent;
        }

        .page {
            margin: 0 auto;
            padding: 2.2rem 2.2rem 3rem;
            background: rgba(255, 255, 255, 0.96);
            border-radius: 22px;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2.5rem;
        }

        .brand {
            font-family: "Space Grotesk", sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--ink);
            text-decoration: none;
        }

        .stepper {
            display: flex;
            gap: 0.75rem;
            background: var(--card);
            border-radius: 999px;
            padding: 0.4rem;
            box-shadow: var(--shadow);
        }

        .step {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 999px;
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.25s ease;
        }

        .step.is-active {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .step.is-done {
            color: var(--success);
        }

        .card {
            background: var(--card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 2rem;
        }

        .card-title {
            font-family: "Space Grotesk", sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .layout {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 2rem;
            align-items: start;
        }

        .field {
            margin-bottom: 1rem;
        }

        .field label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: 0.5rem;
        }

        .input {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 1px solid var(--line);
            border-radius: var(--radius-sm);
            font-size: 0.95rem;
            font-family: inherit;
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }

        .input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(47, 107, 255, 0.15);
        }

        .summary {
            display: grid;
            gap: 1rem;
        }

        .summary-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.95rem;
            color: var(--muted);
        }

        .summary-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1rem;
            border-top: 1px dashed var(--line);
            font-weight: 700;
            color: var(--primary);
        }

        .price {
            color: var(--primary);
            font-weight: 700;
        }

        .primary-btn {
            width: 100%;
            padding: 1rem 1.5rem;
            border: none;
            border-radius: var(--radius-md);
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%);
            box-shadow: 0 12px 25px rgba(47, 107, 255, 0.25);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .primary-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 35px rgba(47, 107, 255, 0.32);
        }

        .ghost-btn {
            width: 100%;
            padding: 0.9rem 1.4rem;
            border-radius: var(--radius-md);
            background: transparent;
            border: 1px solid var(--line);
            color: var(--muted);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .ghost-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .step-panel {
            display: none;
            animation: fadeUp 0.35s ease;
        }

        .step-panel.is-visible {
            display: block;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .instruction-list {
            display: grid;
            gap: 0.8rem;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .method-grid {
            display: grid;
            gap: 1rem;
            margin-top: 1rem;
        }

        .method-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.2rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--line);
            background: #fbfcff;
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }

        .method-card.is-selected {
            border-color: var(--primary);
            box-shadow: 0 12px 25px rgba(47, 107, 255, 0.12);
            background: #f5f8ff;
        }

        .method-info {
            display: flex;
            gap: 0.8rem;
            align-items: center;
        }

        .method-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--primary-soft);
            display: grid;
            place-items: center;
            font-weight: 700;
            color: var(--primary);
        }

        .method-name {
            font-weight: 700;
        }

        .method-meta {
            color: var(--muted);
            font-size: 0.85rem;
        }

        .copy-btn {
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: #fff;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .copy-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .amount-card {
            margin-top: 1.5rem;
            padding: 1.2rem 1.5rem;
            border-radius: var(--radius-md);
            background: var(--primary-soft);
            color: var(--primary);
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .upload-box {
            margin-top: 1.5rem;
            border: 2px dashed #cdd8f1;
            border-radius: var(--radius-lg);
            padding: 2rem;
            text-align: center;
            background: #f9fbff;
            cursor: pointer;
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }

        .upload-box:hover {
            border-color: var(--primary);
            box-shadow: 0 12px 25px rgba(47, 107, 255, 0.1);
        }

        .upload-box strong {
            display: block;
            margin-top: 0.5rem;
        }

        .upload-preview {
            margin-top: 1rem;
            max-width: 100%;
            border-radius: var(--radius-md);
            display: none;
        }

        .success-card {
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .confetti {
            position: absolute;
            inset: -30% 0 auto;
            height: 220px;
            background: radial-gradient(circle, rgba(47,107,255,0.45) 0 2px, transparent 3px) 0 0/24px 24px,
                        radial-gradient(circle, rgba(24,184,115,0.4) 0 2px, transparent 3px) 12px 12px/24px 24px;
            animation: floatConfetti 3.5s linear infinite;
            opacity: 0.6;
        }

        @keyframes floatConfetti {
            0% { transform: translateY(-10px); }
            100% { transform: translateY(40px); }
        }

        .status-list {
            margin: 1.5rem 0;
            display: grid;
            gap: 0.7rem;
            text-align: left;
        }

        .status-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 600;
        }

        .status-item.pending {
            color: var(--warning);
        }

        .info-box {
            padding: 1rem 1.2rem;
            border-radius: var(--radius-md);
            background: #f6f7fb;
            color: var(--muted);
            font-size: 0.95rem;
            margin-bottom: 1.2rem;
        }

        .actions {
            display: grid;
            gap: 0.8rem;
        }

        .outline-btn {
            padding: 0.9rem 1.4rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--primary);
            color: var(--primary);
            background: transparent;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .outline-btn:hover {
            background: var(--primary-soft);
        }

        @media (max-width: 960px) {
            .layout {
                grid-template-columns: 1fr;
            }
            .stepper {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 640px) {
            .page {
                padding: 2rem 1rem 3rem;
            }
            .card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="modal-backdrop">
        <div class="modal-shell">
            <div class="page">
                <div class="header">
                    <a class="brand" href="/hasheem/">Jusgam Checkout</a>
                    <div class="stepper" id="stepper">
                        <div class="step is-active" data-step="1">✔ Billing</div>
                        <div class="step" data-step="2">Payment</div>
                        <div class="step" data-step="3">Complete</div>
                    </div>
                </div>

        <div id="step-1" class="step-panel is-visible">
            <div class="layout">
                <div class="card">
                    <div class="card-title">🧾 Billing Info</div>
                    <div class="field">
                        <label for="fullName">Full Name</label>
                        <input id="fullName" class="input" type="text" placeholder="e.g. Amina Bakari" required>
                    </div>
                    <div class="field">
                        <label for="phone">Phone Number</label>
                        <input id="phone" class="input" type="tel" placeholder="e.g. 0712 345 678" required>
                    </div>
                    <div class="field">
                        <label for="location">Region / City</label>
                        <input id="location" class="input" type="text" placeholder="e.g. Dar es Salaam, Kinondoni" required>
                    </div>
                    <button class="primary-btn" type="button" id="toStep2">Continue to Payment</button>
                </div>

                <div class="card summary">
                    <div class="card-title">🛒 Order Summary</div>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <div class="summary-item">
                                <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                                <span class="price">TSh <?= number_format($item['price'] * $item['quantity'], 0) ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="summary-total">
                            <span>Total</span>
                            <span>TSh <?= number_format($totalAmount, 0) ?></span>
                        </div>
                    <?php else: ?>
                        <div class="summary-item">
                            <span>No items in cart</span>
                            <span class="price">TSh 0</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div id="step-2" class="step-panel">
            <div class="card">
                <div class="card-title">💳 How to Pay</div>
                <div class="instruction-list">
                    <div>1. Make payment to the number below</div>
                    <div>2. Upload your payment screenshot</div>
                    <div>3. Admin verifies your order</div>
                </div>

                <div class="method-grid" id="methodGrid">
                    <?php foreach ($paymentMethods as $method): ?>
                        <?php
                            $methodName = $method['name'] ?? 'Payment';
                            $methodReceiver = $method['receiver'] ?? 'Jusgam';
                            $methodNumber = $method['number'] ?? '';
                            $methodCode = strtolower(preg_replace('/[^a-z0-9]+/', '_', $methodName));
                            $methodIcon = strtoupper(substr($methodName, 0, 1));
                        ?>
                        <div class="method-card" data-method="<?= htmlspecialchars($methodCode) ?>" data-number="<?= htmlspecialchars($methodNumber) ?>">
                            <div class="method-info">
                                <div class="method-icon"><?= htmlspecialchars($methodIcon) ?></div>
                                <div>
                                    <div class="method-name"><?= htmlspecialchars($methodName) ?></div>
                                    <div class="method-meta">Receiver: <?= htmlspecialchars($methodReceiver) ?> · <?= htmlspecialchars($methodNumber) ?></div>
                                </div>
                            </div>
                            <button class="copy-btn" type="button">Copy</button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="amount-card">
                    <span>Total to pay</span>
                    <span>TSh <?= number_format($totalAmount, 0) ?></span>
                </div>

                <label class="upload-box" for="screenshotInput">
                    <input type="file" id="screenshotInput" accept="image/*" style="display:none;">
                    <div>📤 Click to upload screenshot</div>
                    <strong>PNG, JPG hadi 5MB</strong>
                    <img id="screenshotPreview" class="upload-preview" alt="Preview">
                </label>

                <div style="display: grid; gap: 0.8rem; margin-top: 1.5rem;">
                    <button class="primary-btn" type="button" id="submitOrder">Submit Order</button>
                    <button class="ghost-btn" type="button" id="backToStep1">Go Back</button>
                </div>
            </div>
        </div>

        <div id="step-3" class="step-panel">
            <div class="card success-card">
                <div class="confetti"></div>
                <div class="card-title" style="justify-content: center; font-size: 1.4rem;">🎉 Success! Order Submitted</div>
                <div class="status-list">
                    <div class="status-item" style="color: var(--success);">✔ Oda imepokelewa</div>
                    <div class="status-item pending">⏳ Admin review in progress</div>
                    <div class="status-item">📦 You will receive a WhatsApp / SMS update</div>
                </div>
                <div class="info-box">
                    Please wait for payment verification and delivery details.
                    You will receive your order within 1-2 business days.
                </div>
                <div class="actions">
                    <button class="primary-btn" type="button" onclick="window.location.href='/hasheem/order/dashboard'">View My Orders</button>
                    <button class="outline-btn" type="button" onclick="window.location.href='https://wa.me/<?= htmlspecialchars($whatsappNumber) ?>'">Message Admin</button>
                </div>
            </div>
        </div>
            </div>
        </div>
    </div>

    <script>
        const steps = {
            1: document.getElementById('step-1'),
            2: document.getElementById('step-2'),
            3: document.getElementById('step-3')
        };
        const stepper = document.getElementById('stepper');
        let selectedMethod = null;
        const csrfToken = <?= json_encode($csrf_token ?? '') ?>;

        function setStep(step) {
            Object.values(steps).forEach(panel => panel.classList.remove('is-visible'));
            steps[step].classList.add('is-visible');

            stepper.querySelectorAll('.step').forEach(node => {
                const stepNum = Number(node.dataset.step);
                node.classList.toggle('is-active', stepNum === step);
                node.classList.toggle('is-done', stepNum < step);
            });
        }

        function splitName(name) {
            const parts = name.trim().split(/\s+/).filter(Boolean);
            const first = parts.shift() || '';
            const last = parts.length ? parts.join(' ') : first;
            return { first, last };
        }

        document.getElementById('toStep2').addEventListener('click', () => {
            const fullName = document.getElementById('fullName').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const location = document.getElementById('location').value.trim();

            if (!fullName || !phone || !location) {
                alert('Please fill in all required fields.');
                return;
            }
            setStep(2);
        });

        document.getElementById('backToStep1').addEventListener('click', () => {
            setStep(1);
        });

        document.querySelectorAll('.method-card').forEach(card => {
            card.addEventListener('click', () => {
                document.querySelectorAll('.method-card').forEach(c => c.classList.remove('is-selected'));
                card.classList.add('is-selected');
                selectedMethod = card.dataset.method;
            });

            card.querySelector('.copy-btn').addEventListener('click', (e) => {
                e.stopPropagation();
                const number = card.dataset.number;
                navigator.clipboard.writeText(number).then(() => {
                    e.target.textContent = 'Copied';
                    setTimeout(() => (e.target.textContent = 'Copy'), 1500);
                });
            });
        });

        const screenshotInput = document.getElementById('screenshotInput');
        const preview = document.getElementById('screenshotPreview');

        screenshotInput.addEventListener('change', () => {
            const file = screenshotInput.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (event) => {
                preview.src = event.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });

        document.getElementById('submitOrder').addEventListener('click', () => {
            const fullName = document.getElementById('fullName').value.trim();
            const location = document.getElementById('location').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const file = screenshotInput.files[0];

            if (!selectedMethod) {
                alert('Please select a payment method first.');
                return;
            }

            if (!file) {
                alert('Please upload a payment screenshot.');
                return;
            }

            const { first, last } = splitName(fullName);
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('payment_method', selectedMethod);
            formData.append('screenshot', file);
            formData.append('first_name', first);
            formData.append('last_name', last);
            formData.append('email', <?= json_encode($userEmail) ?>);
            formData.append('street', location);
            formData.append('city', location);
            formData.append('state', '');
            formData.append('zip', '');
            formData.append('country', 'Tanzania');
            formData.append('phone', phone);

            fetch('/hasheem/order/create-with-payment', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                const contentType = response.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) {
                    const text = await response.text();
                    throw new Error(text || 'Unexpected server response');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    setStep(3);
                } else {
                    alert('There is a problem: ' + (data.message || 'Please try again.'));
                }
            })
            .catch(() => {
                alert('Network issue or server error. Please try again.');
            });
        });
    </script>
</body>
</html>
