<?php
class Product {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    private function makeSlug($name) {
        $slug = strtolower(trim((string) $name));
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
        $slug = trim($slug, '-');
        if ($slug === '') {
            $slug = 'service-plan';
        }
        return $slug;
    }

    private function getUniqueSlug($name) {
        $baseSlug = $this->makeSlug($name);
        $slug = $baseSlug;
        $suffix = 1;

        while (true) {
            $this->db->query('SELECT id FROM products WHERE slug = :slug LIMIT 1');
            $this->db->bind(':slug', $slug);
            $existing = $this->db->single();
            if (!$existing) {
                return $slug;
            }
            $suffix++;
            $slug = $baseSlug . '-' . $suffix;
        }
    }

    public function countActiveServices() {
        try {
            $this->db->query("SELECT COUNT(*) as total FROM user_services WHERE status = 'active'");
            $row = $this->db->single();
            return $row ? (int) $row->total : 0;
        } catch (Throwable $error) {
            return 0;
        }
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

    public function deletePackage($id) {
        return $this->deleteProduct($id);
    }

    public function getProductBySlug($slug) {
        $this->db->query("SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.slug = :slug AND p.status = 'active'");
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }

    /**
     * Danh sách gọn cho admin (mọi trạng thái), phân trang — tương thích bản Product “gói”.
     */
    public function getAdminPackages($page = 1, $perPage = 6) {
        $offset = max(0, ((int) $page - 1) * (int) $perPage);
        $this->db->query(
            'SELECT id, name, price, ram_mb, cpu_cores, disk_gb, image_url, status
             FROM products
             ORDER BY id DESC
             LIMIT :limit OFFSET :offset'
        );
        $this->db->bind(':limit', (int) $perPage);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    public function countAdminPackages() {
        $this->db->query('SELECT COUNT(*) AS total FROM products');
        $row = $this->db->single();
        return $row ? (int) $row->total : 0;
    }

    /**
     * Tạo gói nhanh với slug tự sinh (không trùng addProduct — form admin đầy đủ vẫn dùng addProduct).
     */
    public function createPackage($data) {
        $name = trim($data['name'] ?? '');
        if ($name === '') {
            return false;
        }

        $slug = $this->getUniqueSlug($name);
        $categoryId = (int) ($data['category_id'] ?? 1);
        $price = (float) ($data['price'] ?? 0);
        $ramMb = (int) ($data['ram_mb'] ?? 0);
        $cpuCores = (int) ($data['cpu_cores'] ?? 0);
        $diskGb = (int) ($data['disk_gb'] ?? 0);
        $description = trim($data['description'] ?? '');
        $imageUrl = trim($data['image_url'] ?? '');

        $this->db->query(
            "INSERT INTO products (category_id, name, slug, description, price, ram_mb, cpu_cores, disk_gb, image_url, status)
             VALUES (:category_id, :name, :slug, :description, :price, :ram_mb, :cpu_cores, :disk_gb, :image_url, 'active')"
        );
        $this->db->bind(':category_id', $categoryId);
        $this->db->bind(':name', $name);
        $this->db->bind(':slug', $slug);
        $this->db->bind(':description', $description);
        $this->db->bind(':price', $price);
        $this->db->bind(':ram_mb', $ramMb);
        $this->db->bind(':cpu_cores', $cpuCores);
        $this->db->bind(':disk_gb', $diskGb);
        $this->db->bind(':image_url', $imageUrl);

        return $this->db->execute();
    }

    /**
     * @param int $limit
     * @param string $sort 'recent' (mặc định) hoặc 'name' — Admin gọi không tham số vẫn giữ hành vi cũ.
     */
    public function getProductPickerList($limit = 200, $sort = 'recent') {
        $limit = max(1, min(500, (int) $limit));
        $orderBy = ($sort === 'name') ? 'name ASC' : 'created_at DESC';
        $sql = "SELECT id, name, slug FROM products WHERE status = 'active' ORDER BY $orderBy LIMIT :limit";
        $this->db->query($sql);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getActiveProductsByIdsOrdered(array $ids) {
        $safeIds = array_values(array_unique(array_filter(array_map('intval', $ids), function ($id) {
            return (int) $id > 0;
        })));
        if (empty($safeIds)) {
            return [];
        }

        $placeholders = [];
        foreach ($safeIds as $i => $id) {
            $placeholders[] = ':hpid' . $i;
        }
        $inList = implode(',', $placeholders);
        $fieldOrder = implode(',', $safeIds);

        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND p.id IN ($inList)
                ORDER BY FIELD(p.id, $fieldOrder)";

        $this->db->query($sql);
        foreach ($safeIds as $i => $id) {
            $this->db->bind(':hpid' . $i, $id, PDO::PARAM_INT);
        }

        return $this->db->resultSet();
    }

    public function getHomepagePackages($limit = 4) {
        $limit = max(1, min(50, (int) $limit));
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active'
                ORDER BY p.created_at DESC
                LIMIT :limit";
        $this->db->query($sql);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}