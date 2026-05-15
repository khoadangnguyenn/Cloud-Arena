DROP DATABASE IF EXISTS cloud_arena;
CREATE DATABASE IF NOT EXISTS cloud_arena;
USE cloud_arena;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS news_likes;
DROP TABLE IF EXISTS news_views;
DROP TABLE IF EXISTS review_likes;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS media;
DROP TABLE IF EXISTS news;
DROP TABLE IF EXISTS ads;
DROP TABLE IF EXISTS pages;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS carts;
DROP TABLE IF EXISTS user_services;
DROP TABLE IF EXISTS user_service_options;
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


-- Detailed 'about' data is inserted later in the seed data section.

CREATE TABLE settings (
    key_name VARCHAR(100) PRIMARY KEY,
    value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'Thành viên viết bình luận',
    product_id INT NULL COMMENT 'XOR: Nếu bình luận sản phẩm',
    news_id INT NULL COMMENT 'XOR: Nếu bình luận bài viết',
    
    rating INT NULL COMMENT 'Cho phép NULL vì bài viết (news) thường chỉ cần comment, không cần rate 1-5 sao',
    comment TEXT NOT NULL,
    likes_count INT DEFAULT 0 COMMENT 'Số lượt thích bình luận',
    status ENUM('pending', 'approved', 'hidden') DEFAULT 'approved' COMMENT 'Admin duyệt bình luận ở đây',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE
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

-- Placeholder for reviews was moved up to before review_likes

-- Bật lại kiểm tra khóa ngoại sau khi import xong
SET FOREIGN_KEY_CHECKS = 1;


-- ==========================================
-- INITIAL SEED DATA
-- ==========================================

-- 0. Reviews (Moved to after users, products, news)

-- 1. Users (Admin & Test User)
-- Username: admin | Password: admin123
INSERT INTO users (id, username, password, email, full_name, avatar, status, credit, reset_token, role, created_at) VALUES
(1, 'admin', '$2y$10$dJ2DZ./LMB9IxBzAivA56.H3Z.zpgIPF9kKtiBXZLKXjOeTrRSz8.', 'admin@cloudarena.local', 'Admin', '/uploads/avatars/av_4e1e59fda148d8cfa83180d0f475eb09.jpg', 'active', 1000, NULL, 'admin', '2026-05-06 01:22:16'),
(2, 'testuser', '$2y$10$PfYcDZ13nUgxtOdBsX/FPuWwnxxvJXBPZ2sqMviPZFk.H34fobDLi', 'test@cloudarena.local', 'Test User 1', 'https://ui-avatars.com/api/?name=Minh+Tuan&background=20c997&color=fff', 'active', 200, NULL, 'member', '2026-05-06 01:22:16'),
(3, 'minhtuan_pro', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'minhtuan@example.com', 'Minh Tuấn', 'https://ui-avatars.com/api/?name=Minh+Tuan&background=20c997&color=fff', 'active', 0, NULL, 'member', '2026-05-14 20:28:46'),
(4, 'testuser2', '$2y$10$/gT5cjOikjQ1BBF/GvhtcubPIEc0xLbpvSr8WMkVDQskfi4sjcTYe', 'test2@cloudarena.local', 'Test User 2', '/uploads/avatars/testuser2.jpg', 'active', 0, NULL, 'member', '2026-05-07 05:41:56'),
(5, 'duclong_gaming', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'duclong@example.com', 'Đức Long', 'https://ui-avatars.com/api/?name=Duc+Long&background=6f42c1&color=fff', 'active', 0, NULL, 'member', '2026-05-14 20:28:46'),
(8, 'guest_contact', '$2y$10$jmxg/heRebqUTZev3BYM/.zRGGIvN2k9MR2Fl1l6hT2fQXasUgCGW', 'guest@cloud-arena.local', 'Guest Contact', NULL, 'active', 0, NULL, 'member', '2026-05-08 07:42:38'),
(9, 'testuser3', '$2y$10$HQkasFyBhTzLUo5c7bGOD.xnLoDqTP2w6y5MEOCFxtrPRSR/s0lim', 'testuser@example.com', 'testuser3', NULL, 'active', 0, NULL, 'member', '2026-05-14 20:45:39'),
(10, 'ngogiang', '$2y$10$7vMM.PEiQc9jggF4A95k6.DmgUXxSDe9xMU7n9JvkkjVTkD00a13.', 'giang@gmail.com', 'ngogiang', '/uploads/avatars/av_4bd30cbf499666e52710799545b7d1fe.jpg', 'active', 0, NULL, 'member', '2026-05-15 05:41:28');

-- 2. Categories 
INSERT INTO categories (name, slug, description) VALUES
('Gaming Server', 'gaming-server', 'Máy chủ chuyên biệt cho game'),
('Web Hosting', 'web-hosting', 'Hosting cho website và ứng dụng web'),
('Shared Hosting', 'shared-hosting', 'Hosting chia sẻ với giá cạnh tranh');

-- 3. Products (Thien Branch)
INSERT INTO products (id, category_id, name, slug, description, price, ram_mb, cpu_cores, disk_gb, image_url, status, created_at) VALUES
(1, 1, 'Gaming Pro 4GB', 'gaming-pro-4gb', 'Server gaming với 4GB RAM, lý tưởng cho game nhỏ', 500000.00, 4096, 4, 50, 'media/1778792044_growtika-9WnjxT1NCoY-unsplash.jpg', 'hidden', '2026-05-14 18:51:39'),
(2, 1, 'Gaming Pro 8GB', 'gaming-pro-8gb', 'Server gaming với 8GB RAM, hỗ trợ game lớn', 1000000.00, 8192, 8, 100, 'media/1778792064_growtika-9WnjxT1NCoY-unsplash.jpg', 'active', '2026-05-14 18:51:39'),
(3, 1, 'Gaming Pro 16GB', 'gaming-pro-16gb', 'Server gaming cao cấp với 16GB RAM', 2000000.00, 16384, 16, 200, 'media/1778792071_growtika-9WnjxT1NCoY-unsplash.jpg', 'active', '2026-05-14 18:51:39'),
(4, 2, 'Web Basic', 'web-basic', 'Hosting web cơ bản', 250000.00, 2048, 2, 20, 'media/1778792078_growtika-9WnjxT1NCoY-unsplash.jpg', 'active', '2026-05-14 18:51:39'),
(5, 2, 'Web Plus', 'web-plus', 'Hosting web nâng cao', 750000.00, 4096, 4, 50, 'media/1778792085_growtika-9WnjxT1NCoY-unsplash.jpg', 'active', '2026-05-14 18:51:39');

-- About --
INSERT INTO about (id, title, subtitle, services_heading, partners_heading, modpacks_heading, gallery_heading, content, image, admin_id, uptime, support, performance, years_active, founded_year, partners, modpacks, gallery, services, sections, cta_heading, cta_text, cta_button_text, cta_button_url, updated_at, background, intro_gif, intro_duration) VALUES
(1, 'G-Server', 'Cung cấp giải pháp máy chủ tốc độ cao, cấu hình mạnh mẽ chuyên biệt cho Game Server và Doanh nghiệp.', NULL, 'Đối Tác Hạ Tầng & Công Nghệ', 'Hỗ Trợ Đa Dạng Modpack & Nền Tảng', 'Hình Ảnh Trải Nghiệm & Datacenter', '', 'img_6a06d1b5bac8c.jpg', 1, '99.99%', '24/7/365', 'Ultra NVMe', '5 Năm', 2021, '[{\"name\":\"Viettel IDC\",\"url\":\"https:\\/\\/viettelidc.com.vn\",\"logo\":\"img_6a061c63436a9.png\"},{\"name\":\"Cisco Systems\",\"url\":\"https:\\/\\/www.cisco.com\",\"logo\":\"img_6a061c63436f8.png\"},{\"name\":\"Vinafone\",\"url\":\"https:\\/\\/www.mobifone.vn\",\"logo\":\"img_6a06b741526f0.jpeg\"},{\"name\":\"TMA Solutions\",\"url\":\"https:\\/\\/www.tmasolutions.vn\",\"logo\":\"img_6a06b7415280e.png\"},{\"name\":\"Eyecode Tech\",\"url\":\"https:\\/\\/www.eyecodetech.vn\",\"logo\":\"img_6a06b741528da.jpeg\"}]', '[{\"name\":\"Minecraft\",\"url\":\"http:\\/\\/localhost\\/Cloud-Arena-main\\/products\",\"logo\":\"img_6a061c63438b4.jpg\"},{\"name\":\"GTA V\",\"url\":\"http:\\/\\/localhost\\/Cloud-Arena-main\\/products\",\"logo\":\"img_6a061c6343902.jpg\"},{\"name\":\"Web Hosting\",\"url\":\"http:\\/\\/localhost\\/Cloud-Arena-main\\/products\",\"logo\":\"img_6a061c6343947.jpg\"}]', '[{\"image\":\"img_6a061c634398e.jpg\",\"title\":\"Minecraft\",\"caption\":\"Xây dựng thế giới riêng của bạn hoạt động 24\\/7 với hiệu năng ổn định, hỗ trợ plugin, modpack và hàng trăm người chơi cùng lúc trên hạ tầng tối ưu dành riêng cho Minecraft Server.\"},{\"image\":\"img_6a061c63439d2.jpg\",\"title\":\"GTA V\",\"caption\":\"Khởi chạy máy chủ FiveM chuyên nghiệp chỉ trong vài phút, đồng bộ tài nguyên siêu nhanh, ping thấp và toàn quyền quản lý cộng đồng GTA V Roleplay của bạn.\"},{\"image\":\"img_6a061c6343a1e.jpg\",\"title\":\"Web Hosting\",\"caption\":\"Triển khai Website, Panel quản trị hoặc Landing Page gaming trên nền tảng hosting tốc độ cao, bảo mật mạnh mẽ và tối ưu cho hệ sinh thái Game Server hiện đại.\"}]', NULL, '[{\"title\":\"Hạ tầng mạng Tiêu chuẩn Công nghiệp\",\"content\":\"<p>G-Server vận hành trên nền tảng phần cứng mạnh mẽ với các thiết bị định tuyến từ <strong>Cisco<\\/strong>, đảm bảo khả năng xử lý hàng triệu gói tin mỗi giây. Hệ thống của chúng tôi được tối ưu hóa cho các giao thức VPN (Site-to-Site, Teleworker) và định tuyến thông minh để giảm thiểu tối đa độ trễ (latency).<\\/p><ul><li><strong>Chống DDoS đa tầng:<\\/strong> Lọc traffic độc hại ngay tại cửa ngõ hạ tầng.<\\/li><li><strong>Băng thông không giới hạn:<\\/strong><\\/li><\\/ul>\"},{\"title\":\"Tối ưu hóa cho Trải nghiệm Game Thuần túy\",\"content\":\"<p>Chúng tôi hiểu rằng những thay đổi về cơ chế server có thể làm hỏng các cỗ máy redstone phức tạp hay các hệ thống farm trong Minecraft. Tại G-Server, chúng tôi cam kết:<\\/p><ul><li><strong>Giữ nguyên cơ chế Vanilla:<\\/strong> Không can thiệp vào hành vi của Piston, Villager hay tốc độ Tick-rate của server.<\\/li><li><strong>Hỗ trợ đa nền tảng:<\\/strong> Dễ dàng triển khai các bản Modpack nặng như RLCraft, SkyFactory hay các Script Roleplay chuyên sâu cho GTA V mà không gặp rào cản kỹ thuật.<\\/li><\\/ul>\"},{\"title\":\"Kích hoạt Tức thì - Hỗ trợ Tận tâm\",\"content\":\"<p>Thời gian của bạn là vàng. Hệ thống quản trị của G-Server cho phép khách hàng khởi tạo dịch vụ chỉ trong vài giây sau khi thanh toán thành công.<\\/p><p>Đội ngũ hỗ trợ của chúng tôi bao gồm những kỹ thuật viên am hiểu sâu về quản trị hệ thống và phát triển game, sẵn sàng giúp bạn giải quyết các vấn đề từ cấu hình IP Route đến cài đặt Plugin\\/Modpack phức tạp 24\\/7.<\\/p>\"}]', 'Sẵn sàng bắt đầu dự án của bạn cùng G-Server?', 'Khởi tạo Game Server hoặc Website của bạn ngay hôm nay với các gói dịch vụ lưu trữ linh hoạt, mạnh mẽ và tiết kiệm nhất.', 'Đăng Ký Ngay', '/users/register', '2026-05-15 07:56:37', 'img_6a061c63435f2.gif', 'img_6a06b2d6c8c6a.gif', 1.10);

-- 4. FAQs (Bao Branch)
INSERT INTO faqs (id, question, answer, category, status) VALUES
(1, 'Cloud Arena là gì?', 'Cloud Arena là nền tảng cung cấp dịch vụ cho thuê Game Server và Cloud Hosting hàng đầu, giúp bạn khởi tạo máy chủ chỉ trong vài giây với hiệu năng cực cao.', 'Chung', 'active'),
(2, 'Tôi có thể thanh toán qua những phương thức nào?', 'Chúng tôi hỗ trợ nhiều phương thức linh hoạt như Chuyển khoản ngân hàng, Ví MoMo, thẻ cào điện thoại và thanh toán qua số dư Credit trên hệ thống.', 'Thanh toán', 'active'),
(3, 'Server của tôi sẽ được khởi tạo trong bao lâu?', 'Hệ thống hoàn toàn tự động. Ngay sau khi thanh toán thành công, Server của bạn sẽ được khởi tạo và gửi thông tin đăng nhập vào Email chỉ trong 30 giây.', 'Kỹ thuật', 'active'),
(4, 'Tôi có thể nâng cấp cấu hình server sau khi mua không?', 'Hoàn toàn được! Bạn có thể nâng cấp RAM, CPU hoặc dung lượng SSD bất cứ lúc nào trong bảng quản trị mà không làm mất dữ liệu hiện có.', 'Dịch vụ', 'active'),
(5, 'Chính sách hoàn tiền của Cloud Arena như thế nào?', 'Chúng tôi cam kết hoàn tiền 100% trong vòng 24h nếu dịch vụ gặp lỗi kỹ thuật từ phía hệ thống mà không thể khắc phục được.', 'Dịch vụ', 'active'),
(6, 'Cloud Arena có hỗ trợ cài đặt Mod cho Game Server không?', 'Có, chúng tôi hỗ trợ One-Click Install cho hầu hết các bản Mod phổ biến của Minecraft, Rust, Terraria và nhiều game khác.', 'Kỹ thuật', 'active'),
(7, 'Dữ liệu của tôi có được sao lưu (Backup) không?', 'Hệ thống tự động sao lưu dữ liệu của bạn mỗi ngày và lưu trữ trong 7 ngày gần nhất để bạn có thể khôi phục bất cứ khi nào cần thiết.', 'Bảo mật', 'active'),
(8, 'Làm thế nào để liên hệ với đội ngũ kỹ thuật?', 'Bạn có thể gửi Ticket hỗ trợ trong trang Dashboard, nhắn tin qua Fanpage hoặc gọi hotline hỗ trợ 24/7 của chúng tôi.', 'Hỗ trợ', 'active'),
(9, 'Cloud Arena có chống DDoS không?', 'Có! Tất cả các Server tại Cloud Arena đều được trang bị hệ thống lọc traffic và chống DDoS Layer 7 mạnh mẽ, đảm bảo uptime 99.9%.', 'Bảo mật', 'active'),
(10, 'Làm sao để nạp Credit vào tài khoản?', 'Bạn vào phần Tài khoản -> Nạp Credit, chọn số tiền cần nạp và quét mã QR chuyển khoản. Credit sẽ được cộng tự động sau 1-3 phút.', 'Thanh toán', 'active'),
(11, 'Tôi có thể sử dụng Server cho mục đích gì?', 'Bạn có thể sử dụng để chạy Game Server, Website, Bot Discord hoặc các ứng dụng cá nhân miễn là không vi phạm pháp luật Việt Nam.', 'Chung', 'active'),
(12, 'Cloud Arena là gì?', '-', 'Chung', 'active'),
(13, 'Thanh toán', '-', '', 'active'),
(14, 'Hỗ trợ', '-', '', 'active'),
(15, 'sasdasd', '-', 'chung', 'active'),
(16, 'fdfdfdvdv', '-', 'chung', 'active'),
(17, 'đasaxxaz', '-', 'chung', 'active'),
(18, 'Tôi muốn host', '-', 'chung', 'active'),
(19, 'nnfcfdnjsxncx', '-', 'chung', 'active'),
(20, 'dạnasxnas', '-', 'chung', 'active');

INSERT INTO faq_categories (id, title, slug, image, created_at) VALUES
(1, 'Chung', 'chung', 'http://localhost/Cloud-Arena-main/uploads/faq_categories/img_6a06b083330e5.jpg', '2026-05-15 05:33:19'),
(2, 'Kỹ thuật', 'category-1778823227', NULL, '2026-05-15 05:33:47'),
(3, 'Dịch vụ', 'category-1778823234', 'http://localhost/Cloud-Arena-main/uploads/faq_categories/img_6a06b4f468927.jpg', '2026-05-15 05:33:54'),
(4, 'Bảo mật', 'category-1778823242', 'http://localhost/Cloud-Arena-main/uploads/faq_categories/img_6a06b4c3e9657.gif', '2026-05-15 05:34:02');

INSERT INTO faq_messages (id, name, email, category, message, page_url, status, reply, reply_by, created_at, replied_at) VALUES
(1, 'Bao', 'náds@gmail.com', 'chung', 'njcsdncjkds', '/Cloud-Arena-main/pages/faq?category=chung&q=host', 'replied', 'dxjasbxsjjsxa', 'Admin', '2026-05-15 06:13:41', '2026-05-15 13:13:53'),
(2, 'ba', 'bjsabdas@gmail.com', 'chung', 'dsasd', '/Cloud-Arena-main/pages/faq?category=chung', 'replied', 'dsasaxas', 'Admin', '2026-05-15 06:15:52', '2026-05-15 13:16:02');

-- 5. News (Khoa Branch)
INSERT INTO news (id, author_id, category_id, title, slug, thumbnail, content, meta_keywords, meta_description, status, views_count, publish_at, is_breaking, breaking_until, seo_score, likes_count, created_at, updated_at) VALUES
(1, 1, 5, 'Ra mắt Cloud Arena: Nền tảng thuê Game Server tự động 100%', 'ra-mat-cloud-arena-nen-tang-thue-game-server-tu-dong-100', '6a0623ca90a5c_news.jpg', '<p>Khách hàng thường mệt mỏi vì thanh toán xong phải chờ Admin duyệt thủ công (có khi mất cả ngày) mới có thông tin IP/Port để chơi.</p><p>Giới thiệu hệ thống tự động hoàn toàn. Giải thích quy trình: Đăng ký -&gt; Chọn cấu hình (products) -&gt; Thanh toán (orders) -&gt; Server tự động khởi tạo và trả về ip_address, port trong vòng 60 giây.</p><p><strong>Tạo tài khoản và nhận ưu đãi khởi tạo server ngay hôm nay.</strong></p>', 'game server, cloud arena, tự động, hosting', 'Nền tảng thuê Game Server tự động 100% tại Cloud Arena.', 'published', 1262, NULL, 1, NULL, 75, 0, '2026-05-14 18:51:39', '2026-05-14 21:40:11'),
(2, 1, 5, 'Hệ thống Credit là gì? Cách tối ưu chi phí thuê Server', 'he-thong-credit-la-gi-cach-toi-uu-chi-phi-thue-server', NULL, '<p>Khách hàng không hiểu Credit để làm gì, tại sao không thanh toán thẳng bằng tiền mặt.</p><p>Giải thích tỷ lệ quy đổi (Ví dụ: 100,000 VNĐ = 100 Credit = 1GB RAM/tháng). Phân tích lợi ích: Nạp một lần, có thể dùng Credit để mua server mới, gia hạn hoặc nâng cấp RAM bất cứ lúc nào mà không cần lắt nhắt chuyển khoản nhiều lần.</p><p><strong>Hướng dẫn vào trang nạp Credit và các cổng thanh toán hỗ trợ.</strong></p>', 'credit, tối ưu chi phí, nạp tiền', 'Tìm hiểu về hệ thống Credit và cách tối ưu chi phí thuê server.', 'published', 853, NULL, 0, NULL, 0, 0, '2026-05-14 18:51:39', '2026-05-14 21:43:37'),
(3, 1, 3, 'Hướng dẫn nâng cấp RAM cho Server chỉ trong 1 click', 'huong-dan-nang-cap-ram-cho-server-chi-trong-1-click', NULL, '<p>Server đang chơi bị giật lag do thiếu RAM, nhưng khách sợ nâng cấp sẽ làm mất dữ liệu hoặc phải chờ lâu.</p><p>Giới thiệu tính năng \"Cộng dồn RAM\" (current_ram_mb). Hướng dẫn các bước vào Dashboard, chọn số RAM cần thêm, hệ thống sẽ tự trừ Credit và apply RAM mới vào server mà không làm mất map hay data.</p><p><strong>Server đang báo đỏ RAM? Nâng cấp ngay chỉ với 100 Credit!</strong></p>', 'nâng cấp ram, server lag, fix lag', 'Nâng cấp RAM server nhanh chóng không mất dữ liệu.', 'published', 1501, NULL, 0, NULL, 0, 0, '2026-05-14 18:51:39', '2026-05-14 19:50:35'),
(4, 1, 4, 'Mua Server càng lâu - Tiết kiệm càng sâu', 'mua-server-cang-lau-tiet-kiem-cang-sau', NULL, '<p>Khuyến khích khách hàng cam kết sử dụng dịch vụ lâu dài thay vì mua lẻ từng tháng.</p><p>Lập bảng so sánh chi phí khi chọn duration_months là 1 tháng, 3 tháng, 6 tháng và 12 tháng. Phân tích bài toán kinh tế (ví dụ: mua 6 tháng tặng 1 tháng). Nhấn mạnh việc mua dài hạn giúp tránh rủi ro quên gia hạn (expired).</p><p><strong>Chọn kỳ hạn 6 tháng tại giỏ hàng để nhận chiết khấu 15%.</strong></p>', 'tiết kiệm, khuyến mãi, thuê server', 'Tiết kiệm chi phí khi thuê server game dài hạn.', 'published', 3200, NULL, 1, NULL, 0, 0, '2026-05-14 18:51:39', '2026-05-14 18:51:39'),
(5, 1, 1, 'Mở Server Terraria Journey''s End: Khám phá thế giới cùng bạn bè', 'mo-server-terraria-journeys-end-kham-pha-the-gioi-cung-ban-be', NULL, '<p>Chơi Terraria qua Steam (Host & Play) thường xuyên bị gián đoạn khi chủ phòng tắt máy hoặc rớt mạng.</p><p>Tạo một thế giới Terraria 24/7 độc lập hoàn toàn. Nền tảng hỗ trợ sẵn tShock giúp admin quản lý server, phân quyền người chơi và chống hack item hiệu quả chỉ với vài click cấu hình (options) trên web.</p><p><strong>Thuê ngay máy chủ Terraria và bắt đầu hành trình đánh boss không giới hạn.</strong></p>', 'terraria, server terraria, tshock', 'Hướng dẫn thuê và tạo server Terraria 24/7 mượt mà.', 'published', 500, NULL, 0, NULL, 0, 0, '2026-05-14 18:51:39', '2026-05-14 18:51:39'),
(6, 1, 1, 'Vận hành Server Rust: Tự động hóa lịch Wipe và Restart', 'van-hanh-server-rust-tu-dong-hoa-lich-wipe-va-restart', NULL, '<p>Chủ server Rust rất vất vả trong việc thức đêm để canh thời gian Wipe map hoặc restart server định kỳ nhằm giảm lag.</p><p>Giới thiệu tính năng Lịch trình (Schedules). Hướng dẫn thiết lập Cronjob ngay trên Panel để tự động gửi thông báo chat rcon, lưu thế giới và khởi động lại vào lúc 4h sáng mà không cần can thiệp thủ công.</p><p><strong>Tự động hóa công việc quản trị Server Rust của bạn ngay hôm nay.</strong></p>', 'rust server, wipe map, tự động hóa, schedules', 'Cách thiết lập lịch Wipe và restart tự động cho Server Rust.', 'published', 751, NULL, 0, NULL, 0, 0, '2026-05-14 18:51:39', '2026-05-14 20:16:56'),
(7, 1, 3, 'Trình Quản lý Tệp tin (File Manager) & Truy cập FTP tốc độ cao', 'trinh-quan-ly-tep-tin-file-manager-truy-cap-ftp-toc-do-cao', NULL, '<p>Nhiều khách hàng gặp khó khăn khi muốn upload map cũ dung lượng lớn hoặc chỉnh sửa file config trực tiếp do giao diện web tải chậm.</p><p>Hướng dẫn sử dụng Web FTP tích hợp sẵn hoặc kết nối qua phần mềm FileZilla. Cung cấp thông tin host, port, username để khách có toàn quyền quản lý dữ liệu (full access) một cách siêu tốc và bảo mật.</p><p><strong>Đăng nhập Control Panel để trải nghiệm trình quản lý file trực quan.</strong></p>', 'ftp, file manager, upload map, quản lý dữ liệu', 'Hướng dẫn sử dụng File Manager và FTP cho Server Game.', 'published', 1202, NULL, 0, NULL, 0, 1, '2026-05-14 18:51:39', '2026-05-15 06:06:00'),
(8, 1, 1, 'Thuê Server Valheim: Hỗ trợ Crossplay PC và Xbox mượt mà', 'thue-server-valheim-ho-tro-crossplay-pc-va-xbox-muot-ma', NULL, '<p>Hội bạn chơi Valheim chia làm hai phe: người dùng PC, người dùng Xbox và không thể kết nối chung một host cá nhân.</p><p>Cung cấp giải pháp máy chủ Valheim có bật sẵn tính năng Crossplay ngay khi khởi tạo. Tối ưu cấu hình để đảm bảo thế giới rộng lớn không bị giật lag khi nhiều người cùng xây dựng và thám hiểm ở các khu vực khác nhau.</p><p><strong>Bắt đầu hành trình sinh tồn RLcraft cùng bạn bè ngay.</strong></p>', 'valheim, crossplay, server valheim', 'Thuê máy chủ Valheim chất lượng cao, hỗ trợ Crossplay.', 'published', 850, NULL, 0, NULL, 0, 0, '2026-05-14 18:51:39', '2026-05-14 18:51:39'),
(9, 1, 3, 'Khi nào nên nâng cấp CPU cho Game Server?', 'khi-nao-nen-nang-cap-cpu-cho-game-server', '', '<p>Server bị delay dù RAM vẫn còn dư khiến nhiều người khó hiểu. Giải thích tình trạng nghẽn CPU khi có quá nhiều AI, plugin hoặc script xử lý thời gian thực. Nâng cấp CPU để cải thiện tốc độ phản hồi server ngay hôm nay.</p>', 'cpu server, nâng cấp cpu, game hosting', 'Dấu hiệu cần nâng cấp CPU cho server game.', 'published', 3, NULL, 0, NULL, 65, 0, '2026-05-14 19:41:23', '2026-05-14 20:07:07'),
(78, 1, 2, 'Cổng Support 24/7: Cách gửi yêu cầu hỗ trợ nhanh nhất', 'cong-support-24-7-cach-gui-yeu-cau-ho-tro-nhanh-nhat', '6a06bdd0648b1_news.jpg', '<p>Khách hàng gặp lỗi game, lỗi server nhưng không biết tìm ai, nhắn tin Fanpage thì trôi tin.</p><p>Hướng dẫn sử dụng tính năng tạo Ticket (contacts) hoặc gửi form (faq_messages) trực tiếp trên web. Cam kết thời gian phản hồi (SLA) từ đội ngũ Admin (role = admin).</p><p><strong>Lưu lại trang Liên hệ / Support để sử dụng khi cần trợ giúp kỹ thuật.</strong></p>', 'support, hỗ trợ, ticket', 'Hệ thống hỗ trợ khách hàng 24/7 chuyên nghiệp.', 'published', 626, NULL, 0, NULL, 65, 1, '2026-05-14 20:07:55', '2026-05-15 07:55:46'),
(79, 1, 1, 'Thuê Server Minecraft chuẩn E-Sports: Không lag, Không delay', 'thue-server-minecraft-chuan-e-sports-khong-lag-khong-delay', NULL, '<p>Dân cày Minecraft (đặc biệt là PvP hoặc server đông người) cực kỳ ghét TPS (Ticks Per Second) bị tụt.</p><p>Bóc tách cấu hình phần cứng của nền tảng (CPU xung nhịp cao, RAM chuẩn server). Giải thích vì sao cấu hình products của Cloud Arena đảm bảo TPS luôn ở mức 20 dù server có 50+ người online.</p><p><strong>Xem ngay bảng giá các gói Minecraft Server Premium.</strong></p>', 'minecraft, e-sports, low ping', 'Server Minecraft hiệu năng cao cho thi đấu chuyên nghiệp.', 'published', 1112, NULL, 0, NULL, 0, 1, '2026-05-14 20:07:55', '2026-05-15 06:29:16'),
(80, 1, 1, 'Cài đặt Modpack Minecraft siêu dễ tại Cloud Arena', 'cai-dat-modpack-minecraft-sieu-de-tai-cloud-arena', NULL, '<p>Tự cài Modpack rất dễ bị crash lỗi phiên bản, sai thư viện.</p><p>Khoe tính năng 1-click install. Liệt kê các Modpack đang hot được hỗ trợ (lấy từ cột JSON modpacks trong table about). Hướng dẫn cách đổi từ server Vanilla sang Modpack qua phần cài đặt.</p><p><strong>Bắt đầu hành trình sinh tồn RLcraft cùng bạn bè ngay.</strong></p>', 'modpack, minecraft mod, 1-click install', 'Hướng dẫn cài đặt Modpack Minecraft chỉ với một cú click.', 'published', 953, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-15 06:29:20'),
(81, 1, 1, 'Mở Server Palworld: Chơi cùng bạn bè cực mượt với cấu hình 16GB RAM', 'mo-server-palworld-choi-cung-ban-be-cuc-muot-voi-cau-hinh-16gb-ram', NULL, '<p>Game Palworld ngốn RAM khủng khiếp, chơi host cá nhân trên PC mạng yếu sẽ bị văng game liên tục.</p><p>Đánh giá độ \"ngốn\" tài nguyên của Palworld. Tư vấn khách hàng nên chọn gói có ram_mb từ 16GB trở lên. Hướng dẫn cách cấu hình file PalWorldSettings.ini cơ bản thông qua web.</p><p><strong>Thuê ngay Server Palworld - Online 24/24, không cần treo máy tính ở nhà.</strong></p>', 'palworld, server palworld, ram 16gb', 'Cấu hình tối ưu để mở server Palworld mượt mà.', 'published', 1401, NULL, 1, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:37:04'),
(82, 1, 1, 'Xây dựng Server GTA V Roleplay (FiveM): Cần chuẩn bị những gì?', 'xay-dung-server-gta-v-roleplay-fivem-can-chuan-bi-nhung-gi', '6a06b796b80b8_news.jpg', '<p>Người mới muốn làm admin GTA V RP nhưng mù mờ về yêu cầu hệ thống.</p><p>Tư vấn từ A-Z: Cần dung lượng ổ cứng (disk_gb) lớn để chứa xe mod, map custom; cần Database riêng; cần cấu hình CPU mạnh để xử lý hàng ngàn script. Giới thiệu gói sản phẩm chuyên dụng cho FiveM của hệ thống.</p><p><strong>Khởi tạo máy chủ FiveM Roleplay của riêng bạn.</strong></p>', 'gta v rp, fivem, roleplay', 'Hướng dẫn mở server GTA V RP với FiveM.', 'published', 1302, NULL, 0, NULL, 75, 0, '2026-05-14 20:07:55', '2026-05-15 06:29:54'),
(83, 1, 1, 'Tạo Server CS:GO/CS2 bắn giải: Ping thấp, Tickrate 128 (hoặc Sub-tick)', 'tao-server-csgo-cs2-ban-giai-ping-thap-tickrate-128', NULL, '<p>Game thủ FPS cần độ trễ mạng (Ping) cực thấp, đạn ảo là điều tối kỵ.</p><p>Nhấn mạnh vị trí đặt máy chủ (Datacenter) tối ưu cho đường truyền trong nước. Giải thích công nghệ Sub-tick của CS2 và lý do cần CPU đơn nhân mạnh để xử lý mượt mà.</p><p><strong>Đặt máy chủ CS2 để train team ngay tối nay.</strong></p>', 'cs2, csgo, tickrate 128', 'Thuê server CS2 chuyên nghiệp cho tập luyện và thi đấu.', 'published', 800, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(84, 1, 3, 'Sự khác biệt giữa CPU Core và RAM trong việc vận hành Server', 'su-khac-biet-giua-cpu-core-va-ram-trong-viec-van-hanh-server', NULL, '<p>Khách hàng không biết nên chi tiền để mua gói nhiều CPU hay nhiều RAM.</p><p>So sánh dễ hiểu: RAM là \"không gian\" (nhiều người chơi, nhiều map rộng = cần nhiều RAM), CPU là \"tốc độ xử lý\" (nhiều quái vật AI, nhiều plugin logic phức tạp = cần CPU). Tư vấn cách chọn cấu hình cân bằng.</p><p><strong>Nếu vẫn phân vân, hãy nhắn tin cho Support để được tư vấn cấu hình chuẩn.</strong></p>', 'cpu vs ram, cấu hình server, kiến thức', 'Phân biệt vai trò của CPU và RAM trong server game.', 'published', 750, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(85, 1, 3, 'Bảo mật Server Game: Tầm quan trọng của tính năng chống DDoS', 'bao-mat-server-game-tam-quan-trong-cua-tinh-nang-chong-ddos', NULL, '<p>Chủ server nơm nớp lo sợ bị đối thủ \"bắn\" DDoS sập server, mất uy tín với người chơi.</p><p>Giải thích DDoS là gì. Phân tích hệ thống Firewall Layer 4/7 mà Cloud Arena đang trang bị để bảo vệ địa chỉ IP (ip_address) của khách. Cam kết uptime (uptime trong bảng about).</p><p><strong>An tâm phát triển cộng đồng với hạ tầng Anti-DDoS chuẩn doanh nghiệp.</strong></p>', 'anti-ddos, bảo mật server, firewall', 'Giải pháp bảo mật và chống DDoS cho server game.', 'published', 1050, NULL, 1, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(86, 1, 3, 'Tại sao ổ cứng NVMe SSD là bắt buộc đối với Game Server?', 'tai-sao-o-cung-nvme-ssd-la-bat-buoc-doi-voi-game-server', NULL, '<p>Một số bên cho thuê giá rẻ dùng ổ HDD cũ khiến server load map siêu chậm.</p><p>Giải thích tốc độ đọc/ghi I/O. Lấy ví dụ thực tế: Khi người chơi bay nhanh trong Minecraft bằng Elytra, server dùng NVMe sẽ load chunk kịp thời, không bị kẹt block ảo. Khẳng định 100% server tại web bạn dùng NVMe.</p><p><strong>Trải nghiệm tốc độ load map x10 với Cloud Arena.</strong></p>', 'nvme, ssd, load map', 'Ưu điểm của ổ cứng NVMe SSD cho server game.', 'published', 890, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(87, 1, 3, 'Quản lý phiên bản Server dễ dàng với hệ thống Options', 'quan-ly-phien-ban-server-de-dang-voi-he-thong-options', NULL, '<p>Muốn đổi từ Spigot sang Paper, hoặc đổi version từ 1.16 lên 1.20 mà không biết code.</p><p>Trực quan hóa cách sử dụng giao diện Options (lưu ở user_service_options). Hướng dẫn khách hàng thao tác chọn dropdown version, hệ thống tự động tải file .jar và chạy lại server.</p><p><strong>Khám phá tính năng quản lý server trực quan.</strong></p>', 'options, server version, spigot, paper', 'Thay đổi phiên bản server game dễ dàng qua web dashboard.', 'published', 670, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(88, 1, 3, 'Hướng dẫn khôi phục dữ liệu: Đừng để mất công sức cày cuốc', 'huong-dan-khoi-phuc-du-lieu-dung-de-mat-cong-suc-cay-cuoc', NULL, '<p>Sợ bị hacker phá map hoặc admin cài nhầm plugin gây hỏng dữ liệu.</p><p>Hướng dẫn quy trình sao lưu (Backup) tự động hàng ngày của nền tảng. Chỉ cách khôi phục lại bản backup ngày hôm qua chỉ với vài thao tác trong Dashboard.</p><p><strong>Tính năng Backup luôn miễn phí cho mọi gói dịch vụ!</strong></p>', 'backup, restore, dữ liệu', 'Cách sử dụng tính năng backup và restore dữ liệu server.', 'published', 1200, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(89, 1, 5, 'Tổng hợp đánh giá từ những Server lớn đang sử dụng Cloud Arena', 'tong-hop-danh-gia-tu-nhung-server-lon-dang-su-dung-cloud-arena', NULL, '<p>User mới chưa tin tưởng vào thương hiệu, cần kiểm chứng.</p><p>Chọn lọc các bài review 5 sao từ bảng reviews. Chụp ảnh màn hình các server đông người chơi đang chạy ổn định trên hạ tầng của bạn. Phỏng vấn ngắn (nếu có) một chủ server.</p><p><strong>Bạn là khách hàng? Hãy để lại Review để nhận ngay 50 Credit thưởng!</strong></p>', 'review, đánh giá, uy tín', 'Khách hàng nói gì về dịch vụ tại Cloud Arena.', 'published', 501, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:29:05'),
(90, 1, 2, 'Giải đáp thắc mắc: Các câu hỏi thường gặp khi thuê Server lần đầu', 'giai-dap-thac-mac-cac-cau-hoi-thuong-gap-khi-thue-server-lan-dau', NULL, '<p>Khách hàng hỏi đi hỏi lại những câu giống hệt nhau.</p><p>Tập hợp top 10 câu hỏi từ bảng faqs. Cấu trúc bài viết theo dạng Hỏi - Đáp rõ ràng, ngắn gọn. Giúp tăng điểm SEO cho các từ khóa \"cách thuê server...\", \"giá thuê server...\".</p><p><strong>Xem thêm bách khoa toàn thư FAQ của chúng tôi tại đây.</strong></p>', 'faq, thắc mắc, hướng dẫn', 'Tổng hợp các câu hỏi thường gặp khi thuê server.', 'published', 2102, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-15 06:27:25'),
(91, 1, 4, 'Tổ chức sự kiện cho Server Game: Cách thu hút người chơi mới', 'to-chuc-su-kien-cho-server-game-cach-thu-hut-nguoi-choi-moi', NULL, '<p>Chủ server thuê máy chủ xong không biết cách kéo member, dẫn đến chán nản và hủy gia hạn.</p><p>Bài viết chia sẻ giá trị (Value content). Gợi ý các event trong game: Đua top, săn Boss cuối tuần, x2 Exp. Khuyên chủ server nên nâng cấp current_ram_mb tạm thời trong những ngày diễn ra event để tránh sập.</p><p><strong>Nâng cấp cấu hình để chuẩn bị cho Event lớn sắp tới!</strong></p>', 'event, marketing, member', 'Bí quyết thu hút người chơi cho server game của bạn.', 'published', 451, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:12:14'),
(92, 1, 5, 'Cập nhật hệ thống tháng này: Giao diện quản lý Server mới', 'cap-nhat-he-thong-thang-nay-giao-dien-quan-ly-server-moi', NULL, '<p>Cần cho khách hàng thấy nền tảng liên tục được phát triển và tối ưu.</p><p>Dạng tin tức Changelog. Liệt kê các lỗi (bugs) đã được fix, các tính năng mới vừa update (Giao diện web mới, game mới vào store).</p><p><strong>Đăng nhập để trải nghiệm ngay diện mạo mới.</strong></p>', 'changelog, update, giao diện mới', 'Bản cập nhật hệ thống mới nhất tại Cloud Arena.', 'published', 980, NULL, 1, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(93, 1, 4, 'Cách tìm kiếm thành viên (Tuyển Staff/Player) cho Server của bạn', 'cach-tim-kiem-thanh-vien-tuyen-staff-player-cho-server-cua-ban', NULL, '<p>Cộng đồng rời rạc.</p><p>Hướng dẫn cách viết bài PR server thu hút. Khuyến khích người dùng tham gia vào Group Facebook hoặc Discord chính thức của Cloud Arena để đăng bài quảng cáo server của họ (cross-promotion).</p><p><strong>Tham gia ngay Discord Cloud Arena để giao lưu và tuyển member.</strong></p>', 'tuyển member, staff, quảng cáo', 'Hướng dẫn tuyển staff và thành viên cho server game.', 'published', 730, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(94, 1, 4, 'Chương trình Giới thiệu (Affiliate): Mời bạn bè - Nhận Credit miễn phí', 'chuong-trinh-gioi-thieu-affiliate-moi-ban-be-nhan-credit-mien-phi', NULL, '<p>Học sinh, sinh viên muốn duy trì máy chủ lâu dài để chơi cùng clan nhưng nguồn tài chính có hạn.</p><p>Ra mắt hệ thống Affiliate. Cấp cho mỗi tài khoản một mã giới thiệu (referral_code). Khi người mới đăng ký và nạp tiền qua link này, cả hai đều nhận được phần trăm hoa hồng cộng thẳng vào số dư Credit.</p><p><strong>Lấy link giới thiệu của bạn và bắt đầu kiếm Credit miễn phí ngay!</strong></p>', 'affiliate, giới thiệu, kiếm credit, miễn phí', 'Kiếm Credit miễn phí thông qua chương trình giới thiệu bạn bè.', 'published', 1150, NULL, 1, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(95, 1, 3, 'Thêm Co-Admin quản lý Server không cần chia sẻ mật khẩu', 'them-co-admin-quan-ly-server-khong-can-chia-se-mat-khau', NULL, '<p>Chủ server muốn nhờ bạn bè cùng quản lý, reset server khi mình đi vắng nhưng lại sợ rủi ro khi gửi chung tài khoản web.</p><p>Giới thiệu tính năng Sub-Users trên hệ thống quản trị. Hướng dẫn cách phân quyền chi tiết: Chỉ cho phép bật/tắt máy chủ, hoặc chỉ cho xem Console trực tiếp mà không được quyền xóa file hay can thiệp thanh toán.</p><p><strong>Thêm bạn đồng hành vào dự án Game Server của bạn một cách an toàn.</strong></p>', 'sub-user, co-admin, quản lý server, bảo mật', 'Hướng dẫn chia sẻ quyền quản lý server game an toàn bằng Sub-user.', 'published', 620, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(96, 1, 3, 'Cách tạo IP chữ (Subdomain) cho Server Game chuyên nghiệp', 'cach-tao-ip-chu-subdomain-cho-server-game-chuyen-nghiep', NULL, '<p>Địa chỉ IP số đi kèm Port (ví dụ: 103.19.xxx.xxx:25565) quá dài và khó nhớ khiến người chơi mới dễ nhập sai khi vào game.</p><p>Hướng dẫn sử dụng tính năng tạo Subdomain tùy chỉnh miễn phí (ví dụ: play.ten-server.cloudarena.vn) ngay trên Panel. Phân tích lợi ích giúp server trông uy tín hơn và dễ dàng xây dựng thương hiệu cộng đồng mạng.</p><p><strong>Tạo ngay một tên miền cực ngầu cho Server của bạn tại Dashboard.</strong></p>', 'subdomain, ip chữ, dns, tên miền server', 'Hướng dẫn tạo Subdomain (IP chữ) miễn phí cho game server.', 'published', 940, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(97, 1, 1, 'Máy chủ ARK: Survival Ascended - Đỉnh cao đồ họa, Cấu hình khủng', 'may-chu-ark-survival-ascended-dinh-cao-do-hoa-cau-hinh-khung', NULL, '<p>ARK: Survival Ascended (ASA) sử dụng Unreal Engine 5 yêu cầu tài nguyên phần cứng cực kỳ khắt khe, các gói server giá rẻ thông thường không thể chạy nổi.</p><p>Phân tích cấu hình đặc thù dành riêng cho ASA tại Cloud Arena: CPU đa luồng thế hệ mới và tối thiểu 16GB RAM. Tối ưu hóa file config hệ thống để tránh tình trạng crash do tràn bộ nhớ (Out of Memory).</p><p><strong>Khám phá thế giới khủng long đồ họa siêu thực với hiệu năng vô song.</strong></p>', 'ark ascended, asa, server ark, cấu hình khủng', 'Thuê server ARK: Survival Ascended cấu hình cao, không lag.', 'published', 1050, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(98, 1, 4, 'Tại sao nên thuê VPS/Game Server thay vì treo máy tại nhà?', 'tai-sao-nen-thue-vps-game-server-thay-vi-treo-may-tai-nha', NULL, '<p>Nhiều người nghĩ tự mở server trên PC cá nhân sẽ tiết kiệm, nhưng lại vật lộn với việc mở Port (Port Forwarding) trong modem, IP động rớt mạng và tiền điện tăng vọt.</p><p>So sánh chi phí tiền điện hàng tháng so với gói cước thuê server. Nêu bật rủi ro hỏng hóc linh kiện PC do chạy 24/24, nguy cơ lộ IP thật dẫn đến bị mạng botnet tấn công. Thuê server là giải pháp an toàn, mở liền tay chơi ngay.</p><p><strong>Chỉ từ một ly trà sữa, sở hữu ngay Server online 24/24.</strong></p>', 'tự host, port forwarding, thuê vps, tiền điện', 'Những lý do nên thuê server game thay vì tự host trên PC cá nhân.', 'published', 1350, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(99, 1, 1, 'Cài đặt GeyserMC: Kết nối người chơi Minecraft Java và Bedrock', 'cai-dat-geysermc-ket-noi-nguoi-choi-minecraft-java-va-bedrock', NULL, '<p>Bạn bè chơi Minecraft trên điện thoại (Bedrock Edition) không thể vào server chung với những người chơi trên PC (Java Edition).</p><p>Hướng dẫn tích hợp plugin GeyserMC và Floodgate trên server Spigot/Paper. Chỉ cách cấu hình cấp thêm port phụ (additional_ports) trên hệ thống để cho phép cả hai nền tảng cùng sinh tồn trong một thế giới duy nhất một cách trơn tru.</p><p><strong>Mở rộng cộng đồng Server của bạn không giới hạn nền tảng thiết bị.</strong></p>', 'geysermc, crossplay minecraft, java bedrock', 'Hướng dẫn cài đặt GeyserMC để chơi chung Minecraft PC và Điện thoại.', 'published', 880, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(100, 1, 1, 'Top 5 Plugin Minecraft bắt buộc phải có cho Server Survival', 'top-5-plugin-minecraft-bat-buoc-phai-co-cho-server-survival', NULL, '<p>Server Survival mới mở thường thiếu tính năng quản lý và bảo vệ người chơi.</p><p>Giới thiệu các plugin phổ biến như EssentialsX, LuckPerms, WorldEdit, CoreProtect và Vault. Phân tích lợi ích của từng plugin trong việc quản lý kinh tế, chống grief và tối ưu trải nghiệm người chơi.</p><p><strong>Cài đặt plugin chỉ với vài cú click trên Dashboard Cloud Arena.</strong></p>', 'minecraft plugin, survival server, essentialsx', 'Danh sách plugin Minecraft cần thiết cho server survival.', 'published', 1450, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(101, 1, 3, 'Server Rust cần bao nhiêu RAM để chạy ổn định?', 'server-rust-can-bao-nhieu-ram-de-chay-on-dinh', NULL, '<p>Nhiều khách hàng thuê server Rust nhưng chọn cấu hình quá yếu khiến wipe map bị lag.</p><p>Phân tích mức sử dụng RAM theo số lượng người chơi và plugin Oxide/uMod. Đề xuất cấu hình tối thiểu 8GB RAM cho server nhỏ và 16GB+ cho cộng đồng đông người.</p><p><strong>Khởi tạo Rust Server tối ưu hiệu năng ngay hôm nay.</strong></p>', 'rust server, ram rust, oxide', 'Tư vấn cấu hình RAM phù hợp cho server Rust.', 'published', 790, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(102, 1, 3, 'Hướng dẫn đổi Map Server Minecraft không mất dữ liệu', 'huong-dan-doi-map-server-minecraft-khong-mat-du-lieu', NULL, '<p>Nhiều admin muốn reset map mới nhưng sợ mất world cũ và dữ liệu người chơi.</p><p>Hướng dẫn cách backup world hiện tại, upload map mới và chuyển đổi trực tiếp trong trình quản lý file của Cloud Arena.</p><p><strong>Tạo thế giới mới cho cộng đồng của bạn chỉ trong vài phút.</strong></p>', 'minecraft map, đổi world, backup world', 'Cách đổi map Minecraft server an toàn.', 'published', 640, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(103, 1, 3, 'Tại sao Server bị Crash khi có quá nhiều Plugin?', 'tai-sao-server-bi-crash-khi-co-qua-nhieu-plugin', NULL, '<p>Nhiều chủ server cài plugin tràn lan khiến TPS tụt và server crash liên tục.</p><p>Giải thích hiện tượng xung đột plugin, leak RAM và plugin lỗi thời. Hướng dẫn kiểm tra logs để xác định plugin gây lỗi.</p><p><strong>Dọn dẹp plugin không cần thiết để tối ưu hiệu năng server ngay.</strong></p>', 'plugin lỗi, crash server, tối ưu plugin', 'Nguyên nhân server crash do plugin và cách xử lý.', 'published', 1120, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(104, 1, 3, 'Cloud Backup hoạt động như thế nào?', 'cloud-backup-hoat-dong-nhu-the-nao', NULL, '<p>Mất dữ liệu là ác mộng lớn nhất với mọi chủ server game.</p><p>Giải thích cơ chế backup định kỳ lên Cloud Storage riêng biệt. Mọi dữ liệu world, plugin và database đều được lưu tự động mỗi ngày.</p><p><strong>Kích hoạt backup tự động để bảo vệ thành quả của cộng đồng bạn.</strong></p>', 'backup cloud, restore server, lưu dữ liệu', 'Tìm hiểu hệ thống cloud backup tại Cloud Arena.', 'published', 560, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(105, 1, 4, 'Hướng dẫn gia hạn Server trước khi hết hạn', 'huong-dan-gia-han-server-truoc-khi-het-han', NULL, '<p>Nhiều khách quên gia hạn khiến server bị tạm ngưng và người chơi bỏ đi.</p><p>Hướng dẫn kiểm tra ngày hết hạn (expired_at), sử dụng Credit để gia hạn nhanh chóng chỉ với vài thao tác.</p><p><strong>Gia hạn ngay để tránh gián đoạn cộng đồng của bạn.</strong></p>', 'gia hạn server, expired, credit', 'Cách gia hạn server game nhanh chóng.', 'published', 890, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(106, 1, 3, 'Panel quản lý Server Game có những tính năng gì?', 'panel-quan-ly-server-game-co-nhung-tinh-nang-gi', NULL, '<p>Nhiều người mới chưa biết cách quản lý server thông qua web panel.</p><p>Giới thiệu các tính năng như Start/Stop/Restart, Console realtime, File Manager, Backup và Monitoring tài nguyên.</p><p><strong>Trải nghiệm Dashboard quản lý server hiện đại ngay hôm nay.</strong></p>', 'dashboard server, panel hosting, console', 'Khám phá tính năng panel quản lý server.', 'published', 920, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(107, 1, 4, 'Có nên mở Server riêng để chơi cùng bạn bè?', 'co-nen-mo-server-rieng-de-choi-cung-ban-be', NULL, '<p>Nhiều nhóm bạn phân vân giữa host LAN và thuê server riêng.</p><p>Phân tích ưu điểm của server online 24/7: không phụ thuộc máy chủ cá nhân, ping ổn định và dễ mở rộng cộng đồng.</p><p><strong>Tạo server riêng để chơi mọi lúc cùng bạn bè.</strong></p>', 'server riêng, multiplayer, hosting game', 'Lý do nên thuê server game riêng.', 'published', 780, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(108, 1, 3, 'Tối ưu TPS Minecraft: Những điều Admin cần biết', 'toi-uu-tps-minecraft-nhung-dieu-admin-can-biet', NULL, '<p>TPS thấp là nguyên nhân khiến gameplay Minecraft bị giật lag nghiêm trọng.</p><p>Hướng dẫn giảm entity, tối ưu chunk loading và sử dụng Paper thay cho Spigot để cải thiện hiệu suất.</p><p><strong>Áp dụng ngay các mẹo tối ưu TPS cho server của bạn.</strong></p>', 'tps minecraft, optimize server, papermc', 'Hướng dẫn tối ưu TPS cho Minecraft server.', 'published', 1250, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(109, 1, 5, 'Cloud Arena cam kết Uptime 99.9% như thế nào?', 'cloud-arena-cam-ket-uptime-99-9-nhu-the-nao', NULL, '<p>Khách hàng lo ngại server downtime làm mất người chơi.</p><p>Giải thích hệ thống điện dự phòng, mạng redundancy và monitoring 24/7 giúp duy trì uptime ổn định.</p><p><strong>An tâm vận hành server với hạ tầng chuẩn doanh nghiệp.</strong></p>', 'uptime, monitoring, datacenter', 'Cam kết uptime và độ ổn định tại Cloud Arena.', 'published', 630, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(110, 1, 3, 'Hướng dẫn sử dụng Console để quản lý Server Game', 'huong-dan-su-dung-console-de-quan-ly-server-game', NULL, '<p>Nhiều admin mới chưa biết sử dụng console để thao tác nhanh trên server.</p><p>Giới thiệu các lệnh cơ bản như stop, save-all, whitelist và op trong Minecraft hoặc các lệnh quản trị phổ biến ở game khác.</p><p><strong>Làm chủ server của bạn với hệ thống Console realtime.</strong></p>', 'console server, lệnh admin, minecraft op', 'Hướng dẫn sử dụng console quản trị server.', 'published', 710, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55'),
(111, 1, 3, 'Top lỗi phổ biến khi tự host Server tại nhà', 'top-loi-pho-bien-khi-tu-host-server-tai-nha', NULL, '<p>Nhiều game thủ tự mở server bằng máy tính cá nhân nhưng gặp vô số vấn đề.</p><p>Liệt kê các lỗi phổ biến như mất điện, mạng yếu, NAT Port, IP động và quá nhiệt phần cứng.</p><p><strong>Chuyển sang hạ tầng chuyên nghiệp để vận hành server ổn định hơn.</strong></p>', 'self-host, mở port, server tại nhà', 'Những rủi ro khi tự host game server tại nhà.', 'published', 1020, NULL, 0, NULL, 0, 0, '2026-05-14 20:07:55', '2026-05-14 20:07:55');
-- Ads (Khoa Branch)
INSERT INTO ads (title, image_url, link_url, position, status) VALUES
('Khuyến mãi Game Server - Giảm 50%', '/public/uploads/ads/promo_banner.jpg', 'https://cloudarena.vn/products', 'sticky-sidebar', 'active'),
('Nạp Credit nhận thêm 20%', '/public/uploads/ads/credit_promo.jpg', 'https://cloudarena.vn/billing', 'sticky-sidebar', 'active');

-- 10. Reviews (Khoa Branch)
INSERT INTO reviews (user_id, product_id, news_id, rating, comment, status) VALUES
(2, 1, NULL, 5, 'Dịch vụ rất tốt, server mượt mà!', 'approved'),
(4, 2, NULL, 4, 'Chất lượng ổn định, hỗ trợ nhiệt tình.', 'approved'),
(5, 1, NULL, 5, 'Rất hài lòng với Cloud Arena!', 'approved'),
(3, NULL, 1, NULL, 'Nền tảng tuyệt vời cho cộng đồng!', 'approved'),
(5, NULL, 1, NULL, 'Đã dùng và thấy rất ổn định.', 'approved');

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
('admin_notification_state_1', '{\"last_opened_id\":3861,\"last_opened_at\":\"2026-05-15 13:37:35\"}'),
('contact_cat_desc_banned', 'Khiếu nại khóa / blacklist'),
('contact_cat_desc_billing_payment', 'Hóa đơn, thanh toán, hoàn tiền'),
('contact_cat_desc_bugs_technical', 'Lỗi kỹ thuật & máy chủ'),
('contact_cat_desc_forgot_password', 'Khôi phục truy cập tài khoản'),
('contact_cat_desc_others', 'Các vấn đề khác'),
('contact_cat_desc_purchase_issue', 'Chọn đơn pending trong form'),
('contact_discord_invite_url', ''),
('contact_discord_typed_block', '> relay / discord #support-hq … CONNECTED\r\n> channel latency … 42ms\r\n> join Discord #support-hq _'),
('contact_form_banned_user_lbl', 'Username'),
('contact_form_banned_user_ph', 'Tên đăng nhập cần hỗ trợ'),
('contact_form_forgot_pw_lbl', 'Mật khẩu trước đó (tuỳ chọn)'),
('contact_form_forgot_pw_ph', ''),
('contact_form_purchase_empty', 'Không có đơn pending.'),
('contact_form_purchase_guest', 'Đăng nhập để chọn đơn hàng chờ xử lý.'),
('contact_form_purchase_opt', '— Chọn đơn —'),
('contact_form_purchase_order_lbl', 'Đơn hàng (pending)'),
('contact_gate_cta_body', 'Mở Support Terminal để chọn loại ticket, đính kèm thông tin đơn hàng hoặc bằng chứng kỹ thuật, và gửi yêu cầu được mã hóa an toàn.'),
('contact_gate_cta_button', 'Tạo Ticket'),
('contact_gate_headline', 'Trung tâm'),
('contact_gate_headline_accent', 'hỗ trợ'),
('contact_gate_subtitle', 'Kết nối trực tiếp tới đội kỹ sư Cloud Arena.'),
('contact_main_back', '← Quay lại'),
('contact_main_btn_reset', 'Reset'),
('contact_main_btn_send', 'Gửi Ticket'),
('contact_main_cat_heading', 'Chọn danh mục ticket'),
('contact_main_email_label', 'Email'),
('contact_main_issue_hint', 'Chọn nhanh bằng các thẻ danh mục phía dưới trang (đồng bộ với ô ẩn).'),
('contact_main_issue_label', 'Loại vấn đề'),
('contact_main_msg_label', 'Nội dung'),
('contact_main_msg_placeholder', '> Mô tả chi tiết lỗi, bước tái hiện, mã đơn (nếu có)…'),
('contact_main_name_label', 'Tên'),
('contact_main_stat_lbl_1', 'Avg response'),
('contact_main_stat_lbl_2', 'Active engineers'),
('contact_main_stat_lbl_3', 'Nodes healthy'),
('contact_main_stat_val_1', '~3m'),
('contact_main_status_online', 'Support online'),
('contact_main_status_title', 'Trạng thái hệ thống hỗ trợ'),
('contact_main_term_title', 'Support Terminal'),
('contact_main_topo_title', 'Network topology'),
('contact_node_card_title', 'Support Node VN-01'),
('contact_node_latency_label', 'Latency'),
('contact_node_online_label', 'Online'),
('contact_node_region', 'Ho Chi Minh City'),
('contact_page_intro', 'Gửi ticket hỗ trợ cho chúng tôi. Đội ngũ sẽ phản hồi sớm nhất có thể.'),
('contact_page_title', 'Liên Hệ'),
('contact_sidebar_title', 'Thông tin liên hệ'),
('contact_ticket_meta_1_2', '{\"priority\":\"high\",\"admin_reply\":\"ok\",\"replied_at\":\"2026-05-15 13:38:15\",\"previous_password_bcrypt\":null}'),
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
('home_hero_bg_image', 'hero_bg_db8ea773262e13220e3e0d25aaafa298.gif'),
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
('site_logo_image', 'brand_f1ce6b8c910f51a2b1f111a7b8cd92c7.png'),
('site_logo_text', 'G-SERVER'),
('site_map_embed_url', 'https://www.google.com/maps?q=268+Ly+Thuong+Kiet+Q10+TPHCM&output=embed')
ON DUPLICATE KEY UPDATE value = VALUES(value);

SET FOREIGN_KEY_CHECKS = 1;