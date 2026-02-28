<?php ob_start(); ?>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <?php
    $totalCategories = count($categories);
    $categoriesWithProducts = count(array_filter($categories, fn($c) => $c['product_count'] > 0));
    $emptyCategories = $totalCategories - $categoriesWithProducts;
    $totalProducts = array_sum(array_column($categories, 'product_count'));
    ?>
    
    <div class="stat-card" style="background: linear-gradient(135deg, rgba(168, 85, 247, 0.15) 0%, rgba(168, 85, 247, 0.05) 100%); border: 1px solid rgba(168, 85, 247, 0.3);">
        <div class="stat-icon" style="color: #a855f7; font-size: 2rem;">📂</div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #a855f7;"><?= $totalCategories ?></div>
        <div class="stat-label" style="color: var(--muted); font-size: 0.9rem;">Total Categories</div>
    </div>
    
    <div class="stat-card" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(34, 197, 94, 0.05) 100%); border: 1px solid rgba(34, 197, 94, 0.3);">
        <div class="stat-icon" style="color: #22c55e; font-size: 2rem;">✓</div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #22c55e;"><?= $categoriesWithProducts ?></div>
        <div class="stat-label" style="color: var(--muted); font-size: 0.9rem;">With Products</div>
    </div>
    
    <div class="stat-card" style="background: linear-gradient(135deg, rgba(234, 179, 8, 0.15) 0%, rgba(234, 179, 8, 0.05) 100%); border: 1px solid rgba(234, 179, 8, 0.3);">
        <div class="stat-icon" style="color: #eab308; font-size: 2rem;">📭</div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #eab308;"><?= $emptyCategories ?></div>
        <div class="stat-label" style="color: var(--muted); font-size: 0.9rem;">Empty Categories</div>
    </div>
    
    <div class="stat-card" style="background: linear-gradient(135deg, rgba(58, 242, 255, 0.15) 0%, rgba(58, 242, 255, 0.05) 100%); border: 1px solid rgba(58, 242, 255, 0.3);">
        <div class="stat-icon" style="color: #3af2ff; font-size: 2rem;">📦</div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: #3af2ff;"><?= $totalProducts ?></div>
        <div class="stat-label" style="color: var(--muted); font-size: 0.9rem;">Total Products</div>
    </div>
</div>

<!-- Header Section -->
<div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1.75rem; font-weight: 700; margin: 0; background: linear-gradient(135deg, #a855f7 0%, #3af2ff 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            📂 Category Management
        </h2>
        <p style="color: var(--muted); margin: 0.5rem 0 0 0; font-size: 0.9rem;">Organize your products into categories</p>
    </div>
    <div>
        <a href="<?= $basePath ?>/admin/categories/create" class="modern-btn" style="background: linear-gradient(135deg, #a855f7 0%, #3af2ff 100%); color: #000; box-shadow: 0 4px 15px rgba(168, 85, 247, 0.3);">
            <span style="font-size: 1.25rem;">➕</span>
            <span>Add Category</span>
        </a>
    </div>
</div>

<!-- Search Bar -->
<div class="filter-bar" style="background: var(--card); border: 1px solid var(--line); border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
    <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 250px;">
            <input type="text" id="searchInput" placeholder="🔍 Search categories..." 
                   style="width: 100%; padding: 0.875rem 1.25rem; background: var(--bg-2); border: 1px solid var(--line); border-radius: 12px; color: var(--text); font-size: 0.95rem; transition: all 0.3s ease;">
        </div>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="success-alert" style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.15) 0%, rgba(34, 197, 94, 0.05) 100%); border: 2px solid rgba(34, 197, 94, 0.4); color: #22c55e; padding: 1.25rem 1.5rem; border-radius: 14px; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 15px rgba(34, 197, 94, 0.1);">
    <span style="font-size: 1.5rem;">✅</span>
    <span style="font-weight: 600;"><?= htmlspecialchars($_GET['success']) ?></span>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="error-alert" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(239, 68, 68, 0.05) 100%); border: 2px solid rgba(239, 68, 68, 0.4); color: #ef4444; padding: 1.25rem 1.5rem; border-radius: 14px; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.1);">
    <span style="font-size: 1.5rem;">❌</span>
    <span style="font-weight: 600;"><?= htmlspecialchars($_GET['error']) ?></span>
</div>
<?php endif; ?>

