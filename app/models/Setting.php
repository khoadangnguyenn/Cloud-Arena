<?php
class Setting {
    private $db;
    private $publicKeys = [
        'site_logo_text',
        'site_logo_image',
        'site_hotline',
        'site_contact_email',
        'site_address',
        'site_about_snippet',
        'site_map_embed_url',
        'home_hero_title_gradient',
        'home_hero_title_plain',
        'home_hero_subtitle',
        'home_hero_bg_image',
        'home_card_tech_title',
        'home_review_key',
        'home_product_ids',
        'home_about_kicker',
        'home_about_heading',
        'home_about_lead',
        'home_about_feat1_title',
        'home_about_feat1_text',
        'home_about_feat2_title',
        'home_about_feat2_text',
        'home_about_feat3_title',
        'home_about_feat3_text',
        'profile_page_title',
        'profile_page_intro',
        'profile_section_avatar_title',
        'profile_avatar_upload_label',
        'profile_avatar_hint',
        'profile_section_personal_title',
        'profile_section_password_title',
        'profile_label_display_name',
        'profile_label_email',
        'profile_label_current_password',
        'profile_label_new_password',
        'profile_label_confirm_password',
        'profile_btn_save',
        'profile_btn_update_password',
        'contact_page_title',
        'contact_page_intro',
        'contact_sidebar_title'
    ];

    private $defaultPublicSettings = [
        'site_logo_text' => 'G-SERVER',
        'site_logo_image' => '',
        'site_hotline' => '0123 456 789',
        'site_contact_email' => 'contact@gameserver.vn',
        'site_address' => '268 Lý Thường Kiệt, Q10, TP.HCM',
        'site_about_snippet' => 'Nền tảng cho thuê Game Server ổn định, hiệu năng cao.',
        'site_map_embed_url' => 'https://www.google.com/maps?q=268+Ly+Thuong+Kiet+Q10+TPHCM&output=embed',
        'home_hero_title_gradient' => 'Game Server Hosting',
        'home_hero_title_plain' => 'Cho Mọi Game Thủ',
        'home_hero_subtitle' => 'Máy chủ game chuyên nghiệp với hiệu năng cao, hỗ trợ modpack và quản lý dễ dàng. Khởi động Server chỉ trong vài phút với công nghệ ảo hóa tiên tiến nhất.',
        'home_hero_bg_image' => '',
        'home_card_tech_title' => 'Năng lực công nghệ',
        'home_review_key' => '',
        'home_product_ids' => '',
        'home_about_kicker' => 'Tính năng vượt trội',
        'home_about_heading' => 'Tại sao chọn G-SERVER?',
        'home_about_lead' => 'Nền tảng tập trung cho cộng đồng game thủ: triển khai nhanh, bảo mật cao và vận hành ổn định xuyên suốt.',
        'home_about_feat1_title' => 'Hiệu năng tối đa',
        'home_about_feat1_text' => 'Sử dụng CPU Intel Core i9 & AMD Ryzen mới nhất, cùng ổ cứng NVMe Gen4 cho tốc độ xử lý vượt trội.',
        'home_about_feat2_title' => 'Anti-DDoS mạnh mẽ',
        'home_about_feat2_text' => 'Lớp bảo vệ đa tầng giúp lọc bỏ các cuộc tấn công DDoS lên đến hàng trăm Gbps, giữ server luôn ổn định.',
        'home_about_feat3_title' => 'Backup tự động',
        'home_about_feat3_text' => 'Dữ liệu của bạn luôn an toàn với hệ thống sao lưu tự động hàng ngày. Khôi phục nhanh chóng khi cần thiết.',
        'profile_page_title' => 'Hồ sơ thành viên',
        'profile_page_intro' => 'Quản lý thông tin tài khoản và bảo mật.',
        'profile_section_avatar_title' => 'Ảnh đại diện',
        'profile_avatar_upload_label' => 'Tải ảnh lên',
        'profile_avatar_hint' => 'JPG, PNG, GIF, WEBP. Tối đa 2MB.',
        'profile_section_personal_title' => 'Thông tin cá nhân',
        'profile_section_password_title' => 'Đổi mật khẩu',
        'profile_label_display_name' => 'Họ tên hiển thị',
        'profile_label_email' => 'Địa chỉ email',
        'profile_label_current_password' => 'Mật khẩu hiện tại',
        'profile_label_new_password' => 'Mật khẩu mới',
        'profile_label_confirm_password' => 'Xác nhận mật khẩu',
        'profile_btn_save' => 'Lưu thay đổi',
        'profile_btn_update_password' => 'Cập nhật mật khẩu',
        'contact_page_title' => 'Liên Hệ',
        'contact_page_intro' => 'Gửi ticket hỗ trợ cho chúng tôi. Đội ngũ sẽ phản hồi sớm nhất có thể.',
        'contact_sidebar_title' => 'Thông tin liên hệ'
    ];

    public function __construct() {
        $this->db = new Database;
    }

    public function getPublicSettings() {
        $settings = $this->defaultPublicSettings;

        $this->db->query('SELECT key_name, value FROM settings');
        $rows = $this->db->resultSet();

        foreach ($rows as $row) {
            if (in_array($row->key_name, $this->publicKeys, true)) {
                $settings[$row->key_name] = $row->value;
            }
        }

        return $settings;
    }

    public function updatePublicSettings($data) {
        foreach ($this->publicKeys as $key) {
            if (!array_key_exists($key, $data)) {
                continue;
            }

            $value = trim((string) $data[$key]);
            $this->db->query(
                'INSERT INTO settings (key_name, value)
                 VALUES (:key_name, :value)
                 ON DUPLICATE KEY UPDATE value = VALUES(value)'
            );
            $this->db->bind(':key_name', $key);
            $this->db->bind(':value', $value);

            if (!$this->db->execute()) {
                return false;
            }
        }

        return true;
    }

    public function getValueByKey($keyName, $defaultValue = '') {
        $this->db->query(
            'SELECT value
             FROM settings
             WHERE key_name = :key_name
             LIMIT 1'
        );
        $this->db->bind(':key_name', trim((string) $keyName));
        $row = $this->db->single();
        if (!$row) {
            return $defaultValue;
        }
        return (string) $row->value;
    }

    public function upsertValue($keyName, $value) {
        $this->db->query(
            'INSERT INTO settings (key_name, value)
             VALUES (:key_name, :value)
             ON DUPLICATE KEY UPDATE value = VALUES(value)'
        );
        $this->db->bind(':key_name', trim((string) $keyName));
        $this->db->bind(':value', (string) $value);
        return $this->db->execute();
    }
}
