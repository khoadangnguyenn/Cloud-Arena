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

    /**
     * @return object[]
     */
    public function listApprovedProductReviewsForPicker($limit = 120) {
        $safeLimit = max(1, min(500, (int) $limit));
        $this->db->query(
            'SELECT r.user_id, r.review_id, r.rating, r.comment, r.created_at,
                    u.username, u.full_name, p.name AS product_name
             FROM reviews r
             INNER JOIN users u ON u.id = r.user_id
             INNER JOIN products p ON p.id = r.product_id
             WHERE r.product_id IS NOT NULL AND r.status = :status
             ORDER BY r.created_at DESC
             LIMIT :limit'
        );
        $this->db->bind(':status', 'approved');
        $this->db->bind(':limit', $safeLimit);
        return $this->db->resultSet();
    }

    public function getApprovedProductReviewByKey($userId, $reviewId) {
        $userId = (int) $userId;
        $reviewId = (int) $reviewId;
        if ($userId <= 0 || $reviewId <= 0) {
            return null;
        }
        $this->db->query(
            'SELECT r.rating, r.comment, r.created_at, u.username, u.full_name, u.avatar, p.name AS product_name
             FROM reviews r
             INNER JOIN users u ON u.id = r.user_id
             INNER JOIN products p ON p.id = r.product_id
             WHERE r.user_id = :uid AND r.review_id = :rid
               AND r.product_id IS NOT NULL
               AND r.status = :status'
        );
        $this->db->bind(':uid', $userId);
        $this->db->bind(':rid', $reviewId);
        $this->db->bind(':status', 'approved');
        return $this->db->single();
    }
}
