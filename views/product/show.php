<?php
$config   = require __DIR__ . '/../../config/app.php';
$basePath = rtrim($config['base_path'] ?? '', '/');

$product         = $product         ?? [];
$download_links  = $download_links  ?? [];
$download_labels = $download_labels ?? [];
$is_unlocked     = $is_unlocked     ?? false;
$unlocked_exp    = $unlocked_exp    ?? 0;
$related         = $related         ?? [];
$screenshots     = $screenshots     ?? [];
$most_downloaded = $most_downloaded ?? [];
$recently_added  = $recently_added  ?? [];

function showPlatBadge(string $t): string {
    $m = ['pc'=>'gc-badge-pc','mobile'=>'gc-badge-mobile','ps4'=>'gc-badge-ps4','ps5'=>'gc-badge-ps5','emulator'=>'gc-badge-emu','cloud'=>'gc-badge-cloud'];
    return $m[strtolower($t)] ?? 'gc-badge-pc';
}

function showRenderCard(array $g, string $basePath, bool $showRating = false): string {
    $html = '<a href="' . htmlspecialchars($basePath) . '/product/' . (int)$g['id'] . '" class="game-card">';
    $html .= '<div class="gc-img"';
    if (!empty($g['preview_video_url'])) {
        $html .= ' data-preview-video="' . htmlspecialchars($g['preview_video_url']) . '"';
    }
    $html .= '>';
    if (!empty($g['image_url'])) {
        $html .= '<img src="' . htmlspecialchars($g['image_url']) . '" alt="' . htmlspecialchars($g['name']) . '" loading="lazy">';
    } else {
        $html .= '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:var(--text-500);">&#127918;</div>';
    }
    $html .= '<span class="gc-badge ' . showPlatBadge($g['game_type'] ?? 'pc') . '">' . htmlspecialchars($g['game_type'] ?? 'PC') . '</span>';
    $html .= '<div class="gc-overlay">';
    if ($showRating) {
        $html .= '<div class="gc-overlay-rating">&#9733;&#9733;&#9733;&#9733;&#9733; <span style="color:var(--text-300);">5.0</span></div>';
    }
    $html .= '<button class="gc-overlay-btn" onclick="event.preventDefault();">';
    $html .= '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>';
    $html .= 'Download</button></div></div>';
    $html .= '<div class="gc-body"><div class="gc-title">' . htmlspecialchars($g['name']) . '</div>';
    $html .= '<div class="gc-meta"><span class="gc-genre">' . htmlspecialchars($g['category_name'] ?? '') . '</span>';
    $html .= '<span class="gc-free"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Free</span>';
    $html .= '</div></div></a>';
    return $html;
}

$title = htmlspecialchars($product['name'] ?? 'Game') . ' | JUSGAM';

// Parse trailer video
$trailerData = null;
if (!empty($product['trailer_video_url'])) {
    $trailerData = \Core\VideoEmbed::parse($product['trailer_video_url']);
    if ($trailerData['type'] === 'unknown') $trailerData = null;
}

// Parse tutorial video
$tutorialData = null;
if (!empty($product['tutorial_video_link'])) {
    $tutorialData = \Core\VideoEmbed::parse($product['tutorial_video_link']);
    if ($tutorialData['type'] === 'unknown') $tutorialData = null;
}

include __DIR__ . '/../layouts/user-header.php';
?>

