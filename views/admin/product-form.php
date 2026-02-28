<?php ob_start();

if (isset($product) && $product) {
    $isGame = $product['product_type'] === 'game';
    $title = $isGame ? 'Edit Game' : 'Edit Product';
    $isEdit = true;
} else {
    $type = $_GET['type'] ?? 'accessory';
    $isGame = $type === 'game';
    $title = $isGame ? 'Add New Game' : 'Add New Product';
    $isEdit = false;
}

$download_links = [];
$download_labels = [];
if ($isEdit && !empty($product['download_links'])) {
    $download_links = json_decode($product['download_links'] ?? '[]', true) ?? [];
    $download_labels = json_decode($product['download_link_labels'] ?? '[]', true) ?? [];
}
?>

<style>
/* ── Form Layout ── */
.pf { max-width: 920px; }

.pf-header {
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 28px;
}
.pf-header-icon {
    width: 48px; height: 48px; border-radius: 14px;
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
    display: grid; place-items: center; font-size: 1.4rem;
    box-shadow: 0 8px 24px rgba(65,209,255,0.2);
}
.pf-header h2 { font-size: 1.35rem; font-weight: 700; }
.pf-header small { color: var(--muted); font-size: 0.82rem; font-weight: 400; }

/* ── Tabs ── */
.pf-tabs {
    display: flex; gap: 4px; padding: 4px;
    background: rgba(15,23,42,0.6); border: 1px solid var(--line);
    border-radius: 14px; margin-bottom: 24px;
    position: relative; overflow: hidden;
}
.pf-tab {
    flex: 1; padding: 11px 16px; text-align: center;
    font-size: 0.82rem; font-weight: 600; letter-spacing: 0.02em;
    color: var(--muted); cursor: pointer; border: none; background: none;
    border-radius: 11px; transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    position: relative; z-index: 1; white-space: nowrap;
}
.pf-tab:hover { color: var(--text); }
.pf-tab.active {
    color: #0a1020;
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
    box-shadow: 0 4px 16px rgba(65,209,255,0.3);
}
.pf-tab[data-tab="game"] { display: none; }
.pf-tab[data-tab="game"].show { display: block; }

/* ── Panels ── */
.pf-panel {
    display: none;
    animation: pfFadeSlide 0.35s cubic-bezier(0.4,0,0.2,1) forwards;
}
.pf-panel.active { display: block; }

@keyframes pfFadeSlide {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Card sections ── */
.pf-card {
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: var(--radius);
    padding: 24px;
    margin-bottom: 20px;
    backdrop-filter: blur(12px);
    transition: border-color 0.3s, box-shadow 0.3s, transform 0.3s;
}
.pf-card:hover {
    border-color: rgba(65,209,255,0.3);
    box-shadow: 0 8px 32px rgba(65,209,255,0.06);
    transform: translateY(-1px);
}
.pf-card-title {
    display: flex; align-items: center; gap: 10px;
    font-size: 0.92rem; font-weight: 700; color: var(--text);
    margin-bottom: 20px; padding-bottom: 14px;
    border-bottom: 1px solid var(--line);
}
.pf-card-title .icon {
    width: 32px; height: 32px; border-radius: 9px;
    display: grid; place-items: center; font-size: 0.95rem;
    background: rgba(65,209,255,0.1); border: 1px solid rgba(65,209,255,0.2);
}

/* ── Form Groups ── */
.pf-group { margin-bottom: 18px; }
.pf-label {
    display: block; font-size: 0.78rem; font-weight: 600;
    color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em;
    margin-bottom: 8px;
}
.pf-label .opt { font-weight: 400; text-transform: none; opacity: 0.6; }
.pf-hint { font-size: 0.75rem; color: var(--muted); margin-top: 6px; opacity: 0.7; }

.pf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.pf-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }

