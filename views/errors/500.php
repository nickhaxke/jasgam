<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error - Jusgam</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <!-- Error Card -->
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-8">
                <!-- Error Icon -->
                <div class="mb-6 flex justify-center">
                    <div class="w-20 h-20 bg-red-900/20 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-12v2m0-4v2M7.414 5.586L5.828 7.172m3.536 3.536L7.172 12.828m3.536-3.536l1.414 1.414M12 12m0 0l1.414 1.414m0 0L16.828 12.828m-3.536 3.536l3.536-3.536M12 12v3m0-3h3"></path>
                        </svg>
                    </div>
                </div>

                <h1 class="text-5xl font-bold text-red-400 mb-2">500</h1>
                <h2 class="text-2xl font-bold mb-4">Server Error</h2>
                <p class="text-gray-400 mb-8">
                    Something went wrong on our end. Our team has been notified and is working to fix it.
                </p>

                <!-- Suggestions -->
                <div class="bg-gray-800/30 rounded-lg p-4 mb-8 text-left text-sm text-gray-400">
                    <p class="mb-3">Try these steps:</p>
                    <ul class="space-y-2">
                        <li>✓ Refresh the page</li>
                        <li>✓ Clear your browser cache</li>
                        <li>✓ Return home and try again</li>
                        <li>✓ Contact support if the problem persists</li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <a href="/" class="block w-full py-3 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-semibold transition">
                        Back to Home
                    </a>
                    <a href="javascript:location.reload()" class="block w-full py-3 text-indigo-400 hover:text-indigo-300 border border-indigo-600 rounded-lg font-semibold transition">
                        Refresh Page
                    </a>
                </div>
            </div>

            <!-- Footer Help -->
            <div class="mt-6 text-gray-500 text-sm">
                <p>Error Code: 500</p>
                <p>Time: <?php echo date('Y-m-d H:i:s'); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
