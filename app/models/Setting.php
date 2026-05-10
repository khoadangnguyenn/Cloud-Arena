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
        'site_map_embed_url'
    ];

    private $defaultPublicSettings = [
        'site_logo_text' => 'G-SERVER',
        'site_logo_image' => '',
        'site_hotline' => '0123 456 789',
        'site_contact_email' => 'contact@gameserver.vn',
        'site_address' => '268 Lý Thường Kiệt, Q10, TP.HCM',
        'site_about_snippet' => 'Nền tảng cho thuê Game Server ổn định, hiệu năng cao.',
        'site_map_embed_url' => 'https://www.google.com/maps?q=268+Ly+Thuong+Kiet+Q10+TPHCM&output=embed'
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
