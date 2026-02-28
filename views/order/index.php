<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Jusgam</title>
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            text-align: center;
            margin: 2rem 0;
        }

        .section-title {
            font-family: 'Rajdhani', monospace;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent);
            text-transform: uppercase;
            margin: 1rem 0;
        }

        .orders-grid {
            display: grid;
            gap: 2rem;
            margin: 2rem 0;
        }

        .order-card {
            background: var(--bg-1);
            border: 1px solid var(--bg-2);
            border-radius: 12px;
            padding: 2rem;
            transition: all 0.3s;
        }

        .order-card:hover {
            border-color: var(--accent);
            box-shadow: 0 0 20px rgba(58, 242, 255, 0.15);
        }

        .order-header {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr auto;
            gap: 2rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--bg-2);
        }

        .order-info p:first-child {
            font-size: 0.85rem;
            color: var(--ink-2);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .order-info p:last-child {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .order-id {
            color: var(--accent);
        }

        .order-total {
            color: #22c55e;
            font-size: 1.3rem;
            font-family: 'Rajdhani', monospace;
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

        .status-failed {
            background: rgba(239, 68, 68, 0.2);
            color: #ff6464;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }

        .order-date {
            color: var(--ink-1);
        }

        .btn-view {
            background: transparent;
            color: var(--accent);
            border: 2px solid var(--accent);
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-view:hover {
            background: var(--accent);
            color: var(--bg-0);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .empty-state h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--ink-2);
            margin-bottom: 1.5rem;
        }

        .btn-shop {
            display: inline-block;
            background: var(--accent);
            color: var(--bg-0);
            padding: 0.8rem 2rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn-shop:hover {
            background: #2dd4ed;
            transform: translateY(-2px);
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
            .order-header {
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
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
                <a href="/hasheem/products">Shop</a>
                <a href="/hasheem/cart">Cart</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="page-header">
            <h1 class="section-title">📦 My Orders</h1>
            <p style="color: var(--ink-2);">View your purchase history</p>
        </div>

        <div class="container">
            <?php if (empty($orders)): ?>
                <div style="background: var(--bg-1); border: 1px solid var(--bg-2); border-radius: 12px;">
                    <div class="empty-state">
                        <div class="empty-icon">📦</div>
                        <h2>No Orders Yet</h2>
                        <p>You haven't made any purchases yet. Start browsing our game library!</p>
                        <a href="/hasheem/products" class="btn-shop">Browse Games</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="orders-grid">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <p>Order ID</p>
                                    <p class="order-id">#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></p>
                                </div>
                                <div class="order-info">
                                    <p>Total</p>
                                    <p class="order-total">TZS <?= number_format($order['total'], 2) ?></p>
                                </div>
                                <div class="order-info">
                                    <p>Status</p>
                                    <p>
                                        <span class="order-status status-<?= htmlspecialchars($order['status']) ?>">
                                            <?= ucfirst(htmlspecialchars($order['status'])) ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="order-info">
                                    <p>Date</p>
                                    <p class="order-date"><?= date('M d, Y', strtotime($order['created_at'])) ?></p>
                                </div>
                                <div style="display: flex; align-items: flex-end;">
                                    <a href="/hasheem/order/<?= (int)$order['id'] ?>" class="btn-view">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
        
        <div class="flex items-center gap-8">
          <a href="/products" class="nav-link text-gray-300 hover:text-white font-semibold">🎮 Games</a>
          <a href="/orders" class="nav-link text-indigo-400 font-semibold">📦 Orders</a>
          <a href="/logout" class="text-red-400 hover:text-red-300 font-semibold">Logout</a>
        </div>
      </div>
    </div>
  </nav>

  <main class="container mx-auto px-4 py-12">
    <!-- Page Header -->
    <div class="mb-12" style="animation: slideInDown 0.6s ease;">
      <div class="flex items-center gap-3 mb-4">
        <span class="text-5xl">📦</span>
        <h1 class="text-5xl font-black neon-text">MY ORDERS</h1>
      </div>
      <p class="text-lg text-gray-400 mt-2">View and manage your game purchases</p>
    </div>

    <?php if (empty($orders)): ?>
      <div class="bg-gradient-to-b from-gray-900 to-gray-950 rounded-2xl p-16 text-center border border-indigo-600/20">
        <span class="text-6xl block mb-4">📦</span>
        <p class="text-2xl font-bold text-gray-300 mb-2">No Orders Yet</p>
        <p class="text-gray-500 mb-8">Start your gaming collection by browsing our library!</p>
        <a href="/products" class="inline-block bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold px-8 py-3 rounded-lg transition transform hover:scale-105">
          🎮 BROWSE GAMES
        </a>
      </div>
    <?php else: ?>
      <div class="space-y-6">
        <?php foreach ($orders as $order): ?>
          <div class="order-card bg-gray-900/50 rounded-xl border border-indigo-600/20 overflow-hidden hover:bg-gray-900/80 transition">
            <div class="p-6">
              <!-- Order Header -->
              <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6 pb-6 border-b border-indigo-600/20">
                <div>
                  <p class="text-gray-500 text-sm font-semibold">ORDER ID</p>
                  <p class="font-black text-lg text-indigo-300">#<?php echo (int)$order['id']; ?></p>
                </div>
                <div>
                  <p class="text-gray-500 text-sm font-semibold">TOTAL</p>
                  <p class="font-black text-lg bg-gradient-to-r from-emerald-400 to-cyan-400 -webkit-background-clip-text -webkit-text-fill-color-transparent">
                    TZS <?php echo number_format($order['total'], 2); ?>
                  </p>
                </div>
                <div>
                  <p class="text-gray-500 text-sm font-semibold">STATUS</p>
                  <p>
                    <?php 
                      $status = htmlspecialchars($order['status']);
                      $statusClass = 'status-' . $status;
                      echo "<span class='status-badge $statusClass'>" . strtoupper($status) . "</span>";
                    ?>
                  </p>
                </div>
                <div>
                  <p class="text-gray-500 text-sm font-semibold">DATE</p>
                  <p class="font-semibold text-gray-300"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                </div>
                <div class="flex items-end">
                  <a href="/orders/<?php echo (int)$order['id']; ?>" class="inline-block bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white px-4 py-2 rounded-lg font-bold transition flex items-center gap-2">
                    📋 VIEW
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <footer class="bg-gray-950 border-t border-indigo-600/20 backdrop-blur mt-20">
    <div class="container mx-auto px-4 py-16">
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