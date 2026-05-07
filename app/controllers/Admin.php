<?php
class Admin extends Controller {
    private $settingModel;
    private $userModel;
    private $productModel;
    private $orderModel;
    private $contactModel;

    public function __construct() {
        $this->requireAdmin();
        $this->settingModel = $this->model('Setting');
        $this->userModel = $this->model('User');
        $this->productModel = $this->model('Product');
        $this->orderModel = $this->model('Order');
        $this->contactModel = $this->model('Contact');
    }

    private function requireAdmin() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }
    }

    private function getNavBadges() {
        $ticketCount = $this->contactModel->countContacts(['status' => 'unread']);
        $newUsers = $this->userModel->countNewUsersSince(30);
        return [
            'tickets' => $ticketCount,
            'users' => $newUsers,
            'news' => 0,
            'notifications' => $ticketCount + $newUsers
        ];
    }

    private function isAjaxRequest() {
        $requestedWith = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
        $accept = strtolower($_SERVER['HTTP_ACCEPT'] ?? '');
        return $requestedWith === 'xmlhttprequest' || strpos($accept, 'application/json') !== false;
    }

    private function respondJson($payload, $statusCode = 200) {
        http_response_code((int) $statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit();
    }

    private function uploadBrandingAsset($currentFileName = '') {
        if (
            !isset($_FILES['branding_asset']) ||
            !is_array($_FILES['branding_asset']) ||
            (int) ($_FILES['branding_asset']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE
        ) {
            return ['success' => true, 'filename' => $currentFileName, 'message' => ''];
        }

        $file = $_FILES['branding_asset'];
        $uploadError = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($uploadError !== UPLOAD_ERR_OK) {
            return ['success' => false, 'filename' => $currentFileName, 'message' => 'Không thể tải logo lên. Vui lòng thử lại.'];
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > (2 * 1024 * 1024)) {
            return ['success' => false, 'filename' => $currentFileName, 'message' => 'Logo phải nhỏ hơn hoặc bằng 2MB.'];
        }

        $originalName = trim((string) ($file['name'] ?? ''));
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExtensions = ['png', 'svg', 'ico'];
        if (!in_array($extension, $allowedExtensions, true)) {
            return ['success' => false, 'filename' => $currentFileName, 'message' => 'Chỉ chấp nhận tệp PNG, SVG hoặc ICO.'];
        }

        if ($extension === 'svg') {
            $svgContent = @file_get_contents($file['tmp_name']);
            $normalizedSvg = strtolower((string) $svgContent);
            if (
                $svgContent === false ||
                strpos($normalizedSvg, '<script') !== false ||
                strpos($normalizedSvg, 'onload=') !== false ||
                strpos($normalizedSvg, 'javascript:') !== false
            ) {
                return ['success' => false, 'filename' => $currentFileName, 'message' => 'Tệp SVG không an toàn. Vui lòng chọn tệp khác.'];
            }
        }

        $uploadDir = APPROOT . '/../public/uploads/branding/';
        if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            return ['success' => false, 'filename' => $currentFileName, 'message' => 'Không thể tạo thư mục lưu logo.'];
        }

        $uniqueSuffix = (string) mt_rand(100000, 999999);
        if (function_exists('random_bytes')) {
            try {
                $uniqueSuffix = bin2hex(random_bytes(4));
            } catch (Throwable $error) {
                $uniqueSuffix = (string) mt_rand(100000, 999999);
            }
        }
        $newFileName = 'brand_logo_' . date('YmdHis') . '_' . $uniqueSuffix . '.' . $extension;
        $targetPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => false, 'filename' => $currentFileName, 'message' => 'Không thể lưu logo lên máy chủ.'];
        }

        $oldFile = basename((string) $currentFileName);
        if ($oldFile !== '' && $oldFile !== $newFileName) {
            $oldPath = $uploadDir . $oldFile;
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        return ['success' => true, 'filename' => $newFileName, 'message' => ''];
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
                'new_users' => $this->userModel->countNewUsersSince(30),
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
            ],
            'nav_badges' => $this->getNavBadges()
        ];
        $this->view('admin/index', $data);
    }

    public function services() {
        header('Location: ' . URLROOT . '/adminproducts');
        exit();
    }

    public function tickets() {
        header('Location: ' . URLROOT . '/admincontacts');
        exit();
    }

    public function news() {
        header('Location: ' . URLROOT . '/adminnews');
        exit();
    }

    public function users() {
        $filters = [
            'keyword' => trim($_GET['keyword'] ?? ''),
            'status' => trim($_GET['status'] ?? ''),
            'role' => trim($_GET['role'] ?? '')
        ];
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $perPage = 10;

        $totalUsers = $this->userModel->countAdminUsers($filters);
        $lastPage = max(1, (int) ceil($totalUsers / $perPage));
        if ($page > $lastPage) {
            $page = $lastPage;
        }

        $users = $this->userModel->getAdminUsers($filters, $page, $perPage);
        $activeUsers = $this->userModel->countAdminUsers(['status' => 'active']);
        $bannedUsers = $this->userModel->countAdminUsers(['status' => 'banned']);

        $flash = $_SESSION['admin_users_flash'] ?? null;
        unset($_SESSION['admin_users_flash']);

        $data = [
            'title' => 'Quản lý người dùng',
            'subtitle' => '',
            'users' => $users,
            'filters' => $filters,
            'pagination' => [
                'page' => $page,
                'last_page' => $lastPage,
                'total' => $totalUsers
            ],
            'summary' => [
                'total' => $this->userModel->countAllUsers(),
                'active' => $activeUsers,
                'banned' => $bannedUsers
            ],
            'flash' => $flash,
            'nav_badges' => $this->getNavBadges()
        ];
        $this->view('admin/users/index', $data);
    }

    public function editUser($userId = 0) {
        $userId = (int) $userId;
        if ($userId <= 0) {
            $_SESSION['admin_users_flash'] = [
                'type' => 'danger',
                'message' => 'Người dùng không hợp lệ.'
            ];
            header('Location: ' . URLROOT . '/admin/users');
            exit();
        }

        $targetUser = $this->userModel->getUserById($userId);
        if (!$targetUser) {
            $_SESSION['admin_users_flash'] = [
                'type' => 'danger',
                'message' => 'Không tìm thấy người dùng cần chỉnh sửa.'
            ];
            header('Location: ' . URLROOT . '/admin/users');
            exit();
        }

        $errors = [
            'full_name' => '',
            'email' => '',
            'role' => '',
            'status' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = trim($_POST['role'] ?? '');
            $status = trim($_POST['status'] ?? '');

            if ($fullName === '') {
                $errors['full_name'] = 'Họ tên không được để trống.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email không hợp lệ.';
            } elseif ($this->userModel->isEmailUsedByAnother($email, $userId)) {
                $errors['email'] = 'Email này đã được sử dụng bởi tài khoản khác.';
            }
            if (!in_array($role, ['admin', 'member'], true)) {
                $errors['role'] = 'Vai trò không hợp lệ.';
            }
            if (!in_array($status, ['active', 'banned'], true)) {
                $errors['status'] = 'Trạng thái không hợp lệ.';
            }

            if (implode('', $errors) === '') {
                $updatedProfile = $this->userModel->updateProfile($userId, $fullName, $email);
                $updatedRole = $this->userModel->updateRole($userId, $role);
                $updatedStatus = $this->userModel->updateStatus($userId, $status);
                $isSuccess = $updatedProfile && $updatedRole && $updatedStatus;

                $_SESSION['admin_users_flash'] = [
                    'type' => $isSuccess ? 'success' : 'danger',
                    'message' => $isSuccess ? 'Đã cập nhật hồ sơ người dùng.' : 'Không thể cập nhật hồ sơ người dùng.'
                ];

                header('Location: ' . URLROOT . '/admin/users');
                exit();
            }

            $targetUser->full_name = $fullName;
            $targetUser->email = $email;
            $targetUser->role = $role;
            $targetUser->status = $status;
        }

        $data = [
            'title' => 'Chỉnh sửa hồ sơ người dùng',
            'subtitle' => '',
            'user' => $targetUser,
            'errors' => $errors,
            'nav_badges' => $this->getNavBadges()
        ];
        $this->view('admin/users/edit', $data);
    }

    public function updateUserRole($userId = 0) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/admin/users');
            exit();
        }

        $role = trim($_POST['role'] ?? '');
        $updated = $this->userModel->updateRole((int) $userId, $role);
        if ($this->isAjaxRequest()) {
            $this->respondJson([
                'success' => (bool) $updated,
                'message' => $updated ? 'Vai trò đã cập nhật (tự động).' : 'Không thể cập nhật vai trò.'
            ], $updated ? 200 : 422);
        }
        $_SESSION['admin_users_flash'] = [
            'type' => $updated ? 'success' : 'danger',
            'message' => $updated ? 'Đã cập nhật vai trò người dùng.' : 'Không thể cập nhật vai trò.'
        ];

        header('Location: ' . URLROOT . '/admin/users');
        exit();
    }

    public function toggleUserStatus($userId = 0) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/admin/users');
            exit();
        }

        $targetStatus = trim($_POST['target_status'] ?? '');
        $updated = $this->userModel->updateStatus((int) $userId, $targetStatus);
        $_SESSION['admin_users_flash'] = [
            'type' => $updated ? 'success' : 'danger',
            'message' => $updated
                ? ($targetStatus === 'banned' ? 'Đã khóa hồ sơ người dùng.' : 'Đã mở khóa hồ sơ người dùng.')
                : 'Không thể cập nhật trạng thái.'
        ];

        header('Location: ' . URLROOT . '/admin/users');
        exit();
    }

    public function deleteUser($userId = 0) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/admin/users');
            exit();
        }

        $userId = (int) $userId;
        $targetUser = $this->userModel->getUserById($userId);
        if (!$targetUser) {
            $_SESSION['admin_users_flash'] = [
                'type' => 'danger',
                'message' => 'Không tìm thấy thành viên để xóa.'
            ];
            header('Location: ' . URLROOT . '/admin/users');
            exit();
        }

        if ((int) $_SESSION['user_id'] === $userId) {
            $_SESSION['admin_users_flash'] = [
                'type' => 'danger',
                'message' => 'Không thể xóa chính tài khoản quản trị đang đăng nhập.'
            ];
            header('Location: ' . URLROOT . '/admin/users');
            exit();
        }

        if (($targetUser->role ?? 'member') === 'admin') {
            $_SESSION['admin_users_flash'] = [
                'type' => 'danger',
                'message' => 'Chỉ cho phép xóa tài khoản thành viên.'
            ];
            header('Location: ' . URLROOT . '/admin/users');
            exit();
        }

        $deleted = $this->userModel->deleteUserById($userId);
        $_SESSION['admin_users_flash'] = [
            'type' => $deleted ? 'success' : 'danger',
            'message' => $deleted ? 'Đã xóa thành viên khỏi hệ thống.' : 'Không thể xóa thành viên.'
        ];
        header('Location: ' . URLROOT . '/admin/users');
        exit();
    }

    public function contacts() {
        header('Location: ' . URLROOT . '/admincontacts');
        exit();
    }

    public function settings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $existingSettings = $this->settingModel->getPublicSettings();
            $formData = [
                'site_logo_text' => trim($_POST['site_logo_text'] ?? ''),
                'site_hotline' => trim($_POST['site_hotline'] ?? ''),
                'site_contact_email' => trim($_POST['site_contact_email'] ?? ''),
                'site_address' => trim($_POST['site_address'] ?? ''),
                'site_about_snippet' => trim($_POST['site_about_snippet'] ?? ''),
                'site_map_embed_url' => trim($_POST['site_map_embed_url'] ?? ''),
                'site_logo_image' => trim($existingSettings['site_logo_image'] ?? '')
            ];

            $errors = [];
            if ($formData['site_logo_text'] === '') {
                $errors['site_logo_text'] = 'Tên hiển thị logo không được để trống.';
            }
            if ($formData['site_hotline'] === '') {
                $errors['site_hotline'] = 'Hotline không được để trống.';
            }
            if (!filter_var($formData['site_contact_email'], FILTER_VALIDATE_EMAIL)) {
                $errors['site_contact_email'] = 'Email liên hệ không hợp lệ.';
            }
            if ($formData['site_address'] === '') {
                $errors['site_address'] = 'Địa chỉ không được để trống.';
            }
            if ($formData['site_map_embed_url'] !== '' && filter_var($formData['site_map_embed_url'], FILTER_VALIDATE_URL) === false) {
                $errors['site_map_embed_url'] = 'URL bản đồ không hợp lệ.';
            }

            if (empty($errors)) {
                $uploadResult = $this->uploadBrandingAsset($formData['site_logo_image']);
                if (!$uploadResult['success']) {
                    $errors['branding_asset'] = $uploadResult['message'];
                } else {
                    $formData['site_logo_image'] = $uploadResult['filename'];
                }
            }

            if (empty($errors)) {
                $saved = $this->settingModel->updatePublicSettings($formData);
                $_SESSION['admin_settings_flash'] = [
                    'type' => $saved ? 'success' : 'danger',
                    'message' => $saved ? 'Đã cập nhật thông tin công khai thành công.' : 'Không thể lưu cài đặt. Vui lòng thử lại.'
                ];

                header('Location: ' . URLROOT . '/admin/settings');
                exit();
            }

            $data = [
                'title' => 'Cài đặt hệ thống',
                'subtitle' => '',
                'settings' => $formData,
                'errors' => $errors,
                'flash' => [
                    'type' => 'danger',
                    'message' => 'Vui lòng kiểm tra lại các trường dữ liệu.'
                ],
                'nav_badges' => $this->getNavBadges()
            ];
            $this->view('admin/settings/index', $data);
            return;
        }

        $flash = $_SESSION['admin_settings_flash'] ?? null;
        unset($_SESSION['admin_settings_flash']);

        $data = [
            'title' => 'Cài đặt hệ thống',
            'subtitle' => '',
            'settings' => $this->settingModel->getPublicSettings(),
            'errors' => [],
            'flash' => $flash,
            'nav_badges' => $this->getNavBadges()
        ];
        $this->view('admin/settings/index', $data);
    }
}
