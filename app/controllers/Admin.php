<?php
class Admin extends Controller {
    private $settingModel;
    private $userModel;
    private $productModel;
    private $orderModel;
    private $contactModel;
    private $adminNotificationModel;

    public function __construct() {
        $this->requireAdmin();
        $this->settingModel = $this->model('Setting');
        $this->userModel = $this->model('User');
        $this->productModel = $this->model('Product');
        $this->orderModel = $this->model('Order');
        $this->contactModel = $this->model('Contact');
        $this->adminNotificationModel = $this->model('AdminNotification');
    }

    private function requireAdmin() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }
    }

    private function getNavBadges() {
        return $this->getAdminNavBadges();
    }

    private function getNotificationStateKey() {
        return 'admin_notification_state_' . (int) ($_SESSION['user_id'] ?? 0);
    }

    private function getNotificationState() {
        $raw = $this->settingModel->getValueByKey($this->getNotificationStateKey(), '');
        $decoded = json_decode((string) $raw, true);
        if (!is_array($decoded)) {
            $decoded = [];
        }

        $lastOpenedId = (int) ($decoded['last_opened_id'] ?? 0);
        $lastOpenedAt = trim((string) ($decoded['last_opened_at'] ?? ''));
        if ($lastOpenedAt === '') {
            $ticketSeenAt = trim((string) ($decoded['ticket_last_seen_at'] ?? ''));
            $orderSeenAt = trim((string) ($decoded['order_last_seen_at'] ?? ''));
            $lastOpenedAt = max($ticketSeenAt, $orderSeenAt);
        }

        if ($lastOpenedId <= 0 && $lastOpenedAt !== '' && $lastOpenedAt !== '1970-01-01 00:00:00') {
            try {
                $lastOpenedId = (int) $this->adminNotificationModel->getLatestNotificationIdByCreatedAt($lastOpenedAt);
            } catch (Throwable $error) {
                $lastOpenedId = 0;
            }
        }

        return [
            'last_opened_id' => max(0, $lastOpenedId),
            'last_opened_at' => $lastOpenedAt !== '' ? $lastOpenedAt : '1970-01-01 00:00:00'
        ];
    }

    private function saveNotificationState($lastOpenedAt, $lastOpenedId = 0) {
        $payload = json_encode([
            'last_opened_id' => max(0, (int) $lastOpenedId),
            'last_opened_at' => trim((string) $lastOpenedAt) !== '' ? trim((string) $lastOpenedAt) : '1970-01-01 00:00:00'
        ]);
        if ($payload === false) {
            return false;
        }
        return $this->settingModel->upsertValue($this->getNotificationStateKey(), $payload);
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

    public function notifications() {
        try {
            $this->adminNotificationModel->syncRecentTicketNotifications(200);
            $this->adminNotificationModel->syncCompletedOrderNotifications(200);
            $state = $this->getNotificationState();
            $lastOpenedId = (int) ($state['last_opened_id'] ?? 0);
            $lastOpenedAt = $state['last_opened_at'];
            $rows = $this->adminNotificationModel->getRecentNotifications(40);

            $items = [];
            foreach ($rows as $row) {
                $createdAt = (string) ($row->created_at ?? '');
                $relativeUrl = trim((string) ($row->url ?? ''));
                $href = $relativeUrl !== '' ? (URLROOT . '/' . ltrim($relativeUrl, '/')) : (URLROOT . '/admin');
                $icon = $row->type === 'revenue' ? 'ti-wallet' : 'ti-email';
                $notificationId = (int) ($row->id ?? 0);
                $items[] = [
                    'type' => (string) ($row->type ?? 'ticket'),
                    'icon' => $icon,
                    'title' => (string) ($row->title ?? 'Thông báo'),
                    'message' => (string) ($row->message ?? ''),
                    'href' => $href,
                    'created_at' => $createdAt,
                    'created_at_label' => $createdAt !== '' ? date('d/m/Y H:i', strtotime($createdAt)) : '',
                    'is_new' => $lastOpenedId > 0
                        ? ($notificationId > $lastOpenedId)
                        : ($createdAt !== '' ? ($createdAt > $lastOpenedAt) : false)
                ];
            }
            $unseenCount = $lastOpenedId > 0
                ? $this->adminNotificationModel->countUnreadSinceId($lastOpenedId)
                : $this->adminNotificationModel->countUnreadSince($lastOpenedAt);
        } catch (Throwable $error) {
            $items = [];
            $unseenCount = 0;
        }

        $this->respondJson([
            'success' => true,
            'unseen_count' => (int) $unseenCount,
            'items' => $items
        ]);
    }

    public function markNotificationsSeen() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respondJson([
                'success' => false,
                'message' => 'Phương thức không hợp lệ.'
            ], 405);
        }

        if (!$this->verifyCsrf('csrf_admin')) {
            $this->respondJson(['success' => false, 'message' => 'Yêu cầu không hợp lệ.'], 403);
        }

        try {
            $this->adminNotificationModel->syncRecentTicketNotifications(200);
            $this->adminNotificationModel->syncCompletedOrderNotifications(200);
            $latestNotificationCreatedAt = $this->adminNotificationModel->getLatestNotificationCreatedAt();
            $latestNotificationId = $this->adminNotificationModel->getLatestNotificationId();
            $openedAt = date('Y-m-d H:i:s');
            if (!empty($latestNotificationCreatedAt) && strcmp((string) $latestNotificationCreatedAt, $openedAt) > 0) {
                $openedAt = (string) $latestNotificationCreatedAt;
            }
            $saved = $this->saveNotificationState($openedAt, $latestNotificationId);
        } catch (Throwable $error) {
            $saved = false;
        }

        $this->respondJson([
            'success' => (bool) $saved
        ], $saved ? 200 : 500);
    }

    private function uploadBrandingAsset($currentFileName = '') {
        if (
            !isset($_FILES['branding_asset']) ||
            !is_array($_FILES['branding_asset']) ||
            (int) ($_FILES['branding_asset']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE
        ) {
            return ['success' => true, 'filename' => $currentFileName, 'message' => ''];
        }

        $uploadDir = APPROOT . '/../public/uploads/branding/';
        return SecureUpload::storeBrandingUpload($_FILES['branding_asset'], $uploadDir, $currentFileName);
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
            if (!$this->verifyCsrf('csrf_admin')) {
                $_SESSION['admin_users_flash'] = ['type' => 'danger', 'message' => 'Yêu cầu không hợp lệ.'];
                header('Location: ' . URLROOT . '/admin/users');
                exit();
            }

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

        if (!$this->verifyCsrf('csrf_admin')) {
            if ($this->isAjaxRequest()) {
                $this->respondJson(['success' => false, 'message' => 'Yêu cầu không hợp lệ.'], 403);
            }
            $_SESSION['admin_users_flash'] = ['type' => 'danger', 'message' => 'Yêu cầu không hợp lệ.'];
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

        if (!$this->verifyCsrf('csrf_admin')) {
            $_SESSION['admin_users_flash'] = ['type' => 'danger', 'message' => 'Yêu cầu không hợp lệ.'];
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

    public function resetPassword($userId = 0) {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
            exit();
        }

        if (!$this->verifyCsrf('csrf_admin')) {
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
            exit();
        }

        $userId = (int) $userId;
        $targetUser = $this->userModel->getUserById($userId);
        if (!$targetUser) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy người dùng.']);
            exit();
        }

        if ((int) $_SESSION['user_id'] === $userId) {
            echo json_encode(['success' => false, 'message' => 'Không thể reset mật khẩu tài khoản đang đăng nhập.']);
            exit();
        }

        $hashed = password_hash('resetpassword', PASSWORD_DEFAULT);
        if ($this->userModel->updatePassword($userId, $hashed)) {
            echo json_encode(['success' => true, 'message' => 'Reset mật khẩu thành công!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể reset mật khẩu. Vui lòng thử lại.']);
        }
        exit();
    }

    public function deleteUser($userId = 0) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/admin/users');
            exit();
        }

        if (!$this->verifyCsrf('csrf_admin')) {
            $_SESSION['admin_users_flash'] = ['type' => 'danger', 'message' => 'Yêu cầu không hợp lệ.'];
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
            if (!$this->verifyCsrf('csrf_admin')) {
                $_SESSION['admin_settings_flash'] = ['type' => 'danger', 'message' => 'Yêu cầu không hợp lệ.'];
                header('Location: ' . URLROOT . '/admin/settings');
                exit();
            }

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