/* ── Inputs ── */
.pf-input, .pf-select, .pf-textarea {
    width: 100%; padding: 12px 14px;
    background: rgba(10,16,30,0.7);
    border: 1px solid var(--line);
    border-radius: 10px; color: var(--text);
    font-family: inherit; font-size: 0.88rem;
    transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
    outline: none;
}
.pf-input:focus, .pf-select:focus, .pf-textarea:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(65,209,255,0.12), 0 0 20px rgba(65,209,255,0.06);
    background: rgba(10,16,30,0.9);
}
.pf-input::placeholder, .pf-textarea::placeholder { color: rgba(159,176,200,0.4); }
.pf-textarea { resize: vertical; min-height: 100px; }
.pf-select { cursor: pointer; }

/* ── File Uploads ── */
.pf-upload {
    position: relative; padding: 28px 20px; text-align: center;
    border: 2px dashed var(--line); border-radius: 12px;
    background: rgba(10,16,30,0.4);
    transition: border-color 0.3s, background 0.3s, transform 0.2s;
    cursor: pointer;
}
.pf-upload:hover {
    border-color: var(--accent);
    background: rgba(65,209,255,0.03);
    transform: scale(1.005);
}
.pf-upload.dragover {
    border-color: var(--accent);
    background: rgba(65,209,255,0.06);
    transform: scale(1.01);
}
.pf-upload input[type="file"] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.pf-upload-icon {
    width: 44px; height: 44px; margin: 0 auto 10px;
    border-radius: 12px; display: grid; place-items: center;
    background: rgba(65,209,255,0.1); color: var(--accent); font-size: 1.2rem;
}
.pf-upload-text { font-size: 0.82rem; color: var(--muted); line-height: 1.5; }
.pf-upload-text strong { color: var(--accent); }

.pf-thumb-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(90px,1fr)); gap: 8px;
    margin-top: 12px;
}
.pf-thumb {
    aspect-ratio: 1; border-radius: 8px; overflow: hidden;
    border: 1px solid var(--line);
    transition: transform 0.25s, box-shadow 0.25s;
}
.pf-thumb:hover { transform: scale(1.05); box-shadow: 0 4px 16px rgba(0,0,0,0.4); }
.pf-thumb img { width: 100%; height: 100%; object-fit: cover; }

.pf-preview-img {
    display: inline-block; border-radius: 10px; overflow: hidden;
    border: 1px solid var(--line); margin-bottom: 12px;
    transition: transform 0.3s, box-shadow 0.3s;
}
.pf-preview-img:hover {
    transform: perspective(600px) rotateY(-3deg) scale(1.02);
    box-shadow: 0 12px 40px rgba(0,0,0,0.5);
}
.pf-preview-img img { display: block; max-width: 140px; max-height: 140px; }

/* ── Download link rows ── */
.pf-dl-row {
    display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px;
    align-items: end; padding: 14px;
    background: rgba(10,16,30,0.5); border: 1px solid var(--line);
    border-radius: 10px; margin-bottom: 10px;
    transition: border-color 0.25s, transform 0.2s;
    animation: pfFadeSlide 0.3s ease forwards;
}
.pf-dl-row:hover {
    border-color: rgba(65,209,255,0.25);
    transform: translateX(2px);
}
.pf-dl-label {
    font-size: 0.72rem; font-weight: 600; color: var(--muted);
    text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px;
}
.pf-dl-remove {
    width: 38px; height: 38px; border-radius: 9px;
    display: grid; place-items: center; cursor: pointer;
    background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3);
    color: #f87171; font-size: 1rem; font-weight: 700;
    transition: all 0.2s;
}
.pf-dl-remove:hover {
    background: rgba(239,68,68,0.2); transform: scale(1.08);
}
.pf-dl-add {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 18px; border-radius: 10px; cursor: pointer;
    background: rgba(65,209,255,0.08); border: 1px solid rgba(65,209,255,0.25);
    color: var(--accent); font-size: 0.82rem; font-weight: 600;
    transition: all 0.25s; margin-top: 6px;
}
.pf-dl-add:hover {
    background: rgba(65,209,255,0.15);
    box-shadow: 0 4px 16px rgba(65,209,255,0.1);
    transform: translateY(-1px);
}

