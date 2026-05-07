<?php
class Order {
    private $db;
    public function __construct() {
        $this->db = new Database;
    }

    public function getMonthlyRevenue() {
        $this->db->query(
            "SELECT COALESCE(SUM(total_amount), 0) AS total
             FROM orders
             WHERE status = 'completed'
             AND DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')"
        );
        $row = $this->db->single();
        return $row ? (float) $row->total : 0;
    }

    public function getLastFiveMonthRevenue() {
        $this->db->query(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_key, COALESCE(SUM(total_amount), 0) AS revenue
             FROM orders
             WHERE status = 'completed'
               AND created_at >= DATE_SUB(NOW(), INTERVAL 4 MONTH)
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY month_key ASC"
        );
        return $this->db->resultSet();
    }
}
