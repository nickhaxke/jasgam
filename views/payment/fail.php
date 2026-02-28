<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - Jusgam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
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

        .error-card { animation: slideDown 0.6s ease; }
        .error-icon { animation: shake 0.5s ease; }
    </style>
</head>
<body class="bg-gray-950 text-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full error-card">
            <!-- Error Card -->
            <div class="bg-gradient-to-b from-gray-900 to-gray-950 border border-red-600/30 rounded-2xl p-8 text-center backdrop-blur">
                <!-- Error Icon -->
                <div class="mb-6 flex justify-center">
                    <div class="w-20 h-20 bg-red-500/20 border border-red-600/30 rounded-full flex items-center justify-center error-icon">
                        <span class="text-4xl">✕</span>
                    </div>
                </div>

                <!-- Heading -->
                <h1 class="text-4xl font-black mb-2 bg-gradient-to-r from-red-400 to-pink-400 -webkit-background-clip-text -webkit-text-fill-color-transparent">
                    PAYMENT FAILED
                </h1>
                <p class="text-gray-300 mb-8">
                    💔 Oops! Something went wrong with your payment. Let's fix this!
                </p>

                <!-- Error Details -->
                <div class="bg-red-900/10 border border-red-600/50 rounded-xl p-4 mb-6">
                    <p class="text-red-300 text-sm font-semibold">
                        <strong>🔍 Reason:</strong> <?= htmlspecialchars(ucwords(str_replace('_', ' ', $reason))) ?>
                    </p>
                    <p class="text-red-300 text-sm mt-3">
                        Need help? Our 24/7 gaming support team is here for you!
                    </p>
                </div>

                <!-- What to Do Next -->
                <div class="bg-gray-900/50 border border-red-600/20 rounded-xl p-4 mb-6 text-left">
                    <h3 class="font-black mb-4 text-red-300">💡 QUICK FIXES:</h3>
                    <ul class="text-gray-400 text-sm space-y-2 font-semibold">
                        <li class="flex items-center gap-2">
                            <span class="text-red-400">✓</span> Double-check payment details
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-red-400">✓</span> Ensure card has sufficient funds
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-red-400">✓</span> Try a different payment method
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-red-400">✓</span> Contact your bank
                        </li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <a href="/cart" class="block w-full py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 rounded-xl font-black transition transform hover:scale-105">
                        🛒 BACK TO CART
                    </a>
                    <a href="/products" class="block w-full py-3 text-orange-400 hover:text-orange-300 border-2 border-orange-600/50 hover:border-orange-600 rounded-xl font-black transition">
                        🎮 KEEP SHOPPING
                    </a>
                    <a href="/" class="block w-full py-3 text-gray-400 hover:text-gray-300 transition font-semibold">
                        🏠 Home
                    </a>
                    <a href="mailto:support@jusgam.com" class="block w-full py-3 text-red-400 hover:text-red-300 text-sm font-bold transition">
                        💬 CONTACT 24/7 SUPPORT
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>