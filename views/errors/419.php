<?php
// CSRF Token Validation Error (419 Unprocessable Entity)
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>419 - Token Expired</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Sora', system-ui, -apple-system, sans-serif;
      background: linear-gradient(150deg, #060a12 0%, #0a1625 40%, #0c2232 100%);
      color: #f2fbff;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .error-container {
      text-align: center;
      max-width: 600px;
    }
    .error-code {
      font-family: 'Rajdhani', sans-serif;
      font-size: 120px;
      font-weight: 700;
      background: linear-gradient(120deg, #3af2ff, #00d4ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 10px;
    }
    .error-title {
      font-size: 32px;
      margin-bottom: 16px;
      font-weight: 700;
    }
    .error-message {
      font-size: 16px;
      color: #b5d6e6;
      margin-bottom: 32px;
      line-height: 1.6;
    }
    .error-instruction {
      background: rgba(58, 242, 255, 0.1);
      border: 1px solid #3af2ff;
      padding: 1rem;
      border-radius: 6px;
      margin-bottom: 2rem;
      font-size: 0.95rem;
      color: #b5d6e6;
    }
    .back-button {
      display: inline-block;
      padding: 14px 32px;
      background: linear-gradient(120deg, #3af2ff, #00d4ff);
      color: #021119;
      text-decoration: none;
      border-radius: 12px;
      font-weight: 700;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
      font-size: 14px;
    }
    .back-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(58, 242, 255, 0.3);
    }
  </style>
</head>
<body>
  <div class="error-container">
    <div class="error-code">419</div>
    <h1 class="error-title">Token Expired</h1>
    <p class="error-message">
      <?= isset($error) ? htmlspecialchars($error) : 'Your session security token has expired or is invalid. This usually happens when your form submission took too long or your session has timed out.' ?>
    </p>
    <div class="error-instruction">
      <strong>💡 What to do:</strong><br>
      Please refresh the page or go back and try again. Your new session will have a fresh security token.
    </div>
    <a href="javascript:history.back()" class="back-button">← Go Back</a>
  </div>
</body>
</html>