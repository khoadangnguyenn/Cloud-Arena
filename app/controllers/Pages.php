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
        $settings = $this->getPublicSettings();
        $featuredProducts = [];
        $featuredReview = null;

        $idsRaw = trim($settings['home_product_ids'] ?? '');
        if ($idsRaw !== '') {
            $idList = array_values(array_filter(array_map('intval', explode(',', $idsRaw)), function ($n) {
                return (int) $n > 0;
            }));
            if (!empty($idList)) {
                try {
                    $featuredProducts = $this->productModel->getActiveProductsByIdsOrdered($idList);
                } catch (Throwable $error) {
                    $featuredProducts = [];
                }
            }
        }
        if (empty($featuredProducts)) {
            try {
                $featuredProducts = $this->productModel->getHomepagePackages(4);
            } catch (Throwable $error) {
                $featuredProducts = [];
            }
        }

        $reviewKey = trim($settings['home_review_key'] ?? '');
        if ($reviewKey !== '' && preg_match('/^[1-9][0-9]*:[1-9][0-9]*$/', $reviewKey)) {
            $rkParts = explode(':', $reviewKey, 2);
            try {
                $featuredReview = $this->commentModel->getApprovedProductReviewByKey((int) $rkParts[0], (int) $rkParts[1]);
            } catch (Throwable $error) {
                $featuredReview = null;
            }
        }

        $data = [
            'title' => 'Trang chủ',
            'featured_products' => $featuredProducts,
            'featured_review' => $featuredReview
        ];
        $this->view('client/pages/index', $data);
    }

    public function about() {
        $aboutModel = $this->model('About');
        $about = $aboutModel->get();
        $data = ['title' => 'Giới thiệu', 'about' => $about];
        $this->view('client/about', $data);
    }

    public function contact() {
        $isLoggedIn = isset($_SESSION['user_id']);
        $currentUser = null;

        if ($isLoggedIn) {
            $currentUser = $this->userModel->getUserById((int) $_SESSION['user_id']);
        }

        if (empty($_SESSION['csrf_contact'])) {
            $_SESSION['csrf_contact'] = bin2hex(random_bytes(32));
        }

        $data = [
            'title' => 'Liên hệ',
            'is_logged_in' => $isLoggedIn,
            'csrf_token' => $_SESSION['csrf_contact'],
            'form' => [
                'name' => $isLoggedIn ? trim((string) ($currentUser->full_name ?? $currentUser->username ?? '')) : '',
                'email' => $isLoggedIn ? trim((string) ($currentUser->email ?? '')) : '',
                'subject' => '',
                'message' => '',
                'website' => ''
            ],
            'errors' => [],
            'success_message' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $submittedToken = trim((string) ($_POST['csrf_token'] ?? ''));
            if (!hash_equals((string) ($_SESSION['csrf_contact'] ?? ''), $submittedToken)) {
                $data['errors']['general'] = 'Yêu cầu không hợp lệ.';
                $this->view('client/contact', $data);
                return;
            }

            if (trim((string) ($_POST['website'] ?? '')) !== '') {
                header('Location: ' . URLROOT . '/pages/contact');
                exit();
            }

            $data['form']['subject'] = trim($_POST['subject'] ?? '');
            $data['form']['message'] = trim($_POST['message'] ?? '');

            if ($isLoggedIn && $currentUser) {
                $data['form']['name'] = trim((string) ($currentUser->full_name ?: $currentUser->username));
                $data['form']['email'] = trim((string) $currentUser->email);
            } else {
                $data['form']['name'] = trim($_POST['name'] ?? '');
                $data['form']['email'] = trim($_POST['email'] ?? '');
            }

            if ($data['form']['name'] === '') $data['errors']['name'] = 'Vui lòng nhập họ tên.';
            if (!filter_var($data['form']['email'], FILTER_VALIDATE_EMAIL)) $data['errors']['email'] = 'Email không hợp lệ.';
            if (strlen($data['form']['message']) < 10) $data['errors']['message'] = 'Nội dung quá ngắn.';

            if (empty($data['errors'])) {
                $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : $this->contactModel->getOrCreateGuestUserId();
                $created = $this->contactModel->createContact([
                    'user_id' => $userId,
                    'name' => $data['form']['name'],
                    'email' => $data['form']['email'],
                    'subject' => $data['form']['subject'],
                    'message' => $data['form']['message']
                ]);

                if ($created) {
                    $_SESSION['contact_success'] = 'Gửi liên hệ thành công.';
                    header('Location: ' . URLROOT . '/pages/contact');
                    exit();
                }
            }
        } elseif (!empty($_SESSION['contact_success'])) {
            $data['success_message'] = $_SESSION['contact_success'];
            unset($_SESSION['contact_success']);
        }

        $this->view('client/contact', $data);
    }

    public function faq() {
        $faqModel = $this->model('Faq');
        $perPage = 8;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $category = isset($_GET['category']) ? trim($_GET['category']) : null;
        $q = isset($_GET['q']) ? trim($_GET['q']) : null;
        $cats = $faqModel->getCategories();
        $faqs = [];
        $paginationHtml = '';
        
        if($q){
            $total = $faqModel->countSearchActive($q, $category);
            require_once APPROOT . '/helpers/Pagination.php';
            $base = URLROOT . '/pages/faq?q=' . urlencode($q);
            if($category) $base .= '&category=' . urlencode($category);
            $pagination = new Pagination($total, $perPage, $page, $base);
            $faqs = $faqModel->searchActive($pagination->getLimit(), $pagination->getOffset(), $q, $category);
            $paginationHtml = $pagination->createLinks();
        } elseif($category){
            $total = $faqModel->countActive($category);
            require_once APPROOT . '/helpers/Pagination.php';
            $base = URLROOT . '/pages/faq?category=' . urlencode($category);
            $pagination = new Pagination($total, $perPage, $page, $base);
            $faqs = $faqModel->getPageActive($pagination->getLimit(), $pagination->getOffset(), $category);
            $paginationHtml = $pagination->createLinks();
        } else {
            // General FAQ list if no search or category
            $total = $faqModel->countActive();
            require_once APPROOT . '/helpers/Pagination.php';
            $pagination = new Pagination($total, $perPage, $page, URLROOT . '/pages/faq');
            $faqs = $faqModel->getPageActive($pagination->getLimit(), $pagination->getOffset());
            $paginationHtml = $pagination->createLinks();
        }
        
        $data = [
            'title' => 'Hỏi đáp',
            'faqs' => $faqs,
            'pagination' => $paginationHtml,
            'categories' => $cats,
            'current_category' => $category
        ];
        $this->view('client/faq', $data);
    }

    // AJAX: receive a FAQ/chat message from client
    public function faqMessage(){
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => 'Invalid method']);
            return;
        }
        $faqModel = $this->model('Faq');
        $name = isset($_POST['name']) ? trim((string)$_POST['name']) : null;
        $email = isset($_POST['email']) ? trim((string)$_POST['email']) : null;
        $category = isset($_POST['category']) ? trim((string)$_POST['category']) : null;
        $message = isset($_POST['message']) ? trim((string)$_POST['message']) : '';
        $page_url = isset($_POST['page_url']) ? trim((string)$_POST['page_url']) : null;

        if($message === ''){
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => 'Nội dung trống']);
            return;
        }

        $payload = ['name' => $name, 'email' => $email, 'category' => $category, 'message' => $message, 'page_url' => $page_url, 'status' => 'new'];
        $ok = $faqModel->createMessage($payload);
        header('Content-Type: application/json; charset=utf-8');
        if($ok) echo json_encode(['success' => true]); else echo json_encode(['success' => false, 'error' => 'Không thể lưu tin nhắn']);
    }

    // AJAX: fetch message history by email or page_url
    public function faqMessages(){
        if($_SERVER['REQUEST_METHOD'] !== 'GET'){
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => 'Invalid method']);
            return;
        }
        $faqModel = $this->model('Faq');
        $email = isset($_GET['email']) ? trim((string)$_GET['email']) : null;
        $page_url = isset($_GET['page_url']) ? trim((string)$_GET['page_url']) : null;
        if(!$email && !$page_url){
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => 'Missing parameters']);
            return;
        }
        $msgs = $faqModel->getConversation($email ?: null, $page_url ?: null, 200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'messages' => $msgs]);
    }
}