/* ── Toggle Switches ── */
.pf-toggle {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 16px; border-radius: 10px; cursor: pointer;
    background: rgba(10,16,30,0.5); border: 1px solid var(--line);
    transition: all 0.3s;
}
.pf-toggle:hover { border-color: rgba(65,209,255,0.3); }
.pf-toggle input { display: none; }
.pf-toggle-track {
    width: 42px; height: 24px; border-radius: 12px; position: relative;
    background: rgba(99,116,151,0.3); flex-shrink: 0;
    transition: background 0.3s;
}
.pf-toggle-track::after {
    content: ''; position: absolute; top: 3px; left: 3px;
    width: 18px; height: 18px; border-radius: 50%;
    background: var(--muted); transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
}
.pf-toggle input:checked ~ .pf-toggle-track {
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
}
.pf-toggle input:checked ~ .pf-toggle-track::after {
    left: 21px; background: #fff;
    box-shadow: 0 2px 8px rgba(65,209,255,0.4);
}
.pf-toggle-label { font-size: 0.85rem; font-weight: 600; color: var(--text); }
.pf-toggle-desc { font-size: 0.72rem; color: var(--muted); margin-top: 2px; }

/* ── Footer Actions ── */
.pf-footer {
    display: flex; align-items: center; gap: 12px;
    padding-top: 24px; border-top: 1px solid var(--line);
    margin-top: 8px;
}
.pf-btn {
    padding: 13px 28px; border-radius: 12px; font-weight: 700;
    font-size: 0.88rem; cursor: pointer; border: none;
    transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    display: inline-flex; align-items: center; gap: 8px;
}
.pf-btn-primary {
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
    color: #0a1020;
    box-shadow: 0 4px 20px rgba(65,209,255,0.25);
}
.pf-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(65,209,255,0.35);
}
.pf-btn-primary:active { transform: translateY(0); }
.pf-btn-ghost {
    background: transparent; border: 1px solid var(--line);
    color: var(--muted);
}
.pf-btn-ghost:hover {
    border-color: rgba(65,209,255,0.3); color: var(--text);
}

/* ── Step indicator ── */
.pf-steps {
    display: flex; align-items: center; gap: 0;
    margin-bottom: 24px;
}
.pf-step {
    display: flex; align-items: center; gap: 8px;
    font-size: 0.75rem; font-weight: 600; color: var(--muted);
    transition: color 0.3s;
}
.pf-step.active { color: var(--accent); }
.pf-step.done { color: var(--success); }
.pf-step-dot {
    width: 24px; height: 24px; border-radius: 50%;
    display: grid; place-items: center; font-size: 0.7rem;
    border: 2px solid var(--line); background: transparent;
    transition: all 0.3s;
}
.pf-step.active .pf-step-dot {
    border-color: var(--accent);
    background: rgba(65,209,255,0.15);
    box-shadow: 0 0 12px rgba(65,209,255,0.2);
}
.pf-step.done .pf-step-dot {
    border-color: var(--success); background: rgba(34,197,94,0.15);
}
.pf-step-line {
    flex: 1; height: 2px; background: var(--line);
    margin: 0 10px; border-radius: 1px;
    position: relative; overflow: hidden;
}
.pf-step-line::after {
    content: ''; position: absolute; inset: 0;
    background: var(--accent); transform: scaleX(0); transform-origin: left;
    transition: transform 0.4s cubic-bezier(0.4,0,0.2,1);
}
.pf-step-line.filled::after { transform: scaleX(1); }

/* ── Responsive ── */
@media (max-width: 640px) {
    .pf-row, .pf-row-3 { grid-template-columns: 1fr; }
    .pf-dl-row { grid-template-columns: 1fr; }
    .pf-tabs { flex-wrap: wrap; }
    .pf-tab { flex: none; }
}
</style>

