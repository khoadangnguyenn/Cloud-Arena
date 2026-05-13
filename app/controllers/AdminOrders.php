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

    // public function updateStatus($orderId) {
    //     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //         // Lấy dữ liệu gửi lên (có thể là form-data hoặc JSON raw tùy cách fetch)
    //         $inputData = json_decode(file_get_contents('php://input'), true);
    //         $status = isset($inputData['status']) ? filter_var($inputData['status'], FILTER_SANITIZE_STRING) : filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

    //         $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            
    //         $valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
    //         if (!in_array($status, $valid_statuses)) {
    //             if ($isAjax) {
    //                 echo json_encode(['success' => false, 'message' => 'Trạng thái không hợp lệ.']);
    //                 exit;
    //             }
    //             die('Trạng thái không hợp lệ.');
    //         }

    //         if ($this->orderModel->updateOrderStatus($orderId, $status)) {
    //             if ($isAjax) {
    //                 echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thành công!', 'new_status' => $status]);
    //                 exit;
    //             }
    //             header('Location: ' . URLROOT . '/adminorders');
    //         } else {
    //             if ($isAjax) {
    //                 echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật CSDL.']);
    //                 exit;
    //             }
    //             die('Có lỗi xảy ra khi cập nhật trạng thái.');
    //         }
    //     }
    // }
    public function updateStatus($orderId) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
            
            $valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
            if (!in_array($status, $valid_statuses)) {
                die('Trạng thái không hợp lệ.');
            }

            // Nếu Admin duyệt đơn thành Completed -> Cấp phát Server
            if ($status == 'completed') {
                $currentOrder = $this->orderModel->getOrderById($orderId);
                
                if ($currentOrder && $currentOrder->status != 'completed') {
                    $this->orderModel->provisionServices($orderId);
                }
            }

            if ($this->orderModel->updateOrderStatus($orderId, $status)) {
                header('Location: ' . URLROOT . '/admin/orders');
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