<?php
class AdminOrders extends Controller {
    private $orderModel;

    public function __construct() {
        if (!isAdmin()) {
            header('Location: ' . URLROOT . '/users/login');
            exit;
        }
        $this->orderModel = $this->model('Order');
    }

    public function index() {
        $limit = 10;
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $orders = $this->orderModel->getOrders($limit, $offset);
        $totalOrders = $this->orderModel->getTotalOrders();
        $totalPages = ceil($totalOrders / $limit);

        $data = [
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ];

        $this->view('admin/orders/index', $data);
    }
    public function updateStatus($orderId) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // 1. Kiểm tra mã bảo mật CSRF (Khớp với thẻ input hidden ở View)
            if (!$this->verifyCsrf('csrf_admin')) {
                die('Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
            }

            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
            
            $valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
            if (!in_array($status, $valid_statuses)) {
                die('Trạng thái không hợp lệ.');
            }

            // 2. Nếu Admin duyệt đơn thành Completed -> Cấp phát Server
            if ($status == 'completed') {
                $currentOrder = $this->orderModel->getOrderById($orderId);
                
                if ($currentOrder && $currentOrder->status != 'completed') {
                    $this->orderModel->provisionServices($orderId);
                }
            }

            // 3. Cập nhật vào DB và chuyển hướng
            if ($this->orderModel->updateOrderStatus($orderId, $status)) {
                header('Location: ' . URLROOT . '/admin/orders');
                exit(); // Thêm exit để dừng thực thi sau khi chuyển hướng
            } else {
                die('Có lỗi xảy ra khi cập nhật trạng thái.');
            }
        }
    }
    public function show($id) {
        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            die('Đơn hàng không tồn tại!');
        }
        
        // Lấy danh sách sản phẩm trong đơn hàng này
        $items = $this->orderModel->getOrderItems($id);
        
        $data = [
            'order' => $order,
            'items' => $items
        ];
        
        $this->view('admin/orders/show', $data);
    }
}