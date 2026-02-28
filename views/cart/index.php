<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$title = 'Shopping Cart - Jusgam';
include __DIR__ . '/../layouts/user-header.php';
?>

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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Sora', sans-serif;
            background: linear-gradient(135deg, var(--bg-0) 0%, var(--bg-1) 50%, var(--bg-0) 100%);
            color: var(--ink-0);
            min-height: 100vh;
        }
        header {
            background: rgba(6, 10, 18, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--bg-2);
            padding: 1.5rem 2rem;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo { font-family: 'Rajdhani', monospace; font-size: 1.8rem; font-weight: 700; color: var(--accent); text-decoration: none; }
        nav { display: flex; gap: 2rem; }
        nav a { color: var(--ink-0); text-decoration: none; transition: color 0.3s; font-weight: 500; }
        nav a:hover { color: var(--accent); }
        .container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        h1 { font-size: 2.5rem; font-weight: 700; color: var(--accent); text-transform: uppercase; margin: 2rem 0; text-align: center; }
        .cart-grid { display: grid; grid-template-columns: 1fr 400px; gap: 2rem; }
        .cart-items { background: var(--bg-1); border: 1px solid var(--bg-2); border-radius: 12px; overflow: hidden; }
        .cart-item { display: grid; grid-template-columns: 100px 1fr auto; gap: 2rem; padding: 1.5rem; border-bottom: 1px solid var(--bg-2); align-items: center; }
        .item-icon { font-size: 3rem; text-align: center; }
        .item-details h4 { font-weight: 700; margin-bottom: 0.5rem; }
        .item-category { font-size: 0.85rem; color: var(--ink-2); }
        .item-price { color: var(--accent); font-weight: 700; }
        .btn-remove { background: #ff6464; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-weight: 700; }
        .summary-card { background: var(--bg-1); border: 1px solid var(--bg-2); border-radius: 12px; padding: 2rem; height: fit-content; position: sticky; top: 20px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--bg-2); }
        .summary-total { display: flex; justify-content: space-between; font-weight: 700; font-size: 1.3rem; color: var(--accent); margin: 1.5rem 0; padding: 1rem 0; border-top: 2px solid var(--accent); border-bottom: 2px solid var(--accent); }
        .btn-checkout { width: 100%; padding: 1rem; background: var(--accent); color: var(--bg-0); border: none; border-radius: 6px; font-weight: 700; cursor: pointer; margin-top: 1.5rem; text-decoration: none; display: block; text-align: center; }
        .btn-continue { width: 100%; padding: 0.8rem; background: transparent; color: var(--accent); border: 2px solid var(--accent); border-radius: 6px; font-weight: 700; cursor: pointer; margin-top: 1rem; text-decoration: none; display: block; text-align: center; }
        .empty-cart { text-align: center; padding: 3rem; }
        .empty-cart a { display: inline-block; background: var(--accent); color: var(--bg-0); padding: 0.8rem 2rem; border-radius: 6px; text-decoration: none; font-weight: 700; margin-top: 1rem; }
        
        /* FOOTER */
        footer {
            background: linear-gradient(180deg, rgba(15, 20, 25, 0.6) 0%, rgba(10, 14, 25, 0.8) 100%);
            border-top: 1px solid var(--bg-2);
            padding: 60px 0 30px;
            margin-top: 60px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-content > div {
            animation: slideUp 0.6s ease forwards;
            opacity: 0;
        }

        .footer-content > div:nth-child(1) { animation-delay: 0.1s; }
        .footer-content > div:nth-child(2) { animation-delay: 0.2s; }
        .footer-content > div:nth-child(3) { animation-delay: 0.3s; }
        .footer-content > div:nth-child(4) { animation-delay: 0.4s; }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        footer h3 {
            color: var(--accent);
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 0 10px rgba(58, 242, 255, 0.3);
        }

        footer a {
            color: var(--ink-1);
            text-decoration: none;
            font-size: 0.95rem;
            display: block;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            padding-left: 10px;
            border-left: 2px solid transparent;
        }

        footer a:hover {
            color: var(--accent);
            border-left-color: var(--accent);
            padding-left: 15px;
        }

        .footer-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
            margin: 30px 0;
            animation: slideLeft 0.8s ease forwards;
            opacity: 0;
            animation-delay: 0.45s;
        }

        @keyframes slideLeft {
            from {
                opacity: 0;
                transform: scaleX(0);
            }
            to {
                opacity: 1;
                transform: scaleX(1);
            }
        }

        .footer-bottom {
            text-align: center;
            color: var(--ink-2);
            font-size: 0.9rem;
            padding-top: 20px;
            border-top: 1px solid var(--bg-2);
            animation: fadeIn 0.6s ease forwards;
            opacity: 0;
            animation-delay: 0.7s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .footer-socials {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 15px;
        }

        .footer-socials a {
            display: inline-flex;
            width: 40px;
            height: 40px;
            align-items: center;
            justify-content: center;
            background: var(--bg-2);
            border-radius: 50%;
            color: var(--accent);
            font-size: 1.2rem;
            border: 1px solid var(--accent);
            margin: 0;
            padding: 0;
            transition: all 0.3s ease;
            animation: scaleIn 0.5s ease forwards;
            opacity: 0;
        }

        .footer-socials a:nth-child(1) { animation-delay: 0.5s; }
        .footer-socials a:nth-child(2) { animation-delay: 0.55s; }
        .footer-socials a:nth-child(3) { animation-delay: 0.6s; }
        .footer-socials a:nth-child(4) { animation-delay: 0.65s; }

        .footer-socials a:hover {
            background: var(--accent);
            color: var(--bg-0);
            border-left: none;
            padding: 0;
            transform: translateY(-3px) scale(1.15);
            box-shadow: 0 10px 30px rgba(58, 242, 255, 0.3);
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.5);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>

    <main>
        <h1>🛒 Shopping Cart</h1>
        <div class="container">
            <?php if (!empty($items)): ?>
                <div class="cart-grid">
                    <div class="cart-items">
                        <?php foreach ($items as $productId => $item): ?>
                            <div class="cart-item">
                                <div class="item-icon">🎮</div>
                                <div class="item-details">
                                    <h4><?= htmlspecialchars($item['name'] ?? 'Product') ?></h4>
                                    <div class="item-category"><?= htmlspecialchars($item['category'] ?? 'Item') ?></div>
                                    <div class="item-price">TZS <?= number_format($item['price'] ?? 0, 2) ?> × <?= $item['quantity'] ?></div>
                                </div>
                                <div class="item-actions">
                                    <form method="POST" action="/hasheem/cart/remove" style="margin: 0;">
                                        <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                        <input type="hidden" name="product_id" value="<?= intval($productId) ?>">
                                        <button type="submit" class="btn-remove">Remove</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-card">
                        <div style="font-weight: 700; margin-bottom: 1.5rem; font-size: 1.2rem;">Order Summary</div>
                        
                        <div class="summary-row">
                            <span>Items:</span>
                            <span><?= count($items) ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Quantity:</span>
                            <span><?php $qty = 0; foreach($items as $item) $qty += $item['quantity']; echo $qty; ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span>FREE</span>
                        </div>
                        
                        <?php $total = 0; foreach($items as $item) $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 0); ?>
                        
                        <div class="summary-total">
                            <span>Total:</span>
                            <span>TZS <?= number_format($total, 2) ?></span>
                        </div>

                        <?php if ($_SESSION['user'] ?? null): ?>
                            <a href="/hasheem/checkout" class="btn-checkout">🔥 Checkout Now</a>
                        <?php else: ?>
                            <a href="/hasheem/login" class="btn-checkout">Login to Checkout</a>
                        <?php endif; ?>
                        
                        <a href="/hasheem/products" class="btn-continue">Continue Shopping</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="cart-items" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
                    <div class="empty-cart">
                        <div style="font-size: 5rem; margin-bottom: 1rem;">🛒</div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 1rem;">Your cart is empty</h3>
                        <p style="color: var(--ink-2); margin-bottom: 1rem;">Time to add some amazing games!</p>
                        <a href="/hasheem/products">🛍️ Start Shopping</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/../layouts/user-footer.php'; ?>
    <footer>
        <div class="container">
            <div class="footer-content">
                <div>
                    <h3>GAMES</h3>
                    <a href="/hasheem/">📱 MOBILE GAMES</a>
                    <a href="/hasheem/">☁️ CLOUD GAMES</a>
                    <a href="/hasheem/">💻 PC GAMES</a>
                    <a href="/hasheem/">🛠️ ACCESSORIES</a>
                </div>
                <div>
                    <h3>MENU</h3>
                    <a href="/hasheem/">HOME</a>
                    <a href="/hasheem/products">PRODUCT</a>
                    <a href="/hasheem/cart">CART</a>
                    <a href="/hasheem/orders">ORDERS</a>
                </div>
                <div>
                    <h3>📞 WASILIANA NASI</h3>
                    <a href="tel:+255621215237">☎️ +255 621 215237</a>
                    <a href="https://wa.me/255621215237" target="_blank">💬 WhatsApp</a>
                    <a href="mailto:support@jusgam.local">📧 Email</a>
                    <a href="#">🌐 Facebook</a>
                </div>
                <div>
                    <h3>NEWS</h3>
                    <a href="#">ABOUT US</a>
                    <a href="#">🔒 Privacy Policy</a>
                    <a href="#">⚖️ Terms & Conditions</a>
                    <a href="#">📋 FAQ</a>
                </div>
            </div>

            <div class="footer-divider"></div>

            <div class="footer-bottom">
                <p>© 2026 <strong style="color: var(--accent); text-shadow: 0 0 10px rgba(58,242,255,0.3);">JUSGAM</strong> • Tanzania 🇹🇿 • All Rights Reserved</p>
                <p style="margin-top: 10px; font-size: 0.85rem; color: var(--ink-2);"><a href="https://www.jointasoft.com" target="_blank">Build With Jointasoft</a></p>
                <div class="footer-socials">
                    <a href="#" title="Facebook">f</a>
                    <a href="#" title="Twitter">𝕏</a>
                    <a href="#" title="Instagram">📷</a>
                    <a href="#" title="TikTok">🎵</a>
                </div>
            </div>
        </div>
    </footer>
