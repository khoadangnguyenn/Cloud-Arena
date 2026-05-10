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

    public function getRevenueNotificationSummary($sinceTimestamp = null, $limit = 5) {
        $safeSince = trim((string) $sinceTimestamp) !== '' ? trim((string) $sinceTimestamp) : '1970-01-01 00:00:00';
        $safeLimit = max(1, (int) $limit);

        $this->db->query(
            "SELECT COUNT(*) AS total_orders,
                    COALESCE(SUM(total_amount), 0) AS total_revenue,
                    MAX(created_at) AS latest_created_at
             FROM orders
             WHERE status = 'completed'
               AND created_at > :since_timestamp"
        );
        $this->db->bind(':since_timestamp', $safeSince);
        $meta = $this->db->single();

        $this->db->query(
            "SELECT id, user_id, total_amount, created_at
             FROM orders
             WHERE status = 'completed'
               AND created_at > :since_timestamp
             ORDER BY created_at DESC
             LIMIT :limit_rows"
        );
        $this->db->bind(':since_timestamp', $safeSince);
        $this->db->bind(':limit_rows', $safeLimit);
        $items = $this->db->resultSet();

        return [
            'count' => $meta ? (int) $meta->total_orders : 0,
            'revenue' => $meta ? (float) $meta->total_revenue : 0,
            'latest_created_at' => ($meta && !empty($meta->latest_created_at)) ? (string) $meta->latest_created_at : null,
            'items' => $items
        ];
    }

    public function getLatestCompletedOrderCreatedAt() {
        $this->db->query(
            "SELECT MAX(created_at) AS latest_created_at
             FROM orders
             WHERE status = 'completed'"
        );
        $row = $this->db->single();
        if (!$row || empty($row->latest_created_at)) {
            return null;
        }
        return (string) $row->latest_created_at;
    }

    public function getRecentRevenueNotifications($limit = 30) {
        $safeLimit = max(1, (int) $limit);
        $this->db->query(
            "SELECT id, user_id, total_amount, created_at
             FROM orders
             WHERE status = 'completed'
             ORDER BY created_at DESC
             LIMIT :limit_rows"
        );
        $this->db->bind(':limit_rows', $safeLimit);
        return $this->db->resultSet();
    }

    public function countNewRevenueNotificationsSince($sinceTimestamp = null) {
        $safeSince = trim((string) $sinceTimestamp) !== '' ? trim((string) $sinceTimestamp) : '1970-01-01 00:00:00';
        $this->db->query(
            "SELECT COUNT(*) AS total
             FROM orders
             WHERE status = 'completed'
               AND created_at > :since_timestamp"
        );
        $this->db->bind(':since_timestamp', $safeSince);
        $row = $this->db->single();
        return $row ? (int) $row->total : 0;
    }
}
