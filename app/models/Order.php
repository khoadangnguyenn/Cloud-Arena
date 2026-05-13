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

    /**
     * Đơn chờ xử lý của user (cho form ticket vấn đề đơn hàng).
     *
     * @return object[]
     */
    public function getPendingOrdersForUser($userId) {
        $uid = (int) $userId;
        if ($uid <= 0) {
            return [];
        }

        $this->db->query(
            "SELECT o.id, o.total_amount, o.created_at,
                    COALESCE(GROUP_CONCAT(CONCAT(p.name, ' ×', oi.quantity) ORDER BY oi.id SEPARATOR ', '), '') AS items_label
             FROM orders o
             LEFT JOIN order_items oi ON oi.order_id = o.id
             LEFT JOIN products p ON p.id = oi.product_id
             WHERE o.user_id = :user_id AND o.status = 'pending'
             GROUP BY o.id, o.total_amount, o.created_at
             ORDER BY o.created_at DESC"
        );
        $this->db->bind(':user_id', $uid);

        return $this->db->resultSet();
    }

    public function getPendingOrderByIdForUser($orderId, $userId) {
        $oid = (int) $orderId;
        $uid = (int) $userId;
        if ($oid <= 0 || $uid <= 0) {
            return null;
        }

        $this->db->query(
            "SELECT o.id, o.total_amount, o.created_at, o.status
             FROM orders o
             WHERE o.id = :order_id AND o.user_id = :user_id AND o.status = 'pending'
             LIMIT 1"
        );
        $this->db->bind(':order_id', $oid);
        $this->db->bind(':user_id', $uid);

        $row = $this->db->single();
        return $row ?: null;
    }

    /**
     * Tóm tắt đơn (mọi trạng thái) khi đối chiếu ticket — chỉ khi đúng chủ sở hữu.
     */
    public function getOrderTicketSummaryByIdForUser($orderId, $userId) {
        $oid = (int) $orderId;
        $uid = (int) $userId;
        if ($oid <= 0 || $uid <= 0) {
            return null;
        }

        $this->db->query(
            "SELECT o.id, o.total_amount, o.created_at, o.status,
                    COALESCE(GROUP_CONCAT(CONCAT(p.name, ' ×', oi.quantity) ORDER BY oi.id SEPARATOR ', '), '') AS items_label
             FROM orders o
             LEFT JOIN order_items oi ON oi.order_id = o.id
             LEFT JOIN products p ON p.id = oi.product_id
             WHERE o.id = :order_id AND o.user_id = :user_id
             GROUP BY o.id, o.total_amount, o.created_at, o.status
             LIMIT 1"
        );
        $this->db->bind(':order_id', $oid);
        $this->db->bind(':user_id', $uid);

        $row = $this->db->single();
        return $row ?: null;
    }
}