<!-- Categories Table -->
<div class="modern-table-container" style="background: var(--card); border: 1px solid var(--line); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
    <table id="categoriesTable" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: linear-gradient(135deg, rgba(168, 85, 247, 0.1) 0%, rgba(58, 242, 255, 0.1) 100%); border-bottom: 2px solid var(--line);">
                <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 700; color: var(--text); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    🆔 ID
                </th>
                <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 700; color: var(--text); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    📝 Category Name
                </th>
                <th style="padding: 1.25rem 1.5rem; text-align: left; font-weight: 700; color: var(--text); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    🔗 Slug
                </th>
                <th style="padding: 1.25rem 1.5rem; text-align: center; font-weight: 700; color: var(--text); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    📦 Products
                </th>
                <th style="padding: 1.25rem 1.5rem; text-align: center; font-weight: 700; color: var(--text); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    ⚙️ Actions
                </th>
            </tr>
        </thead>
        <tbody id="categoriesTableBody">
            <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 3rem;">
                        <div style="color: var(--muted); font-size: 1.1rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📂</div>
                            <div>No categories found.</div>
                            <a href="<?= $basePath ?>/admin/categories/create" style="color: var(--accent); text-decoration: none; font-weight: 600; margin-top: 0.5rem; display: inline-block;">
                                Create one now →
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            <?php foreach ($categories as $category): ?>
                <tr class="category-row" style="border-bottom: 1px solid var(--line); transition: all 0.3s ease;">
                    
                    <td style="padding: 1.25rem 1.5rem; color: var(--muted); font-weight: 600; font-size: 0.9rem;">
                        #<?= str_pad($category['id'], 3, '0', STR_PAD_LEFT) ?>
                    </td>
                    
                    <td style="padding: 1.25rem 1.5rem;">
                        <div style="font-weight: 700; color: var(--text); font-size: 1.05rem;">
                            <?= htmlspecialchars($category['name']) ?>
                        </div>
                    </td>
                    
                    <td style="padding: 1.25rem 1.5rem;">
                        <code style="padding: 0.4rem 0.875rem; background: var(--bg-2); border: 1px solid var(--line); color: #a855f7; border-radius: 6px; font-family: monospace; font-size: 0.85rem;">
                            <?= htmlspecialchars($category['slug']) ?>
                        </code>
                    </td>
                    
                    <td style="padding: 1.25rem 1.5rem; text-align: center;">
                        <?php
                        $count = (int)$category['product_count'];
                        $color = $count > 0 ? '#22c55e' : '#6b7280';
                        ?>
                        <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 50px; padding: 0.5rem 0.875rem; background: <?= $color ?>20; border: 1px solid <?= $color ?>; color: <?= $color ?>; border-radius: 8px; font-weight: 700; font-size: 0.95rem;">
                            <?= $count ?>
                        </span>
                    </td>
                    
                    <td style="padding: 1.25rem 1.5rem;">
                        <div style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                            <a href="<?= $basePath ?>/admin/categories/edit/<?= $category['id'] ?>" 
                               class="action-btn edit-btn"
                               title="Edit Category">
                                ✏️
                            </a>
                            
                            <form method="POST" action="<?= $basePath ?>/admin/categories/delete/<?= $category['id'] ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this category? This action cannot be undone.');">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                <button type="submit" class="action-btn delete-btn" title="Delete" <?= $count > 0 ? 'disabled' : '' ?>>
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
    }
    
    .modern-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(168, 85, 247, 0.4);
    }
    
    #searchInput:focus {
        outline: none;
        border-color: #a855f7;
        box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.1);
    }
    
    .category-row:hover {
        background: rgba(168, 85, 247, 0.05);
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
    
    .action-btn:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }
    
    .action-btn:not(:disabled):hover {
        transform: scale(1.1);
    }
    
    .edit-btn:hover {
        background: rgba(168, 85, 247, 0.2);
        box-shadow: 0 0 8px rgba(168, 85, 247, 0.3);
    }
    
    .delete-btn:not(:disabled):hover {
        background: rgba(239, 68, 68, 0.3);
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.4);
        transform: scale(1.15);
    }
    
    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
    }
</style>

<script>
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const categoryRows = document.querySelectorAll('.category-row');
    
    function filterCategories() {
        const searchTerm = searchInput.value.toLowerCase();
        let visibleCount = 0;
        
        categoryRows.forEach(row => {
            const categoryName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const slug = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            
            if (categoryName.includes(searchTerm) || slug.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show/hide empty message
        const tbody = document.getElementById('categoriesTableBody');
        if (visibleCount === 0 && categoryRows.length > 0) {
            const noResults = document.getElementById('noResults');
            if (!noResults) {
                const noResultsRow = document.createElement('tr');
                noResultsRow.innerHTML = `
                    <td colspan="5" style="text-align: center; padding: 3rem;">
                        <div style="color: var(--muted); font-size: 1.1rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
                            <div>No categories match your search</div>
                            <button onclick="clearSearch()" style="margin-top: 1rem; padding: 0.75rem 1.5rem; background: var(--accent); color: var(--bg-0); border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                                Clear Search
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
    
    function clearSearch() {
        searchInput.value = '';
        filterCategories();
    }
    
    searchInput.addEventListener('input', filterCategories);
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.success-alert, .error-alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/admin.php';
