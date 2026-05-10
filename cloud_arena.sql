CREATE DATABASE IF NOT EXISTS cloud_arena;
USE cloud_arena;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS admin_notifications;
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
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL COMMENT 'Lưu trữ mật khẩu đã được hash (mã hóa)',
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) COMMENT 'Thuộc tính phức hợp (Họ và Tên)',
    avatar VARCHAR(255),
    status ENUM('active', 'banned') DEFAULT 'active',
    reset_token VARCHAR(255) NULL,
    role ENUM('admin', 'member') NOT NULL COMMENT 'Xử lý phân cấp Disjoint (Admin hoặc Member)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
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

-- ==========================================
-- 4. NHÓM NỘI DUNG, QUẢN TRỊ (THỰC THỂ MẠNH)
-- ==========================================

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
    status ENUM('active', 'hidden') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE about (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    admin_id INT NULL COMMENT 'Khóa ngoại trỏ về Admin đã cập nhật nội dung',
    uptime VARCHAR(50) DEFAULT NULL COMMENT 'Ví dụ: 99.9%',
    support VARCHAR(255) DEFAULT NULL COMMENT 'Ví dụ: 24/7',
    performance VARCHAR(255) DEFAULT NULL COMMENT 'Thông tin hiệu năng',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE settings (
    key_name VARCHAR(100) PRIMARY KEY,
    value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- 5. CÁC THỰC THỂ YẾU (WEAK ENTITIES)
-- ==========================================
-- Lưu ý kỹ thuật cho Thực thể yếu: Khóa chính là cụm (parent_id, partial_id).
-- Khi thêm dữ liệu bằng PHP, partial_id sẽ được tính bằng MAX(partial_id) + 1 của parent đó.

CREATE TABLE news (
    admin_id INT NOT NULL COMMENT 'Thực thể cha: Admin',
    news_id INT NOT NULL COMMENT 'Partial Key',
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT NOT NULL,
    meta_keywords VARCHAR(255),
    meta_description TEXT,
    status ENUM('published', 'draft') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (admin_id, news_id),
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
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

CREATE TABLE admin_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('ticket', 'revenue') NOT NULL,
    source_key VARCHAR(120) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    url VARCHAR(255),
    payload JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_admin_notifications_source_key (source_key),
    INDEX idx_admin_notifications_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reviews (
    user_id INT NOT NULL COMMENT 'Thực thể cha: Member',
    review_id INT NOT NULL COMMENT 'Partial Key',
    
    product_id INT NULL COMMENT 'XOR: Nhắm tới Sản phẩm',
    target_admin_id INT NULL COMMENT 'XOR: Nhắm tới cụm khóa của News (admin_id)',
    target_news_id INT NULL COMMENT 'XOR: Nhắm tới cụm khóa của News (news_id)',
    
    rating INT DEFAULT 5,
    comment TEXT NOT NULL,
    status ENUM('pending', 'approved', 'hidden') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (user_id, review_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (target_admin_id, target_news_id) REFERENCES news(admin_id, news_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bật lại kiểm tra khóa ngoại sau khi import xong
SET FOREIGN_KEY_CHECKS = 1;

-- ==========================================
-- SEED DATA (TEST DATA)
-- ==========================================

-- Insert test admin user
-- Username: admin | Password: admin123
INSERT INTO users (username, password, email, full_name, role, status) VALUES
('admin', '$2y$10$yq.ZiVb5q2H7mU5qK9j3wulpTCOSvPQiN0Yqqrx6fIEW4I.OxdBFy', 'admin@cloudarena.local', 'Administrator', 'admin', 'active'),
('testuser', '$2y$10$yq.ZiVb5q2H7mU5qK9j3wulpTCOSvPQiN0Yqqrx6fIEW4I.OxdBFy', 'test@cloudarena.local', 'Test User', 'member', 'active');

-- Insert test categories
INSERT INTO categories (name, slug, description) VALUES
('Gaming Server', 'gaming-server', 'Máy chủ chuyên biệt cho game'),
('Web Hosting', 'web-hosting', 'Hosting cho website và ứng dụng web'),
('Shared Hosting', 'shared-hosting', 'Hosting chia sẻ với giá cạnh tranh');

-- Insert test products
INSERT INTO products (category_id, name, slug, description, price, ram_mb, cpu_cores, disk_gb, status) VALUES
(1, 'Gaming Pro 4GB', 'gaming-pro-4gb', 'Server gaming với 4GB RAM, lý tưởng cho game nhỏ', 500000, 4096, 4, 50, 'active'),
(1, 'Gaming Pro 8GB', 'gaming-pro-8gb', 'Server gaming với 8GB RAM, hỗ trợ game lớn', 1000000, 8192, 8, 100, 'active'),
(1, 'Gaming Pro 16GB', 'gaming-pro-16gb', 'Server gaming cao cấp với 16GB RAM', 2000000, 16384, 16, 200, 'active'),
(2, 'Web Basic', 'web-basic', 'Hosting web cơ bản', 250000, 2048, 2, 20, 'active'),
(2, 'Web Plus', 'web-plus', 'Hosting web nâng cao', 750000, 4096, 4, 50, 'active');