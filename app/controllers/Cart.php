<?php
class Cart extends Controller {
    private $orderModel;
    private $productModel;
    private $cartModel;

    public function __construct() {
        $this->orderModel = $this->model('Order');
        $this->productModel = $this->model('Product');
        $this->cartModel = $this->model('CartModel'); // Khởi tạo CartModel mới
    }

    public function index() {
        $cartId = $this->cartModel->getCartId();
        $cartItems = $this->cartModel->getCartItems($cartId);
        $totalAmount = 0;

        // Tính tổng tiền dựa trên subtotal đã query từ DB
        if (!empty($cartItems)) {
            foreach ($cartItems as $item) {
                $totalAmount += $item->subtotal;
            }
        }

        $data = [
            'title' => 'Giỏ hàng của bạn - Cloud Arena',
            'description' => 'Kiểm tra và quản lý các gói Server đang có trong giỏ hàng Cloud Arena của bạn trước khi tiến hành thanh toán.',
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount
        ];

        $this->view('client/cart/index', $data);
    }

    public function add($productId = null) {
        if (!$productId) {
            header('Location: ' . URLROOT . '/products');
            exit;
        }

        $product = $this->productModel->getProductById($productId);
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($_GET['ajax']) && $_GET['ajax'] == '1') || (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json') !== false);
        
        if (!$product) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại hoặc đã bị ẩn!']);
                exit;
            }
            header('Location: ' . URLROOT . '/products?error=not_found');
            exit;
        }

        $quantity = 1;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $rawInput = file_get_contents('php://input');
            $inputData = json_decode($rawInput, true);
            if (is_array($inputData) && isset($inputData['quantity'])) {
                $quantity = (int)$inputData['quantity'];
            } else {
                $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            }
        }
        
        if ($quantity <= 0) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Số lượng không hợp lệ']);
                exit;
            }
            header('Location: ' . URLROOT . '/products/show/' . $productId . '?error=invalid_quantity');
            exit; 
        }

        // GỌI DB THÊM VÀO GIỎ HÀNG
        $cartId = $this->cartModel->getCartId();
        $this->cartModel->addItem($cartId, $productId, $quantity);
        $cartCount = $this->cartModel->getTotalItemCount($cartId);

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => 'Đã thêm ' . $product->name . ' vào giỏ hàng!', 
                'cartCount' => $cartCount
            ]);
            exit;
        }

        header('Location: ' . URLROOT . '/cart');
        exit;
    }

    public function remove($productId = null) {
        $acceptHeader = isset($_SERVER['HTTP_ACCEPT']) ? strtolower($_SERVER['HTTP_ACCEPT']) : '';
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
            || (isset($_GET['ajax']) && $_GET['ajax'] == '1')
            || (strpos($acceptHeader, 'application/json') !== false);

        // BẮT BỆNH Ở ĐÂY: Nếu URL không có $productId, ta lôi nó ra từ cục JSON Javascript gửi lên
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$productId) {
            $rawInput = file_get_contents('php://input');
            $inputData = json_decode($rawInput, true);
            if (is_array($inputData) && isset($inputData['productId'])) {
                $productId = $inputData['productId'];
            } elseif (isset($_POST['productId'])) {
                $productId = $_POST['productId'];
            }
        }

        // Nếu nỗ lực tìm kiếm ID vẫn thất bại, báo lỗi đàng hoàng cho JS
        if (!$productId) {
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy ID sản phẩm để xóa']);
                exit;
            }
            header('Location: ' . URLROOT . '/cart');
            exit;
        }

        $cartId = $this->cartModel->getCartId();
        $this->cartModel->removeItem($cartId, $productId);
        
        if ($isAjax) {
            $cartItems = $this->cartModel->getCartItems($cartId);
            $cartCount = $this->cartModel->getTotalItemCount($cartId);
            $totalAmount = 0;
            
            if (!empty($cartItems)) {
                foreach ($cartItems as $item) {
                    $totalAmount += $item->subtotal;
                }
            }
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => true, 
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
                'cartCount' => $cartCount,
                'totalAmount' => number_format($totalAmount, 0, ',', '.') . 'đ',
                'isEmpty' => empty($cartItems)
            ]);
            exit;
        }

        header('Location: ' . URLROOT . '/cart');
        exit;
    }

    public function update() {
        $acceptHeader = isset($_SERVER['HTTP_ACCEPT']) ? strtolower($_SERVER['HTTP_ACCEPT']) : '';
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
            || (isset($_GET['ajax']) && $_GET['ajax'] == '1')
            || (strpos($acceptHeader, 'application/json') !== false);

        $cartId = $this->cartModel->getCartId();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $productId = null;
            $quantity = null;

            // Đọc dữ liệu JSON từ các nút bấm (+), (-) 
            $rawInput = file_get_contents('php://input');
            $inputData = json_decode($rawInput, true);

            if (is_array($inputData) && isset($inputData['productId']) && isset($inputData['quantity'])) {
                $productId = (int)$inputData['productId'];
                $quantity = (int)$inputData['quantity'];
            } elseif (isset($_POST['productId']) && isset($_POST['quantity'])) {
                $productId = (int)$_POST['productId'];
                $quantity = (int)$_POST['quantity'];
            }

            if ($productId !== null && $quantity !== null) {
                if ($quantity > 0) {
                    $this->cartModel->updateItemQuantity($cartId, $productId, $quantity);
                } else {
                    $this->cartModel->removeItem($cartId, $productId);
                }
                
                $cartItems = $this->cartModel->getCartItems($cartId);
                $cartCount = $this->cartModel->getTotalItemCount($cartId);
                $itemSubtotal = 0;
                $totalAmount = 0;
                
                if (!empty($cartItems)) {
                    foreach ($cartItems as $item) {
                        $totalAmount += $item->subtotal;
                        if ($item->product_id == $productId) {
                            $itemSubtotal = $item->subtotal;
                        }
                    }
                }
                
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => true,
                        'itemSubtotal' => number_format($itemSubtotal, 0, ',', '.') . 'đ',
                        'totalAmount' => number_format($totalAmount, 0, ',', '.') . 'đ',
                        'cartCount' => $cartCount
                    ]);
                    exit;
                }
            } elseif (isset($_POST['quantities'])) {
                foreach ($_POST['quantities'] as $pId => $qty) {
                    if ((int)$qty > 0) {
                        $this->cartModel->updateItemQuantity($cartId, $pId, (int)$qty);
                    } else {
                        $this->cartModel->removeItem($cartId, $pId);
                    }
                }
            }
        }
        header('Location: ' . URLROOT . '/cart');
        exit;
    }

    public function checkout() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/users/login');
            exit;
        }

        $cartId = $this->cartModel->getCartId();
        $cartItems = $this->cartModel->getCartItems($cartId);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($cartItems)) {
            $userId = $_SESSION['user_id'];
            
            $address = isset($_POST['address']) ? filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING) : '';
            $phone = isset($_POST['phone']) ? filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING) : '';

            if (empty($address) || empty($phone)) {
                header('Location: ' . URLROOT . '/cart/checkout?error=missing_info');
                exit;
            }

            // Kiểm tra số điện thoại: Phải bắt đầu bằng số 0 và có đúng 10 chữ số
            if (!preg_match('/^(0)[0-9]{9}$/', $phone)) {
                header('Location: ' . URLROOT . '/cart/checkout?error=invalid_phone');
                exit;
            }

            // Tính tổng tiền và format lại mảng cart giống cấu trúc $_SESSION cũ
            // để OrderModel->createOrder() nhận diện được ($productId => $quantity)
            $totalAmount = 0;
            $orderCartData = []; 
            
            foreach ($cartItems as $item) {
                $totalAmount += $item->subtotal;
                $orderCartData[$item->product_id] = $item->quantity; 
            }
            
            // Tiến hành ghi order xuống DB
            $orderId = $this->orderModel->createOrder($userId, $orderCartData, $totalAmount, $address, $phone);
            
            if ($orderId) {
                // Đặt hàng thành công -> Xóa giỏ hàng trong Database
                $this->cartModel->clearCart($cartId); 
                header('Location: ' . URLROOT . '/pages/success');
                exit;
            } else {
                die('Có lỗi xảy ra khi đặt hàng.');
            }
        } else {
            $data = [
                'title' => 'Thanh toán đơn hàng - Cloud Arena',
                'description' => 'Tiến hành điền thông tin và thanh toán an toàn các gói dịch vụ Game Server tại Cloud Arena.'
            ];
            $this->view('client/cart/checkout', $data);
        }
    }
}