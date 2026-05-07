<?php
class Users extends Controller {

    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }
    }

    // GET /users/login  — show form
    // POST /users/login — process credentials
    public function login() {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $data = [
                'title'        => 'Đăng nhập',
                'username'     => htmlspecialchars($username),
                'password'     => '',
                'username_err' => '',
                'password_err' => '',
                'login_err'    => '',
            ];

            if (empty($username)) {
                $data['username_err'] = 'Vui lòng nhập tên đăng nhập.';
            }
            if (empty($password)) {
                $data['password_err'] = 'Vui lòng nhập mật khẩu.';
            }

            if (empty($data['username_err']) && empty($data['password_err'])) {
                $loggedInUser = $this->userModel->login($username, $password);

                if ($loggedInUser) {
                    if ($loggedInUser->status === 'banned') {
                        $data['login_err'] = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.';
                        $this->view('client/users/login', $data);
                        return;
                    }

                    // Set session
                    $_SESSION['user_id']   = $loggedInUser->id;
                    $_SESSION['user_name'] = $loggedInUser->full_name ?: $loggedInUser->username;
                    $_SESSION['user_role'] = $loggedInUser->role;
                    $_SESSION['user_avatar'] = $loggedInUser->avatar ?? '';

                    if ($loggedInUser->role === 'admin') {
                        header('Location: ' . URLROOT . '/admin');
                    } else {
                        header('Location: ' . URLROOT);
                    }
                    exit();
                } else {
                    $data['login_err'] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
                }
            }

            $this->view('client/users/login', $data);
            return;
        }

        // GET — show blank form
        $data = [
            'title'        => 'Đăng nhập',
            'username'     => '',
            'password'     => '',
            'username_err' => '',
            'password_err' => '',
            'login_err'    => '',
        ];
        $this->view('client/users/login', $data);
    }

    // GET /users/register  — show form
    // POST /users/register — process registration
    public function register() {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username         = trim($_POST['username'] ?? '');
            $email            = trim($_POST['email'] ?? '');
            $password         = trim($_POST['password'] ?? '');
            $confirm_password = trim($_POST['confirm_password'] ?? '');

            $data = [
                'title'                => 'Đăng ký',
                'username'             => htmlspecialchars($username),
                'email'                => htmlspecialchars($email),
                'password'             => '',
                'confirm_password'     => '',
                'username_err'         => '',
                'email_err'            => '',
                'password_err'         => '',
                'confirm_password_err' => '',
            ];

            // Validate username
            if (empty($username)) {
                $data['username_err'] = 'Vui lòng nhập tên đăng nhập.';
            } elseif (strlen($username) < 3 || strlen($username) > 50) {
                $data['username_err'] = 'Tên đăng nhập phải từ 3 đến 50 ký tự.';
            } elseif ($this->userModel->findUserByUsername($username)) {
                $data['username_err'] = 'Tên đăng nhập đã tồn tại.';
            }

            // Validate email
            if (empty($email)) {
                $data['email_err'] = 'Vui lòng nhập email.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Email không hợp lệ.';
            } elseif ($this->userModel->findUserByEmail($email)) {
                $data['email_err'] = 'Email này đã được sử dụng.';
            }

            // Validate password
            if (empty($password)) {
                $data['password_err'] = 'Vui lòng nhập mật khẩu.';
            } elseif (strlen($password) < 6) {
                $data['password_err'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
            }

            // Validate confirm password
            if (empty($confirm_password)) {
                $data['confirm_password_err'] = 'Vui lòng xác nhận mật khẩu.';
            } elseif ($password !== $confirm_password) {
                $data['confirm_password_err'] = 'Mật khẩu xác nhận không khớp.';
            }

            $hasErrors = $data['username_err'] || $data['email_err']
                      || $data['password_err'] || $data['confirm_password_err'];

            if (!$hasErrors) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);

                $registerData = [
                    'username' => $username,
                    'email'    => $email,
                    'password' => $data['password'],
                ];

                if ($this->userModel->register($registerData)) {
                    $_SESSION['register_success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
                    header('Location: ' . URLROOT . '/users/login');
                    exit();
                } else {
                    die('Có lỗi xảy ra khi đăng ký tài khoản.');
                }
            }

            $this->view('client/users/register', $data);
            return;
        }

        // GET — show blank form
        $data = [
            'title'                => 'Đăng ký',
            'username'             => '',
            'email'                => '',
            'password'             => '',
            'confirm_password'     => '',
            'username_err'         => '',
            'email_err'            => '',
            'password_err'         => '',
            'confirm_password_err' => '',
        ];
        $this->view('client/users/register', $data);
    }

    // GET /users/logout
    public function logout() {
        session_destroy();
        header('Location: ' . URLROOT . '/users/login');
        exit();
    }

    public function profile() {
        $this->requireAuth();
        $currentUser = $this->userModel->getUserById((int) $_SESSION['user_id']);
        if (!$currentUser) {
            session_destroy();
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }

        $errors = [
            'full_name' => '',
            'email' => '',
            'current_password' => '',
            'new_password' => '',
            'confirm_password' => '',
            'avatar' => ''
        ];
        $successMessage = $_SESSION['profile_success'] ?? '';
        unset($_SESSION['profile_success']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = trim($_POST['action'] ?? '');

            if ($action === 'profile_info') {
                $fullName = trim($_POST['full_name'] ?? '');
                $email = trim($_POST['email'] ?? '');

                if ($fullName === '') {
                    $errors['full_name'] = 'Họ và tên không được để trống.';
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Email không hợp lệ.';
                } elseif ($email !== $currentUser->email && $this->userModel->findUserByEmail($email)) {
                    $errors['email'] = 'Email đã được sử dụng.';
                }

                if ($errors['full_name'] === '' && $errors['email'] === '') {
                    $updated = $this->userModel->updateProfile((int) $_SESSION['user_id'], $fullName, $email);
                    if ($updated) {
                        $_SESSION['user_name'] = $fullName;
                        $_SESSION['profile_success'] = 'Đã cập nhật thông tin cá nhân.';
                        header('Location: ' . URLROOT . '/users/profile');
                        exit();
                    }
                }
            }

            if ($action === 'change_password') {
                $currentPassword = trim($_POST['current_password'] ?? '');
                $newPassword = trim($_POST['new_password'] ?? '');
                $confirmPassword = trim($_POST['confirm_password'] ?? '');

                if ($currentPassword === '') {
                    $errors['current_password'] = 'Vui lòng nhập mật khẩu hiện tại.';
                } elseif (!password_verify($currentPassword, $currentUser->password)) {
                    $errors['current_password'] = 'Mật khẩu hiện tại không chính xác.';
                }

                if (strlen($newPassword) < 6) {
                    $errors['new_password'] = 'Mật khẩu mới cần ít nhất 6 ký tự.';
                }
                if ($confirmPassword !== $newPassword) {
                    $errors['confirm_password'] = 'Xác nhận mật khẩu không khớp.';
                }

                if ($errors['current_password'] === '' && $errors['new_password'] === '' && $errors['confirm_password'] === '') {
                    $updated = $this->userModel->updatePassword((int) $_SESSION['user_id'], password_hash($newPassword, PASSWORD_DEFAULT));
                    if ($updated) {
                        $_SESSION['profile_success'] = 'Đã cập nhật mật khẩu.';
                        header('Location: ' . URLROOT . '/users/profile');
                        exit();
                    }
                }
            }

            if ($action === 'upload_avatar') {
                if (!empty($_FILES['avatar']['name'])) {
                    $uploadDir = APPROOT . '/../public/uploads/avatars/';
                    if (!is_dir($uploadDir)) {
                        @mkdir($uploadDir, 0755, true);
                    }

                    $extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (!in_array($extension, $allowed, true)) {
                        $errors['avatar'] = 'Chỉ hỗ trợ ảnh JPG, PNG, GIF hoặc WEBP.';
                    } elseif ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
                        $errors['avatar'] = 'Dung lượng ảnh tối đa là 2MB.';
                    } else {
                        $safeUsername = strtolower((string) ($currentUser->username ?? 'user' . (int) $_SESSION['user_id']));
                        $safeUsername = preg_replace('/[^a-z0-9_-]+/i', '-', $safeUsername);
                        $safeUsername = trim((string) $safeUsername, '-_');
                        if ($safeUsername === '') {
                            $safeUsername = 'user' . (int) $_SESSION['user_id'];
                        }

                        $avatarFileName = $safeUsername . '.' . $extension;
                        $avatarRelativeUrl = '/uploads/avatars/' . $avatarFileName;
                        $target = $uploadDir . $avatarFileName;

                        foreach ($allowed as $oldExt) {
                            $oldCandidate = $uploadDir . $safeUsername . '.' . $oldExt;
                            if (is_file($oldCandidate) && $oldCandidate !== $target) {
                                @unlink($oldCandidate);
                            }
                        }

                        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
                            $this->userModel->updateAvatar((int) $_SESSION['user_id'], $avatarRelativeUrl);
                            $_SESSION['user_avatar'] = $avatarRelativeUrl;
                            $_SESSION['profile_success'] = 'Đã cập nhật ảnh đại diện.';
                            header('Location: ' . URLROOT . '/users/profile');
                            exit();
                        }
                        $errors['avatar'] = 'Không thể tải ảnh lên. Vui lòng thử lại.';
                    }
                } else {
                    $errors['avatar'] = 'Vui lòng chọn ảnh đại diện.';
                }
            }
        }

        $user = $this->userModel->getUserById((int) $_SESSION['user_id']);
        if ($user) {
            $_SESSION['user_avatar'] = $user->avatar ?? '';
        }
        $data = [
            'title' => 'Hồ sơ người dùng',
            'user' => $user,
            'errors' => $errors,
            'success_message' => $successMessage
        ];
        $this->view('client/users/profile', $data);
    }
}
