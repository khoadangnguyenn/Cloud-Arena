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
            $this->db->query("SELECT COUNT(*) AS total FROM user_services WHERE status = 'active'");
            $row = $this->db->single();
            return $row ? (int) $row->total : 0;
        } catch (Throwable $error) {
            return 0;
        }
    }

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

    public function getHomepagePackages($limit = 4) {
        $safeLimit = max(1, min(4, (int) $limit));

        $this->db->query(
            'SELECT id, name, slug, description, price, ram_mb, cpu_cores, disk_gb
             FROM products
             WHERE status = :status
             ORDER BY created_at DESC, id DESC
             LIMIT :limit'
        );
        $this->db->bind(':status', 'active');
        $this->db->bind(':limit', $safeLimit);

        return $this->db->resultSet();
    }

    /**
     * @param int[] $ids
     * @return object[]
     */
    public function getActiveProductsByIdsOrdered(array $ids) {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), function ($id) {
            return $id > 0;
        })));
        if (empty($ids)) {
            return [];
        }
        $ids = array_slice($ids, 0, 4);

        $placeholders = [];
        foreach ($ids as $i => $id) {
            $placeholders[] = ':id' . $i;
        }
        $inSql = implode(',', $placeholders);
        $orderSql = 'FIELD(id,' . implode(',', array_map('intval', $ids)) . ')';

        $this->db->query(
            "SELECT id, name, slug, description, price, ram_mb, cpu_cores, disk_gb
             FROM products
             WHERE status = :status AND id IN ($inSql)
             ORDER BY $orderSql"
        );
        $this->db->bind(':status', 'active');
        foreach ($ids as $i => $id) {
            $this->db->bind(':id' . $i, $id);
        }

        return $this->db->resultSet();
    }

    public function getProductPickerList() {
        $this->db->query(
            'SELECT id, name FROM products WHERE status = :status ORDER BY name ASC'
        );
        $this->db->bind(':status', 'active');
        return $this->db->resultSet();
    }

    public function deletePackage($id) {
        $this->db->query('DELETE FROM products WHERE id = :id');
        $this->db->bind(':id', (int) $id);
        return $this->db->execute();
    }
}
