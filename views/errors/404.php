<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found - Jusgam</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <!-- Error Card -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-8">
                <!-- Error Icon -->
                <div class="mb-6 flex justify-center">
                    <div class="w-20 h-20 bg-yellow-900/20 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <h1 class="text-5xl font-bold text-yellow-400 mb-2">404</h1>
                <h2 class="text-2xl font-bold mb-4">Page Not Found</h2>
                <p class="text-gray-400 mb-8">
                    The page you're looking for doesn't exist or has been moved. Let's get you back on track.
                </p>

                <!-- Suggestions -->
                <div class="bg-gray-800/30 rounded-lg p-4 mb-8 text-left text-sm text-gray-400">
                    <p class="mb-3">What you can do:</p>
                    <ul class="space-y-2">
                        <li>✓ Check the URL for typos</li>
                        <li>✓ Browse our game library</li>
                        <li>✓ Visit the home page</li>
                        <li>✓ Contact support if you need help</li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <a href="/" class="block w-full py-3 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-semibold transition">
                        Back to Home
                    </a>
                    <a href="/products" class="block w-full py-3 text-indigo-400 hover:text-indigo-300 border border-indigo-600 rounded-lg font-semibold transition">
                        Browse Games
                    </a>
                </div>
            </div>

            <!-- Footer Help -->
            <div class="mt-6 text-gray-500 text-sm">
                <p>Error Code: 404</p>
                <p>Time: <?php echo date('Y-m-d H:i:s'); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
