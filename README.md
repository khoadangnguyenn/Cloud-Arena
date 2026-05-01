<div align="center">

# 🎮 Cloud-Arena
**Game Server Rental Platform**

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4.svg?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1.svg?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-Styling-38B2AC.svg?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-Logic-F7DF1E.svg?style=flat-square&logo=javascript&logoColor=black)](https://developer.mozilla.org/)

*Nền tảng cho thuê và quản lý máy chủ game tối ưu, vận hành trên kiến trúc Custom MVC.*

</div>

<br>

## 📌 Overview

**G-SERVER** là một nền tảng website chuyên cung cấp dịch vụ cho thuê máy chủ game. Dự án được xây dựng từ con số không (from scratch) sử dụng kiến trúc **Model-View-Controller (MVC)** tùy chỉnh, giúp tối ưu hóa hiệu suất, dễ dàng bảo trì và phân tách rõ ràng giữa logic nghiệp vụ và giao diện người dùng.

---

## 🏗️ MVC Architecture

Hệ thống hoạt động dựa trên mô hình MVC tự xây dựng với luồng xử lý chặt chẽ:

### 1. Cấu trúc thư mục cốt lõi
- 📁 **/app**: Chứa toàn bộ logic nghiệp vụ (Controller, Model, Core). Thư mục này được bảo mật và **không thể truy cập trực tiếp từ URL**.
- 📁 **/public**: Nơi lưu trữ tài nguyên tĩnh (CSS, JS, Images) và file `index.php` đóng vai trò là Entry Point của toàn bộ ứng dụng.
- ⚙️ **Core Classes**:
    - `App.php`: Hệ thống Routing URL thông minh, tự động điều hướng request.
    - `Controller.php`: Lớp cơ sở (Base Controller) cung cấp các phương thức dùng chung như `view()` và `model()`.
    - `Database.php`: Wrapper sử dụng PDO, xử lý kết nối CSDL và chống SQL Injection.

### 2. Workflow
*Pipeline cơ bản:* **Client truy cập ➔ Route (index.php) ➔ Controller ➔ Model (CSDL) ➔ View (Giao diện HTML)**

1. **Request**: Trình duyệt gọi URL ➔ File `.htaccess` điều hướng tất cả về `public/index.php`.
2. **Route**: `App.php` phân tích URL để xác định Controller và Action tương ứng.
3. **Action**: Controller tiếp nhận yêu cầu, xử lý logic và gọi Model để lấy/cập nhật dữ liệu.
4. **Response**: Controller đóng gói dữ liệu, truyền vào View và hiển thị kết quả HTML cuối cùng cho người dùng.

---

## 🗄️ Database

Dự án sử dụng **MySQL** làm hệ quản trị cơ sở dữ liệu. Dưới đây là các bảng (tables) cấu thành nên hệ thống:

- 👤 `users`: Quản lý thông tin tài khoản, mật khẩu và phân quyền hệ thống (Admin / Member).
- 📦 `products`: Danh mục các gói dịch vụ server (Cấu hình RAM, CPU, Mức giá).
- 📰 `news`: Lưu trữ hệ thống tin tức, thông báo và các bài viết hướng dẫn.
- 💬 `contacts`: Quản lý các tin nhắn, phản hồi hỗ trợ từ khách hàng.
- 🖥️ `user_servers` *(Mở rộng)*: Quản lý chi tiết các máy chủ thực tế mà khách đã thuê (Theo dõi Status: *Running / Stopped*).
- 💳 `orders`: Ghi nhận lịch sử giao dịch, đơn hàng và trạng thái thanh toán.

---

## 📂 System Architecture

---
```
Cloud-Arena/
├── app/                        # Chứa toàn bộ logic backend của ứng dụng
│   ├── config/
│   │   └── config.php          # Chứa cấu hình Database và các hằng số (URLROOT, APPROOT)
│   ├── core/
│   │   ├── App.php             # Router: Phân tích URL và gọi Controller tương ứng
│   │   ├── Controller.php      # Controller gốc: Chứa hàm gọi Model và View
│   │   └── Database.php        # Wrapper PDO kết nối và truy vấn MySQL
│   ├── controllers/            # Xử lý logic và nhận Request từ người dùng
│   │   ├── Pages.php           # Controller cho trang chủ, giới thiệu,...
│   │   ├── Products.php        # Controller hiển thị sản phẩm cho khách
│   │   ├── Cart.php            # Controller quản lý giỏ hàng
│   │   ├── Users.php           # Controller Đăng ký / Đăng nhập
│   │   ├── Admin.php           # Controller cho Dashboard Admin
│   │   └── AdminProducts.php   # Controller quản lý sản phẩm trong Admin
│   ├── models/                 # Chứa các class giao tiếp với Database
│   │   ├── User.php
│   │   └── Product.php
│   └── views/                  # Chứa file HTML/PHP để hiển thị giao diện
│       ├── admin/              # Giao diện cho Admin (sử dụng template Srtdash)
│       ├── client/             # Giao diện cho Khách/Thành viên (sử dụng Tailwind CSS)
│       └── layouts/            # Chứa Header và Footer chung
├── public/                     # Thư mục gốc công khai ra ngoài internet
│   ├── index.php               # Front-controller (Nơi đón mọi request)
│   ├── .htaccess               # Chuyển hướng URL đẹp
│   ├── css/                    # Custom CSS
│   ├── js/                     # Custom Javascript
│   ├── uploads/                # Nơi lưu trữ hình ảnh tải lên
│   └── admin_assets/           # Chứa các file CSS/JS của template Srtdash
└── database.sql                # File SQL cấu trúc các bảng
```

## 🚀 Getting Started
1. Giải nén vào `xampp/htdocs`.
2. Tạo database `game_server_db` trên phpmyadmin và import `database.sql`.
3. Truy cập: `http://localhost/Game%20Server%20Rental%20Platform/public/`.

