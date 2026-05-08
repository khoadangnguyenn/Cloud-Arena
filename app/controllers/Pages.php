<?php
class Pages extends Controller {
    private $contactModel;
    private $productModel;
    private $commentModel;
    private $userModel;

    
    public function __construct() {
        $this->contactModel = $this->model('Contact');
        $this->productModel = $this->model('Product');
        $this->commentModel = $this->model('Comment');
        $this->userModel = $this->model('User');
    }

    public function index() {
        $featuredProducts = [];
        $featuredReview = null;

        try {
            $featuredProducts = $this->productModel->getHomepagePackages(4);
        } catch (Throwable $error) {
            $featuredProducts = [];
        }

        try {
            $featuredReview = $this->commentModel->getLatestFiveStarProductReview();
        } catch (Throwable $error) {
            $featuredReview = null;
        }

        $data = [
            'title' => 'Trang chủ',
            'featured_products' => $featuredProducts,
            'featured_review' => $featuredReview
        ];
        $this->view('client/pages/index', $data);
    }

    public function about() {
        $data = ['title' => 'Giới thiệu'];
        $this->view('client/about', $data);
    }

    public function contact() {
        $isLoggedIn = isset($_SESSION['user_id']);
        $currentUser = null;

        if ($isLoggedIn) {
            $currentUser = $this->userModel->getUserById((int) $_SESSION['user_id']);
            if (!$currentUser) {
                session_destroy();
                header('Location: ' . URLROOT . '/users/login');
                exit();
            }
        }
        $data = [
            'title' => 'Liên hệ',
            'is_logged_in' => $isLoggedIn,
            'form' => [
                'name' => $isLoggedIn ? trim((string) ($currentUser->full_name ?: $currentUser->username)) : '',
                'email' => $isLoggedIn ? trim((string) $currentUser->email) : '',
                'subject' => '',
                'message' => '',
                'website' => '' // honeypot
            ],
            'errors' => [],
            'success_message' => ''
        ];


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data['form']['subject'] = trim($_POST['subject'] ?? '');
            $data['form']['message'] = trim($_POST['message'] ?? '');
            $data['form']['website'] = trim($_POST['website'] ?? '');

            if ($isLoggedIn) {
                // KHÓA NGUỒN DỮ LIỆU name/email từ DB user đăng nhập
                $data['form']['name'] = trim((string) ($currentUser->full_name ?: $currentUser->username));
                $data['form']['email'] = trim((string) $currentUser->email);
            } else {
                $data['form']['name'] = trim($_POST['name'] ?? '');
                $data['form']['email'] = trim($_POST['email'] ?? '');
            }

            if ($isLoggedIn) {
                if ($data['form']['name'] === '') {
                    $data['errors']['general'] = 'Tài khoản thiếu thông tin họ tên. Vui lòng cập nhật hồ sơ.';
                }
                if (!filter_var($data['form']['email'], FILTER_VALIDATE_EMAIL)) {
                    $data['errors']['general'] = 'Email tài khoản không hợp lệ. Vui lòng cập nhật hồ sơ.';
                }
            } else {
                if ($data['form']['name'] === '') {
                    $data['errors']['name'] = 'Vui lòng nhập họ tên.';
                } elseif (strlen($data['form']['name']) > 100) {
                    $data['errors']['name'] = 'Họ tên tối đa 100 ký tự.';
                }
            
                if ($data['form']['email'] === '') {
                    $data['errors']['email'] = 'Vui lòng nhập email.';
                } elseif (!filter_var($data['form']['email'], FILTER_VALIDATE_EMAIL)) {
                    $data['errors']['email'] = 'Email không đúng định dạng.';
                } elseif (strlen($data['form']['email']) > 100) {
                    $data['errors']['email'] = 'Email tối đa 100 ký tự.';
                }
            }

            if ($data['form']['subject'] !== '' && strlen($data['form']['subject']) > 255) {
                $data['errors']['subject'] = 'Chủ đề tối đa 255 ký tự.';
            }

            if ($data['form']['message'] === '') {
                $data['errors']['message'] = 'Vui lòng nhập nội dung.';
            } elseif (strlen($data['form']['message']) < 10) {
                $data['errors']['message'] = 'Nội dung tối thiểu 10 ký tự.';
            }

            if (empty($data['errors'])) {
                $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : $this->contactModel->getOrCreateGuestUserId();
                if ($userId > 0) {
                    $created = $this->contactModel->createContact([
                        'user_id' => $userId,
                        'name' => $data['form']['name'],
                        'email' => $data['form']['email'],
                        'subject' => $data['form']['subject'],
                        'message' => $data['form']['message']
                    ]);

                    if ($created) {
                        $_SESSION['contact_success'] = 'Gửi liên hệ thành công. Chúng tôi sẽ phản hồi sớm nhất.';
                        header('Location: ' . URLROOT . '/pages/contact');
                        exit();
                    }
                }

                $data['errors']['general'] = 'Không thể gửi liên hệ lúc này. Vui lòng thử lại.';
            }
        } elseif (!empty($_SESSION['contact_success'])) {
            $data['success_message'] = $_SESSION['contact_success'];
            unset($_SESSION['contact_success']);
        }

        $this->view('client/contact', $data);
    }

    public function faq() {
        $data = ['title' => 'Hỏi đáp'];
        $this->view('client/faq', $data);
    }
}
