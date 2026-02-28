<?php
// Admin Migrations Panel
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Migrations</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --accent: #3af2ff;
            --bg-0: #060a12;
            --bg-1: #0f1419;
            --bg-2: #1a1f2e;
            --ink-0: #f2fbff;
            --ink-1: #b8c5d6;
            --ink-2: #7a8a9e;
            --success: #22c55e;
            --danger: #ff4444;
            --warning: #fbbf24;
        }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--bg-0);
            color: var(--ink-0);
            padding: 2rem;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--bg-2);
        }

        .header h1 {
            font-size: 1.8rem;
            color: var(--accent);
        }

        .back-link {
            color: var(--accent);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border: 1px solid var(--accent);
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            background: var(--accent);
            color: var(--bg-0);
        }

        .info-box {
            background: rgba(58, 242, 255, 0.1);
            border-left: 4px solid var(--accent);
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
            color: var(--ink-1);
        }

        .migrations-grid {
            display: grid;
            gap: 1.5rem;
        }

        .migration-card {
            background: var(--bg-1);
            border: 1px solid var(--bg-2);
            border-radius: 8px;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .migration-card:hover {
            border-color: var(--accent);
            background: var(--bg-2);
        }

        .migration-info h3 {
            color: var(--accent);
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .migration-info p {
            color: var(--ink-2);
            font-size: 0.9rem;
            margin-top: 0.3rem;
        }

        .migration-status {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-badge.executed {
            background: rgba(34, 197, 94, 0.2);
            color: var(--success);
        }

        .status-badge.pending {
            background: rgba(251, 191, 36, 0.2);
            color: var(--warning);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--accent);
            color: var(--bg-0);
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--ink-2);
        }

        .empty-state h2 {
            color: var(--ink-1);
            margin-bottom: 0.5rem;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
            display: none;
        }

        .alert.success {
            background: rgba(34, 197, 94, 0.1);
            border-left: 4px solid var(--success);
            color: var(--success);
        }

        .alert.error {
            background: rgba(255, 68, 68, 0.1);
            border-left: 4px solid var(--danger);
            color: var(--danger);
        }

        .alert.show {
            display: block;
        }

        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid var(--accent);
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔄 Database Migrations</h1>
            <a href="/hasheem/admin/dashboard" class="back-link">← Back to Dashboard</a>
        </div>

        <div id="alert" class="alert"></div>

        <div class="info-box">
            <strong>ℹ️ Database Migrations</strong><br>
            The migrations listed below are database schema updates. Click <strong>Run Migration</strong> to execute a pending migration. Already executed migrations are marked as completed.
        </div>

        <?php if (empty($migrations)): ?>
            <div class="empty-state">
                <h2>No Migrations Found</h2>
                <p>All migrations have been executed or no migration files are available.</p>
            </div>
        <?php else: ?>
            <div class="migrations-grid">
                <?php foreach ($migrations as $migration): ?>
                    <div class="migration-card">
                        <div class="migration-info">
                            <h3><?= htmlspecialchars($migration['filename']) ?></h3>
                            <p>📁 Size: <?= number_format($migration['size']) ?> bytes</p>
                        </div>
                        <div class="migration-status">
                            <div class="status-badge <?= $migration['executed'] ? 'executed' : 'pending' ?>">
                                <?= $migration['executed'] ? '✅ Executed' : '⏳ Pending' ?>
                            </div>
                            <?php if (!$migration['executed']): ?>
                                <button class="btn btn-primary" onclick="runMigration('<?= htmlspecialchars($migration['filename']) ?>')">
                                    Run Migration
                                </button>
                            <?php else: ?>
                                <button class="btn btn-success" disabled>
                                    ✅ Done
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function showAlert(message, type = 'success') {
            const alert = document.getElementById('alert');
            alert.textContent = message;
            alert.className = `alert ${type} show`;
            setTimeout(() => {
                alert.classList.remove('show');
            }, 5000);
        }

        const csrfToken = <?= json_encode(\Core\Security\CSRF::getToken()) ?>;
        const basePath = <?= json_encode(rtrim((require __DIR__ . '/../../config/app.php')['base_path'] ?? '/hasheem', '/')) ?>;

        function runMigration(filename) {
            if (!confirm(`Are you sure you want to run migration: ${filename}?`)) {
                return;
            }

            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<span class="loading"></span> Running...';

            const fd = new FormData();
            fd.append('_token', csrfToken);

            fetch(basePath + `/admin/migrations/run/${filename}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                body: fd,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(`✅ ${data.message}`, 'success');
                    // Reload page after 2 seconds
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showAlert(`❌ ${data.error}`, 'error');
                    btn.disabled = false;
                    btn.innerHTML = 'Run Migration';
                }
            })
            .catch(error => {
                showAlert(`❌ Error: ${error.message}`, 'error');
                btn.disabled = false;
                btn.innerHTML = 'Run Migration';
            });
        }
    </script>
</body>
</html>
<?php
ob_end_flush();
?>
