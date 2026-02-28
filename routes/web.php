<?php

// ── Home ──────────────────────────────────────────────────
$router->get('/', 'HomeController@index');

// ── Setup (first-time database setup) ────────────────────
$router->get('/setup/database', function() {
    require 'setup-db.php';
});

// ── Admin Auth (login/logout only – NO register/forgot/reset) ─
$router->get('/login',  'AuthController@showLogin',  ['guest']);
$router->post('/login', 'AuthController@login',      ['guest']);
$router->get('/logout', 'AuthController@logout',     ['auth']);

// ── Games / Products (public – no auth required) ─────────
$router->get('/games',        'ProductController@games');
$router->get('/products',     'ProductController@games');   // alias
$router->get('/product/{id}', 'ProductController@show');

// ── Ad Unlock (public) ────────────────────────────────────
$router->post('/ad-unlock', 'AdUnlockController@unlock');

// ── API – Search (public) ────────────────────────────────
$router->get('/api/search', 'ProductController@apiSearch');

// ── Downloads ─────────────────────────────────────────────
$router->get('/download/ad',      'DownloadController@interstitial');
$router->get('/download/{token}', 'DownloadController@download');

// ── Admin – Dashboard ────────────────────────────────────
$router->get('/admin',           'AdminDashboardController@index', ['auth', 'role:admin']);
$router->get('/admin/dashboard', 'AdminDashboardController@index', ['auth', 'role:admin']);

// ── Admin – Games / Products ─────────────────────────────
$router->get('/admin/products',                  'AdminProductController@index',      ['auth', 'role:admin']);
$router->get('/admin/products/create',           'AdminProductController@create',     ['auth', 'role:admin']);
$router->post('/admin/products',                 'AdminProductController@store',      ['auth', 'role:admin']);
$router->get('/admin/products/edit/{id}',        'AdminProductController@edit',       ['auth', 'role:admin']);
$router->post('/admin/products/update/{id}',     'AdminProductController@update',     ['auth', 'role:admin']);
$router->post('/admin/products/delete/{id}',     'AdminProductController@delete',     ['auth', 'role:admin']);
$router->post('/admin/products/activate/{id}',   'AdminProductController@activate',   ['auth', 'role:admin']);
$router->post('/admin/products/deactivate/{id}', 'AdminProductController@deactivate', ['auth', 'role:admin']);

// ── Admin – Categories ───────────────────────────────────
$router->get('/admin/categories',                'AdminCategoryController@index',  ['auth', 'role:admin']);
$router->get('/admin/categories/create',         'AdminCategoryController@create', ['auth', 'role:admin']);
$router->post('/admin/categories/store',         'AdminCategoryController@store',  ['auth', 'role:admin']);
$router->get('/admin/categories/edit/{id}',      'AdminCategoryController@edit',   ['auth', 'role:admin']);
$router->post('/admin/categories/update/{id}',   'AdminCategoryController@update', ['auth', 'role:admin']);
$router->post('/admin/categories/delete/{id}',   'AdminCategoryController@delete', ['auth', 'role:admin']);

// ── Admin – Users ────────────────────────────────────────
$router->get('/admin/users',                'AdminUserController@index',   ['auth', 'role:admin']);
$router->post('/admin/users/block/{id}',    'AdminUserController@block',   ['auth', 'role:admin']);
$router->post('/admin/users/unblock/{id}',  'AdminUserController@unblock', ['auth', 'role:admin']);
$router->post('/admin/users/delete/{id}',   'AdminUserController@delete',  ['auth', 'role:admin']);

// ── Admin – Migrations ───────────────────────────────────
$router->get('/admin/migrations',                   'AdminMigrationController@index', ['auth', 'role:admin']);
$router->post('/admin/migrations/run/{migration}',  'AdminMigrationController@run',   ['auth', 'role:admin']);

// ── Admin – Settings ─────────────────────────────────────
$router->get('/admin/settings',        'AdminSettingsController@index', ['auth', 'role:admin']);
$router->post('/admin/settings/save',  'AdminSettingsController@save',  ['auth', 'role:admin']);

// ── Admin – Announcements ────────────────────────────────
$router->get('/admin/announcements',                   'AdminAnnouncementController@index',        ['auth', 'role:admin']);
$router->get('/admin/announcements/create',            'AdminAnnouncementController@create',       ['auth', 'role:admin']);
$router->post('/admin/announcements/store',            'AdminAnnouncementController@store',        ['auth', 'role:admin']);
$router->get('/admin/announcements/edit/{id}',         'AdminAnnouncementController@edit',         ['auth', 'role:admin']);
$router->post('/admin/announcements/update/{id}',      'AdminAnnouncementController@update',       ['auth', 'role:admin']);
$router->post('/admin/announcements/toggle/{id}',      'AdminAnnouncementController@toggleActive', ['auth', 'role:admin']);
$router->post('/admin/announcements/delete/{id}',      'AdminAnnouncementController@delete',       ['auth', 'role:admin']);

// ── Admin – Security ─────────────────────────────────────
$router->get('/admin/security',               'AdminSecurityController@index',      ['auth', 'role:admin']);
$router->post('/admin/security/clear-logs',   'AdminSecurityController@clearLogs',  ['auth', 'role:admin']);
$router->get('/admin/security/export',        'AdminSecurityController@exportLogs', ['auth', 'role:admin']);

// ── API – Announcements (public) ─────────────────────────
$router->get('/api/announcements/active',          'AdminAnnouncementController@getActive');
$router->post('/api/announcements/dismiss/{id}',   'AdminAnnouncementController@dismiss');
