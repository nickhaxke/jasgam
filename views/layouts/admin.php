<?php $pageTitle = $title ?? 'Admin Panel'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <style>
        :root {
            --bg: #0b0f19;
            --bg-2: #0f172a;
            --bg-3: #111b2f;
            --card: rgba(20, 27, 45, 0.72);
            --line: rgba(99, 116, 151, 0.25);
            --text: #e6edf6;
            --muted: #9fb0c8;
            --accent: #41d1ff;
            --accent-2: #2b6bff;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --radius: 14px;
            --radius-lg: 16px;
            --shadow: 0 20px 40px rgba(7, 10, 20, 0.45);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Space Grotesk", "Sora", sans-serif;
            background: radial-gradient(900px 500px at 10% -10%, rgba(65, 209, 255, 0.12), transparent 60%),
                        radial-gradient(700px 450px at 90% 10%, rgba(43, 107, 255, 0.12), transparent 60%),
                        var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: grid;
            grid-template-columns: 270px 1fr;
        }

        .sidebar {
            background: linear-gradient(180deg, rgba(17, 27, 47, 0.95), rgba(11, 15, 25, 0.9));
            border-right: 1px solid var(--line);
            padding: 24px 18px;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .brand-badge {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(65, 209, 255, 0.9), rgba(43, 107, 255, 0.9));
            display: grid;
            place-items: center;
            font-weight: 700;
            color: #081220;
        }

        .brand h1 {
            font-size: 1.1rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .nav {
            display: grid;
            gap: 8px;
        }

        .nav a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            border-radius: 12px;
            color: var(--muted);
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .nav a span {
            font-size: 0.85rem;
            color: var(--accent);
        }

        .nav a:hover,
        .nav a.active {
            color: var(--text);
            border-color: rgba(65, 209, 255, 0.35);
            background: rgba(15, 23, 42, 0.8);
            box-shadow: inset 0 0 0 1px rgba(65, 209, 255, 0.2);
        }

        .layout {
            display: grid;
            grid-template-rows: auto 1fr;
            min-height: 100vh;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 22px 28px;
            border-bottom: 1px solid var(--line);
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 5;
        }

        .topbar h2 {
            font-size: 1.4rem;
        }

        .topbar-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .search {
            background: rgba(10, 16, 30, 0.8);
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 8px 14px;
            color: var(--text);
            min-width: 240px;
        }

        .search:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(65, 209, 255, 0.15);
        }

        .chip {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.85rem;
            background: rgba(65, 209, 255, 0.15);
            color: var(--accent);
        }

        .content {
            padding: 28px;
        }

        .glass {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            backdrop-filter: blur(14px);
        }

        .grid {
            display: grid;
            gap: 18px;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .kpi {
            padding: 16px;
        }

        .kpi-label {
            color: var(--muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .kpi-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-top: 8px;
        }

        .kpi-trend {
            font-size: 0.85rem;
            color: var(--muted);
            margin-top: 6px;
        }

        .section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .section-title h3 {
            font-size: 1.1rem;
        }

        .btn {
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid transparent;
            background: rgba(15, 23, 42, 0.9);
            color: var(--text);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
            color: #0a1020;
        }

        .btn-outline {
            border-color: var(--line);
            background: transparent;
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.4);
            color: #ff9a9a;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.92rem;
        }

        .table th,
        .table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--line);
            text-align: left;
        }

        .table th {
            color: var(--muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tr:hover {
            background: rgba(65, 209, 255, 0.08);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.78rem;
            text-transform: uppercase;
            border: 1px solid transparent;
        }

        .badge-success {
            color: #86efac;
            background: rgba(34, 197, 94, 0.2);
            border-color: rgba(34, 197, 94, 0.35);
        }

        .badge-warning {
            color: #fde68a;
            background: rgba(245, 158, 11, 0.15);
            border-color: rgba(245, 158, 11, 0.35);
        }

        .badge-danger {
            color: #fecaca;
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.35);
        }

        .chart {
            height: 220px;
            border-radius: 14px;
            background: linear-gradient(180deg, rgba(65, 209, 255, 0.12), rgba(11, 15, 25, 0.2));
            border: 1px solid rgba(65, 209, 255, 0.25);
            position: relative;
            overflow: hidden;
        }

        .chart::after {
            content: '';
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(90deg, transparent, transparent 28px, rgba(255, 255, 255, 0.04) 28px, rgba(255, 255, 255, 0.04) 29px);
        }

        .chart-line {
            position: absolute;
            inset: 20px 20px 40px 20px;
            border-radius: 12px;
            border: 2px solid rgba(65, 209, 255, 0.4);
        }

        .chart-bars {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            align-items: end;
            height: 100%;
            padding: 24px 20px;
        }

        .bar {
            border-radius: 6px 6px 0 0;
            background: linear-gradient(180deg, rgba(65, 209, 255, 0.8), rgba(43, 107, 255, 0.6));
        }

        @media (max-width: 980px) {
            body {
                grid-template-columns: 1fr;
            }
            .sidebar {
                position: relative;
                height: auto;
            }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-badge">JG</div>
            <div>
                <h1>Jusgam Admin</h1>
                <div style="font-size: 0.75rem; color: var(--muted);">Digital Commerce Hub</div>
            </div>
        </div>
        <nav class="nav">
            <a href="/hasheem/admin/dashboard" data-path="/admin">📊 Dashboard</a>
            <a href="/hasheem/admin/products?type=game" data-path="/admin/products">🎮 Games Management</a>
            <a href="/hasheem/admin/products?type=accessory" data-path="/admin/products">🛍️ Products Management</a>
            <a href="/hasheem/admin/categories" data-path="/admin/categories">📂 Categories</a>
            <a href="/hasheem/admin/orders" data-path="/admin/orders">📦 Orders</a>
            <a href="/hasheem/admin/payments" data-path="/admin/payments">💳 Payment Verifications</a>
            <a href="/hasheem/admin/users" data-path="/admin/users">👥 Users</a>
            <a href="/hasheem/admin/reports" data-path="/admin/reports">📈 Sales Reports</a>
            <a href="/hasheem/admin/announcements" data-path="/admin/announcements">📣 Announcements</a>
            <a href="/hasheem/admin/security" data-path="/admin/security">🛡️ Security & Access</a>
            <a href="/hasheem/admin/settings" data-path="/admin/settings">⚙️ Settings</a>
            <a href="/hasheem/logout" style="margin-top: 10px; color: #fca5a5;">🚪 Logout</a>
        </nav>
    </aside>

    <div class="layout">
        <header class="topbar">
            <h2><?= htmlspecialchars($pageTitle) ?></h2>
            <div class="topbar-actions">
                <input class="search" placeholder="Search admin data...">
                <span class="chip">LIVE</span>
                <a class="btn btn-outline" href="/hasheem/admin/settings">⚙️ Quick Settings</a>
            </div>
        </header>
        <main class="content">
            <?= isset($content) ? $content : '' ?>
        </main>
    </div>

    <script>
        const links = document.querySelectorAll('.nav a[data-path]');
        const path = window.location.pathname;
        links.forEach(link => {
            if (path.startsWith('/hasheem' + link.dataset.path)) {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>