<!-- ═══ DETAIL HERO ═══ -->
<div class="detail-hero">
    <div class="detail-hero-bg" style="background-image:url('<?= htmlspecialchars($product['cover_image'] ?? $product['image_url'] ?? '') ?>');"></div>
    <div class="detail-hero-content">
        <div class="detail-cover">
            <?php if (!empty($product['image_url'])): ?>
                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name'] ?? '') ?>">
            <?php else: ?>
                <div style="width:100%;aspect-ratio:3/4;display:flex;align-items:center;justify-content:center;font-size:4rem;color:var(--text-500);background:var(--bg-surface);">&#127918;</div>
            <?php endif; ?>
        </div>
        <div class="detail-info">
            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
                <?php if (!empty($product['game_type'])): ?>
                <span class="hero-platform-badge"><?= htmlspecialchars($product['game_type']) ?></span>
                <?php endif; ?>
                <?php if (!empty($product['category_name'])): ?>
                <span class="hero-platform-badge" style="background:rgba(52,211,153,0.15);border-color:rgba(52,211,153,0.4);color:#6ee7b7;"><?= htmlspecialchars($product['category_name']) ?></span>
                <?php endif; ?>
                <span class="hero-platform-badge" style="background:rgba(52,211,153,0.15);border-color:rgba(52,211,153,0.4);color:#6ee7b7;">FREE</span>
            </div>
            <h1><?= htmlspecialchars($product['name'] ?? '') ?></h1>
            <div style="display:flex;align-items:center;gap:8px;margin-top:6px;">
                <span style="color:var(--yellow);font-size:0.85rem;">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                <span style="font-size:0.8rem;color:var(--text-300);">Popular</span>
                <?php if (!empty($product['download_count'])): ?>
                <span style="font-size:0.75rem;color:var(--text-400);margin-left:8px;">
                    &#128229; <?= number_format((int)$product['download_count']) ?> downloads
                </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="page-content">

    <!-- Breadcrumb -->
    <nav style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;font-size:0.8rem;color:var(--text-400);margin-bottom:var(--gap-xl);margin-top:var(--gap-md);">
        <a href="<?= htmlspecialchars($basePath) ?>/" style="transition:color 0.2s;">Home</a>
        <span style="opacity:0.4;">/</span>
        <a href="<?= htmlspecialchars($basePath) ?>/games" style="transition:color 0.2s;">Games</a>
        <?php if (!empty($product['game_type'])): ?>
        <span style="opacity:0.4;">/</span>
        <a href="<?= htmlspecialchars($basePath) ?>/games?platform=<?= urlencode($product['game_type']) ?>" style="transition:color 0.2s;"><?= htmlspecialchars($product['game_type']) ?></a>
        <?php endif; ?>
        <span style="opacity:0.4;">/</span>
        <span style="color:var(--text-200);"><?= htmlspecialchars($product['name'] ?? '') ?></span>
    </nav>

    <div class="detail-layout" style="margin-bottom:var(--gap-2xl);">

        <!-- LEFT: Cover + Download -->
        <div>
            <!-- Cover image (mobile fallback below hero) -->
            <div style="border-radius:var(--r-lg);overflow:hidden;border:1px solid var(--border);background:var(--bg-surface);aspect-ratio:3/4;margin-bottom:var(--gap-md);" class="detail-cover-mobile">
                <?php if (!empty($product['image_url'])): ?>
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name'] ?? '') ?>" style="width:100%;height:100%;object-fit:cover;">
                <?php else: ?>
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:4rem;color:var(--text-500);">&#127918;</div>
                <?php endif; ?>
            </div>

            <!-- Download Box -->
            <div class="dl-box" id="download-box">
                <?php if ($is_unlocked && !empty($download_links)): ?>
                    <div class="dl-unlocked">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Download Unlocked
                    </div>
                    <div class="dl-links">
                        <?php foreach ($download_links as $i => $link): ?>
                        <a href="<?= htmlspecialchars($link) ?>" target="_blank" rel="noopener">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <?= htmlspecialchars($download_labels[$i] ?? 'Download ' . ($i + 1)) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="dl-label">Free Download</div>
                    <div class="dl-hint">Watch a short ad to unlock the download link.</div>

                    <button id="btn-watch-ad" class="btn btn-primary" style="width:100%;padding:14px 20px;font-size:0.9rem;" onclick="startAdUnlock(<?= (int)$product['id'] ?>)">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Watch Ad &amp; Download
                    </button>

                    <!-- Countdown -->
                    <div id="ad-countdown" style="display:none;text-align:center;padding:var(--gap-md) 0;">
                        <div style="font-size:0.85rem;font-weight:600;margin-bottom:4px;color:var(--text-200);">Loading ad...</div>
                        <div id="countdown-num" style="font-size:2.5rem;font-weight:900;background:var(--gradient-neon);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">15</div>
                        <div style="font-size:0.7rem;color:var(--text-400);margin-top:4px;">seconds remaining</div>
                        <div style="height:4px;background:rgba(139,92,246,0.15);border-radius:2px;margin-top:12px;overflow:hidden;">
                            <div id="progress-bar" style="height:100%;width:0%;background:var(--gradient-neon);border-radius:2px;transition:width 1s linear;"></div>
                        </div>
                    </div>

                    <div id="ad-error" style="display:none;color:var(--red);font-size:0.8rem;text-align:center;margin-top:12px;"></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT: Info -->
        <div>
            <!-- Trailer Video -->
            <?php if ($trailerData): ?>
            <div class="info-panel">
                <h2>Trailer</h2>
                <div class="trailer-container">
                    <?= \Core\VideoEmbed::renderPlayer($product['trailer_video_url'], ['autoplay' => true, 'muted' => true, 'loop' => true]) ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tutorial / Installation Guide -->
            <?php if (!empty($product['tutorial_video_link'])): ?>
            <div class="info-panel">
                <h2>Need Help Installing?</h2>
                <p style="color:var(--text-300);font-size:0.85rem;line-height:1.6;margin-bottom:var(--gap-md);">
                    Kama hujui jinsi ya ku-install au kucheza game hii, tumekuandalia video ya maelekezo. Bonyeza button hapa chini kutazama.
                </p>
                <a href="<?= htmlspecialchars($product['tutorial_video_link']) ?>" target="_blank" rel="noopener" class="btn btn-glass" style="display:inline-flex;align-items:center;gap:8px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Watch Tutorial
                </a>
            </div>
            <?php endif; ?>

            <!-- Description -->
            <?php if (!empty($product['description'])): ?>
            <div class="info-panel">
                <h2>About This Game</h2>
                <div style="color:var(--text-200);font-size:0.9rem;line-height:1.7;"><?= nl2br(htmlspecialchars($product['description'])) ?></div>
            </div>
            <?php endif; ?>

            <!-- Screenshots Gallery -->
            <?php if (!empty($screenshots)): ?>
            <div class="info-panel">
                <h2>Screenshots</h2>
                <div class="screenshot-grid">
                    <?php foreach ($screenshots as $ss): ?>
                    <a class="screenshot-item" data-lightbox="<?= htmlspecialchars($ss['image_url']) ?>">
                        <img src="<?= htmlspecialchars($ss['image_url']) ?>" alt="Screenshot" loading="lazy">
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Game Info -->
            <div class="info-panel">
                <h2>Game Info</h2>
                <div class="info-badges">
                    <?php if (!empty($product['game_type'])): ?>
                    <div class="info-badge">
                        <dt>Platform</dt>
                        <dd><?= htmlspecialchars($product['game_type']) ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($product['category_name'])): ?>
                    <div class="info-badge">
                        <dt>Genre</dt>
                        <dd><?= htmlspecialchars($product['category_name']) ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($product['file_size'])): ?>
                    <div class="info-badge">
                        <dt>Size</dt>
                        <dd><?= htmlspecialchars($product['file_size']) ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($product['download_count'])): ?>
                    <div class="info-badge">
                        <dt>Downloads</dt>
                        <dd><?= number_format((int)$product['download_count']) ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($product['created_at'])): ?>
                    <div class="info-badge">
                        <dt>Added</dt>
                        <dd><?= date('M d, Y', strtotime($product['created_at'])) ?></dd>
                    </div>
                    <?php endif; ?>
                    <div class="info-badge">
                        <dt>Price</dt>
                        <dd class="val-free">FREE</dd>
                    </div>
                    <div class="info-badge">
                        <dt>Sign-Up</dt>
                        <dd>Not Required</dd>
                    </div>
                </div>
            </div>

            <!-- Tip -->
            <div style="display:flex;gap:12px;padding:var(--gap-md);background:rgba(139,92,246,0.05);border:1px solid rgba(139,92,246,0.15);border-radius:var(--r-md);">
                <svg style="width:20px;height:20px;color:var(--accent-light);flex-shrink:0;margin-top:2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p style="font-size:0.8rem;color:var(--text-300);line-height:1.6;">
                    <strong style="color:var(--text-100);">How to download:</strong> Click "Watch Ad & Download", wait for the short countdown, then your download link appears. No account or payment needed.
                </p>
            </div>
        </div>
    </div>

    <!-- ═══ RELATED GAMES ═══ -->
    <?php if (!empty($related)): ?>
    <section style="margin-bottom:var(--gap-2xl);">
        <div class="section-head">
            <div class="section-title"><span class="glow-dot"></span> More <?= htmlspecialchars($product['game_type'] ?? '') ?> Games</div>
            <a href="<?= htmlspecialchars($basePath) ?>/games?platform=<?= urlencode($product['game_type'] ?? '') ?>" class="section-link">View All &rarr;</a>
        </div>
        <div class="game-grid">
            <?php foreach ($related as $r): ?>
            <?= showRenderCard($r, $basePath) ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ═══ MOST DOWNLOADED ═══ -->
    <?php if (!empty($most_downloaded)): ?>
    <section style="margin-bottom:var(--gap-2xl);">
        <div class="section-head">
            <div class="section-title"><span class="glow-dot"></span> Most Downloaded</div>
            <a href="<?= htmlspecialchars($basePath) ?>/games" class="section-link">View All &rarr;</a>
        </div>
        <div class="game-grid">
            <?php foreach ($most_downloaded as $g): ?>
            <?= showRenderCard($g, $basePath) ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ═══ RECENTLY ADDED ═══ -->
    <?php if (!empty($recently_added)): ?>
    <section style="margin-bottom:var(--gap-2xl);">
        <div class="section-head">
            <div class="section-title"><span class="glow-dot"></span> Recently Added</div>
            <a href="<?= htmlspecialchars($basePath) ?>/games" class="section-link">View All &rarr;</a>
        </div>
        <div class="game-grid">
            <?php foreach ($recently_added as $g): ?>
            <?= showRenderCard($g, $basePath) ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

