DROP DATABASE IF EXISTS cloud_arena;
CREATE DATABASE IF NOT EXISTS cloud_arena;
USE cloud_arena;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS media;
DROP TABLE IF EXISTS news;
DROP TABLE IF EXISTS pages;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS carts;
DROP TABLE IF EXISTS user_services;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS faqs;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS admin_notifications;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS about;
DROP TABLE IF EXISTS faq_messages;
DROP TABLE IF EXISTS faq_categories;
DROP TABLE IF EXISTS news_categories;
DROP TABLE IF EXISTS user_service_options;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL COMMENT 'Lưu trữ mật khẩu đã được hash (mã hóa)',
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) COMMENT 'Thuộc tính phức hợp (Họ và Tên)',
    avatar VARCHAR(255),
    status ENUM('active', 'banned') DEFAULT 'active',
    credit INT DEFAULT 0 COMMENT 'Số dư Credit của người dùng (100 Credit = 1GB RAM / tháng)',
    reset_token VARCHAR(255) NULL,
    role ENUM('admin', 'member') NOT NULL COMMENT 'Xử lý phân cấp Disjoint (Admin hoặc Member)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_notifications (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 2. NHÓM SẢN PHẨM & VẬN HÀNH DỊCH VỤ
-- ==========================================

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    ram_mb INT,
    cpu_cores INT,
    disk_gb INT,
    image_url VARCHAR(255),
    status ENUM('active', 'hidden') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    ip_address VARCHAR(50),
    port INT,
    status ENUM('active', 'suspended', 'expired') DEFAULT 'active',
    current_ram_mb INT COMMENT 'Lưu trữ RAM hiện tại (hỗ trợ tính năng mua thêm RAM)',
    expires_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional table to store per-service options (engine type, version, etc.)
CREATE TABLE user_service_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_service_id INT NOT NULL,
    option_key VARCHAR(100) NOT NULL,
    option_value VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_service_id) REFERENCES user_services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 3. NHÓM GIAO DỊCH (GIỎ HÀNG & ĐƠN HÀNG)
-- ==========================================

CREATE TABLE carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL COMMENT 'NULL nếu là khách vãng lai (Guest)',
    session_id VARCHAR(100) COMMENT 'Session ID dành cho khách chưa đăng nhập',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    duration_months INT DEFAULT 1 COMMENT 'Số tháng khách muốn thuê',
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    total_amount DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Thuộc tính dẫn xuất: Tổng tiền của đơn',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL COMMENT 'Lưu giá cứng tại thời điểm chốt đơn',
    quantity INT DEFAULT 1,
    duration_months INT DEFAULT 1 COMMENT 'Số tháng thực tế đã thanh toán',
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT COMMENT 'Lưu mã HTML của trang tĩnh',
    status ENUM('published', 'draft') DEFAULT 'published',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(100) NULL,
    status ENUM('active', 'hidden') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `faq_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) DEFAULT NULL,
    `email` VARCHAR(200) DEFAULT NULL,
    `category` VARCHAR(100) DEFAULT NULL,
    `message` TEXT NOT NULL,
    `page_url` VARCHAR(255) DEFAULT NULL,
    `status` VARCHAR(30) DEFAULT 'new',
    `reply` TEXT DEFAULT NULL,
    `reply_by` VARCHAR(100) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `replied_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FAQ categories table: stores title, slug and optional image for each FAQ category