<div class="pf">
    <!-- Header -->
    <div class="pf-header">
        <div class="pf-header-icon"><?= $isGame ? '&#127918;' : '&#128722;' ?></div>
        <div>
            <h2><?= htmlspecialchars($title) ?></h2>
            <small><?= $isEdit ? 'Update product details and settings' : 'Fill in the details to add a new product' ?></small>
        </div>
    </div>

    <!-- Step Indicator -->
    <div class="pf-steps" id="pf-steps">
        <div class="pf-step active" data-step="basic">
            <span class="pf-step-dot">1</span> Basic Info
        </div>
        <div class="pf-step-line" id="line-1"></div>
        <div class="pf-step" data-step="media">
            <span class="pf-step-dot">2</span> Media
        </div>
        <div class="pf-step-line" id="line-2"></div>
        <div class="pf-step" data-step="game" id="step-game" style="display:<?= $isGame ? 'flex' : 'none' ?>;">
            <span class="pf-step-dot">3</span> Game Config
        </div>
        <div class="pf-step-line" id="line-3" style="display:<?= $isGame ? 'block' : 'none' ?>;"></div>
        <div class="pf-step" data-step="publish">
            <span class="pf-step-dot"><?= $isGame ? '4' : '3' ?></span> Publish
        </div>
    </div>

    <!-- Tabs -->
    <div class="pf-tabs" id="pf-tabs">
        <button type="button" class="pf-tab active" data-tab="basic" onclick="switchTab('basic')">Basic Info</button>
        <button type="button" class="pf-tab" data-tab="media" onclick="switchTab('media')">Media & Images</button>
        <button type="button" class="pf-tab <?= $isGame ? 'show' : '' ?>" data-tab="game" onclick="switchTab('game')">Game Settings</button>
        <button type="button" class="pf-tab" data-tab="publish" onclick="switchTab('publish')">Publish</button>
    </div>

    <form method="post" action="<?= htmlspecialchars($form_action ?? '/admin/products') ?>" enctype="multipart/form-data" id="pf-form">
        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

        <!-- ─── PANEL 1: BASIC INFO ─── -->
        <div class="pf-panel active" id="panel-basic">
            <div class="pf-card">
                <div class="pf-card-title"><span class="icon">&#128221;</span> Product Details</div>

                <div class="pf-group">
                    <label class="pf-label">Product Name</label>
                    <input type="text" name="name" class="pf-input" value="<?= htmlspecialchars($product['name'] ?? '') ?>" placeholder="e.g. Cyber Legends, PlayStation Gift Card" required>
                </div>

                <div class="pf-row">
                    <div class="pf-group">
                        <label class="pf-label">Product Type</label>
                        <select name="product_type" id="product_type" class="pf-select" onchange="toggleGameType()" required>
                            <option value="accessory" <?= $isGame ? '' : 'selected' ?>>Accessory / Product</option>
                            <option value="game" <?= $isGame ? 'selected' : '' ?>>Game</option>
                        </select>
                    </div>
                    <div class="pf-group" id="game_type_field" style="display:<?= $isGame ? 'block' : 'none' ?>;">
                        <label class="pf-label">Game Platform</label>
                        <select name="game_type" class="pf-select">
                            <option value="">Select Platform</option>
                            <option value="PC" <?= isset($product) && ($product['game_type'] ?? '') === 'PC' ? 'selected' : '' ?>>PC</option>
                            <option value="Mobile" <?= isset($product) && ($product['game_type'] ?? '') === 'Mobile' ? 'selected' : '' ?>>Mobile</option>
                            <option value="PS4" <?= isset($product) && ($product['game_type'] ?? '') === 'PS4' ? 'selected' : '' ?>>PS4</option>
                            <option value="PS5" <?= isset($product) && ($product['game_type'] ?? '') === 'PS5' ? 'selected' : '' ?>>PS5</option>
                            <option value="Emulator" <?= isset($product) && ($product['game_type'] ?? '') === 'Emulator' ? 'selected' : '' ?>>Emulator</option>
                            <option value="Cloud" <?= isset($product) && ($product['game_type'] ?? '') === 'Cloud' ? 'selected' : '' ?>>Cloud</option>
                        </select>
                    </div>
                </div>

                <div class="pf-group">
                    <label class="pf-label">Description</label>
                    <textarea name="description" class="pf-textarea" placeholder="Product details, features, system requirements..."><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Accessory-only fields -->
            <div class="pf-card" id="offer_fields" style="display:<?= $isGame ? 'none' : 'block' ?>;">
                <div class="pf-card-title"><span class="icon">&#128176;</span> Pricing & Category</div>

                <div class="pf-row">
                    <div class="pf-group">
                        <label class="pf-label">Category</label>
                        <select name="category_id" class="pf-select" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories ?? [] as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>" <?= isset($product) && ($product['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="pf-group" id="price_field" style="display:<?= $isGame ? 'none' : 'block' ?>;">
                        <label class="pf-label">Price ($)</label>
                        <input type="number" id="price_input" name="price" step="0.01" class="pf-input" value="<?= htmlspecialchars($product['price'] ?? '') ?>" placeholder="59.99" required>
                    </div>
                </div>

                <div class="pf-row">
                    <div class="pf-group">
                        <label class="pf-label">Offer % <span class="opt">(optional)</span></label>
                        <input type="number" name="offer_percent" class="pf-input" value="<?= htmlspecialchars($product['offer_percent'] ?? '0') ?>" min="0" max="100" placeholder="0">
                    </div>
                    <div class="pf-group">
                        <label class="pf-label">Offer End Date <span class="opt">(optional)</span></label>
                        <input type="datetime-local" name="offer_end_date" class="pf-input" value="<?= htmlspecialchars($product['offer_end_date'] ?? '') ?>">
                    </div>
                </div>

                <div class="pf-group">
                    <label class="pf-label">Stock Quantity</label>
                    <input type="number" name="stock" class="pf-input" value="<?= htmlspecialchars($product['stock'] ?? '999') ?>" placeholder="999">
                </div>
            </div>

            <div class="pf-footer">
                <button type="button" class="pf-btn pf-btn-primary" onclick="switchTab('media')">Continue to Media &#8594;</button>
            </div>
        </div>

        <!-- ─── PANEL 2: MEDIA ─── -->
        <div class="pf-panel" id="panel-media">
            <!-- Primary Image -->
            <div class="pf-card">
                <div class="pf-card-title"><span class="icon">&#128444;</span> Primary Image</div>
                <?php if ($isEdit && !empty($product['image_url'])): ?>
                <div class="pf-preview-img">
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="Current">
                </div>
                <?php endif; ?>
                <div class="pf-upload" id="upload-image">
                    <input type="file" name="image" accept="image/*">
                    <div class="pf-upload-icon">&#128247;</div>
                    <div class="pf-upload-text"><strong>Click to upload</strong> or drag and drop<br>JPG, PNG, WebP &middot; Max 2MB</div>
                </div>
            </div>

            <!-- Cover Image -->
            <div class="pf-card">
                <div class="pf-card-title"><span class="icon">&#127748;</span> Cover Image <span style="font-weight:400;color:var(--muted);font-size:0.78rem;margin-left:auto;">Optional</span></div>
                <?php if ($isEdit && !empty($product['cover_image'])): ?>
                <div class="pf-preview-img">
                    <img src="<?= htmlspecialchars($product['cover_image']) ?>" alt="Cover" style="max-width:200px;max-height:120px;">
                </div>
                <?php endif; ?>
                <div class="pf-upload" id="upload-cover">
                    <input type="file" name="cover_image" accept="image/*">
                    <div class="pf-upload-icon">&#127749;</div>
                    <div class="pf-upload-text"><strong>Click to upload cover</strong><br>Wide/landscape format recommended &middot; Max 2MB</div>
                </div>
            </div>

            <!-- Screenshots -->
            <div class="pf-card">
                <div class="pf-card-title"><span class="icon">&#128248;</span> Screenshots <span style="font-weight:400;color:var(--muted);font-size:0.78rem;margin-left:auto;">Up to 6</span></div>
                <?php if ($isEdit && !empty($additional_images)): ?>
                <div class="pf-thumb-grid">
                    <?php foreach ($additional_images as $img): ?>
                    <div class="pf-thumb"><img src="<?= htmlspecialchars($img['image_url']) ?>" alt="Screenshot"></div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="pf-upload" id="upload-screenshots" style="margin-top:<?= ($isEdit && !empty($additional_images)) ? '12px' : '0' ?>;">
                    <input type="file" name="images[]" accept="image/*" multiple>
                    <div class="pf-upload-icon">&#128193;</div>
                    <div class="pf-upload-text"><strong>Click to upload screenshots</strong><br>Select multiple files &middot; Max 6, each up to 2MB</div>
                </div>
            </div>

            <div class="pf-footer">
                <button type="button" class="pf-btn pf-btn-ghost" onclick="switchTab('basic')">&#8592; Back</button>
                <button type="button" class="pf-btn pf-btn-primary" id="media-next" onclick="switchTab(document.querySelector('.pf-tab[data-tab=game].show') ? 'game' : 'publish')">Continue &#8594;</button>
            </div>
        </div>

        <!-- ─── PANEL 3: GAME SETTINGS ─── -->
        <div class="pf-panel" id="panel-game">
            <!-- Download Links -->
            <div class="pf-card">
                <div class="pf-card-title"><span class="icon">&#128229;</span> Download Links</div>
                <div id="downloads_container">
                    <?php for ($i = 0; $i < max(2, count($download_links)); $i++): ?>
                    <div class="pf-dl-row">
                        <div>
                            <div class="pf-dl-label">URL Link <?= $i + 1 ?></div>
                            <input type="text" name="download_links[]" class="pf-input" value="<?= htmlspecialchars($download_links[$i] ?? '') ?>" placeholder="https://example.com/download/game.zip">
                        </div>
                        <div>
                            <div class="pf-dl-label">Button Label</div>
                            <input type="text" name="download_link_labels[]" class="pf-input" value="<?= htmlspecialchars($download_labels[$i] ?? 'DOWNLOAD LINK ' . ($i + 1)) ?>" placeholder="e.g., Main File, Crack">
                        </div>
                        <button type="button" class="pf-dl-remove" onclick="removeDownloadLink(this)">&#10005;</button>
                    </div>
                    <?php endfor; ?>
                </div>
                <button type="button" class="pf-dl-add" onclick="addDownloadLink()">+ Add Download Link</button>
            </div>

            <!-- Video Links -->
            <div class="pf-card">
                <div class="pf-card-title"><span class="icon">&#127916;</span> Video Links</div>

                <div class="pf-group">
                    <label class="pf-label">Tutorial Video <span class="opt">(optional)</span></label>
                    <input type="url" name="tutorial_video_link" class="pf-input" value="<?= htmlspecialchars($product['tutorial_video_link'] ?? '') ?>" placeholder="https://youtube.com/watch?v=...">
                    <div class="pf-hint">Installation or gameplay guide video</div>
                </div>

                <div class="pf-row">
                    <div class="pf-group">
                        <label class="pf-label">Preview Video <span class="opt">(optional)</span></label>
                        <input type="url" name="preview_video_url" class="pf-input" value="<?= htmlspecialchars($product['preview_video_url'] ?? '') ?>" placeholder="https://example.com/preview.mp4">
                        <div class="pf-hint">Short clip for card hover preview (MP4 recommended)</div>
                    </div>
                    <div class="pf-group">
                        <label class="pf-label">Trailer Video <span class="opt">(optional)</span></label>
                        <input type="url" name="trailer_video_url" class="pf-input" value="<?= htmlspecialchars($product['trailer_video_url'] ?? '') ?>" placeholder="https://youtube.com/watch?v=...">
                        <div class="pf-hint">Full trailer shown on game detail page</div>
                    </div>
                </div>
            </div>

            <!-- Extra Info -->
            <div class="pf-card">
                <div class="pf-card-title"><span class="icon">&#9881;</span> Extra Details</div>
                <div class="pf-group">
                    <label class="pf-label">File Size <span class="opt">(optional)</span></label>
                    <input type="text" name="file_size" class="pf-input" value="<?= htmlspecialchars($product['file_size'] ?? '') ?>" placeholder="e.g. 2.5 GB" style="max-width:280px;">
                </div>

                <div class="pf-row">
                    <label class="pf-toggle">
                        <input type="checkbox" name="is_featured" value="1" <?= ($isEdit && ($product['is_featured'] ?? 0)) ? 'checked' : '' ?>>
                        <span class="pf-toggle-track"></span>
                        <div>
                            <div class="pf-toggle-label">Featured</div>
                            <div class="pf-toggle-desc">Show in hero slider on homepage</div>
                        </div>
                    </label>
                    <label class="pf-toggle">
                        <input type="checkbox" name="is_trending" value="1" <?= ($isEdit && ($product['is_trending'] ?? 0)) ? 'checked' : '' ?>>
                        <span class="pf-toggle-track"></span>
                        <div>
                            <div class="pf-toggle-label">Trending</div>
                            <div class="pf-toggle-desc">Show in trending section</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="pf-footer">
                <button type="button" class="pf-btn pf-btn-ghost" onclick="switchTab('media')">&#8592; Back</button>
                <button type="button" class="pf-btn pf-btn-primary" onclick="switchTab('publish')">Continue to Publish &#8594;</button>
            </div>
        </div>

        <!-- ─── PANEL 4: PUBLISH ─── -->
        <div class="pf-panel" id="panel-publish">
            <div class="pf-card">
                <div class="pf-card-title"><span class="icon">&#128640;</span> Ready to Publish</div>

                <label class="pf-toggle" style="margin-bottom:20px;">
                    <input type="checkbox" name="is_active" value="1" <?= $isEdit && ($product['is_active'] ?? 0) ? 'checked' : '' ?>>
                    <span class="pf-toggle-track"></span>
                    <div>
                        <div class="pf-toggle-label">Product is Active</div>
                        <div class="pf-toggle-desc">When active, product is visible to users</div>
                    </div>
                </label>

                <div style="padding:16px;border-radius:10px;background:rgba(65,209,255,0.04);border:1px solid rgba(65,209,255,0.12);font-size:0.82rem;color:var(--muted);line-height:1.6;">
                    Review all tabs before publishing. You can always come back to edit after saving.
                </div>
            </div>

            <div class="pf-footer">
                <button type="button" class="pf-btn pf-btn-ghost" onclick="switchTab(document.querySelector('.pf-tab[data-tab=game].show') ? 'game' : 'media')">&#8592; Back</button>
                <button type="submit" class="pf-btn pf-btn-primary">
                    <?= $isEdit ? '&#128190; Update Product' : '&#10133; Create Product' ?>
                </button>
                <a href="<?= htmlspecialchars($basePath ?? '/hasheem') ?>/admin/products" class="pf-btn pf-btn-ghost">Cancel</a>
            </div>
        </div>
    </form>
