<?php
class Product {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getProducts($limit, $offset, $keyword = '', $isAdmin = false, $categoryId = null, $minPrice = null, $maxPrice = null) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id";
        
        $where = [];
        if (!$isAdmin) {
            $where[] = "p.status = 'active'";
        }
        if (!empty($keyword)) {
            $where[] = "p.name LIKE :keyword";
        }
        if (!empty($categoryId)) {
            $where[] = "p.category_id = :category_id";
        }
        if ($minPrice !== null && $minPrice !== '') {
            $where[] = "p.price >= :min_price";
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $where[] = "p.price <= :max_price";
        }
        
        if (count($where) > 0) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";

        $this->db->query($sql);
        
        if (!empty($keyword)) { $this->db->bind(':keyword', '%' . $keyword . '%'); }
        if (!empty($categoryId)) { $this->db->bind(':category_id', $categoryId); }
        if ($minPrice !== null && $minPrice !== '') { $this->db->bind(':min_price', $minPrice); }
        if ($maxPrice !== null && $maxPrice !== '') { $this->db->bind(':max_price', $maxPrice); }
        
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);

        return $this->db->resultSet();
    }

    public function getTotalProducts($keyword = '', $isAdmin = false, $categoryId = null, $minPrice = null, $maxPrice = null) {
        $sql = "SELECT COUNT(*) as total FROM products";
        
        $where = [];
        if (!$isAdmin) {
            $where[] = "status = 'active'";
        }
        if (!empty($keyword)) {
            $where[] = "name LIKE :keyword";
        }
        if (!empty($categoryId)) {
            $where[] = "category_id = :category_id";
        }
        if ($minPrice !== null && $minPrice !== '') {
            $where[] = "price >= :min_price";
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $where[] = "price <= :max_price";
        }

        if (count($where) > 0) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $this->db->query($sql);
        
        if (!empty($keyword)) { $this->db->bind(':keyword', '%' . $keyword . '%'); }
        if (!empty($categoryId)) { $this->db->bind(':category_id', $categoryId); }
        if ($minPrice !== null && $minPrice !== '') { $this->db->bind(':min_price', $minPrice); }
        if ($maxPrice !== null && $maxPrice !== '') { $this->db->bind(':max_price', $maxPrice); }

        $row = $this->db->single();
        return $row->total;
    }

    public function getProductById($id) {
        $this->db->query("SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    public function getCategories() {
        $this->db->query("SELECT * FROM categories");
        return $this->db->resultSet();
    }

    public function addProduct($data) {
        $this->db->query("INSERT INTO products (category_id, name, slug, description, price, ram_mb, cpu_cores, disk_gb, image_url, status) 
                          VALUES (:category_id, :name, :slug, :description, :price, :ram_mb, :cpu_cores, :disk_gb, :image_url, :status)");
        
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':ram_mb', $data['ram_mb']);
        $this->db->bind(':cpu_cores', $data['cpu_cores']);
        $this->db->bind(':disk_gb', $data['disk_gb']);
        $this->db->bind(':image_url', $data['image_url']);
        $this->db->bind(':status', $data['status']);

        return $this->db->execute();
    }

    // Cập nhật sản phẩm
    public function updateProduct($data) {
        $this->db->query("UPDATE products 
                          SET category_id = :category_id, name = :name, slug = :slug, description = :description, 
                              price = :price, ram_mb = :ram_mb, cpu_cores = :cpu_cores, disk_gb = :disk_gb, 
                              image_url = :image_url, status = :status 
                          WHERE id = :id");
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':ram_mb', $data['ram_mb']);
        $this->db->bind(':cpu_cores', $data['cpu_cores']);
        $this->db->bind(':disk_gb', $data['disk_gb']);
        $this->db->bind(':image_url', $data['image_url']);
        $this->db->bind(':status', $data['status']);

        return $this->db->execute();
    }

    public function deleteProduct($id) {
        $this->db->query("DELETE FROM products WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getProductBySlug($slug) {
        $this->db->query("SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.slug = :slug AND p.status = 'active'");
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }

    // ==========================================
    // THÊM HÀM NÀY CHO ADMIN DASHBOARD
    // ==========================================
    public function countActiveServices() {
        $this->db->query("
            SELECT COUNT(*) AS total 
            FROM user_services 
            WHERE status = 'active'
        ");
        $row = $this->db->single();
        return $row ? (int)$row->total : 0;
    }
    /**
     * Lấy danh sách sản phẩm liên quan (cùng danh mục)
     */
    public function getRelatedProducts($category_id, $current_product_id, $limit = 4) {
        $this->db->query("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.category_id = :category_id 
              AND p.id != :current_id 
              AND p.status = 'active' 
            ORDER BY RAND() 
            LIMIT :limit
        ");
        
        $this->db->bind(':category_id', $category_id);
        $this->db->bind(':current_id', $current_product_id);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }
    // Lấy tất cả đánh giá của 1 sản phẩm (Chỉ lấy những đánh giá có trạng thái active/đã duyệt)
    public function getReviews($product_id) {
        $this->db->query("
            SELECT r.*, u.full_name, u.username, u.avatar 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.product_id = :product_id 
              AND r.status = 'approved' -- Đổi từ 'active' thành 'approved' ở đây
            ORDER BY r.created_at DESC
        ");
        $this->db->bind(':product_id', $product_id);
        return $this->db->resultSet();
    }

    // Kiểm tra xem user này đã mua sản phẩm và đơn hàng đã hoàn tất chưa
    public function canReview($user_id, $product_id) {
        $this->db->query("
            SELECT COUNT(*) as count 
            FROM order_items oi 
            JOIN orders o ON oi.order_id = o.id 
            WHERE o.user_id = :user_id 
              AND oi.product_id = :product_id 
              AND o.status = 'completed'
        ");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':product_id', $product_id);
        $row = $this->db->single();
        return $row->count > 0;
    }

    // Kiểm tra xem user này đã từng đánh giá sản phẩm này chưa (Tránh spam)
    public function hasReviewed($user_id, $product_id) {
        $this->db->query("SELECT COUNT(*) as count FROM reviews WHERE user_id = :user_id AND product_id = :product_id");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':product_id', $product_id);
        $row = $this->db->single();
        return $row->count > 0;
    }

    // Lưu đánh giá mới vào DB (Khớp với các cột của bạn)
    public function addReview($user_id, $product_id, $rating, $comment) {
        // Tạm thời set mặc định status là 'active' để review hiện lên luôn. 
        // Nếu bạn muốn Admin duyệt trước khi hiện, hãy đổi chữ 'active' thành 'pending'.
        $this->db->query("
            INSERT INTO reviews (user_id, product_id, rating, comment, status) 
            VALUES (:user_id, :product_id, :rating, :comment, 'active')
        ");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':product_id', $product_id);
        $this->db->bind(':rating', $rating);
        $this->db->bind(':comment', $comment);
        return $this->db->execute();
    }
}