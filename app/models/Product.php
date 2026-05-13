<?php
class Product {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getProducts($limit, $offset, $keyword = '') {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'active'";
        
        if (!empty($keyword)) {
            $sql .= " AND p.name LIKE :keyword";
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";

        $this->db->query($sql);
        
        if (!empty($keyword)) {
            $this->db->bind(':keyword', '%' . $keyword . '%');
        }
        
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);

        return $this->db->resultSet();
    }

    public function getTotalProducts($keyword = '') {
        $sql = "SELECT COUNT(*) as total FROM products WHERE status = 'active'";
        
        if (!empty($keyword)) {
            $sql .= " AND name LIKE :keyword";
        }

        $this->db->query($sql);
        
        if (!empty($keyword)) {
            $this->db->bind(':keyword', '%' . $keyword . '%');
        }

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

    public function countActiveServices() {
        $this->db->query("SELECT COUNT(*) as total FROM user_services WHERE status = 'active'");
        $row = $this->db->single();
        return $row->total ?? 0;
    }
}