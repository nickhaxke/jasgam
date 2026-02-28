<?php
ob_start();
$config = require __DIR__ . '/../../config/app.php';
$basePath = rtrim($config['base_path'] ?? '', '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jusgam - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --accent: #3af2ff;
            --accent-dark: #2dd4ed;
            --bg-0: #060a12;
            --bg-1: #0f1419;
            --bg-2: #1a1f2e;
            --ink-0: #f2fbff;
            --ink-1: #b8c5d6;
            --ink-2: #7a8a9e;
        }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--bg-0);
            color: var(--ink-0);
            min-height: 100vh;
        }

        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
            gap: 0;
        }

        .login-left {
            background: linear-gradient(135deg, var(--bg-0) 0%, var(--bg-1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            border-right: 1px solid var(--bg-2);
        }

        .login-right {
            background: var(--bg-0);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .logo-section {
            text-align: center;
            max-width: 400px;
        }

        .logo-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .logo-section h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--accent) 0%, #22c55e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 0.05em;
        }

        .logo-section p {
            color: var(--ink-2);
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .features {
            text-align: left;
            margin-top: 2rem;
        }

        .feature {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            align-items: flex-start;
        }

        .feature-icon {
            font-size: 1.5rem;
            line-height: 1.5;
        }

        .feature-text h3 {
            font-size: 0.9rem;
            color: var(--accent);
            margin-bottom: 0.3rem;
            text-transform: uppercase;
        }

        .feature-text p {
            font-size: 0.85rem;
            color: var(--ink-2);
        }

        .login-form-box {
            width: 100%;
            max-width: 450px;
        }

        .form-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-header h2 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: var(--ink-2);
            font-size: 0.95rem;
        }


        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.9rem 1rem;
            background: var(--bg-2);
            border: 1px solid var(--bg-2);
            border-radius: 6px;
            color: var(--ink-0);
            font-size: 0.95rem;
            font-family: 'Sora', sans-serif;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 12px rgba(58, 242, 255, 0.2);
        }

        .form-group input::placeholder {
            color: var(--ink-2);
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .form-check input {
            width: 16px;
            height: 16px;
        }

        .form-check label {
            margin: 0;
            color: var(--ink-1);
            cursor: pointer;
            text-transform: none;
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: var(--accent);
            color: var(--bg-0);
            border: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.95rem;
            font-family: 'Rajdhani', monospace;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .btn-login:hover {
            background: var(--accent-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(58, 242, 255, 0.3);
        }


        .form-footer {
            text-align: center;
            font-size: 0.9rem;
            color: var(--ink-1);
        }

        .form-footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .form-footer a:hover {
            color: var(--accent-dark);
            text-decoration: underline;
        }

        .error-message {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff8080;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            text-align: center;
        }

        .back-home {
            position: fixed;
            top: 1.5rem;
            left: 1.5rem;
            color: var(--accent);
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .back-home:hover {
            gap: 0.8rem;
        }

        @media (max-width: 1024px) {
            .login-wrapper {
                grid-template-columns: 1fr;
            }

            .login-left {
                display: none;
            }

            .back-home {
                position: static;
                margin-bottom: 1.5rem;
                display: inline-flex;
            }
        }

        @media (max-width: 480px) {
            .form-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <a href="<?= $basePath ?>/" class="back-home">← Back to Store</a>

    <div class="login-wrapper">
        <!-- LEFT SECTION -->
        <div class="login-left">
            <div class="logo-section">
                <div class="logo-icon">🎮</div>
                <h1>JUSGAM</h1>
                <p>Premium gaming experience. Join thousands of players and level up your gaming journey.</p>

                <div class="features">
                    <div class="feature">
                        <div class="feature-icon">🎯</div>
                        <div class="feature-text">
                            <h3>Best Games</h3>
                            <p>Access exclusive gaming titles</p>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">⚡</div>
                        <div class="feature-text">
                            <h3>Fast Downloads</h3>
                            <p>High-speed game delivery</p>
                        </div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon">🔒</div>
                        <div class="feature-text">
                            <h3>Secure Payment</h3>
                            <p>Safe and verified transactions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SECTION -->
        <div class="login-right">
            <div class="login-form-box">
                <div class="form-header">
                    <h2>LOGIN</h2>
                    <p>Access your account to continue</p>
                </div>

                <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <form method="post" action="<?= $basePath ?>/login">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    
                    <div class="form-group">
                        <label>📧 Email</label>
                        <input type="email" name="email" placeholder="email@example.com" required autofocus>
                    </div>

                    <div class="form-group">
                        <label>🔐 Password</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="btn-login">🔓 LOGIN</button>
                </form>

                <div class="form-footer">
                    Don't have an account? <a href="<?= $basePath ?>/register">Create one</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>