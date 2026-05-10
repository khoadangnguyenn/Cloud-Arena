<?php
class AdminProducts extends Controller {
    private $productModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }
        $this->productModel = $this->model('Product');
    }

    public function index() {
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $perPage = 6;
        $totalPackages = $this->productModel->countAdminPackages();
        $lastPage = max(1, (int) ceil($totalPackages / $perPage));
        if ($page > $lastPage) {
            $page = $lastPage;
        }

        $flash = $_SESSION['admin_products_flash'] ?? null;
        unset($_SESSION['admin_products_flash']);

        $data = [
            'title' => 'Quản lý dịch vụ',
            'packages' => $this->productModel->getAdminPackages($page, $perPage),
            'pagination' => [
                'page' => $page,
                'last_page' => $lastPage,
                'total' => $totalPackages,
                'per_page' => $perPage
            ],
            'flash' => $flash
        ];
        $this->view('admin/products/index', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/adminproducts');
            exit();
        }

        if (!$this->verifyCsrf('csrf_admin')) {
            $_SESSION['admin_products_flash'] = ['type' => 'danger', 'message' => 'Yêu cầu không hợp lệ.'];
            header('Location: ' . URLROOT . '/adminproducts');
            exit();
        }

        $name = trim($_POST['name'] ?? '');
        $price = (float) ($_POST['price'] ?? 0);
        $ramGb = (int) ($_POST['ram_gb'] ?? 0);
        $cpuCores = (int) ($_POST['cpu_cores'] ?? 0);
        $diskGb = (int) ($_POST['disk_gb'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $imageUrl = trim($_POST['image_url'] ?? '');

        if ($name === '' || $price <= 0) {
            $_SESSION['admin_products_flash'] = [
                'type' => 'danger',
                'message' => 'Tên gói và giá tiền là bắt buộc.'
            ];
            header('Location: ' . URLROOT . '/adminproducts');
            exit();
        }

        $created = $this->productModel->createPackage([
            'name' => $name,
            'price' => $price,
            'ram_mb' => max(0, $ramGb * 1024),
            'cpu_cores' => max(0, $cpuCores),
            'disk_gb' => max(0, $diskGb),
            'description' => $description,
            'image_url' => $imageUrl
        ]);

        $_SESSION['admin_products_flash'] = [
            'type' => $created ? 'success' : 'danger',
            'message' => $created ? 'Đã thêm gói dịch vụ mới.' : 'Không thể thêm gói dịch vụ.'
        ];

        header('Location: ' . URLROOT . '/adminproducts');
        exit();
    }

    public function delete($id = 0) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/adminproducts');
            exit();
        }

        if (!$this->verifyCsrf('csrf_admin')) {
            $_SESSION['admin_products_flash'] = ['type' => 'danger', 'message' => 'Yêu cầu không hợp lệ.'];
            header('Location: ' . URLROOT . '/adminproducts');
            exit();
        }

        $deleted = $this->productModel->deletePackage((int) $id);
        $_SESSION['admin_products_flash'] = [
            'type' => $deleted ? 'success' : 'danger',
            'message' => $deleted ? 'Đã xóa gói dịch vụ.' : 'Không thể xóa gói dịch vụ.'
        ];

        header('Location: ' . URLROOT . '/adminproducts');
        exit();
    }
}
