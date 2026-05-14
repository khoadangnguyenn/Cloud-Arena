<?php
class CartModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
        // Đảm bảo session đã được khởi tạo để lấy session_id
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Lấy ID giỏ hàng hiện tại (nếu chưa có thì tạo mới)
     */
    public function getCartId() {
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $sessionId = session_id();

        // 1. Kiểm tra xem user hoặc session này đã có giỏ hàng chưa
        if ($userId) {
            $this->db->query('SELECT id FROM carts WHERE user_id = :user_id');
            $this->db->bind(':user_id', $userId);
        } else {
            $this->db->query('SELECT id FROM carts WHERE session_id = :session_id AND user_id IS NULL');
            $this->db->bind(':session_id', $sessionId);
        }

        $row = $this->db->single();

        if ($row) {
            return $row->id; // Đã có giỏ hàng, trả về ID
        }

        // 2. Nếu chưa có, tạo giỏ hàng mới
        $this->db->query('INSERT INTO carts (user_id, session_id) VALUES (:user_id, :session_id)');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':session_id', $sessionId);
        $this->db->execute();

        return $this->db->lastInsertId();
    }

    /**
     * Lấy toàn bộ sản phẩm trong một giỏ hàng (JOIN với bảng products)
     */
    public function getCartItems($cartId) {
        $this->db->query('
            SELECT 
                ci.product_id, 
                ci.quantity, 
                ci.duration_months,
                p.name, 
                p.price, 
                p.image_url, 
                p.description,
                (p.price * ci.quantity) as subtotal
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.cart_id = :cart_id
        ');
        $this->db->bind(':cart_id', $cartId);
        return $this->db->resultSet();
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addItem($cartId, $productId, $quantity = 1, $durationMonths = 1) {
        // Kiểm tra xem sản phẩm đã có trong giỏ hàng này chưa
        $this->db->query('SELECT id, quantity FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id');
        $this->db->bind(':cart_id', $cartId);
        $this->db->bind(':product_id', $productId);
        $existingItem = $this->db->single();

        if ($existingItem) {
            // Nếu có rồi thì cộng dồn số lượng
            $newQuantity = $existingItem->quantity + $quantity;
            $this->db->query('UPDATE cart_items SET quantity = :quantity WHERE id = :id');
            $this->db->bind(':quantity', $newQuantity);
            $this->db->bind(':id', $existingItem->id);
            return $this->db->execute();
        } else {
            // Nếu chưa có thì thêm dòng mới
            $this->db->query('INSERT INTO cart_items (cart_id, product_id, quantity, duration_months) VALUES (:cart_id, :product_id, :quantity, :duration_months)');
            $this->db->bind(':cart_id', $cartId);
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':quantity', $quantity);
            $this->db->bind(':duration_months', $durationMonths);
            return $this->db->execute();
        }
    }

    /**
     * Cập nhật chính xác số lượng của 1 sản phẩm
     */
    public function updateItemQuantity($cartId, $productId, $quantity) {
        $this->db->query('UPDATE cart_items SET quantity = :quantity WHERE cart_id = :cart_id AND product_id = :product_id');
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':cart_id', $cartId);
        $this->db->bind(':product_id', $productId);
        return $this->db->execute();
    }

    /**
     * Xóa 1 sản phẩm khỏi giỏ hàng
     */
    public function removeItem($cartId, $productId) {
        $this->db->query('DELETE FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id');
        $this->db->bind(':cart_id', $cartId);
        $this->db->bind(':product_id', $productId);
        return $this->db->execute();
    }

    /**
     * Làm rỗng giỏ hàng (Xóa toàn bộ items)
     */
    public function clearCart($cartId) {
        $this->db->query('DELETE FROM cart_items WHERE cart_id = :cart_id');
        $this->db->bind(':cart_id', $cartId);
        return $this->db->execute();
        
        // Bạn có thể tùy chọn xóa luôn cái vỏ giỏ hàng ở bảng carts nếu muốn:
        // $this->db->query('DELETE FROM carts WHERE id = :cart_id');
        // $this->db->bind(':cart_id', $cartId);
        // $this->db->execute();
    }

    /**
     * Lấy tổng số lượng sản phẩm trong giỏ (để hiển thị badge nhỏ góc trên)
     */
    public function getTotalItemCount($cartId) {
        $this->db->query('SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = :cart_id');
        $this->db->bind(':cart_id', $cartId);
        $row = $this->db->single();
        return $row->total ? (int)$row->total : 0;
    }

    /**
     * [TÍNH NĂNG NÂNG CAO] Đồng bộ giỏ hàng từ Guest sang User khi họ đăng nhập
     */
    public function mergeGuestCartToUser($userId) {
        $sessionId = session_id();
        
        // Cập nhật các giỏ hàng đang dùng session_id này thành user_id
        $this->db->query('UPDATE carts SET user_id = :user_id, session_id = NULL WHERE session_id = :session_id AND user_id IS NULL');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':session_id', $sessionId);
        return $this->db->execute();
    }
}