<?php
class Admin extends Controller {
    private $settingModel;
    private $userModel;
    private $productModel;
    private $orderModel;
    private $contactModel;
    private $commentModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }
        $this->settingModel = $this->model('Setting');
        $this->userModel = $this->model('User');
        $this->productModel = $this->model('Product');
        $this->orderModel = $this->model('Order');
        $this->contactModel = $this->model('Contact');
        $this->commentModel = $this->model('Comment');
    }

    public function index() {
        $recentUsers = $this->userModel->getRecentUsers(4);
        $revenueRows = $this->orderModel->getLastFiveMonthRevenue();
        $revenueMap = [];
        foreach ($revenueRows as $row) {
            $revenueMap[$row->month_key] = (float) $row->revenue;
        }

        $revenueSeries = [];
        for ($i = 4; $i >= 0; $i--) {
            $monthKey = date('Y-m', strtotime("-{$i} month"));
            $revenueSeries[] = [
                'label' => 'Thg ' . date('m', strtotime($monthKey . '-01')),
                'revenue' => $revenueMap[$monthKey] ?? 0
            ];
        }

        $activeUsers = $this->userModel->countAdminUsers(['status' => 'active']);
        $unreadTickets = $this->contactModel->countContacts(['status' => 'unread']);

        $data = [
            'title' => 'Bảng điều khiển',
            'stats' => [
                'active_servers' => $this->productModel->countActiveServices(),
                'total_users' => $this->userModel->countAllUsers(),
                'active_users' => $activeUsers,
                'new_users' => $this->userModel->countNewUsersSince(7),
                'monthly_revenue' => $this->orderModel->getMonthlyRevenue(),
                'unread_tickets' => $unreadTickets,
                'system_uptime' => '99.9%'
            ],
            'recent_users' => $recentUsers,
            'revenue_series' => $revenueSeries,
            'system_usage' => [
                'cpu' => 45,
                'memory' => 62,
                'disk' => 38
            ]
        ];
        $this->view('admin/index', $data);
    }

    public function settings($section = 'homepage') {
        $section = $this->normalizeSettingsSection($section);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->verifyCsrf('csrf_admin')) {
                $_SESSION['admin_settings_flash'] = ['type' => 'danger', 'message' => 'Yêu cầu không hợp lệ.'];
                header('Location: ' . URLROOT . '/admin/settings/' . $section);
                exit();
            }

            $postedSection = $this->normalizeSettingsSection($_POST['settings_section'] ?? $section);
            $existingSettings = $this->settingModel->getPublicSettings();
            $formData = $existingSettings;
            $errors = [];

            if ($postedSection === 'homepage') {
                $formData['site_logo_text'] = trim($_POST['site_logo_text'] ?? '');
                $formData['home_hero_title_gradient'] = trim($_POST['home_hero_title_gradient'] ?? '');
                $formData['home_hero_title_plain'] = trim($_POST['home_hero_title_plain'] ?? '');
                $formData['home_hero_subtitle'] = trim($_POST['home_hero_subtitle'] ?? '');
                $formData['home_product_ids'] = $this->buildHomeProductIdsFromPost();

                if ($formData['site_logo_text'] === '') $errors['site_logo_text'] = 'Tên hiển thị logo không được để trống.';
                
                if (empty($errors)) {
                    $uploadResult = $this->uploadBrandingAsset($formData['site_logo_image'] ?? '');
                    if (!$uploadResult['success']) {
                        $errors['branding_asset'] = $uploadResult['message'];
                    } else {
                        $formData['site_logo_image'] = $uploadResult['filename'];
                    }
                }
            } elseif ($postedSection === 'contact') {
                $formData['site_hotline'] = trim($_POST['site_hotline'] ?? '');
                $formData['site_contact_email'] = trim($_POST['site_contact_email'] ?? '');
                $formData['site_address'] = trim($_POST['site_address'] ?? '');
                $formData['site_about_snippet'] = trim($_POST['site_about_snippet'] ?? '');
                $formData['site_map_embed_url'] = trim($_POST['site_map_embed_url'] ?? '');
            }

            if (empty($errors)) {
                $saved = $this->settingModel->updatePublicSettings($formData);
                $_SESSION['admin_settings_flash'] = [
                    'type' => $saved ? 'success' : 'danger',
                    'message' => $saved ? 'Đã lưu cài đặt.' : 'Không thể lưu cài đặt.'
                ];
                header('Location: ' . URLROOT . '/admin/settings/' . $postedSection);
                exit();
            }

            $data = [
                'title' => 'Cài đặt giao diện',
                'settings_section' => $postedSection,
                'settings' => $formData,
                'errors' => $errors
            ];
            $this->view('admin/settings/index', $data);
            return;
        }

        $flash = $_SESSION['admin_settings_flash'] ?? null;
        unset($_SESSION['admin_settings_flash']);

        $data = [
            'title' => 'Cài đặt giao diện',
            'settings_section' => $section,
            'settings' => $this->settingModel->getPublicSettings(),
            'errors' => [],
            'flash' => $flash
        ];
        $this->view('admin/settings/index', $data);
    }

    private function normalizeSettingsSection($section) {
        $s = strtolower(trim((string) $section));
        return in_array($s, ['homepage', 'contact', 'profile'], true) ? $s : 'homepage';
    }

    private function buildHomeProductIdsFromPost() {
        $slots = [];
        for ($i = 1; $i <= 4; $i++) {
            $id = (int)($_POST['home_product_slot_' . $i] ?? 0);
            if ($id > 0) $slots[] = $id;
        }
        return implode(',', $slots);
    }

    private function uploadBrandingAsset($currentFileName = '') {
        if (!isset($_FILES['branding_asset']) || $_FILES['branding_asset']['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => true, 'filename' => $currentFileName];
        }

        $uploadDir = APPROOT . '/../public/uploads/branding/';
        return SecureUpload::storeBrandingUpload($_FILES['branding_asset'], $uploadDir, $currentFileName);
    }
}
