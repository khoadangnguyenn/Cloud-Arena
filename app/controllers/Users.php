<?php
class Users extends Controller
{
    // Default index to avoid missing method errors
    public function index()
    {
        header('Location: ' . URLROOT . '/users/login');
        exit;
    }

    public function login()
    {
        // If POST, process login
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize POST
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $userModel = $this->model('User');
            $user = $userModel->login($username, $password);

            if ($user) {
                // Prevent banned users from logging in
                if (isset($user->status) && $user->status === 'banned') {
                    $data = [
                        'title' => 'Đăng nhập',
                        'username' => $username,
                        'username_err' => 'Tài khoản của bạn đã bị cấm.',
                        'password_err' => ''
                    ];
                    $this->view('client/users/login', $data);
                    return;
                }
                // Set session
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->full_name ?: $user->username;
                $_SESSION['user_role'] = $user->role ?? 'member';
                if (!empty($user->avatar)) {
                    $_SESSION['user_avatar'] = $user->avatar;
                }
                // Store credit in session for quick display in navbar
                $_SESSION['user_credit'] = isset($user->credit) ? (int)$user->credit : 0;
                header('Location: ' . URLROOT . '/pages/index');
                exit;
            } else {
                $data = [
                    'title' => 'Đăng nhập',
                    'username' => $username,
                    'username_err' => 'Tên đăng nhập hoặc mật khẩu không đúng',
                    'password_err' => ''
                ];
                $this->view('client/users/login', $data);
            }
            return;
        }

        $data = ['title' => 'Đăng nhập', 'username' => '', 'username_err' => '', 'password_err' => ''];
        $this->view('client/users/login', $data);
    }

    public function logout()
    {
        // Clear session and redirect to home
        // Unset all session variables
        $_SESSION = [];
        // Destroy the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
        header('Location: ' . URLROOT . '/');
        exit;
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            $userModel = $this->model('User');

            $data = [
                'title' => 'Đăng ký',
                'username' => $username,
                'email' => $email,
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Validate
            if (empty($username)) $data['username_err'] = 'Vui lòng nhập tên đăng nhập';
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $data['email_err'] = 'Email không hợp lệ';
            if (empty($password) || strlen($password) < 6) $data['password_err'] = 'Mật khẩu ít nhất 6 ký tự';
            if ($password !== $confirm_password) $data['confirm_password_err'] = 'Mật khẩu xác nhận không khớp';

            // Check unique
            if ($userModel->findUserByUsername($username)) $data['username_err'] = 'Tên đăng nhập đã tồn tại';
            if ($userModel->findUserByEmail($email)) $data['email_err'] = 'Email đã được sử dụng';

            // If no errors, register
            if (empty($data['username_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $payload = ['username' => $username, 'email' => $email, 'password' => $hashed];
                if ($userModel->register($payload)) {
                    header('Location: ' . URLROOT . '/users/login');
                    exit;
                } else {
                    die('Lỗi đăng ký tài khoản');
                }
            }

            // Show form with errors
            $this->view('client/users/register', $data);
            return;
        }

        $data = ['title' => 'Đăng ký', 'username' => '', 'email' => '', 'username_err' => '', 'email_err' => '', 'password_err' => '', 'confirm_password_err' => ''];
        $this->view('client/users/register', $data);
    }

    public function profile()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }

        $userModel = $this->model('User');
        $user = $userModel->getById($_SESSION['user_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Determine action: update_profile or change_password
            if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
                $current = $_POST['current_password'] ?? '';
                $new = $_POST['new_password'] ?? '';
                $confirm = $_POST['confirm_password'] ?? '';

                if (empty($new) || strlen($new) < 6) {
                    $error = 'Mật khẩu mới phải có ít nhất 6 ký tự';
                } elseif ($new !== $confirm) {
                    $error = 'Mật khẩu xác nhận không khớp';
                } else {
                    // Verify current
                    $userRow = $this->model('User')->getById($_SESSION['user_id']);
                    $userModel = $this->model('User');
                    if (!$userModel->verifyPassword($_SESSION['user_id'], $current)) {
                        $error = 'Mật khẩu hiện tại không chính xác';
                    } else {
                        $hashed = password_hash($new, PASSWORD_DEFAULT);
                        $userModel->updatePassword($_SESSION['user_id'], $hashed);
                        $success = 'Đổi mật khẩu thành công';
                    }
                }
            } else {
                // profile update
                $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : ($user->full_name ?? '');
                $email = isset($_POST['email']) ? trim($_POST['email']) : ($user->email ?? '');
                $avatarFilename = $user->avatar ?? null;

                // Handle avatar upload
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK && !empty($_FILES['avatar']['name'])) {
                    $uploadDir = dirname(APPROOT) . '/public/uploads/avatars/';
                    
                    $res = SecureUpload::storeRasterUpload($_FILES['avatar'], $uploadDir, 'av_');
                    
                    if ($res['ok']) {
                        $avatarFilename = 'avatars/' . $res['filename']; 
                    } else {
                        $error = $res['message']; 
                    }
                }

                $payload = ['full_name' => $full_name, 'email' => $email, 'avatar' => $avatarFilename];
                $this->model('User')->updateProfile($_SESSION['user_id'], $payload);
                $_SESSION['user_name'] = $full_name ?: $_SESSION['user_name'];
                if (!empty($avatarFilename)) {
                    $_SESSION['user_avatar'] = $avatarFilename;
                }
                header('Location: ' . URLROOT . '/users/profile');
                exit();
            }
        }

        $data = ['title' => 'Hồ sơ cá nhân', 'user' => $user, 'error' => $error ?? '', 'success' => $success ?? ''];
        $this->view('client/users/profile', $data);
    }

    public function dashboard()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }
        $userServiceModel = $this->model('UserServiceModel');
        $services = $userServiceModel->getUserServices($_SESSION['user_id']);
        $data = ['title' => 'Dashboard cá nhân', 'services' => $services];
        $this->view('client/users/dashboard', $data);
    }
}
