import re

with open('/Applications/XAMPP/xamppfiles/htdocs/Cloud-Arena/Game-Server-Rental-Platform/database.sql', 'r') as f:
    db_sql = f.read()

with open('/Applications/XAMPP/xamppfiles/htdocs/Cloud-Arena/Cloud-Arena-Giang-Branch/cloud_arena_back.sql', 'r') as f:
    back_sql = f.read()

def get_insert(table):
    pattern = r"INSERT INTO `" + table + r"` \([^)]+\) VALUES\s*([\s\S]*?);"
    match = re.search(pattern, back_sql)
    if match:
        return match.group(0).replace("`" + table + "`", table)
    return ""

settings_insert = get_insert('settings')
contacts_insert = get_insert('contacts')
orders_insert = get_insert('orders')
admin_notifications_insert = get_insert('admin_notifications')

# We also need to update users. The schema in database.sql has credit after status.
# In back_sql, users has: `id`, `username`, `password`, `email`, `full_name`, `avatar`, `status`, `reset_token`, `role`, `created_at`
# Let's just manually prepare users:
users_insert = """INSERT INTO users (id, username, password, email, full_name, avatar, status, credit, reset_token, role, created_at) VALUES
(1, 'admin', '$2a$10$iYI.B2yyF7i75alKEO6XHeUMfcZJgz52DTg67oggoVxUVqyBHZmvS', 'admin@cloudarena.local', 'Administrator 1', '/uploads/avatars/admin.png', 'active', 1000, NULL, 'admin', '2026-05-06 08:22:16'),
(2, 'testuser', '$2y$10$PfYcDZ13nUgxtOdBsX/FPuWwnxxvJXBPZ2sqMviPZFk.H34fobDLi', 'test@cloudarena.local', 'Test User 1', '/uploads/avatars/av_ba55501f0ec6a5a3769658f31dbf9834.png', 'active', 200, NULL, 'member', '2026-05-06 08:22:16'),
(4, 'testuser2', '$2y$10$/gT5cjOikjQ1BBF/GvhtcubPIEc0xLbpvSr8WMkVDQskfi4sjcTYe', 'test2@cloudarena.local', 'Test User 2', '/uploads/avatars/testuser2.jpg', 'active', 0, NULL, 'member', '2026-05-07 12:41:56'),
(8, 'guest_contact', '$2y$10$jmxg/heRebqUTZev3BYM/.zRGGIvN2k9MR2Fl1l6hT2fQXasUgCGW', 'guest@cloud-arena.local', 'Guest Contact', NULL, 'active', 0, NULL, 'member', '2026-05-08 14:42:38');"""

# admin_notifications schema in database.sql
admin_notif_schema = """CREATE TABLE IF NOT EXISTS admin_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('ticket', 'revenue') NOT NULL,
    source_key VARCHAR(120) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    url VARCHAR(255),
    payload LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_admin_notifications_source_key` (`source_key`),
    KEY `idx_admin_notifications_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"""

# Let's replace the parts in db_sql
# 1. Add DROP TABLE IF EXISTS admin_notifications;
db_sql = db_sql.replace("DROP TABLE IF EXISTS users;", "DROP TABLE IF EXISTS admin_notifications;\nDROP TABLE IF EXISTS users;")

# 2. Add CREATE TABLE admin_notifications after CREATE TABLE users
db_sql = db_sql.replace(") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n-- ==========================================\n-- 2. NHÓM SẢN PHẨM & VẬN HÀNH DỊCH VỤ", 
    ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n" + admin_notif_schema + "\n\n-- ==========================================\n-- 2. NHÓM SẢN PHẨM & VẬN HÀNH DỊCH VỤ")

# 3. Replace users insert
db_sql = re.sub(r"INSERT INTO users \([^)]+\) VALUES\s*\([\s\S]*?\);\n", users_insert + "\n", db_sql)

# 4. Replace Contacts
db_sql = re.sub(r"-- 6\. Contacts \(Giang Branch Backup\)\nINSERT INTO contacts \([^)]+\) VALUES[\s\S]*?;", "-- 6. Contacts (Giang Branch Backup)\n" + contacts_insert, db_sql)

# 5. Replace Orders
db_sql = re.sub(r"-- 7\. Orders & Items \(Giang Branch Backup\)\nINSERT INTO orders \([^)]+\) VALUES[\s\S]*?;", "-- 7. Orders & Items (Giang Branch Backup)\n" + orders_insert, db_sql)

# 6. Replace Admin Notifications
# We need to remove the CREATE TABLE from the end and replace the INSERT
db_sql = re.sub(r"-- 8\. Admin Notifications \(Giang Branch Backup\)\nCREATE TABLE IF NOT EXISTS admin_notifications [\s\S]*?;\n\nINSERT INTO admin_notifications \([^)]+\) VALUES[\s\S]*?;", "-- 8. Admin Notifications (Giang Branch Backup)\n" + admin_notifications_insert, db_sql)

# 7. Replace Settings
db_sql = re.sub(r"-- 9\. Settings \(Giang Branch Backup\)\nINSERT INTO settings \([^)]+\) VALUES[\s\S]*?ON DUPLICATE KEY UPDATE value = VALUES\(value\);", "-- 9. Settings (Giang Branch Backup)\n" + settings_insert + "\nON DUPLICATE KEY UPDATE value = VALUES(value);", db_sql)

# Also handle "ON DUPLICATE KEY UPDATE value = VALUES(value);"
if "ON DUPLICATE KEY UPDATE value = VALUES(value);" not in db_sql:
    # it might have been removed or missed, let's just make sure settings uses it
    pass

with open('/Applications/XAMPP/xamppfiles/htdocs/Cloud-Arena/Game-Server-Rental-Platform/database.sql', 'w') as f:
    f.write(db_sql)

print("Done")