</div>

<script>
var tabOrder = ['basic', 'media', 'game', 'publish'];

function switchTab(name) {
    // Deactivate all tabs and panels
    document.querySelectorAll('.pf-tab').forEach(function(t){ t.classList.remove('active'); });
    document.querySelectorAll('.pf-panel').forEach(function(p){ p.classList.remove('active'); });

    // Activate selected
    var tab = document.querySelector('.pf-tab[data-tab="'+name+'"]');
    var panel = document.getElementById('panel-'+name);
    if (tab) tab.classList.add('active');
    if (panel) {
        panel.classList.add('active');
        // Re-trigger animation
        panel.style.animation = 'none';
        panel.offsetHeight; // reflow
        panel.style.animation = '';
    }

    // Update step indicators
    updateSteps(name);
}

function updateSteps(activeName) {
    var steps = document.querySelectorAll('.pf-step');
    var lines = document.querySelectorAll('.pf-step-line');
    var activeIdx = tabOrder.indexOf(activeName);
    var visibleIdx = 0;

    steps.forEach(function(step) {
        var sName = step.dataset.step;
        var sIdx = tabOrder.indexOf(sName);
        step.classList.remove('active','done');
        if (sIdx < activeIdx) step.classList.add('done');
        else if (sIdx === activeIdx) step.classList.add('active');
    });

    lines.forEach(function(line, i) {
        var visibleSteps = [];
        steps.forEach(function(s){ if (s.style.display !== 'none') visibleSteps.push(s); });
        if (i < activeIdx) line.classList.add('filled');
        else line.classList.remove('filled');
    });
}

