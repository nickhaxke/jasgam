<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$title = 'Preparing Download – JUSGAM';
$downloadLink  = $downloadLink  ?? '#';
$downloadLabel = $downloadLabel ?? 'Download';
$config   = require __DIR__ . '/../../config/app.php';
$basePath = rtrim($config['base_path'] ?? '', '/');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($title); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.0/dist/tailwind.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #080d1a 0%, #0a1628 100%);
      min-height: 100vh;
      color: #e2e8f0;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      background: #0d1526;
      border: 1px solid rgba(37,99,235,.3);
      border-radius: 1.25rem;
      padding: 2.5rem;
      max-width: 480px;
      width: 100%;
      text-align: center;
      box-shadow: 0 25px 60px rgba(0,0,0,.5);
    }
    #progress-wrap {
      height: 6px;
      background: rgba(37,99,235,.2);
      border-radius: 3px;
      overflow: hidden;
      margin: 1.5rem 0;
    }
    #progress-fill {
      height: 100%;
      width: 0%;
      background: linear-gradient(90deg, #2563eb, #06b6d4);
      border-radius: 3px;
      transition: width 1s linear;
    }
    .btn-dl {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: .5rem;
      padding: .875rem 2rem;
      background: linear-gradient(135deg, #1d4ed8, #2563eb);
      color: #fff;
      font-weight: 700;
      border-radius: .875rem;
      text-decoration: none;
      box-shadow: 0 10px 30px rgba(37,99,235,.4);
      transition: all .3s ease;
      width: 100%;
    }
    .btn-dl:hover {
      background: linear-gradient(135deg, #2563eb, #3b82f6);
      transform: translateY(-2px);
      box-shadow: 0 16px 40px rgba(37,99,235,.55);
      color: #fff;
    }
    .logo-link {
      display: inline-flex;
      align-items: center;
      gap: .5rem;
      text-decoration: none;
      margin-bottom: 1.5rem;
    }
    .gradient-text {
      background: linear-gradient(135deg, #60a5fa, #06b6d4);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
  </style>
</head>
<body>
  <div class="px-4 w-full" style="max-width:520px;">

    <!-- Logo -->
    <a href="<?php echo htmlspecialchars($basePath); ?>/" class="logo-link" style="display:flex;justify-content:center;align-items:center;gap:.5rem;text-decoration:none;margin-bottom:1.5rem;">
      <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#2563eb,#06b6d4);display:flex;align-items:center;justify-content:center;">
        <svg style="width:20px;height:20px;color:#fff;" fill="currentColor" viewBox="0 0 24 24">
          <path d="M7.97 16L5 19c-.67.67-1 .67-1.41.29A1.987 1.987 0 013 17.83V17c0-.55.45-1 1-1h3.03l.94-1zm5.76-16a5.5 5.5 0 015.5 5.5c0 1.85-.95 3.6-2.5 4.64L18 13h-2v-1.5A3.5 3.5 0 0012.5 8H7.38L4.12 4.74A5.5 5.5 0 0113.73 0z"/>
        </svg>
      </div>
      <span class="gradient-text" style="font-size:1.25rem;font-weight:900;letter-spacing:.1em;">JUSGAM</span>
    </a>

    <div class="card">
      <!-- Ad placement (replace with your Adsterra code) -->
      <div id="ad-slot" style="min-height:100px;background:rgba(37,99,235,.05);border:1px dashed rgba(37,99,235,.2);border-radius:.75rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:center;color:#4b5563;font-size:.8rem;">
        <!-- Adsterra banner / script goes here -->
        Advertisement
      </div>

      <div id="waiting-state">
        <div style="font-size:1rem;font-weight:600;color:#e2e8f0;margin-bottom:.5rem;">Preparing your download…</div>
        <div style="font-size:.875rem;color:#64748b;">Please wait while the ad loads</div>

        <div id="progress-wrap">
          <div id="progress-fill"></div>
        </div>

        <div style="font-size:3rem;font-weight:900;" class="gradient-text" id="count-num">30</div>
        <div style="font-size:.75rem;color:#64748b;margin-top:.25rem;">seconds remaining</div>
      </div>

      <div id="ready-state" style="display:none;">
        <div style="color:#4ade80;font-size:1rem;font-weight:700;margin-bottom:1rem;display:flex;align-items:center;justify-content:center;gap:.5rem;">
          <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          Your download is ready!
        </div>
        <a id="dl-btn" href="<?php echo htmlspecialchars($downloadLink); ?>" class="btn-dl" target="_blank" rel="noopener">
          <svg style="width:1.1rem;height:1.1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
          <?php echo htmlspecialchars($downloadLabel); ?>
        </a>
        <p style="font-size:.7rem;color:#4b5563;margin-top:1rem;">
          <a href="<?php echo htmlspecialchars($basePath); ?>/" style="color:#60a5fa;text-decoration:none;">← Back to JUSGAM</a>
        </p>
      </div>
    </div>

    <p style="text-align:center;font-size:.7rem;color:#374151;margin-top:1rem;">
      JUSGAM &bull; Free Games Portal &bull;
      <a href="<?php echo htmlspecialchars($basePath); ?>/" style="color:#3b82f6;text-decoration:none;">jusgam.com</a>
    </p>
  </div>

  <script>
    var total   = 30;
    var elapsed = 0;
    var fill    = document.getElementById('progress-fill');
    var numEl   = document.getElementById('count-num');

    var timer = setInterval(function() {
      elapsed++;
      var remaining = total - elapsed;
      numEl.textContent = remaining;
      fill.style.width = ((elapsed / total) * 100) + '%';
      if (remaining <= 0) {
        clearInterval(timer);
        document.getElementById('waiting-state').style.display = 'none';
        document.getElementById('ready-state').style.display   = 'block';
        // Auto-click after 1s
        setTimeout(function() {
          var btn = document.getElementById('dl-btn');
          if (btn) btn.click();
        }, 1000);
      }
    }, 1000);
  </script>
</body>
</html>
