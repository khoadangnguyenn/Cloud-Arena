<?php
class Pages extends Controller {
    private $contactModel;
    private $adminNotificationModel;
    private $productModel;
    private $commentModel;
    private $userModel;

    
    public function __construct() {
        $this->contactModel = $this->model('Contact');
        $this->adminNotificationModel = $this->model('AdminNotification');
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
            'description' => 'Thuê máy chủ game, gói NVMe, CPU mạnh và băng thông ổn định — ' . SITENAME . '.',
            'featured_products' => $featuredProducts,
            'featured_review' => $featuredReview
        ];
        $this->view('client/pages/index', $data);
    }

    public function about() {
        $data = [
            'title' => 'Giới thiệu',
            'description' => 'Lịch sử hình thành, sứ mệnh và đội ngũ kỹ sư ' . SITENAME . ' — nền tảng game hosting & modpack.'
        ];
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
        if (empty($_SESSION['csrf_contact'])) {
            $_SESSION['csrf_contact'] = bin2hex(random_bytes(32));
        }
        $data = [
            'title' => 'Liên hệ',
            'description' => 'Liên hệ ' . SITENAME . ': hotline, email, địa chỉ và form hỗ trợ nhanh.',
            'is_logged_in' => $isLoggedIn,
            'csrf_token' => $_SESSION['csrf_contact'],
            'form' => [
                'name' => $isLoggedIn ? trim((string) ($currentUser->full_name ?: $currentUser->username)) : '',
                'email' => $isLoggedIn ? trim((string) $currentUser->email) : '',
                'subject' => '',
                'message' => '',
                'website' => ''
            ],
            'errors' => [],
            'success_message' => ''
        ];


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF validation
            $submittedToken = trim((string) ($_POST['csrf_token'] ?? ''));
            if (!hash_equals((string) ($_SESSION['csrf_contact'] ?? ''), $submittedToken)) {
                $data['errors']['general'] = 'Yêu cầu không hợp lệ. Vui lòng tải lại trang và thử lại.';
                $this->view('client/contact', $data);
                return;
            }

            // Honeypot – silently succeed if a bot filled the hidden field
            if (trim((string) ($_POST['website'] ?? '')) !== '') {
                $_SESSION['contact_success'] = 'Gửi liên hệ thành công. Chúng tôi sẽ phản hồi sớm nhất.';
                header('Location: ' . URLROOT . '/pages/contact');
                exit();
            }

            // Rate limiting: 60 seconds between submissions per session
            if ((time() - (int) ($_SESSION['contact_last_submit'] ?? 0)) < 60) {
                $data['errors']['general'] = 'Bạn vừa gửi liên hệ. Vui lòng đợi ít nhất 60 giây trước khi gửi tiếp.';
                $this->view('client/contact', $data);
                return;
            }

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
            } elseif (strlen($data['form']['message']) > 5000) {
                $data['errors']['message'] = 'Nội dung tối đa 5000 ký tự.';
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
                        try {
                            $createdAt = trim((string) ($created['created_at'] ?? ''));
                            if ($createdAt !== '') {
                                $this->adminNotificationModel->createTicketCreatedNotification([
                                    'user_id' => (int) ($created['user_id'] ?? 0),
                                    'contact_id' => (int) ($created['contact_id'] ?? 0),
                                    'name' => $data['form']['name'],
                                    'email' => $data['form']['email'],
                                    'subject' => $data['form']['subject'],
                                    'created_at' => $createdAt
                                ]);
                            }
                        } catch (Throwable $error) {
                            // Keep contact flow successful even if notification sync fails.
                        }
                        $_SESSION['contact_success'] = 'Gửi liên hệ thành công. Chúng tôi sẽ phản hồi sớm nhất.';
                        $_SESSION['contact_last_submit'] = time();
                        $_SESSION['csrf_contact'] = bin2hex(random_bytes(32));
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
        $data = [
            'title' => 'Hỏi đáp',
            'description' => 'Câu hỏi thường gặp về cho thuê server game, bảng giá và hỗ trợ kỹ thuật — ' . SITENAME . '.'
        ];
        $this->view('client/faq', $data);
    }
}
