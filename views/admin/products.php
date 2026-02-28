<?php ob_start(); ?>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <?php
    $totalGames = count(array_filter($products, fn($p) => $p['product_type'] === 'game'));
    $totalAccessories = count(array_filter($products, fn($p) => $p['product_type'] === 'accessory'));
    $activeProducts = count(array_filter($products, fn($p) => $p['is_active'] == 1));
    $inactiveProducts = count($products) - $activeProducts;
    ?>
    
    <div class="stat-card" style="background: linear-gradient(135deg, rgba(58, 242, 255, 0.15) 0%, rgba(58, 242, 255, 0.05) 100%); border: 1px solid rgba(58, 242, 255, 0.3);">
        <div class="stat-icon" style="color: #3af2ff; font-size: 2rem;">🎮</div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #3af2ff;"><?= $totalGames ?></div>
        <div class="stat-label" style="color: var(--muted); font-size: 0.9rem;">Total Games</div>
    </div>
    
    <div class="stat-card" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(34, 197, 94, 0.05) 100%); border: 1px solid rgba(34, 197, 94, 0.3);">
        <div class="stat-icon" style="color: #22c55e; font-size: 2rem;">🛍️</div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #22c55e;"><?= $totalAccessories ?></div>
        <div class="stat-label" style="color: var(--muted); font-size: 0.9rem;">Accessories</div>
    </div>
    
    <div class="stat-card" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(34, 197, 94, 0.05) 100%); border: 1px solid rgba(34, 197, 94, 0.3);">
        <div class="stat-icon" style="color: #22c55e; font-size: 2rem;">✓</div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #22c55e;"><?= $activeProducts ?></div>
        <div class="stat-label" style="color: var(--muted); font-size: 0.9rem;">Active Products</div>
    </div>
    
    <div class="stat-card" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(239, 68, 68, 0.05) 100%); border: 1px solid rgba(239, 68, 68, 0.3);">
        <div class="stat-icon" style="color: #ef4444; font-size: 2rem;">✗</div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #ef4444;"><?= $inactiveProducts ?></div>
        <div class="stat-label" style="color: var(--muted); font-size: 0.9rem;">Inactive</div>
    </div>
</div>

<!-- Header Section -->
<div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.75rem; font-weight: 700; margin: 0; background: linear-gradient(135deg, #3af2ff 0%, #22c55e 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            🎮 Games & Products Management
        </h2>
        <p style="color: var(--muted); margin: 0.5rem 0 0 0; font-size: 0.9rem;">Manage your gaming inventory and accessories</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <a href="<?= $basePath ?>/admin/products/create?type=game" class="modern-btn btn-game">
            <span style="font-size: 1.25rem;">🎮</span>
            <span>Add Game</span>
        </a>
        <a href="<?= $basePath ?>/admin/products/create?type=accessory" class="modern-btn btn-accessory">
            <span style="font-size: 1.25rem;">🛍️</span>
            <span>Add Accessory</span>
        </a>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="filter-bar" style="background: var(--card); border: 1px solid var(--line); border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
    <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 250px;">
            <input type="text" id="searchInput" placeholder="🔍 Search products..." 
                   style="width: 100%; padding: 0.875rem 1.25rem; background: var(--bg-2); border: 1px solid var(--line); border-radius: 12px; color: var(--text); font-size: 0.95rem; transition: all 0.3s ease;">
        </div>
        <select id="typeFilter" style="padding: 0.875rem 1.25rem; background: var(--bg-2); border: 1px solid var(--line); border-radius: 12px; color: var(--text); font-size: 0.95rem; cursor: pointer;">
            <option value="">All Types</option>
            <option value="game">🎮 Games Only</option>
            <option value="accessory">🛍️ Accessories Only</option>
        </select>
        <select id="statusFilter" style="padding: 0.875rem 1.25rem; background: var(--bg-2); border: 1px solid var(--line); border-radius: 12px; color: var(--text); font-size: 0.95rem; cursor: pointer;">
            <option value="">All Status</option>
            <option value="active">✓ Active Only</option>
            <option value="inactive">✗ Inactive Only</option>
        </select>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="success-alert" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(34, 197, 94, 0.05) 100%); border: 2px solid rgba(34, 197, 94, 0.4); color: #22c55e; padding: 1.25rem 1.5rem; border-radius: 14px; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 15px rgba(34, 197, 94, 0.1);">
    <span style="font-size: 1.5rem;">✅</span>
    <span style="font-weight: 600;"><?= htmlspecialchars($_GET['success']) ?></span>
