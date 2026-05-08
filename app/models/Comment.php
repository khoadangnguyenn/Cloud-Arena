<?php
class Comment {
    private $db;
    public function __construct() {
        $this->db = new Database;
    }

    public function getLatestFiveStarProductReview() {
        $this->db->query(
            'SELECT r.rating, r.comment, r.created_at, u.username, u.full_name, u.avatar, p.name AS product_name
             FROM reviews r
             INNER JOIN users u ON u.id = r.user_id
             INNER JOIN products p ON p.id = r.product_id
             WHERE r.product_id IS NOT NULL
               AND r.rating >= 5
               AND r.status = :status
             ORDER BY r.created_at DESC
             LIMIT 1'
        );
        $this->db->bind(':status', 'approved');
        $review = $this->db->single();

        if ($review) {
            return $review;
        }

        $this->db->query(
            'SELECT r.rating, r.comment, r.created_at, u.username, u.full_name, u.avatar, p.name AS product_name
             FROM reviews r
             INNER JOIN users u ON u.id = r.user_id
             INNER JOIN products p ON p.id = r.product_id
             WHERE r.product_id IS NOT NULL
               AND r.rating >= 5
               AND r.status <> :hidden_status
             ORDER BY r.created_at DESC
             LIMIT 1'
        );
        $this->db->bind(':hidden_status', 'hidden');
        return $this->db->single();
    }
}