function toggleGameType() {
    var isGame = document.getElementById('product_type').value === 'game';
    var gameTab = document.querySelector('.pf-tab[data-tab="game"]');
    var gameStep = document.getElementById('step-game');
    var line3 = document.getElementById('line-3');
    var gameTypeField = document.getElementById('game_type_field');
    var offerFields = document.getElementById('offer_fields');
    var priceField = document.getElementById('price_field');
    var priceInput = document.getElementById('price_input');
    var categoryInput = document.querySelector('select[name="category_id"]');

    // Update publish step number
    var publishSteps = document.querySelectorAll('.pf-step[data-step="publish"] .pf-step-dot');
    publishSteps.forEach(function(d){ d.textContent = isGame ? '4' : '3'; });

    if (isGame) {
        if (gameTab) gameTab.classList.add('show');
        if (gameStep) gameStep.style.display = 'flex';
        if (line3) line3.style.display = 'block';
        if (gameTypeField) gameTypeField.style.display = 'block';
        if (offerFields) offerFields.style.display = 'none';
        if (priceField) priceField.style.display = 'none';
        if (priceInput) { priceInput.value = '0'; priceInput.required = false; }
        if (categoryInput) categoryInput.required = false;
    } else {
        if (gameTab) gameTab.classList.remove('show');
        if (gameStep) gameStep.style.display = 'none';
        if (line3) line3.style.display = 'none';
        if (gameTypeField) gameTypeField.style.display = 'none';
        if (offerFields) offerFields.style.display = 'block';
        if (priceField) priceField.style.display = 'block';
        if (priceInput) priceInput.required = true;
        if (categoryInput) categoryInput.required = true;

        // If currently on game tab, switch away
        if (document.getElementById('panel-game').classList.contains('active')) {
            switchTab('media');
        }
    }
}