CREATE TABLE IF NOT EXISTS `faq_categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(150) NOT NULL UNIQUE,
    `image` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `about` (
  `id` int(11) NOT NULL PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `services_heading` varchar(255) DEFAULT NULL,
  `partners_heading` varchar(255) DEFAULT NULL,
  `modpacks_heading` varchar(255) DEFAULT NULL,
  `gallery_heading` varchar(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL COMMENT 'Khóa ngoại trỏ về Admin đã cập nhật nội dung',
  `uptime` varchar(50) DEFAULT NULL COMMENT 'Ví dụ: 99.9%',
  `support` varchar(255) DEFAULT NULL COMMENT 'Ví dụ: 24/7',
  `performance` varchar(255) DEFAULT NULL COMMENT 'Thông tin hiệu năng',
  `years_active` varchar(50) DEFAULT NULL,
  `founded_year` int(11) DEFAULT NULL,
  `partners` text DEFAULT NULL COMMENT 'JSON array of partners {name,url,logo}',
  `modpacks` text DEFAULT NULL COMMENT 'JSON array of supported modpacks {name,url,logo}',
  `gallery` text DEFAULT NULL COMMENT 'JSON array of gallery items {image,caption}',
  `services` text DEFAULT NULL COMMENT 'JSON array of services {title,icon,description}',
  `sections` text DEFAULT NULL COMMENT 'JSON array of sections {title,content}',
  `cta_heading` varchar(255) DEFAULT NULL,
  `cta_text` text DEFAULT NULL,
  `cta_button_text` varchar(100) DEFAULT NULL,
  `cta_button_url` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `background` varchar(255) DEFAULT NULL COMMENT 'Optional background image (GIF allowed)',
  `intro_gif` varchar(255) DEFAULT NULL COMMENT 'Intro GIF for fullscreen intro (admin-uploadable)',
  `intro_duration` decimal(6,2) DEFAULT 5.52 COMMENT 'Intro GIF duration in seconds'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Ensure a canonical about row exists (id = 1).
INSERT INTO about (id, title, content) 
VALUES (1, 'Tiêu đề mặc định', 'Nội dung mặc định')
ON DUPLICATE KEY UPDATE id=id;

-- (About table already contains extended fields; no ALTERs required)

CREATE TABLE settings (
    key_name VARCHAR(100) PRIMARY KEY,
    value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 5. News Categories (Danh mục tin tức)
-- ==========================================
-- Lưu ý kỹ thuật cho Thực thể yếu: Khóa chính là cụm (parent_id, partial_id).
-- Khi thêm dữ liệu bằng PHP, partial_id sẽ được tính bằng MAX(partial_id) + 1 của parent đó.

CREATE TABLE news_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    status ENUM('active', 'hidden') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data for news categories
INSERT INTO news_categories (name, slug, description, status) VALUES
('Server Game', 'gaming-server', 'Tin tức về máy chủ game và gaming', 'active'),
('Web Hosting', 'web-hosting', 'Tin tức về hosting và web server', 'active'),
('Hướng dẫn', 'huong-dan', 'Các bài viết hướng dẫn sử dụng', 'active'),
('Khuyến mãi', 'khuyen-mai', 'Các chương trình khuyến mãi và ưu đãi', 'active'),
('Công nghệ', 'cong-nghe', 'Tin tức công nghệ và kỹ thuật', 'active')
ON DUPLICATE KEY UPDATE name = VALUES(name);

CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author_id INT NULL COMMENT 'Người đăng bài (Admin). NULL nếu tài khoản Admin bị xóa',
    category_id INT NULL COMMENT 'Thuộc danh mục tin tức nào',
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    thumbnail VARCHAR(255) COMMENT 'Lưu đường dẫn ảnh upload lên server',
    content LONGTEXT NOT NULL,
    meta_keywords VARCHAR(255) COMMENT 'Phục vụ quản lý từ khoá SEO',
    meta_description TEXT COMMENT 'Phục vụ quản lý mô tả SEO',
    status ENUM('published', 'draft') DEFAULT 'published',
    views_count INT DEFAULT 0 COMMENT 'Đếm số lượt đọc bài viết',
    publish_at DATETIME NULL COMMENT 'Ngày xuất bản bài viết',
    is_breaking TINYINT(1) DEFAULT 0 COMMENT 'Đánh dấu bài viết hot/breaking',
    breaking_until DATETIME NULL COMMENT 'Thời gian hết hạn hot',
    seo_score INT DEFAULT 0 COMMENT 'Điểm SEO của bài viết',
    likes_count INT DEFAULT 0 COMMENT 'Số lượt thích bài viết',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES news_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ads table for sidebar advertisements
CREATE TABLE IF NOT EXISTS ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    image_url VARCHAR(255),
    link_url VARCHAR(255),
    position ENUM('sticky-sidebar', 'banner-top', 'banner-bottom') DEFAULT 'sticky-sidebar',
    status ENUM('active', 'inactive') DEFAULT 'active',
    start_at DATETIME NULL,
    end_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Review likes table for comment likes
CREATE TABLE IF NOT EXISTS review_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    review_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, review_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE news_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    news_id INT NOT NULL,
    ip_address VARCHAR(50),
    source VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE news_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    news_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, news_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE media (
    admin_id INT NOT NULL COMMENT 'Thực thể cha: Admin',
    media_id INT NOT NULL COMMENT 'Partial Key',
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (admin_id, media_id),
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE contacts (
    user_id INT NOT NULL COMMENT 'Thực thể cha. Nếu là khách, gán ID của tài khoản Guest mặc định',
    contact_id INT NOT NULL COMMENT 'Partial Key',
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, contact_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'Thành viên viết bình luận',
    product_id INT NULL COMMENT 'XOR: Nếu bình luận sản phẩm',
    news_id INT NULL COMMENT 'XOR: Nếu bình luận bài viết',
    
    rating INT NULL COMMENT 'Cho phép NULL vì bài viết (news) thường chỉ cần comment, không cần rate 1-5 sao',
    comment TEXT NOT NULL,
    likes_count INT DEFAULT 0 COMMENT 'Số lượt thích bình luận',
    status ENUM('pending', 'approved', 'hidden') DEFAULT 'pending' COMMENT 'Admin duyệt bình luận ở đây',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bật lại kiểm tra khóa ngoại sau khi import xong
SET FOREIGN_KEY_CHECKS = 1;


-- ==========================================
-- INITIAL SEED DATA
-- ==========================================

-- 1. Users (Admin & Test User)
-- Username: admin | Password: admin123
INSERT INTO users (id, username, password, email, full_name, avatar, status, credit, reset_token, role, created_at) VALUES
(1, 'admin', '$2a$10$iYI.B2yyF7i75alKEO6XHeUMfcZJgz52DTg67oggoVxUVqyBHZmvS', 'admin@cloudarena.local', 'Administrator 1', '/uploads/avatars/admin.png', 'active', 1000, NULL, 'admin', '2026-05-06 08:22:16'),
(2, 'testuser', '$2y$10$PfYcDZ13nUgxtOdBsX/FPuWwnxxvJXBPZ2sqMviPZFk.H34fobDLi', 'test@cloudarena.local', 'Test User 1', '/uploads/avatars/av_ba55501f0ec6a5a3769658f31dbf9834.png', 'active', 200, NULL, 'member', '2026-05-06 08:22:16'),
(4, 'testuser2', '$2y$10$/gT5cjOikjQ1BBF/GvhtcubPIEc0xLbpvSr8WMkVDQskfi4sjcTYe', 'test2@cloudarena.local', 'Test User 2', '/uploads/avatars/testuser2.jpg', 'active', 0, NULL, 'member', '2026-05-07 12:41:56'),
(8, 'guest_contact', '$2y$10$jmxg/heRebqUTZev3BYM/.zRGGIvN2k9MR2Fl1l6hT2fQXasUgCGW', 'guest@cloud-arena.local', 'Guest Contact', NULL, 'active', 0, NULL, 'member', '2026-05-08 14:42:38');

-- 2. Categories 
INSERT INTO categories (name, slug, description) VALUES
('Gaming Server', 'gaming-server', 'Máy chủ chuyên biệt cho game'),
('Web Hosting', 'web-hosting', 'Hosting cho website và ứng dụng web'),
('Shared Hosting', 'shared-hosting', 'Hosting chia sẻ với giá cạnh tranh');

-- 3. Products (Thien Branch)
INSERT INTO products (category_id, name, slug, description, price, ram_mb, cpu_cores, disk_gb, status) VALUES
(1, 'Gaming Pro 4GB', 'gaming-pro-4gb', 'Server gaming với 4GB RAM, lý tưởng cho game nhỏ', 500000, 4096, 4, 50, 'active'),
(1, 'Gaming Pro 8GB', 'gaming-pro-8gb', 'Server gaming với 8GB RAM, hỗ trợ game lớn', 1000000, 8192, 8, 100, 'active'),
(1, 'Gaming Pro 16GB', 'gaming-pro-16gb', 'Server gaming cao cấp với 16GB RAM', 2000000, 16384, 16, 200, 'active'),
(2, 'Web Basic', 'web-basic', 'Hosting web cơ bản', 250000, 2048, 2, 20, 'active'),
(2, 'Web Plus', 'web-plus', 'Hosting web nâng cao', 750000, 4096, 4, 50, 'active'),
(1, 'Minecraft FREE (1GB - Intel 1355U)', 'minecraft-free-1gb', 'Gói thử nghiệm Minecraft miễn phí (1GB RAM). Chọn PaperMC hoặc Vanilla và phiên bản khi thuê.', 0.00, 1024, 1, 10, 'active');

-- About --

INSERT INTO about (id, title, subtitle, services_heading, partners_heading, modpacks_heading, gallery_heading, content, uptime, support, performance, years_active, founded_year, partners, modpacks, sections, cta_heading, cta_text, cta_button_text, cta_button_url) VALUES (
    1, 'G-Server - Khởi Tạo Đam Mê, Nền Tảng Lưu Trữ Không Giới Hạn', 'Cung cấp giải pháp máy chủ tốc độ cao, cấu hình mạnh mẽ chuyên biệt cho Game Server và Doanh nghiệp.', 'Dịch Vụ Của Chúng Tôi', 'Đối Tác Hạ Tầng & Công Nghệ', 'Hỗ Trợ Đa Dạng Modpack & Nền Tảng', 'Hình Ảnh Trải Nghiệm & Datacenter', '', '99.99%', '24/7/365', 'Ultra NVMe', '5 Năm', 2021, '[
        {"name": "Viettel IDC", "url": "https://viettelidc.com.vn", "logo": "/uploads/viettelidc_logo.png"},
        {"name": "Cisco Systems", "url": "https://www.cisco.com", "logo": "/uploads/cisco_logo.png"},
        {"name": "Cloudflare", "url": "https://www.cloudflare.com", "logo": "/uploads/cloudflare_logo.png"}
    ]',
    '[
        {"name": "Vanilla (Tối ưu cơ chế)", "url": "#", "logo": "/uploads/vanilla_icon.png"},
        {"name": "RLCraft", "url": "#", "logo": "/uploads/rlcraft_icon.png"},
        {"name": "SkyFactory 4", "url": "#", "logo": "/uploads/skyfactory_icon.png"}
    ]',
    '[
        {
            "title": "Hạ tầng mạng Tiêu chuẩn Công nghiệp",
            "content": "<p>G-Server vận hành trên nền tảng phần cứng mạnh mẽ với các thiết bị định tuyến từ <strong>Cisco</strong>, đảm bảo khả năng xử lý hàng triệu gói tin mỗi giây. Hệ thống của chúng tôi được tối ưu hóa cho các giao thức VPN (Site-to-Site, Teleworker) và định tuyến thông minh để giảm thiểu tối đa độ trễ (latency).</p><ul><li><strong>Chống DDoS đa tầng:</strong> Lọc traffic độc hại ngay tại cửa ngõ hạ tầng.</li><li><strong>Băng thông không giới hạn:</strong> Đảm bảo đường truyền luôn thông suốt kể cả trong giờ cao điểm.</li></ul>"
        },
        {
            "title": "Tối ưu hóa cho Trải nghiệm Game Thuần túy",
            "content": "<p>Chúng tôi hiểu rằng những thay đổi về cơ chế server có thể làm hỏng các cỗ máy redstone phức tạp hay các hệ thống farm trong Minecraft. Tại G-Server, chúng tôi cam kết:</p><ul><li><strong>Giữ nguyên cơ chế Vanilla:</strong> Không can thiệp vào hành vi của Piston, Villager hay tốc độ Tick-rate của server.</li><li><strong>Hỗ trợ đa nền tảng:</strong> Dễ dàng triển khai các bản Modpack nặng như RLCraft, SkyFactory hay các Script Roleplay chuyên sâu cho GTA V mà không gặp rào cản kỹ thuật.</li></ul>"
        },
        {
            "title": "Kích hoạt Tức thì - Hỗ trợ Tận tâm",
            "content": "<p>Thời gian của bạn là vàng. Hệ thống quản trị của G-Server cho phép khách hàng khởi tạo dịch vụ chỉ trong vài giây sau khi thanh toán thành công.</p><p>Đội ngũ hỗ trợ của chúng tôi bao gồm những kỹ thuật viên am hiểu sâu về quản trị hệ thống và phát triển game, sẵn sàng giúp bạn giải quyết các vấn đề từ cấu hình IP Route đến cài đặt Plugin/Modpack phức tạp 24/7.</p>"
        }
    ]',
    'Sẵn sàng bắt đầu dự án của bạn cùng G-Server?',
    'Khởi tạo Game Server hoặc Website của bạn ngay hôm nay với các gói dịch vụ lưu trữ linh hoạt, mạnh mẽ và tiết kiệm nhất.',
    'Đăng Ký Ngay',
    '/san-pham'
) ON DUPLICATE KEY UPDATE title = VALUES(title), subtitle = VALUES(subtitle), services_heading = VALUES(services_heading), partners_heading = VALUES(partners_heading), modpacks_heading = VALUES(modpacks_heading), gallery_heading = VALUES(gallery_heading), content = VALUES(content), uptime = VALUES(uptime), support = VALUES(support), performance = VALUES(performance), years_active = VALUES(years_active), founded_year = VALUES(founded_year), partners = VALUES(partners), modpacks = VALUES(modpacks), sections = VALUES(sections), cta_heading = VALUES(cta_heading), cta_text = VALUES(cta_text), cta_button_text = VALUES(cta_button_text), cta_button_url = VALUES(cta_button_url);
-- 4. FAQs (Bao Branch)


-- 5. News (Khoa Branch)
INSERT INTO news (author_id, category_id, title, slug, content, meta_description, meta_keywords, status, is_breaking, views_count) VALUES
(1, 5, 'Ra mắt Cloud Arena: Nền tảng thuê Game Server tự động 100%', 'ra-mat-cloud-arena-nen-tang-thue-game-server-tu-dong-100', '<p>Khách hàng thường mệt mỏi vì thanh toán xong phải chờ Admin duyệt thủ công (có khi mất cả ngày) mới có thông tin IP/Port để chơi.</p><p>Giới thiệu hệ thống tự động hoàn toàn. Giải thích quy trình: Đăng ký -> Chọn cấu hình (products) -> Thanh toán (orders) -> Server tự động khởi tạo và trả về ip_address, port trong vòng 60 giây.</p><p><strong>Tạo tài khoản và nhận ưu đãi khởi tạo server ngay hôm nay.</strong></p>', 'Nền tảng thuê Game Server tự động 100% tại Cloud Arena.', 'game server, cloud arena, tự động, hosting', 'published', 1, 1250),
(1, 5, 'Hệ thống Credit là gì? Cách tối ưu chi phí thuê Server', 'he-thong-credit-la-gi-cach-toi-uu-chi-phi-thue-server', '<p>Khách hàng không hiểu Credit để làm gì, tại sao không thanh toán thẳng bằng tiền mặt.</p><p>Giải thích tỷ lệ quy đổi (Ví dụ: 100,000 VNĐ = 100 Credit = 1GB RAM/tháng). Phân tích lợi ích: Nạp một lần, có thể dùng Credit để mua server mới, gia hạn hoặc nâng cấp RAM bất cứ lúc nào mà không cần lắt nhắt chuyển khoản nhiều lần.</p><p><strong>Hướng dẫn vào trang nạp Credit và các cổng thanh toán hỗ trợ.</strong></p>', 'Tìm hiểu về hệ thống Credit và cách tối ưu chi phí thuê server.', 'credit, tối ưu chi phí, nạp tiền', 'published', 0, 850),
(1, 3, 'Hướng dẫn nâng cấp RAM cho Server chỉ trong 1 click', 'huong-dan-nang-cap-ram-cho-server-chi-trong-1-click', '<p>Server đang chơi bị giật lag do thiếu RAM, nhưng khách sợ nâng cấp sẽ làm mất dữ liệu hoặc phải chờ lâu.</p><p>Giới thiệu tính năng "Cộng dồn RAM" (current_ram_mb). Hướng dẫn các bước vào Dashboard, chọn số RAM cần thêm, hệ thống sẽ tự trừ Credit và apply RAM mới vào server mà không làm mất map hay data.</p><p><strong>Server đang báo đỏ RAM? Nâng cấp ngay chỉ với 100 Credit!</strong></p>', 'Nâng cấp RAM server nhanh chóng không mất dữ liệu.', 'nâng cấp ram, server lag, fix lag', 'published', 0, 1500),
(1, 4, 'Mua Server càng lâu - Tiết kiệm càng sâu', 'mua-server-cang-lau-tiet-kiem-cang-sau', '<p>Khuyến khích khách hàng cam kết sử dụng dịch vụ lâu dài thay vì mua lẻ từng tháng.</p><p>Lập bảng so sánh chi phí khi chọn duration_months là 1 tháng, 3 tháng, 6 tháng và 12 tháng. Phân tích bài toán kinh tế (ví dụ: mua 6 tháng tặng 1 tháng). Nhấn mạnh việc mua dài hạn giúp tránh rủi ro quên gia hạn (expired).</p><p><strong>Chọn kỳ hạn 6 tháng tại giỏ hàng để nhận chiết khấu 15%.</strong></p>', 'Tiết kiệm chi phí khi thuê server game dài hạn.', 'tiết kiệm, khuyến mãi, thuê server', 'published', 1, 3200),
(1, 1, 'Mở Server Terraria Journey''s End: Khám phá thế giới cùng bạn bè', 'mo-server-terraria-journeys-end-kham-pha-the-gioi-cung-ban-be', '<p>Chơi Terraria qua Steam (Host & Play) thường xuyên bị gián đoạn khi chủ phòng tắt máy hoặc rớt mạng.</p><p>Tạo một thế giới Terraria 24/7 độc lập hoàn toàn. Nền tảng hỗ trợ sẵn tShock giúp admin quản lý server, phân quyền người chơi và chống hack item hiệu quả chỉ với vài click cấu hình (options) trên web.</p><p><strong>Thuê ngay máy chủ Terraria và bắt đầu hành trình đánh boss không giới hạn.</strong></p>', 'Hướng dẫn thuê và tạo server Terraria 24/7 mượt mà.', 'terraria, server terraria, tshock', 'published', 0, 500),
(1, 1, 'Vận hành Server Rust: Tự động hóa lịch Wipe và Restart', 'van-hanh-server-rust-tu-dong-hoa-lich-wipe-va-restart', '<p>Chủ server Rust rất vất vả trong việc thức đêm để canh thời gian Wipe map hoặc restart server định kỳ nhằm giảm lag.</p><p>Giới thiệu tính năng Lịch trình (Schedules). Hướng dẫn thiết lập Cronjob ngay trên Panel để tự động gửi thông báo chat rcon, lưu thế giới và khởi động lại vào lúc 4h sáng mà không cần can thiệp thủ công.</p><p><strong>Tự động hóa công việc quản trị Server Rust của bạn ngay hôm nay.</strong></p>', 'Cách thiết lập lịch Wipe và restart tự động cho Server Rust.', 'rust server, wipe map, tự động hóa, schedules', 'published', 0, 750),
(1, 3, 'Trình Quản lý Tệp tin (File Manager) & Truy cập FTP tốc độ cao', 'trinh-quan-ly-tep-tin-file-manager-truy-cap-ftp-toc-do-cao', '<p>Nhiều khách hàng gặp khó khăn khi muốn upload map cũ dung lượng lớn hoặc chỉnh sửa file config trực tiếp do giao diện web tải chậm.</p><p>Hướng dẫn sử dụng Web FTP tích hợp sẵn hoặc kết nối qua phần mềm FileZilla. Cung cấp thông tin host, port, username để khách có toàn quyền quản lý dữ liệu (full access) một cách siêu tốc và bảo mật.</p><p><strong>Đăng nhập Control Panel để trải nghiệm trình quản lý file trực quan.</strong></p>', 'Hướng dẫn sử dụng File Manager và FTP cho Server Game.', 'ftp, file manager, upload map, quản lý dữ liệu', 'published', 0, 1200),
(1, 1, 'Thuê Server Valheim: Hỗ trợ Crossplay PC và Xbox mượt mà', 'thue-server-valheim-ho-tro-crossplay-pc-va-xbox-muot-ma', '<p>Hội bạn chơi Valheim chia làm hai phe: người dùng PC, người dùng Xbox và không thể kết nối chung một host cá nhân.</p><p>Cung cấp giải pháp máy chủ Valheim có bật sẵn tính năng Crossplay ngay khi khởi tạo. Tối ưu cấu hình để đảm bảo thế giới rộng lớn không bị giật lag khi nhiều người cùng xây dựng và thám hiểm ở các khu vực khác nhau.</p><p><strong>Bắt đầu hành trình sinh tồn RLcraft cùng bạn bè ngay.</strong></p>', 'Thuê máy chủ Valheim chất lượng cao, hỗ trợ Crossplay.', 'valheim, crossplay, server valheim', 'published', 0, 850);

-- Ads (Khoa Branch)
INSERT INTO ads (title, image_url, link_url, position, status) VALUES
('Khuyến mãi Game Server - Giảm 50%', '/public/uploads/ads/promo_banner.jpg', 'https://cloudarena.vn/products', 'sticky-sidebar', 'active'),
('Nạp Credit nhận thêm 20%', '/public/uploads/ads/credit_promo.jpg', 'https://cloudarena.vn/billing', 'sticky-sidebar', 'active');

-- Analytics & Interactions
INSERT INTO news_views (news_id, ip_address, source) VALUES
(1, '127.0.0.1', 'direct'), (1, '127.0.0.1', 'direct'), (1, '127.0.0.1', 'direct'),
(2, '127.0.0.1', 'facebook'), (2, '127.0.0.1', 'facebook'),
(3, '127.0.0.1', 'google'), (3, '127.0.0.1', 'google'), (3, '127.0.0.1', 'google'), (3, '127.0.0.1', 'google'),
(4, '127.0.0.1', 'direct'), (4, '127.0.0.1', 'direct'),
(5, '127.0.0.1', 'direct'),
(6, '127.0.0.1', 'google'), (6, '127.0.0.1', 'google'), (6, '127.0.0.1', 'google');

-- Sửa lại ID người dùng từ 3 thành 8 (hoặc ID bất kỳ đã tồn tại trong bảng users)
INSERT INTO news_likes (user_id, news_id) VALUES
(1, 1), (2, 1), (8, 1), 
(1, 2), (2, 2),
(1, 4), (2, 4), (8, 4), (4, 4);

-- 6. Contacts (Giang Branch Backup)
INSERT INTO contacts (`user_id`, `contact_id`, `name`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(2, 1, 'test', 'test@cloudarena.local', 'need test', 'i wanna help', 'replied', '2026-05-06 08:55:12'),
(2, 2, 'Giang', 'test@cloudarena.local', 'Cần tư vấn gói dịch vụ', 'Dịch vụ này tôi muốn biểt nó có thể tương thích với gói modpack nào ?', 'replied', '2026-05-07 10:08:37'),
(2, 3, 'Bảo', 'guest@picoctf.org', 'Ý kiến', 'Tôi không có ý kiến gì', 'replied', '2026-05-07 11:57:43'),
(2, 5, 'Test User 1', 'test@cloudarena.local', 'test chức năng noti', 'letscheckitout', 'unread', '2026-05-10 08:36:41'),
(8, 1, 'guest', 'guest@gmail.com', 'Test Guest', 'I wanna contact', 'read', '2026-05-08 14:42:38'),
(8, 2, 'guest2', 'guest2@gmail.com', 'Test Guest 2', 'I wanna contact 2', 'unread', '2026-05-08 14:43:03'),
(8, 4, 'Instructor Test', 'admin@example.com', 'Asking', 'aaaaaaaaaaaaaa', 'replied', '2026-05-10 09:21:09');

-- 7. Orders & Items (Giang Branch Backup)
INSERT INTO orders (`id`, `user_id`, `status`, `total_amount`, `created_at`) VALUES
(1, 2, 'completed', 500000.00, '2026-01-05 03:00:00'),
(2, 2, 'completed', 750000.00, '2026-01-12 07:30:00'),
(3, 2, 'completed', 250000.00, '2026-01-20 02:15:00'),
(4, 2, 'completed', 1000000.00, '2026-02-03 04:00:00'),
(5, 2, 'completed', 500000.00, '2026-02-14 09:00:00'),
(6, 2, 'completed', 750000.00, '2026-02-25 03:45:00'),
(7, 2, 'completed', 2000000.00, '2026-03-07 02:00:00'),
(8, 2, 'completed', 1000000.00, '2026-03-15 06:00:00'),
(9, 2, 'completed', 500000.00, '2026-03-22 08:30:00'),
(10, 2, 'completed', 750000.00, '2026-03-28 04:20:00'),
(11, 2, 'completed', 1500000.00, '2026-04-04 03:00:00'),
(12, 2, 'completed', 2000000.00, '2026-04-11 07:00:00'),
(13, 2, 'completed', 500000.00, '2026-04-19 02:30:00'),
(14, 2, 'completed', 2500000.00, '2026-05-01 03:00:00'),
(15, 2, 'completed', 1000000.00, '2026-05-05 07:30:00'),
(16, 4, 'completed', 500000.00, '2026-05-10 12:07:04');

-- 8. Admin Notifications (Giang Branch Backup)
INSERT INTO admin_notifications (`id`, `type`, `source_key`, `title`, `message`, `url`, `payload`, `created_at`) VALUES
(1, 'ticket', 'ticket_created:8:2', 'Ticket mới từ guest2', 'Test Guest 2 - guest2@gmail.com', '/admincontacts?user_id=8&contact_id=2', '{\"user_id\":8,\"contact_id\":2}', '2026-05-08 14:43:03'),
(2, 'ticket', 'ticket_created:8:1', 'Ticket mới từ guest', 'Test Guest - guest@gmail.com', '/admincontacts?user_id=8&contact_id=1', '{\"user_id\":8,\"contact_id\":1}', '2026-05-08 14:42:38'),
(3, 'ticket', 'ticket_created:2:4', 'Ticket mới từ Test User 1', 'Fix - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=4', '{\"user_id\":2,\"contact_id\":4}', '2026-05-08 14:12:24'),
(4, 'ticket', 'ticket_created:2:3', 'Ticket mới từ Bảo', 'Ý kiến - guest@picoctf.org', '/admincontacts?user_id=2&contact_id=3', '{\"user_id\":2,\"contact_id\":3}', '2026-05-07 11:57:43'),
(5, 'ticket', 'ticket_created:2:2', 'Ticket mới từ Giang', 'Cần tư vấn gói dịch vụ - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=2', '{\"user_id\":2,\"contact_id\":2}', '2026-05-07 10:08:37'),
(6, 'ticket', 'ticket_created:2:1', 'Ticket mới từ test', 'need test - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=1', '{\"user_id\":2,\"contact_id\":1}', '2026-05-06 08:55:12'),
(7, 'revenue', 'order_completed:15', 'Đơn hàng #15 đã hoàn tất', 'Khách #2 thanh toán 1.000.000đ.', '/admin', '{\"order_id\":15,\"user_id\":2,\"total_amount\":1000000}', '2026-05-05 07:30:00'),
(8, 'revenue', 'order_completed:14', 'Đơn hàng #14 đã hoàn tất', 'Khách #2 thanh toán 2.500.000đ.', '/admin', '{\"order_id\":14,\"user_id\":2,\"total_amount\":2500000}', '2026-05-01 03:00:00'),
(9, 'revenue', 'order_completed:13', 'Đơn hàng #13 đã hoàn tất', 'Khách #2 thanh toán 500.000đ.', '/admin', '{\"order_id\":13,\"user_id\":2,\"total_amount\":500000}', '2026-04-19 02:30:00'),
(10, 'revenue', 'order_completed:12', 'Đơn hàng #12 đã hoàn tất', 'Khách #2 thanh toán 2.000.000đ.', '/admin', '{\"order_id\":12,\"user_id\":2,\"total_amount\":2000000}', '2026-04-11 07:00:00'),
(11, 'revenue', 'order_completed:11', 'Đơn hàng #11 đã hoàn tất', 'Khách #2 thanh toán 1.500.000đ.', '/admin', '{\"order_id\":11,\"user_id\":2,\"total_amount\":1500000}', '2026-04-04 03:00:00'),
(12, 'revenue', 'order_completed:10', 'Đơn hàng #10 đã hoàn tất', 'Khách #2 thanh toán 750.000đ.', '/admin', '{\"order_id\":10,\"user_id\":2,\"total_amount\":750000}', '2026-03-28 04:20:00'),
(13, 'revenue', 'order_completed:9', 'Đơn hàng #9 đã hoàn tất', 'Khách #2 thanh toán 500.000đ.', '/admin', '{\"order_id\":9,\"user_id\":2,\"total_amount\":500000}', '2026-03-22 08:30:00'),
(14, 'revenue', 'order_completed:8', 'Đơn hàng #8 đã hoàn tất', 'Khách #2 thanh toán 1.000.000đ.', '/admin', '{\"order_id\":8,\"user_id\":2,\"total_amount\":1000000}', '2026-03-15 06:00:00'),
(15, 'revenue', 'order_completed:7', 'Đơn hàng #7 đã hoàn tất', 'Khách #2 thanh toán 2.000.000đ.', '/admin', '{\"order_id\":7,\"user_id\":2,\"total_amount\":2000000}', '2026-03-07 02:00:00'),
(16, 'revenue', 'order_completed:6', 'Đơn hàng #6 đã hoàn tất', 'Khách #2 thanh toán 750.000đ.', '/admin', '{\"order_id\":6,\"user_id\":2,\"total_amount\":750000}', '2026-02-25 03:45:00'),
(17, 'revenue', 'order_completed:5', 'Đơn hàng #5 đã hoàn tất', 'Khách #2 thanh toán 500.000đ.', '/admin', '{\"order_id\":5,\"user_id\":2,\"total_amount\":500000}', '2026-02-14 09:00:00'),
(18, 'revenue', 'order_completed:4', 'Đơn hàng #4 đã hoàn tất', 'Khách #2 thanh toán 1.000.000đ.', '/admin', '{\"order_id\":4,\"user_id\":2,\"total_amount\":1000000}', '2026-02-03 04:00:00'),
(19, 'revenue', 'order_completed:3', 'Đơn hàng #3 đã hoàn tất', 'Khách #2 thanh toán 250.000đ.', '/admin', '{\"order_id\":3,\"user_id\":2,\"total_amount\":250000}', '2026-01-20 02:15:00'),
(20, 'revenue', 'order_completed:2', 'Đơn hàng #2 đã hoàn tất', 'Khách #2 thanh toán 750.000đ.', '/admin', '{\"order_id\":2,\"user_id\":2,\"total_amount\":750000}', '2026-01-12 07:30:00'),
(21, 'revenue', 'order_completed:1', 'Đơn hàng #1 đã hoàn tất', 'Khách #2 thanh toán 500.000đ.', '/admin', '{\"order_id\":1,\"user_id\":2,\"total_amount\":500000}', '2026-01-05 03:00:00'),
(295, 'ticket', 'ticket_created:8:3', 'Ticket mới từ Khách', 'Test chức năng noti - khach@gmail.com', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3}', '2026-05-10 03:35:10'),
(576, 'ticket', 'ticket_created:2:5', 'Ticket mới từ Test User 1', 'test chức năng noti - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=5', '{\"user_id\":2,\"contact_id\":5}', '2026-05-10 03:36:41'),
(995, 'ticket', 'ticket_created:4:1', 'Ticket mới từ Test User 2', 'Test chức năng noti 2 - test2@cloudarena.local', '/admincontacts?user_id=4&contact_id=1', '{\"user_id\":4,\"contact_id\":1}', '2026-05-10 03:43:45'),
(1657, 'ticket', 'ticket_created:8:4', 'Ticket mới từ khách', 'heheheheheh - khach@gmail.com', '/admincontacts?user_id=8&contact_id=4', '{\"user_id\":8,\"contact_id\":4}', '2026-05-10 03:45:49'),
(2627, 'ticket', 'ticket_created:8:5', 'Ticket mới từ baaaaaa', 'weneedba - ba@gmail.com', '/admincontacts?user_id=8&contact_id=5', '{\"user_id\":8,\"contact_id\":5}', '2026-05-10 03:51:06'),
(2628, 'ticket', 'ticket_created:8:6', 'Ticket mới từ giang', 'need test - giang@gmail.com', '/admincontacts?user_id=8&contact_id=6', '{\"user_id\":8,\"contact_id\":6}', '2026-05-10 03:51:38'),
(3825, 'ticket', 'ticket_created:8:3:20260510155304', 'Ticket mới từ giang', 'Cần tư vấn - giang@gmail.com', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3,\"created_at\":\"2026-05-10 15:53:04\"}', '2026-05-10 08:53:04'),
(3826, 'ticket', 'ticket_created:2:5:20260510153641', 'Ticket mới từ Test User 1', 'test chức năng noti - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=5', '{\"user_id\":2,\"contact_id\":5,\"created_at\":\"2026-05-10 15:36:41\"}', '2026-05-10 08:36:41'),
(3827, 'ticket', 'ticket_created:8:2:20260508214303', 'Ticket mới từ guest2', 'Test Guest 2 - guest2@gmail.com', '/admincontacts?user_id=8&contact_id=2', '{\"user_id\":8,\"contact_id\":2,\"created_at\":\"2026-05-08 21:43:03\"}', '2026-05-08 14:43:03'),
(3828, 'ticket', 'ticket_created:8:1:20260508214238', 'Ticket mới từ guest', 'Test Guest - guest@gmail.com', '/admincontacts?user_id=8&contact_id=1', '{\"user_id\":8,\"contact_id\":1,\"created_at\":\"2026-05-08 21:42:38\"}', '2026-05-08 14:42:38'),
(3829, 'ticket', 'ticket_created:2:4:20260508211224', 'Ticket mới từ Test User 1', 'Fix - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=4', '{\"user_id\":2,\"contact_id\":4,\"created_at\":\"2026-05-08 21:12:24\"}', '2026-05-08 14:12:24'),
(3830, 'ticket', 'ticket_created:2:3:20260507185743', 'Ticket mới từ Bảo', 'Ý kiến - guest@picoctf.org', '/admincontacts?user_id=2&contact_id=3', '{\"user_id\":2,\"contact_id\":3,\"created_at\":\"2026-05-07 18:57:43\"}', '2026-05-07 11:57:43'),
(3831, 'ticket', 'ticket_created:2:2:20260507170837', 'Ticket mới từ Giang', 'Cần tư vấn gói dịch vụ - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=2', '{\"user_id\":2,\"contact_id\":2,\"created_at\":\"2026-05-07 17:08:37\"}', '2026-05-07 10:08:37'),
(3832, 'ticket', 'ticket_created:2:1:20260506155512', 'Ticket mới từ test', 'need test - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=1', '{\"user_id\":2,\"contact_id\":1,\"created_at\":\"2026-05-06 15:55:12\"}', '2026-05-06 08:55:12'),
(3833, 'ticket', 'ticket_created:8:4:20260510110430', 'Ticket mới từ giang', 'Cần tư vấn - guest@picoctf.org', '/admincontacts?user_id=8&contact_id=4', '{\"user_id\":8,\"contact_id\":4,\"created_at\":\"2026-05-10 11:04:30\"}', '2026-05-10 04:04:30'),
(3834, 'ticket', 'ticket_created:8:4:20260510160430', 'Ticket mới từ giang', 'Cần tư vấn - guest@picoctf.org', '/admincontacts?user_id=8&contact_id=4', '{\"user_id\":8,\"contact_id\":4,\"created_at\":\"2026-05-10 16:04:30\"}', '2026-05-10 09:04:30'),
(3835, 'ticket', 'ticket_created:2:6:20260510110524', 'Ticket mới từ Test User 1', 'Test 2 - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=6', '{\"user_id\":2,\"contact_id\":6,\"created_at\":\"2026-05-10 11:05:24\"}', '2026-05-10 04:05:24'),
(3836, 'ticket', 'ticket_created:2:6:20260510160524', 'Ticket mới từ Test User 1', 'Test 2 - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=6', '{\"user_id\":2,\"contact_id\":6,\"created_at\":\"2026-05-10 16:05:24\"}', '2026-05-10 09:05:24'),
(3837, 'ticket', 'ticket_created:4:1:20260510110614', 'Ticket mới từ Test User 2', 'Test 21111 - test2@cloudarena.local', '/admincontacts?user_id=4&contact_id=1', '{\"user_id\":4,\"contact_id\":1,\"created_at\":\"2026-05-10 11:06:14\"}', '2026-05-10 04:06:14'),
(3838, 'ticket', 'ticket_created:4:1:20260510160614', 'Ticket mới từ Test User 2', 'Test 21111 - test2@cloudarena.local', '/admincontacts?user_id=4&contact_id=1', '{\"user_id\":4,\"contact_id\":1,\"created_at\":\"2026-05-10 16:06:14\"}', '2026-05-10 09:06:14'),
(3839, 'ticket', 'ticket_created:8:3:20260510110659', 'Ticket mới từ Giang', 'need test - alexngo4work@gmail.com', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3,\"created_at\":\"2026-05-10 11:06:59\"}', '2026-05-10 04:06:59'),
(3840, 'ticket', 'ticket_created:8:5:20260510110719', 'Ticket mới từ Instructor Test', '1111 - admin@example.com', '/admincontacts?user_id=8&contact_id=5', '{\"user_id\":8,\"contact_id\":5,\"created_at\":\"2026-05-10 11:07:19\"}', '2026-05-10 04:07:19'),
(3841, 'ticket', 'ticket_created:8:5:20260510160719', 'Ticket mới từ Instructor Test', '1111 - admin@example.com', '/admincontacts?user_id=8&contact_id=5', '{\"user_id\":8,\"contact_id\":5,\"created_at\":\"2026-05-10 16:07:19\"}', '2026-05-10 09:07:19'),
(3842, 'ticket', 'ticket_created:8:3:20260510160659', 'Ticket mới từ Giang', 'need test - alexngo4work@gmail.com', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3,\"created_at\":\"2026-05-10 16:06:59\"}', '2026-05-10 09:06:59'),
(3843, 'ticket', 'ticket_created:8:3:20260510161240', 'Ticket mới từ giang', 'Fix - lemdien258@gmail.com', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3,\"created_at\":\"2026-05-10 16:12:40\"}', '2026-05-10 09:12:40'),
(3844, 'ticket', 'ticket_created:2:6:20260510161305', 'Ticket mới từ Test User 1', 'Fix2 - test@cloudarena.local', '/admincontacts?user_id=2&contact_id=6', '{\"user_id\":2,\"contact_id\":6,\"created_at\":\"2026-05-10 16:13:05\"}', '2026-05-10 09:13:05'),
(3845, 'ticket', 'ticket_created:4:1:20260510161319', 'Ticket mới từ Test User 2', 'Fix3 - test2@cloudarena.local', '/admincontacts?user_id=4&contact_id=1', '{\"user_id\":4,\"contact_id\":1,\"created_at\":\"2026-05-10 16:13:19\"}', '2026-05-10 09:13:19'),
(3846, 'ticket', 'ticket_created:8:3:20260510162027', 'Ticket mới từ Giang', 'Cần tư vấn - giang.ngolame@hcmut.edu.vn', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3,\"created_at\":\"2026-05-10 16:20:27\"}', '2026-05-10 09:20:27'),
(3847, 'ticket', 'ticket_created:8:3:20260510162059', 'Ticket mới từ giang', 'Test 2 - lemdien258@gmail.com', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3,\"created_at\":\"2026-05-10 16:20:59\"}', '2026-05-10 09:20:59'),
(3848, 'ticket', 'ticket_created:8:4:20260510162109', 'Ticket mới từ Instructor Test', 'Asking - admin@example.com', '/admincontacts?user_id=8&contact_id=4', '{\"user_id\":8,\"contact_id\":4,\"created_at\":\"2026-05-10 16:21:09\"}', '2026-05-10 09:21:09'),
(3849, 'revenue', 'order_completed:16', 'Đơn hàng #16 đã hoàn tất', 'Khách #4 thanh toán 500.000đ.', '/admin', '{\"order_id\":16,\"user_id\":4,\"total_amount\":500000}', '2026-05-10 12:07:04'),
(3850, 'ticket', 'ticket_created:8:3:20260510203504', 'Ticket mới từ khách', 'test rate - khach@gmail.com', '/admincontacts?user_id=8&contact_id=3', '{\"user_id\":8,\"contact_id\":3,\"created_at\":\"2026-05-10 20:35:04\"}', '2026-05-10 13:35:04');

-- 9. Settings (Giang Branch Backup)
INSERT INTO settings (`key_name`, `value`) VALUES
('about_heading_highlight', 'Chúng Tôi'),
('about_heading_prefix', 'Về'),
('about_para1', 'Cloud Arena tự hào là đơn vị tiên phong trong việc cung cấp các giải pháp máy chủ game hiệu năng cao tại Việt Nam.'),
('about_para2', 'Với đội ngũ kỹ thuật giàu kinh nghiệm và hạ tầng mạng băng thông rộng, chúng tôi cam kết mang lại trải nghiệm chơi game mượt mà nhất cho cộng đồng.'),
('admin_notification_state_1', '{\"last_opened_id\":3850,\"last_opened_at\":\"2026-05-10 20:35:04\"}'),
('contact_page_intro', 'Gửi ticket hỗ trợ cho chúng tôi. Đội ngũ sẽ phản hồi sớm nhất có thể.'),
('contact_page_title', 'Liên Hệ'),
('contact_sidebar_title', 'Thông tin liên hệ'),
('contact_ticket_meta_2_1', '{\"priority\":\"high\",\"admin_reply\":\"no\",\"replied_at\":\"2026-05-06 10:58:51\"}'),
('contact_ticket_meta_2_2', '{\"priority\":\"normal\",\"admin_reply\":\"\u1ee8ng v\u1edbi Vanilla b\u1ea1n nh\u00e9 !\",\"replied_at\":\"2026-05-08 15:52:24\"}'),
('contact_ticket_meta_2_3', '{\"priority\":\"normal\",\"admin_reply\":\"h\u1ea3\",\"replied_at\":\"2026-05-07 15:26:15\"}'),
('contact_ticket_meta_2_5', '{\"priority\":\"high\",\"admin_reply\":null,\"replied_at\":null}'),
('contact_ticket_meta_8_1', '{\"priority\":\"high\",\"admin_reply\":null,\"replied_at\":null}'),
('contact_ticket_meta_8_2', '{\"priority\":\"normal\",\"admin_reply\":null,\"replied_at\":null}'),
('contact_ticket_meta_8_4', '{\"priority\":\"low\",\"admin_reply\":\"ok bro\",\"replied_at\":\"2026-05-10 14:05:05\"}'),
('home_about_feat1_text', 'Sử dụng CPU Intel Core i9 & AMD Ryzen mới nhất, cùng ổ cứng NVMe Gen4 cho tốc độ xử lý vượt trội.'),
('home_about_feat1_title', 'Hiệu năng tối đa'),
('home_about_feat2_text', 'Lớp bảo vệ đa tầng giúp lọc bỏ các cuộc tấn công DDoS lên đến hàng trăm Gbps, giữ server luôn ổn định.'),
('home_about_feat2_title', 'Anti-DDoS mạnh mẽ'),
('home_about_feat3_text', 'Dữ liệu của bạn luôn an toàn với hệ thống sao lưu tự động hàng ngày. Khôi phục nhanh chóng khi cần thiết.'),
('home_about_feat3_title', 'Backup tự động'),
('home_about_heading', 'Tại sao chọn G-SERVER?'),
('home_about_kicker', 'Tính năng vượt trội'),
('home_about_lead', 'Nền tảng tập trung cho cộng đồng game thủ: triển khai nhanh, bảo mật cao và vận hành ổn định xuyên suốt.'),
('home_card_tech_title', 'Năng lực công nghệ'),
('home_hero_bg_image', 'hero_bg_bee5ab4cb1755332d8cb6d17dc0d5ad7.gif'),
('home_hero_subtitle', 'Máy chủ game chuyên nghiệp với hiệu năng cao, hỗ trợ modpack và quản lý dễ dàng. Khởi động Server chỉ trong vài phút với công nghệ ảo hóa tiên tiến nhất.'),
('home_hero_title_gradient', 'Game Server Hosting'),
('home_hero_title_plain', 'Cho Mọi Game Thủ'),
('home_product_ids', '5,1,2,3'),
('home_review_key', ''),
('profile_avatar_hint', 'JPG, PNG, GIF, WEBP. Tối đa 2MB.'),
('profile_avatar_upload_label', 'Tải ảnh lên'),
('profile_btn_save', 'Lưu thay đổi'),
('profile_btn_update_password', 'Cập nhật mật khẩu'),
('profile_label_confirm_password', 'Xác nhận mật khẩu'),
('profile_label_current_password', 'Mật khẩu hiện tại'),
('profile_label_display_name', 'Họ tên hiển thị'),
('profile_label_email', 'Địa chỉ email'),
('profile_label_new_password', 'Mật khẩu mới'),
('profile_page_intro', 'Quản lý thông tin tài khoản và bảo mật.'),
('profile_page_title', 'Hồ sơ thành viên'),
('profile_section_avatar_title', 'Ảnh đại diện'),
('profile_section_password_title', 'Đổi mật khẩu'),
('profile_section_personal_title', 'Thông tin cá nhân'),
('site_about_snippet', 'Nền tảng cho thuê Game Server ổn định, hiệu năng cao.'),
('site_address', '268 Lý Thường Kiệt, Q10, TP.HCM'),
('site_contact_email', 'contact@gameserver.vn'),
('site_hotline', '0123 456 789'),
('site_logo_image', 'brand_logo_20260508144225_50ea750b.png'),
('site_logo_text', 'G-SERVER'),
('site_map_embed_url', 'https://www.google.com/maps?q=268+Ly+Thuong+Kiet+Q10+TPHCM&output=embed')
ON DUPLICATE KEY UPDATE value = VALUES(value);

SET FOREIGN_KEY_CHECKS = 1;