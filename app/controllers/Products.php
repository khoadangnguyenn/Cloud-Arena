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
        $isAjaxSearch = isset($_GET['ajax_search']) && $_GET['ajax_search'] == '1';
        $isAjaxPage = isset($_GET['ajax_page']) && $_GET['ajax_page'] == '1';

        $products = $this->productModel->getProducts($limit, $offset, $keyword);
        
        // Nếu là yêu cầu Live Search (trả về JSON)
        if ($isAjaxSearch) {
            header('Content-Type: application/json');
            echo json_encode(['products' => $products]);
            exit;
        }

        $totalProducts = $this->productModel->getTotalProducts($keyword);
        $totalPages = ceil($totalProducts / $limit);

        $data = [
            'title' => 'Sản phẩm Game Server - Cloud Arena',
            'description' => 'Danh sách các gói Game Server hiệu năng cao, tối ưu cho dự án của bạn tại Cloud Arena. Hỗ trợ nhiều cấu hình đa dạng.',
            'products' => $products,
            'keyword' => $keyword,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ];

        // Nếu là yêu cầu Pagination AJAX (Chỉ trả về view mà không kèm header/footer toàn trang nếu làm router cẩn thận, 
        // ở đây để đơn giản ta load lại view nhưng JS bên frontend chỉ lấy đúng block cần thiết)
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

        $data = [
            'title' => $product->name . ' - Cloud Arena',
            'description' => 'Thuê server ' . $product->name . ' cấu hình cao tại Cloud Arena.',
            'product' => $product
        ];
        $this->view('client/products/show', $data);
    }
        
}