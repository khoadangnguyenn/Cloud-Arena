<?php
class Products extends Controller {
    private $productModel;

    public function __construct() {
        $this->productModel = $this->model('Product');
    }

    public function index() {
        $limit = 6; 
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
        $categoryId = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;
        $minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;

        $isAjaxSearch = isset($_GET['ajax_search']) && $_GET['ajax_search'] == '1';
        $isAjaxPage = isset($_GET['ajax_page']) && $_GET['ajax_page'] == '1';

        $products = $this->productModel->getProducts($limit, $offset, $keyword, false, $categoryId, $minPrice, $maxPrice);
        
        if ($isAjaxSearch) {
            header('Content-Type: application/json');
            echo json_encode(['products' => $products]);
            exit;
        }

        $totalProducts = $this->productModel->getTotalProducts($keyword, false, $categoryId, $minPrice, $maxPrice);
        $totalPages = ceil($totalProducts / $limit);
        $categories = $this->productModel->getCategories();

        $data = [
            'title' => 'Sản phẩm Game Server - Cloud Arena',
            'description' => 'Danh sách các gói Game Server hiệu năng cao.',
            'products' => $products,
            'categories' => $categories, 
            'keyword' => $keyword,
            'categoryId' => $categoryId,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ];
        if ($isAjaxPage) {
            $this->view('client/products/index', $data);
            exit;
        }

        $this->view('client/products/index', $data);
    }

    public function show($slug) {
        $product = $this->productModel->getProductBySlug($slug);

        if (!$product) {
            die('Sản phẩm không tồn tại!'); 
        }

        $relatedProducts = $this->productModel->getRelatedProducts($product->category_id, $product->id, 4);

        // --- LOGIC REVIEW ---
        $reviews = $this->productModel->getReviews($product->id);
        $canReview = false;
        $hasReviewed = false;

        // Nếu đã đăng nhập, kiểm tra xem có quyền đánh giá không
        if (isset($_SESSION['user_id'])) {
            $canReview = $this->productModel->canReview($_SESSION['user_id'], $product->id);
            $hasReviewed = $this->productModel->hasReviewed($_SESSION['user_id'], $product->id);
        }

        // Tính trung bình sao
        $avgRating = 0;
        if (count($reviews) > 0) {
            $totalStars = 0;
            foreach ($reviews as $r) { $totalStars += $r->rating; }
            $avgRating = round($totalStars / count($reviews), 1);
        }

        $data = [
            'title' => $product->name . ' - Cloud Arena',
            'description' => 'Thuê server ' . $product->name . ' hiệu suất cao.',
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'reviews' => $reviews,
            'canReview' => $canReview,
            'hasReviewed' => $hasReviewed,
            'avgRating' => $avgRating
        ];
        $this->view('client/products/show', $data);
    }

    // Hàm nhận dữ liệu từ Form đánh giá
    public function submitReview() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $product_id = $_POST['product_id'];
            $slug = $_POST['slug'];
            $rating = (int)$_POST['rating'];
            $comment = trim($_POST['comment']);

            // Validate và lưu DB
            if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
                if ($this->productModel->canReview($_SESSION['user_id'], $product_id) && !$this->productModel->hasReviewed($_SESSION['user_id'], $product_id)) {
                    $this->productModel->addReview($_SESSION['user_id'], $product_id, $rating, $comment);
                }
            }
            
            // Trở lại trang chi tiết sản phẩm
            header('Location: ' . URLROOT . '/products/show/' . $slug);
            exit();
        }
    }

        
}