// Init on load
toggleGameType();

function addDownloadLink() {
    var container = document.getElementById('downloads_container');
    var count = container.querySelectorAll('.pf-dl-row').length + 1;
    var row = document.createElement('div');
    row.className = 'pf-dl-row';
    row.innerHTML = '<div><div class="pf-dl-label">URL Link '+count+'</div><input type="text" name="download_links[]" class="pf-input" placeholder="https://example.com/download/game.zip"></div><div><div class="pf-dl-label">Button Label</div><input type="text" name="download_link_labels[]" class="pf-input" value="DOWNLOAD LINK '+count+'" placeholder="e.g., Main File, Crack"></div><button type="button" class="pf-dl-remove" onclick="removeDownloadLink(this)">&#10005;</button>';
    container.appendChild(row);
}

function removeDownloadLink(btn) {
    var row = btn.closest('.pf-dl-row');
    row.style.opacity = '0';
    row.style.transform = 'translateX(20px)';
    row.style.transition = 'all 0.25s';
    setTimeout(function(){ row.remove(); }, 250);
}

// Drag-over effects for upload areas
document.querySelectorAll('.pf-upload').forEach(function(area) {
    area.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('dragover'); });
    area.addEventListener('dragleave', function() { this.classList.remove('dragover'); });
    area.addEventListener('drop', function() { this.classList.remove('dragover'); });
});

// Mouse parallax tilt on preview images
document.querySelectorAll('.pf-preview-img').forEach(function(el) {
    el.addEventListener('mousemove', function(e) {
        var rect = this.getBoundingClientRect();
        var x = (e.clientX - rect.left) / rect.width - 0.5;
        var y = (e.clientY - rect.top) / rect.height - 0.5;
        this.style.transform = 'perspective(600px) rotateY('+( x*8)+'deg) rotateX('+(y*-8)+'deg) scale(1.02)';
    });
    el.addEventListener('mouseleave', function() {
        this.style.transform = '';
    });
});

// Mouse glow follow effect on cards
document.querySelectorAll('.pf-card').forEach(function(card) {
    card.addEventListener('mousemove', function(e) {
        var rect = this.getBoundingClientRect();
        var x = e.clientX - rect.left;
        var y = e.clientY - rect.top;
        this.style.background = 'radial-gradient(300px circle at '+x+'px '+y+'px, rgba(65,209,255,0.04), transparent 60%), var(--card)';
    });
    card.addEventListener('mouseleave', function() {
        this.style.background = '';
    });
});
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php';
