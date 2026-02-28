<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= htmlspecialchars((string)$order['id']) ?> - Jusgam</title>
    <link href="/hasheem/assets/css/home.css" rel="stylesheet">
    <style>
        :root {
            --accent: #3af2ff;
            --bg-0: #060a12;
            --bg-1: #0f1419;
            --bg-2: #1a1f2e;
            --ink-0: #f2fbff;
            --ink-1: #b8c5d6;
            --ink-2: #7a8a9e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Sora', sans-serif;
            background: linear-gradient(135deg, var(--bg-0) 0%, var(--bg-1) 50%, var(--bg-0) 100%);
            color: var(--ink-0);
            min-height: 100vh;
        }

        header {
            background: rgba(6, 10, 18, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--bg-2);
            padding: 1.5rem 2rem;
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Rajdhani', monospace;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--accent);
            text-decoration: none;
        }

        nav a {
            color: var(--ink-0);
            text-decoration: none;
            margin-left: 2rem;
            transition: color 0.3s;
            font-weight: 600;
        }

        nav a:hover {
            color: var(--accent);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }

        .order-header {
            background: var(--bg-1);
            border: 1px solid var(--bg-2);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .order-title {
            font-family: 'Rajdhani', monospace;
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .order-meta {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 2rem;
            margin-top: 1.5rem;
        }

        .meta-item p:first-child {
            font-size: 0.85rem;
            color: var(--ink-2);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .meta-item p:last-child {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .order-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-pending {
            background: rgba(217, 119, 6, 0.2);
            color: #fbbf24;
            border: 1px solid rgba(217, 119, 6, 0.4);
        }

        .status-completed {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.4);
        }

        .order-content {
            background: var(--bg-1);
            border: 1px solid var(--bg-2);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .section-title {
            font-family: 'Rajdhani', monospace;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            text-transform: uppercase;
        }

        .order-summary {
            display: grid;
            gap: 1rem;
            padding: 1rem 0;
            border-top: 1px solid var(--bg-2);
            border-bottom: 1px solid var(--bg-2);
            margin-bottom: 1.5rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.95rem;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--accent);
            margin-top: 1rem;
            padding-top: 1rem;
        }

        .btn-back {
            display: inline-block;
            background: transparent;
            color: var(--accent);
            border: 2px solid var(--accent);
            padding: 0.8rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: var(--accent);
            color: var(--bg-0);
        }

        footer {
            background: rgba(6, 10, 18, 0.95);
            border-top: 1px solid var(--bg-2);
            padding: 2rem;
            margin-top: 4rem;
        }

        .footer-grid {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
        }

        footer h3 {
            color: var(--accent);
            margin-bottom: 1rem;
        }

        footer a {
            color: var(--ink-1);
            text-decoration: none;
            transition: color 0.3s;
        }

        footer a:hover {
            color: var(--accent);
        }

        @media (max-width: 768px) {
            .order-meta {
                grid-template-columns: 1fr;
            }

            .footer-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="/hasheem/" class="logo">🎮 JUSGAM</a>
            <nav>
                <a href="/hasheem/">Home</a>
                <a href="/hasheem/order/index">Orders</a>
                <a href="/hasheem/cart">Cart</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="order-header">
                <h1 class="order-title">Order #<?= str_pad((int)$order['id'], 6, '0', STR_PAD_LEFT) ?></h1>
                
                <div class="order-meta">
                    <div class="meta-item">
                        <p>Order Date</p>
                        <p><?= date('M d, Y', strtotime($order['created_at'])) ?></p>
                    </div>
                    <div class="meta-item">
                        <p>Total Amount</p>
                        <p style="color: #22c55e; font-size: 1.3rem;">TZS <?= number_format((float)$order['total'], 2) ?></p>
                    </div>
                    <div class="meta-item">
                        <p>Status</p>
                        <p><span class="order-status status-<?= htmlspecialchars($order['status']) ?>"><?= ucfirst(htmlspecialchars($order['status'])) ?></span></p>
                    </div>
                </div>
            </div>

            <div class="order-content">
                <h2 class="section-title">Order Details</h2>
                
                <div style="background: var(--bg-2); padding: 1.5rem; border-radius: 6px; margin-bottom: 1.5rem;">
                    <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['user_name'] ?? 'N/A') ?></p>
                    <p style="margin-top: 0.5rem;"><strong>Item Count:</strong> <?= (int)$order['item_count'] ?> game(s)</p>
                </div>

                <div class="order-summary">
                    <div class="summary-row">
                        <span>Items (<?= (int)$order['item_count'] ?>)</span>
                        <span>TZS <?= number_format((float)$order['total'], 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span style="color: #22c55e;">FREE</span>
                    </div>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span>TZS <?= number_format((float)$order['total'], 2) ?></span>
                </div>

                <?php if ($order['status'] === 'completed'): ?>
                    <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); padding: 1rem; border-radius: 6px; margin-top: 1.5rem; color: #22c55e;">
                        ✅ Thank you for your purchase! Your games are ready to download.
                    </div>
                <?php elseif ($order['status'] === 'pending'): ?>
                    <div style="background: rgba(217, 119, 6, 0.1); border: 1px solid rgba(217, 119, 6, 0.3); padding: 1rem; border-radius: 6px; margin-top: 1.5rem; color: #fbbf24;">
                        ⏳ Your order is being processed. You will receive a download link soon.
                    </div>
                <?php endif; ?>
            </div>

            <a href="/hasheem/order/index" class="btn-back">← Back to Orders</a>
        </div>
    </main>

    <footer>
        <div class="footer-grid">
            <div>
                <h3>ABOUT</h3>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.5rem;">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Press</a></li>
                </ul>
            </div>
            <div>
                <h3>SHOP</h3>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.5rem;">
                    <li><a href="/hasheem/products">All Games</a></li>
                    <li><a href="#">Popular</a></li>
                    <li><a href="#">New Releases</a></li>
                </ul>
            </div>
            <div>
                <h3>SUPPORT</h3>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.5rem;">
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h3>LEGAL</h3>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.5rem;">
                    <li><a href="#">Privacy</a></li>
                    <li><a href="#">Terms</a></li>
                    <li><a href="#">Cookies</a></li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>
    <style>
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 10px rgba(99, 102, 241, 0.5), 0 0 20px rgba(236, 72, 153, 0.3); }
            50% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.8), 0 0 40px rgba(236, 72, 153, 0.6); }
        }

        .gaming-glow { animation: glow 3s infinite; }
        .neon-text { color: #6366f1; text-shadow: 0 0 10px rgba(99, 102, 241, 0.8), 0 0 20px rgba(236, 72, 153, 0.5); }
        .nav-link { position: relative; transition: color 0.3s ease; }
        .nav-link::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 2px; background: linear-gradient(to right, #6366f1, #ec4899); transition: width 0.3s ease; }
        .nav-link:hover::after { width: 100%; }
        .status-badge { font-weight: 900; display: inline-block; padding: 0.75rem 1.5rem; border-radius: 0.5rem; }
        .status-completed { background-color: rgba(16, 185, 129, 0.1); color: #10b981; border: 2px solid rgba(16, 185, 129, 0.3); }
        .status-pending { background-color: rgba(217, 119, 6, 0.1); color: #d97706; border: 2px solid rgba(217, 119, 6, 0.3); }
        .status-failed { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; border: 2px solid rgba(239, 68, 68, 0.3); }
    </style>
</head>
<body class="bg-gray-950 text-gray-100">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-gray-950/95 backdrop-blur border-b border-indigo-600/20 shadow-2xl">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="relative w-12 h-12 gaming-glow rounded-xl flex items-center justify-center font-bold text-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500">
                        🎮
                    </div>
                    <div>
                        <h1 class="font-black text-xl neon-text">JUSGAM</h1>
                        <p class="text-xs text-indigo-400 font-semibold">GAMING STORE</p>
                    </div>
                </a>
                
                <div class="flex items-center gap-6">
                    <a href="/orders" class="nav-link text-gray-300 hover:text-white font-semibold">📦 Orders</a>
                    <a href="/logout" class="text-red-400 hover:text-red-300 font-semibold">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 py-12">
        <!-- Order Header -->
        <div class="bg-gradient-to-b from-gray-900 to-gray-950 border-2 border-indigo-600/20 rounded-2xl p-8 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-4xl font-black neon-text mb-2">ORDER #<?= htmlspecialchars($order['id']) ?></h2>
                    <p class="text-gray-400">
                        🗓️ <?= date('M d, Y at h:i A', strtotime($order['created_at'])) ?>
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-400 mb-2 font-semibold">STATUS</p>
                    <span class="status-badge status-<?= $order['status'] ?>">
                        <?= strtoupper($order['status']) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-gradient-to-b from-gray-900 to-gray-950 border-2 border-indigo-600/20 rounded-2xl overflow-hidden mb-8">
            <div class="p-8 border-b border-indigo-600/20">
                <h3 class="text-2xl font-black neon-text mb-6">🎮 GAMES IN ORDER</h3>
                <div class="space-y-4">
                    <?php foreach ($order['items'] as $item): ?>
                    <div class="bg-gray-900/50 border border-indigo-600/20 rounded-xl p-5 flex gap-4 items-start hover:border-indigo-600/50 transition">
                        <?php if ($item['cover']): ?>
                        <img src="<?= htmlspecialchars($item['cover']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="w-24 h-32 rounded-lg object-cover border border-indigo-600/30">
                        <?php else: ?>
                        <div class="w-24 h-32 rounded-lg bg-gray-800 flex items-center justify-center text-2xl border border-indigo-600/30">🎮</div>
                        <?php endif; ?>
                        <div class="flex-1">
                            <h4 class="font-black text-lg mb-2 text-indigo-300"><?= htmlspecialchars($item['product_name']) ?></h4>
                            <p class="text-gray-400 text-sm">
                                <span class="font-semibold">Quantity:</span> <span class="text-white font-bold"><?= (int)$item['quantity'] ?>x</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-400 mb-1 font-semibold">UNIT PRICE</p>
                            <p class="font-black text-lg bg-gradient-to-r from-emerald-400 to-cyan-400 -webkit-background-clip-text -webkit-text-fill-color-transparent">
                                TZS <?= number_format((float)$item['unit_price'], 2) ?>
                            </p>
                            <p class="text-sm text-gray-400 mt-3 font-semibold">SUBTOTAL</p>
                            <p class="font-black text-lg bg-gradient-to-r from-emerald-400 to-cyan-400 -webkit-background-clip-text -webkit-text-fill-color-transparent">
                                TZS <?= number_format((float)$item['unit_price'] * (int)$item['quantity'], 2) ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-gray-900/50 p-8 border-t border-indigo-600/20">
                <h4 class="text-xl font-black text-indigo-400 mb-6">💳 ORDER SUMMARY</h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-semibold">Subtotal</span>
                        <span class="font-bold text-white">TZS <?= number_format(array_sum(array_map(function($i) { return (float)$i['unit_price'] * (int)$i['quantity']; }, $order['items'])), 2) ?></span>
                    </div>
                    <div class="flex justify-between items-center text-gray-400">
                        <span class="font-semibold">Shipping</span>
                        <span class="font-bold text-emerald-400">🚀 FREE</span>
                    </div>
                    <div class="flex justify-between items-center text-gray-400">
                        <span class="font-semibold">Tax</span>
                        <span class="font-bold text-white">TZS 0.00</span>
                    </div>
                    <div class="border-t border-indigo-600/20 pt-3 flex justify-between items-center">
                        <span class="text-lg font-black neon-text">TOTAL</span>
                        <span class="text-2xl font-black bg-gradient-to-r from-emerald-400 to-cyan-400 -webkit-background-clip-text -webkit-text-fill-color-transparent">
                            TZS <?= number_format((float)$order['total'], 2) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 justify-center flex-wrap">
            <a href="/orders" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 rounded-xl font-black text-white transition transform hover:scale-105">
                📦 BACK TO ORDERS
            </a>
            <?php if ($order['status'] === 'completed'): ?>
            <a href="/download/<?= htmlspecialchars($order['id']) ?>" class="px-8 py-3 bg-gradient-to-r from-emerald-600 to-cyan-600 hover:from-emerald-500 hover:to-cyan-500 rounded-xl font-black text-white transition transform hover:scale-105">
                ⬇️ DOWNLOAD GAME KEYS
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-950 border-t border-indigo-600/20 backdrop-blur mt-20">
        <div class="max-w-7xl mx-auto px-4 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-8">
                <!-- Brand -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 gaming-glow rounded-lg bg-gradient-to-br from-indigo-500 to-pink-500 flex items-center justify-center font-bold">🎮</div>
                        <span class="font-black neon-text">JUSGAM</span>
                    </div>
                    <p class="text-gray-400 text-sm">Your ultimate gaming destination. Install & play instantly.</p>
                </div>

                <!-- Shop -->
                <div>
                    <h4 class="font-black text-indigo-400 mb-4">SHOP</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="/products" class="hover:text-indigo-400 transition">🎮 All Games</a></li>
                        <li><a href="/products" class="hover:text-indigo-400 transition">⭐ New Releases</a></li>
                        <li><a href="/" class="hover:text-indigo-400 transition">🏠 Home</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h4 class="font-black text-indigo-400 mb-4">SUPPORT</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-indigo-400 transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition">Contact Us</a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition">FAQs</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 class="font-black text-indigo-400 mb-4">LEGAL</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-indigo-400 transition">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-indigo-400 transition">Refund Policy</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-indigo-600/20 pt-8 text-center text-gray-500 text-sm">
                <p>&copy; <?php echo date('Y'); ?> Jusgam. All rights reserved. | Made with ❤️ for Gamers</p>
            </div>
        </div>
    </footer>
</body>
</html>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>