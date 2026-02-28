<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Downloads - Jusgam</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="sticky top-0 z-50 bg-gray-900/80 backdrop-blur border-b border-gray-800">
            <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-pink-500 bg-clip-text text-transparent">
                    <a href="/">Jusgam</a>
                </h1>
                <div class="flex gap-4">
                    <a href="/orders" class="text-indigo-400 hover:text-indigo-300">My Orders</a>
                    <a href="/logout" class="text-red-400 hover:text-red-300">Logout</a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 py-12">
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-4xl font-bold mb-2">Download Your Games</h2>
                <p class="text-gray-400">Order #<?= htmlspecialchars($order['id']) ?></p>
            </div>

            <?php if (empty($downloads)): ?>
            <!-- No Downloads -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <h3 class="text-xl font-semibold mb-2">No Downloads Available</h3>
                <p class="text-gray-400 mb-6">Your game keys will appear here once your order is confirmed.</p>
                <a href="/orders" class="inline-block px-6 py-3 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-semibold transition">
                    View All Orders
                </a>
            </div>
            <?php else: ?>
            <!-- Downloads List -->
            <div class="space-y-4">
                <?php foreach ($downloads as $download): ?>
                <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden hover:border-indigo-600 transition">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-4"><?= htmlspecialchars($download['product_name']) ?></h3>
                        
                        <!-- Download Key -->
                        <div class="bg-gray-800/50 rounded-lg p-4 mb-4">
                            <p class="text-sm text-gray-400 mb-2">Game Key</p>
                            <div class="flex items-center gap-3">
                                <code class="flex-1 text-lg font-mono bg-gray-900 px-4 py-2 rounded border border-gray-700">
                                    <?= htmlspecialchars($download['key']) ?>
                                </code>
                                <button onclick="copyToClipboard('<?= htmlspecialchars($download['key']) ?>')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded font-semibold transition">
                                    Copy
                                </button>
                            </div>
                        </div>

                        <!-- Download Button -->
                        <a href="<?= htmlspecialchars($download['download_url']) ?>" class="block w-full text-center py-3 bg-green-600 hover:bg-green-700 rounded-lg font-semibold transition">
                            Download Game
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h3 class="text-lg font-bold mb-4">Installation Instructions</h3>
                <ol class="text-gray-400 space-y-2 ml-4 list-decimal">
                    <li>Download the game file using the button above</li>
                    <li>Extract the contents to your desired location</li>
                    <li>Copy your game key from above</li>
                    <li>Run the game executable and enter your key when prompted</li>
                    <li>Enjoy your new game!</li>
                </ol>
            </div>
            <?php endif; ?>

            <!-- Footer Actions -->
            <div class="mt-8 flex gap-4 justify-center">
                <a href="/orders/<?= htmlspecialchars($order['id']) ?>" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-semibold transition">
                    View Order Details
                </a>
                <a href="/products" class="px-6 py-3 text-indigo-400 hover:text-indigo-300 border border-indigo-600 rounded-lg font-semibold transition">
                    Browse More Games
                </a>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Game key copied to clipboard!');
            }).catch(() => {
                alert('Failed to copy. Please copy manually.');
            });
        }
    </script>
</body>
</html>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/main.php'; ?>