</div>
<?php endif; ?>

<!-- Products Table -->
<div class="modern-table-container" style="background: var(--card); border: 1px solid var(--line); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
    <table id="productsTable" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: linear-gradient(135deg, rgba(58, 242, 255, 0.1) 0%, rgba(34, 197, 94, 0.1) 100%); border-bottom: 2px solid var(--line);">
                <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 700; color: var(--text); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    🆔 ID
                </th>
                <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 700; color: var(--text); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    📝 Product
                </th>
                <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 700; color: var(--text); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    📂 Type
                </th>
                <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 700; color: var(--text); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    🎯 Platform
                </th>
                <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 700; color: var(--text); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    💵 Price
                </th>
                <th style="padding: 1.25rem 1.5rem; text-align: center; font-weight: 700; color: var(--text); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    📦 Stock
                </th>
                <th style="padding: 1.25rem 1.5rem; text-align: center; font-weight: 700; color: var(--text); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    📊 Status
                </th>
                <th style="padding: 1.25rem 1.5rem; text-align: center; font-weight: 700; color: var(--text); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    ⚙️ Actions
                </th>
            </tr>
        </thead>
        <tbody id="productsTableBody">
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 3rem;">
                        <div style="color: var(--muted); font-size: 1.1rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
                            <div>No products found.</div>
                            <a href="<?= $basePath ?>/admin/products/create" style="color: var(--accent); text-decoration: none; font-weight: 600; margin-top: 0.5rem; display: inline-block;">
                                Create one now →
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            <?php foreach ($products as $product): ?>
                <tr class="product-row" 
                    data-type="<?= strtolower($product['product_type']) ?>" 
                    data-status="<?= $product['is_active'] ? 'active' : 'inactive' ?>"
                    style="border-bottom: 1px solid var(--line); transition: all 0.3s ease;">
                    
                    <td style="padding: 1.25rem 1.5rem; color: var(--muted); font-weight: 600; font-size: 0.9rem;">
                        #<?= str_pad($product['id'], 3, '0', STR_PAD_LEFT) ?>
                    </td>
                    
                    <td style="padding: 1.25rem 1.5rem;">
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <?php if (!empty($product['image_url'])): ?>
                                <div style="position: relative; width: 50px; height: 50px; border-radius: 10px; overflow: hidden; background: var(--bg-2); border: 2px solid var(--line);">
                                    <img src="<?= htmlspecialchars($product['image_url']) ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; border-radius: 10px; background: var(--bg-2); border: 2px solid var(--line); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                    <?= $product['product_type'] === 'game' ? '🎮' : '🛍️' ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div style="font-weight: 700; color: var(--text); font-size: 1rem; margin-bottom: 0.25rem;">
                                    <?= htmlspecialchars($product['name']) ?>
                                </div>
                                <div style="font-size: 0.85rem; color: var(--muted);">
                                    <?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    
                    <td style="padding: 1.25rem 1.5rem;">
                        <?php if ($product['product_type'] === 'game'): ?>
                            <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, rgba(58, 242, 255, 0.2) 0%, rgba(58, 242, 255, 0.1) 100%); border: 1px solid rgba(58, 242, 255, 0.4); color: #3af2ff; border-radius: 8px; font-weight: 600; font-size: 0.85rem;">
                                🎮 GAME
                            </span>
                        <?php else: ?>
                            <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, rgba(34, 197, 94, 0.2) 0%, rgba(34, 197, 94, 0.1) 100%); border: 1px solid rgba(34, 197, 94, 0.4); color: #22c55e; border-radius: 8px; font-weight: 600; font-size: 0.85rem;">
                                🛍️ ACCESSORY
                            </span>
                        <?php endif; ?>
                    </td>
                    
                    <td style="padding: 1.25rem 1.5rem;">
                        <span style="padding: 0.4rem 0.875rem; background: var(--bg-2); border: 1px solid var(--line); color: var(--text); border-radius: 6px; font-weight: 500; font-size: 0.85rem;">
                            <?= htmlspecialchars($product['platform'] ?? 'N/A') ?>
                        </span>
                    </td>
                    
                    <td style="padding: 1.25rem 1.5rem;">
                        <?php if (($product['product_type'] ?? 'accessory') === 'game'): ?>
                            <span style="font-weight: 700; color: #3af2ff; font-size: 0.95rem;">AD UNLOCK</span>
                        <?php else: ?>
                            <span style="font-weight: 700; color: var(--text); font-size: 1rem;">
                                TZS <?= number_format($product['price']) ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    
                    <td style="padding: 1.25rem 1.5rem; text-align: center;">
                        <?php
                        $stockClass = $product['stock'] > 10 ? 'high' : ($product['stock'] > 0 ? 'low' : 'out');
                        $stockColor = $product['stock'] > 10 ? '#22c55e' : ($product['stock'] > 0 ? '#f59e0b' : '#ef4444');
                        ?>
                        <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 50px; padding: 0.5rem 0.875rem; background: <?= $stockColor ?>20; border: 1px solid <?= $stockColor ?>; color: <?= $stockColor ?>; border-radius: 8px; font-weight: 700; font-size: 0.95rem;">
                            <?= htmlspecialchars($product['stock']) ?>
                        </span>
                    </td>
                    
                    <td style="padding: 1.25rem 1.5rem; text-align: center;">
                        <?php if ($product['is_active']): ?>
                            <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, rgba(34, 197, 94, 0.2) 0%, rgba(34, 197, 94, 0.1) 100%); border: 1px solid rgba(34, 197, 94, 0.4); color: #22c55e; border-radius: 8px; font-weight: 600; font-size: 0.85rem;">
                                ✓ Active
                            </span>
                        <?php else: ?>
                            <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(239, 68, 68, 0.1) 100%); border: 1px solid rgba(239, 68, 68, 0.4); color: #ef4444; border-radius: 8px; font-weight: 600; font-size: 0.85rem;">
                                ✗ Inactive
                            </span>
                        <?php endif; ?>
                    </td>
                    
                    <td style="padding: 1.25rem 1.5rem;">
                        <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                            <a href="<?= $basePath ?>/admin/products/edit/<?= $product['id'] ?>" 
                               class="action-btn edit-btn"
                               title="Edit Product">
                                ✏️
                            </a>
                            
                            <?php if ($product['is_active']): ?>
                                <form method="POST" action="<?= $basePath ?>/admin/products/deactivate/<?= $product['id'] ?>" style="display: inline;" onsubmit="return confirm('Deactivate this product?');">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <button type="submit" class="action-btn deactivate-btn" title="Deactivate">
                                        🔴
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="<?= $basePath ?>/admin/products/activate/<?= $product['id'] ?>" style="display: inline;">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <button type="submit" class="action-btn activate-btn" title="Activate">
                                        🟢
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <form method="POST" action="<?= $basePath ?>/admin/products/delete/<?= $product['id'] ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                <button type="submit" class="action-btn delete-btn" title="Delete">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
    .stat-card {
        padding: 1.5rem;
        border-radius: 16px;
        transition: all 0.3s ease;
        cursor: default;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .modern-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.875rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .btn-game {
        background: linear-gradient(135deg, #3af2ff 0%, #22c55e 100%);
        color: #000;
        box-shadow: 0 4px 15px rgba(58, 242, 255, 0.3);
    }
    
    .btn-game:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(58, 242, 255, 0.4);
    }
    
    .btn-accessory {
        background: var(--card);
        border-color: #22c55e;
        color: #22c55e;
    }
    
    .btn-accessory:hover {
        background: rgba(34, 197, 94, 0.1);
        transform: translateY(-2px);
    }
    
    #searchInput:focus,
    #typeFilter:focus,
    #statusFilter:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(58, 242, 255, 0.1);
    }
    
    .product-row:hover {
        background: rgba(58, 242, 255, 0.05);
    }
    
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        background: var(--bg-2);
        text-decoration: none;
    }
    
    .action-btn:hover {
        transform: scale(1.1);
    }
    
    .edit-btn:hover {
        background: rgba(58, 242, 255, 0.2);
        box-shadow: 0 0 8px rgba(58, 242, 255, 0.3);
    }
    
    .activate-btn:hover {
        background: rgba(34, 197, 94, 0.2);
        box-shadow: 0 0 8px rgba(34, 197, 94, 0.3);
    }
    
    .deactivate-btn:hover {
        background: rgba(239, 68, 68, 0.2);
        box-shadow: 0 0 8px rgba(239, 68, 68, 0.3);
    }
    
    .delete-btn:hover {
        background: rgba(239, 68, 68, 0.3);
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.4);
        transform: scale(1.15);
    }
    
    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .filter-bar > div {
            flex-direction: column;
        }
        
        .filter-bar > div > * {
            width: 100% !important;
        }
    }
