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
    <title>Register - Jusgam</title>
    <link href="<?= $basePath ?>/assets/css/home.css" rel="stylesheet">
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .auth-container {
            width: 100%;
            max-width: 450px;
        }

        .auth-card {
            background: var(--bg-1);
            border: 1px solid var(--bg-2);
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 10px 40px rgba(58, 242, 255, 0.1);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .auth-header h1 {
            font-family: 'Rajdhani', monospace;
            font-size: 1.8rem;
            color: var(--ink-0);
            margin-bottom: 0.5rem;
            letter-spacing: 0.05em;
        }

        .auth-header p {
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
            margin-bottom: 0.75rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.9rem 1.2rem;
            background: var(--bg-2);
            border: 1px solid var(--bg-2);
            border-radius: 6px;
            color: var(--ink-0);
            font-size: 0.95rem;
            transition: all 0.3s ease;
            font-family: 'Sora', sans-serif;
        }

        .form-group select {
            width: 100%;
            padding: 0.9rem 1.2rem;
            background: var(--bg-2);
            border: 1px solid var(--bg-2);
            border-radius: 6px;
            color: var(--ink-0);
            font-size: 0.95rem;
            transition: all 0.3s ease;
            font-family: 'Sora', sans-serif;
        }

        .form-group select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 15px rgba(58, 242, 255, 0.3);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 15px rgba(58, 242, 255, 0.3);
        }

        .form-group input::placeholder {
            color: var(--ink-2);
        }


        .form-hint {
            font-size: 0.8rem;
            color: var(--ink-2);
            margin-top: 0.4rem;
        }

        .form-check {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .form-check input {
            width: 18px;
            height: 18px;
            margin-top: 0.2rem;
            cursor: pointer;
        }

        .form-check label {
            margin: 0;
            font-size: 0.9rem;
            color: var(--ink-1);
            cursor: pointer;
            line-height: 1.4;
        }

        .btn-register {
            width: 100%;
            padding: 1rem;
            background: var(--accent);
            color: var(--bg-0);
            border: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 1rem;
            font-family: 'Rajdhani', monospace;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }

        .btn-register:hover {
            background: #2dd4ed;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(58, 242, 255, 0.4);
        }

        .auth-login {
            text-align: center;
            color: var(--ink-1);
            font-size: 0.9rem;
        }

        .auth-login a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-login a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid #ff4444;
            color: #ff6666;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            text-align: center;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 1.5rem;
            }

            .auth-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">🚀</div>
                <h1>Join Jusgam</h1>
                <p>Create your gaming account and start playing</p>
            </div>

            <?php if (!empty($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form method="post" action="<?= $basePath ?>/register">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                
                <div class="form-group">
                    <label>📝 YOUR NAME </label>
                    <input type="text" name="name" placeholder="Your Full Name" required>
                </div>

                <div class="form-group">
                    <label>📧 EMAIL ADDRESS</label>
                    <input type="email" name="email" placeholder="your@email.com" required>
                </div>

                <div class="form-group">
                    <label>🔐 PASSWORD</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                    <div class="form-hint">Min 6 characters</div>
                </div>

                <div class="form-group">
                    <label>🔐 CONFIRM PASSWORD</label>
                    <input type="password" name="password_confirm" placeholder="••••••••" required>
                </div>

                <div class="role-section">
                    <label style="display: block; margin-bottom: 1rem; text-transform: uppercase; font-size: 0.85rem;">👤 CHOOSE AS</label>
                    <label for="terms">I agree to the <a href="#" style="color: var(--accent);">terms of service</a> and <a href="#" style="color: var(--accent);">privacy policy</a></label>
                </div>

                <button type="submit" class="btn-register">✨ CREATE ACCOUNT NOW</button>
            </form>

            <div class="auth-login">
                Already have an account? <a href="<?= $basePath ?>/login">Sign in</a>
            </div>
        </div>
    </div>
</body>
</html>