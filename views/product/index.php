<?php
$title = 'Products - Jusgam';
$products = $products ?? [];
$csrf_token = $csrf_token ?? '';

$additionalStyles = <<<'CSS'
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
        }

        .search-bar {
            display: flex;
            gap: 10px;
            background: var(--bg-1);
            border: 1px solid var(--bg-2);
            padding: 12px 14px;
            border-radius: 10px;
            align-items: center;
            margin-bottom: 16px;
            width: 100%;
        }

        .search-bar input {
            background: transparent;
            border: none;
            color: var(--ink-0);
            width: 100%;
            font-size: 0.9rem;
            outline: none;
        }

        .search-pill {
            background: rgba(58, 242, 255, 0.2);
            border: 1px solid rgba(58, 242, 255, 0.4);
            color: var(--accent);
            font-size: 0.7rem;
            padding: 3px 6px;
            border-radius: 999px;
        }

        .cart-badge {
            position: relative;
            display: inline-block;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -10px;
            background: var(--accent);
            color: var(--bg-0);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .shop-header {
            text-align: center;
            margin: 2rem 0;
        }

        .section-title {
            font-family: 'Rajdhani', monospace;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--accent);
            text-transform: uppercase;
            margin: 1rem 0;
        }

        .section-description {
            color: var(--ink-2);
            font-size: 1.1rem;
        }

        .products-wrapper {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 2.5rem;
            margin: 2rem 0;
        }

        .filters {
            background: var(--bg-1);
            border: 1px solid var(--bg-2);
            border-radius: 10px;
            padding: 1.2rem;
            height: fit-content;
            width: 200px;
            font-size: 0.85rem;
        }

        .filter-title {
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .filter-group {
            margin-bottom: 1.2rem;
            padding-bottom: 1.2rem;
            border-bottom: 1px solid var(--bg-2);
        }

        .filter-group:last-child {
            border-bottom: none;
        }

        .filter-label {
            font-weight: 700;
            font-size: 0.75rem;
            margin-bottom: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--ink-1);
        }

        .filter-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .filter-option:hover {
            color: var(--accent);
        }

        .filter-option input {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1.2rem 1.4rem;
        }

        @media (max-width: 1200px) {
            .products-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .products-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {
            .products-grid {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
        }

        .product-card {
            background: rgba(12, 18, 32, 0.7);
            border: 1px solid rgba(58, 242, 255, 0.18);
            border-radius: 22px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
            position: relative;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.45), 0 0 18px rgba(58, 242, 255, 0.12);
            backdrop-filter: blur(10px);
        }

        .product-card:hover {
            border-color: rgba(58, 242, 255, 0.45);
            box-shadow: 0 22px 50px rgba(0, 0, 0, 0.55), 0 0 28px rgba(58, 242, 255, 0.25);
            transform: translateY(-6px);
        }

        .product-image {
            background: linear-gradient(135deg, var(--bg-0) 0%, var(--bg-2) 100%);
            height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            position: relative;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            overflow: hidden;
        }

        .product-image::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(58, 242, 255, 0.12), transparent 60%);
            pointer-events: none;
        }

        .product-image img {
            transition: transform 0.4s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.04);
        }

        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: linear-gradient(135deg, #ff4fd8, #ff3b4f);
            color: #fff;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.7rem;
            box-shadow: 0 0 12px rgba(255, 79, 216, 0.6);
        }

        .product-body {
            padding: 0.85rem;
            position: relative;
            min-height: 122px;
        }

        .product-title {
            font-weight: 700;
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
            line-height: 1.2;
            letter-spacing: 0.01em;
            color: #fff;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.4em;
        }

        .product-category {
            font-size: 0.72rem;
            color: #7dd3ff;
            margin-bottom: 0.7rem;
        }

        .product-desc {
            font-size: 0.72rem;
            color: #e6f7ff;
            margin-bottom: 0;
            line-height: 1.3;
            position: absolute;
            left: 0.85rem;
            right: 0.85rem;
            bottom: 3.2rem;
            background: rgba(7, 12, 20, 0.92);
            border: 1px solid rgba(58, 242, 255, 0.18);
            padding: 0.5rem 0.6rem;
            border-radius: 8px;
            opacity: 0;
            transform: translateY(6px);
            transition: opacity 0.2s ease, transform 0.2s ease;
            pointer-events: none;
        }

        .product-card:hover .product-desc {
            opacity: 1;
            transform: translateY(0);
        }

        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.7rem;
        }

        .product-rating {
            font-size: 0.74rem;
            color: var(--ink-1);
        }

        .product-price {
            color: var(--accent);
            font-weight: 700;
            font-size: 0.9rem;
        }

        .product-price del {
            color: #94a3b8;
            font-size: 0.78rem;
        }

        .product-price .offer-price {
            color: #22ff99;
            font-size: 0.82rem;
            text-shadow: 0 0 8px rgba(34, 255, 153, 0.5);
        }

        .btn-add {
            width: 100%;
            padding: 0.65rem;
            background: linear-gradient(135deg, #3af2ff, #2dd4ff);
            color: #001018;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            font-family: 'Rajdhani', monospace;
            box-shadow: 0 10px 20px rgba(45, 212, 255, 0.3);
        }

        .btn-add:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 0 22px rgba(58, 242, 255, 0.7);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 3rem 0;
        }

        @media (max-width: 1400px) {
            .products-grid {
                grid-template-columns: repeat(6, minmax(130px, 1fr));
            }
        }

        @media (max-width: 1024px) {
            .products-wrapper {
                grid-template-columns: 180px 1fr;
                gap: 2rem;
            }

            .filters {
                width: 180px;
            }

            .products-grid {
                grid-template-columns: repeat(3, minmax(160px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .products-wrapper {
                grid-template-columns: 1fr;
            }

            .filters {
                display: none;
            }

            .products-grid {
                grid-template-columns: repeat(2, minmax(150px, 1fr));
            }

        }
CSS;

include __DIR__ . '/../layouts/user-header.php';

$categories = [];
foreach ($products as $product) {
    $category = $product['category_name'] ?? 'Other';
    $categories[$category] = true;
}
ksort($categories);
?>

<main>
    <div class="shop-header">
        <h1 class="section-title">All Products</h1>
        <p class="section-description">Accessories and gear only. Filter by category or price.</p>
    </div>

    <div class="container">
        <div class="products-wrapper">
            <aside class="filters">
                <div class="filter-group">
                    <p class="filter-label">Search</p>
                    <div class="search-bar">
                        <span class="search-pill">CTRL + K</span>
                        <input id="product-search" type="text" placeholder="Search products...">
                    </div>
                </div>

                <div class="filter-group">
                    <p class="filter-label">Category</p>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <?php foreach ($categories as $category => $value): ?>
                            <label class="filter-option">
                                <input type="checkbox" class="filter-category" value="<?= htmlspecialchars($category) ?>">
                                <span><?= htmlspecialchars($category) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-group">
                    <p class="filter-label">Price Range</p>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label class="filter-option">
                            <input type="radio" name="price" class="filter-price" value="all" checked>
                            <span>All Prices</span>
                        </label>
                        <label class="filter-option">
                            <input type="radio" name="price" class="filter-price" value="0-50000">
                            <span>TZS 0 - TZS 50,000</span>
                        </label>
                        <label class="filter-option">
                            <input type="radio" name="price" class="filter-price" value="50001-100000">
                            <span>TZS 50,001 - TZS 100,000</span>
                        </label>
                        <label class="filter-option">
                            <input type="radio" name="price" class="filter-price" value="100001+">
                            <span>TZS 100,001+</span>
                        </label>
                    </div>
                </div>
            </aside>

            <div>
                <div class="products-grid" id="products-grid">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <a href="/hasheem/product/<?= (int)$product['id'] ?>" style="text-decoration: none; color: inherit;">
                                <div class="product-card" data-name="<?= htmlspecialchars(strtolower($product['name'] ?? '')) ?>" data-category="<?= htmlspecialchars(strtolower($product['category_name'] ?? 'other')) ?>" data-price="<?= (int)$product['price'] ?>">
                                    <div class="product-image">
                                        <?php if (!empty($product['image_url'])): ?>
                                            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            🛠️
                                        <?php endif; ?>
                                        <?php if (($product['offer_percent'] ?? 0) > 0): ?>
                                            <div class="product-badge">-<?= (int)$product['offer_percent'] ?>%</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-body">
                                        <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                                        <p class="product-category"><?= htmlspecialchars($product['category_name'] ?? 'Product') ?></p>
                                        <p class="product-desc">
                                            <?= htmlspecialchars(substr($product['description'] ?? '', 0, 50)) ?>...
                                        </p>
                                        <div class="product-footer">
                                            <span class="product-price">
                                                <?php if (($product['product_type'] ?? 'accessory') === 'game'): ?>
                                                    🎬 Unlock via Ad
                                                <?php elseif (($product['offer_percent'] ?? 0) > 0): ?>
                                                    TZS <del><?= number_format($product['price'], 2) ?></del>
                                                    <span class="offer-price">→ TZS <?= number_format($product['price'] * (1 - (int)($product['offer_percent'] ?? 0) / 100), 2) ?></span>
                                                <?php else: ?>
                                                    TZS <?= number_format($product['price'], 2) ?>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <form method="POST" action="/hasheem/product/add-to-cart" onclick="event.stopPropagation();">
                                            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn-add" <?php if (($product['stock'] ?? 0) <= 0): ?>disabled<?php endif; ?>>
                                                <?php if (($product['stock'] ?? 0) > 0): ?>
                                                    🛒 Add to Cart
                                                <?php else: ?>
                                                    Out of Stock
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--ink-2);">
                            <p>No products available</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const searchInput = document.getElementById('product-search');
    const categoryInputs = Array.from(document.querySelectorAll('.filter-category'));
    const priceInputs = Array.from(document.querySelectorAll('.filter-price'));
    const productCards = Array.from(document.querySelectorAll('#products-grid .product-card'));

    const applyFilters = () => {
        const query = (searchInput.value || '').trim().toLowerCase();
        const activeCategories = categoryInputs
            .filter(input => input.checked)
            .map(input => input.value.toLowerCase());
        const activePrice = (priceInputs.find(input => input.checked) || {}).value || 'all';

        productCards.forEach(card => {
            const name = card.dataset.name || '';
            const category = card.dataset.category || '';
            const price = parseInt(card.dataset.price || '0', 10);

            const matchesSearch = !query || name.includes(query);
            const matchesCategory = activeCategories.length === 0 || activeCategories.includes(category);
            let matchesPrice = true;

            if (activePrice === '0-50000') {
                matchesPrice = price <= 50000;
            } else if (activePrice === '50001-100000') {
                matchesPrice = price >= 50001 && price <= 100000;
            } else if (activePrice === '100001+') {
                matchesPrice = price >= 100001;
            }

            const show = matchesSearch && matchesCategory && matchesPrice;
            card.parentElement.style.display = show ? '' : 'none';
        });
    };

    searchInput.addEventListener('input', applyFilters);
    categoryInputs.forEach(input => input.addEventListener('change', applyFilters));
    priceInputs.forEach(input => input.addEventListener('change', applyFilters));

    document.addEventListener('keydown', event => {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            searchInput.focus();
        }
    });
</script>

<?php include __DIR__ . '/../layouts/user-footer.php'; ?>