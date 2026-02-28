<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Jusgam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 10px rgba(16, 185, 129, 0.5), 0 0 20px rgba(34, 197, 94, 0.3); }
            50% { box-shadow: 0 0 20px rgba(16, 185, 129, 0.8), 0 0 40px rgba(34, 197, 94, 0.6); }
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .gaming-glow { animation: glow 2s infinite; }
        .success-card { animation: slideDown 0.6s ease; }
    </style>
</head>
<body class="bg-gray-950 text-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full success-card">
            <!-- Success Card -->
            <div class="bg-gradient-to-b from-gray-900 to-gray-950 border border-emerald-600/30 rounded-2xl p-8 text-center backdrop-blur">
                <!-- Success Icon -->
                <div class="mb-6 flex justify-center">
                    <div class="w-20 h-20 bg-emerald-500/20 border border-emerald-600/30 rounded-full flex items-center justify-center gaming-glow">
                        <span class="text-4xl">✓</span>
                    </div>
                </div>

                <!-- Heading -->
                <h1 class="text-4xl font-black mb-2 bg-gradient-to-r from-emerald-400 to-cyan-400 -webkit-background-clip-text -webkit-text-fill-color-transparent">
                    PAYMENT SUCCESS!
                </h1>
                <p class="text-gray-300 mb-8">
                    🎮 Your games are ready to play! Check your email for download links.
                </p>

                <!-- Order Details -->
                <div class="bg-gray-900/50 border border-emerald-600/20 rounded-xl p-5 mb-6 text-left">
                    <div class="flex justify-between items-center mb-4 pb-4 border-b border-emerald-600/20">
                        <span class="text-gray-400">Order ID</span>
                        <span class="font-black text-emerald-400">#<?= htmlspecialchars($order['id']) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Total Amount</span>
                        <span class="font-black text-lg bg-gradient-to-r from-emerald-400 to-cyan-400 -webkit-background-clip-text -webkit-text-fill-color-transparent">
                            TZS <?= number_format((float)$order['total'], 2) ?>
                        </span>
                    </div>
                </div>

                <!-- Items Summary -->
                <div class="mb-6 text-left">
                    <h3 class="font-black mb-4 text-emerald-300">📦 YOUR GAMES</h3>
                    <div class="space-y-3">
                        <?php foreach ($items as $item): ?>
                        <div class="flex justify-between items-center text-sm bg-gray-900/50 p-3 rounded-lg border border-emerald-600/20">
                            <span class="text-gray-300 font-semibold"><?= htmlspecialchars($item['product_name']) ?></span>
                            <span class="bg-emerald-600/20 text-emerald-300 px-2 py-1 rounded font-bold">x<?= (int)$item['quantity'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Message -->
                <div class="bg-emerald-600/20 border border-emerald-600/50 rounded-xl p-4 mb-6">
                    <p class="text-emerald-300 text-sm font-semibold">
                        ⚡ Instant delivery activated! Your game keys are being sent to your email right now.
                    </p>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <a href="/orders/<?= htmlspecialchars($order['id']) ?>" class="block w-full py-3 bg-gradient-to-r from-emerald-600 to-cyan-600 hover:from-emerald-500 hover:to-cyan-500 rounded-xl font-black transition transform hover:scale-105">
                        📋 VIEW ORDER DETAILS
                    </a>
                    <a href="/orders" class="block w-full py-3 text-emerald-400 hover:text-emerald-300 border-2 border-emerald-600/50 hover:border-emerald-600 rounded-xl font-black transition">
                        📦 MY ORDERS
                    </a>
                    <a href="/" class="block w-full py-3 text-gray-400 hover:text-gray-300 transition font-semibold">
                        🏠 Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>