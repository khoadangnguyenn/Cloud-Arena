<?php
class UserServiceModel {
    private $db;

    public function __construct() {
        // Khởi tạo kết nối CSDL
        $this->db = new Database();
    }

    /**
     * Lấy danh sách các dịch vụ (Server) đang chạy của một user
     */
    public function getUserServices($userId) {
        // Liên kết bảng user_services với products để lấy được tên gói (product_name)
        $this->db->query("
            SELECT us.*, p.name as product_name 
            FROM user_services us
            LEFT JOIN products p ON us.product_id = p.id
            WHERE us.user_id = :user_id
            ORDER BY us.expires_at DESC
        ");
        $this->db->bind(':user_id', $userId);
        
        return $this->db->resultSet();
    }
}