</div><!-- /page-content -->

<!-- Ad Unlock JS -->
<script>
var BASE = '<?= htmlspecialchars($basePath, ENT_QUOTES) ?>';
var CSRF_TOKEN = '<?= \Core\Security\CSRF::getToken() ?>';

function startAdUnlock(productId) {
    var btn = document.getElementById('btn-watch-ad');
    var countdown = document.getElementById('ad-countdown');
    var numEl = document.getElementById('countdown-num');
    var progress = document.getElementById('progress-bar');
    var errEl = document.getElementById('ad-error');

    btn.style.display = 'none';
    countdown.style.display = 'block';
    errEl.style.display = 'none';

    var total = 15, elapsed = 0;
    numEl.textContent = total;

    var timer = setInterval(function() {
        elapsed++;
        var remaining = total - elapsed;
        numEl.textContent = remaining;
        progress.style.width = ((elapsed / total) * 100) + '%';
        if (remaining <= 0) {
            clearInterval(timer);
            doUnlock(productId, countdown, errEl, btn);
        }
    }, 1000);
}

function doUnlock(productId, countdown, errEl, btn) {
    var fd = new FormData();
    fd.append('product_id', productId);
    fd.append('_token', CSRF_TOKEN);

    fetch(BASE + '/ad-unlock', { method: 'POST', headers: {'Accept': 'application/json'}, body: fd })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var box = document.getElementById('download-box');
                var html = '<div class="dl-unlocked">'
                    + '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
                    + 'Download Unlocked!</div>';
                html += '<div class="dl-links">';
                var links = data.links || [data.download_url];
                var labels = data.labels || [];
                links.forEach(function(link, i) {
                    var label = labels[i] || ('Download ' + (i + 1));
                    html += '<a href="' + escHtml(link) + '" target="_blank" rel="noopener">'
                        + '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>'
                        + escHtml(label) + '</a>';
                });
                html += '</div>';
                box.innerHTML = html;
            } else {
                countdown.style.display = 'none';
                btn.style.display = 'flex';
                errEl.textContent = data.error || 'Could not unlock. Try again.';
                errEl.style.display = 'block';
            }
        })
        .catch(function() {
            countdown.style.display = 'none';
            btn.style.display = 'flex';
            errEl.textContent = 'Network error. Try again.';
            errEl.style.display = 'block';
        });
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

<?php include __DIR__ . '/../layouts/user-footer.php'; ?>