</style>

<script>
    // Search and filter functionality
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const statusFilter = document.getElementById('statusFilter');
    const productRows = document.querySelectorAll('.product-row');
    
    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const typeValue = typeFilter.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        
        let visibleCount = 0;
        
        productRows.forEach(row => {
            const productName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const productType = row.dataset.type;
            const productStatus = row.dataset.status;
            
            const matchesSearch = productName.includes(searchTerm);
            const matchesType = !typeValue || productType === typeValue;
            const matchesStatus = !statusValue || productStatus === statusValue;
            
            if (matchesSearch && matchesType && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show/hide empty message
        const tbody = document.getElementById('productsTableBody');
        const emptyRow = tbody.querySelector('tr[colspan]');
        
        if (visibleCount === 0 && productRows.length > 0) {
            if (!emptyRow) {
                const noResultsRow = document.createElement('tr');
                noResultsRow.innerHTML = `
                    <td colspan="8" style="text-align: center; padding: 3rem;">
                        <div style="color: var(--muted); font-size: 1.1rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
                            <div>No products match your filters</div>
                            <button onclick="clearFilters()" style="margin-top: 1rem; padding: 0.75rem 1.5rem; background: var(--accent); color: var(--bg-0); border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                                Clear Filters
                            </button>
                        </div>
                    </td>
                `;
                noResultsRow.id = 'noResults';
                tbody.appendChild(noResultsRow);
            }
        } else {
            const noResults = document.getElementById('noResults');
            if (noResults) noResults.remove();
        }
    }
    
    function clearFilters() {
        searchInput.value = '';
        typeFilter.value = '';
        statusFilter.value = '';
        filterProducts();
    }
    
    // Event listeners
    searchInput.addEventListener('input', filterProducts);
    typeFilter.addEventListener('change', filterProducts);
    statusFilter.addEventListener('change', filterProducts);
    
    // Auto-hide success alert after 5 seconds
    const successAlert = document.querySelector('.success-alert');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            successAlert.style.opacity = '0';
            successAlert.style.transform = 'translateY(-20px)';
            setTimeout(() => successAlert.remove(), 500);
        }, 5000);
    }
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php';