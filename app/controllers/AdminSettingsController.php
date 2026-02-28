<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Core\Session;
use Core\Settings;
use Core\Security\CSRF;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $user = Session::get('user');
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            http_response_code(403);
            return View::render('errors/404', ['message' => 'Unauthorized']);
        }

        $settings = Settings::all();
        $paymentMethods = [];
        if (!empty($settings['payment_methods'])) {
            $decoded = json_decode($settings['payment_methods'], true);
            if (is_array($decoded)) {
                $paymentMethods = $decoded;
            }
        }

        if (empty($paymentMethods)) {
            $paymentMethods = [
                ['name' => 'M-Pesa', 'receiver' => 'Hasheem Gaming', 'number' => '255755900101'],
                ['name' => 'Tigo Pesa', 'receiver' => 'Hasheem Gaming', 'number' => '255711558202'],
                ['name' => 'Halopesa', 'receiver' => 'Hasheem Gaming', 'number' => '255744112233'],
                ['name' => 'Bank Transfer', 'receiver' => 'Hasheem Gaming', 'number' => '01522499844']
            ];
        }

        $settings['support_phone'] = $settings['support_phone'] ?? '255621215237';
        $settings['whatsapp_number'] = $settings['whatsapp_number'] ?? '255621215237';
        $settings['support_email'] = $settings['support_email'] ?? 'support@hasheem.local';
        $settings['adsterra_smartlink_url'] = $settings['adsterra_smartlink_url'] ?? '';
        $settings['adsterra_popunder_script'] = $settings['adsterra_popunder_script'] ?? 'https://pl28753721.effectivegatecpm.com/21/9a/17/219a17f2ab331e28855b039bd9d39bc2.js';
        $settings['adsterra_banner_key'] = $settings['adsterra_banner_key'] ?? 'a86f3e605a3bca8e1552a5e1c1e1492c';
        $settings['adsterra_banner_invoke_host'] = $settings['adsterra_banner_invoke_host'] ?? 'https://www.highperformanceformat.com';

        return View::render('admin/settings', [
            'settings' => $settings,
            'paymentMethods' => $paymentMethods,
            'csrf_token' => CSRF::getToken()
        ]);
    }

    public function save()
    {
        $user = Session::get('user');
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            http_response_code(403);
            return View::render('errors/404', ['message' => 'Unauthorized']);
        }

        $token = $_POST['_token'] ?? '';
        if (!CSRF::validateToken($token)) {
            http_response_code(419);
            return View::render('errors/419', ['message' => 'Security token expired. Please refresh and try again.']);
        }

        $supportPhone = trim($_POST['support_phone'] ?? '');
        $whatsappNumber = trim($_POST['whatsapp_number'] ?? '');
        $supportEmail = trim($_POST['support_email'] ?? '');
        $adsterraSmartlinkUrl = trim($_POST['adsterra_smartlink_url'] ?? '');
        $adsterraPopunderScript = trim($_POST['adsterra_popunder_script'] ?? '');
        $adsterraBannerKey = trim($_POST['adsterra_banner_key'] ?? '');
        $adsterraBannerInvokeHost = trim($_POST['adsterra_banner_invoke_host'] ?? '');

        $methodNames = $_POST['method_name'] ?? [];
        $methodReceivers = $_POST['method_receiver'] ?? [];
        $methodNumbers = $_POST['method_number'] ?? [];

        $methods = [];
        $count = max(count($methodNames), count($methodReceivers), count($methodNumbers));
        for ($i = 0; $i < $count; $i++) {
            $name = trim($methodNames[$i] ?? '');
            $receiver = trim($methodReceivers[$i] ?? '');
            $number = trim($methodNumbers[$i] ?? '');
            if ($name === '' || $number === '') {
                continue;
            }
            $methods[] = [
                'name' => $name,
                'receiver' => $receiver,
                'number' => $number
            ];
        }

        Settings::setMany([
            'support_phone' => $supportPhone,
            'whatsapp_number' => $whatsappNumber,
            'support_email' => $supportEmail,
            'adsterra_smartlink_url' => $adsterraSmartlinkUrl,
            'adsterra_popunder_script' => $adsterraPopunderScript,
            'adsterra_banner_key' => $adsterraBannerKey,
            'adsterra_banner_invoke_host' => $adsterraBannerInvokeHost,
            'payment_methods' => json_encode($methods)
        ]);

        $config = require __DIR__ . '/../../config/app.php';
        $basePath = rtrim($config['base_path'] ?? '', '/');
        header('Location: ' . $basePath . '/admin/settings?saved=1');
        exit;
    }
}
