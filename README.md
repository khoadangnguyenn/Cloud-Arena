<div align="center">

# 🎮 CLOUD-ARENA
**Game Server Rental Platform**

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4.svg?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1.svg?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-Styling-38B2AC.svg?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-Logic-F7DF1E.svg?style=flat-square&logo=javascript&logoColor=black)](https://developer.mozilla.org/)

*Nền tảng cho thuê và quản lý máy chủ game tối ưu.*

</div>

<br>

## 📌 Overview

**CLOUD-ARENA** là một nền tảng website chuyên nghiệp cung cấp dịch vụ cho thuê máy chủ game. Dự án được xây dựng trên kiến trúc **Model-View-Controller (MVC)** tùy chỉnh, tích hợp giao diện hiện đại (Dark Mode), hệ thống tin tức, giỏ hàng và bảng điều khiển quản trị (Admin Dashboard) mạnh mẽ.

---

## 🏗️ MVC Architecture

Hệ thống hoạt động dựa trên mô hình MVC với luồng xử lý chặt chẽ:

### 1. Cấu trúc thư mục cốt lõi
- 📁 **/app**: Chứa toàn bộ logic nghiệp vụ (Controller, Model, Core). Thư mục này được bảo mật và **không thể truy cập trực tiếp từ URL**.
- 📁 **/public**: Nơi lưu trữ tài nguyên tĩnh (CSS, JS, Images) và file `index.php` đóng vai trò là Entry Point của toàn bộ ứng dụng.
- ⚙️ **Core Classes**:
    - `App.php`: Hệ thống Routing URL thông minh, tự động điều hướng request.
    - `Controller.php`: Lớp cơ sở (Base Controller) cung cấp các phương thức dùng chung như `view()` và `model()`.
    - `Database.php`: Wrapper sử dụng PDO, xử lý kết nối CSDL và chống SQL Injection.

### 2. Workflow
*Pipeline:* **Client truy cập ➔ Route (index.php) ➔ Controller ➔ Model (CSDL) ➔ View (Giao diện HTML)**

1. **Request**: Trình duyệt gọi URL ➔ File `.htaccess` điều hướng tất cả về `public/index.php`.
2. **Route**: `App.php` phân tích URL để xác định Controller và Action tương ứng.
3. **Action**: Controller tiếp nhận yêu cầu, xử lý logic và gọi Model để lấy/cập nhật dữ liệu.
4. **Response**: Controller đóng gói dữ liệu, truyền vào View và hiển thị kết quả HTML cuối cùng cho người dùng.

---

## 🛠️ Getting Started (Hướng dẫn chạy dự án)

### 1. Yêu cầu hệ thống
- **XAMPP**, WampServer hoặc MAMP.
- PHP version >= **8.2**.
- MySQL Server.
- Apache với module `mod_rewrite` được bật.

### 2. Các bước cài đặt

1. **Copy mã nguồn**:
   - Giải nén và copy thư mục `Game Server Rental Platform` vào thư mục `htdocs` của XAMPP.

2. **Cấu hình Database**:
   - Truy cập `http://localhost/phpmyadmin`.
   - Tạo cơ sở dữ liệu mới tên là `cloud_arena`.
   - Import tệp `database.sql` (nằm ở thư mục gốc) vào CSDL vừa tạo.

3. **Cấu hình ứng dụng**:
   - Mở tệp `app/config/config.php` và cập nhật thông số:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'cloud_arena');
     define('URLROOT', 'http://localhost/Game Server Rental Platform');
     ```

### 3. Cách truy cập
- **Client**: `http://localhost/Game Server Rental Platform/` (Tự động redirect vào thư mục `public`).
- **Admin**: `http://localhost/Game Server Rental Platform/admin` (Mặc định: admin / 123456).

---

## 🗄️ Database Schema

Dự án sử dụng **MySQL** với các bảng chính:
- 👤 `users`: Quản lý tài khoản và phân quyền (Admin/Member).
- 📦 `products`: Gói dịch vụ server (RAM, CPU, Giá).
- 📰 `news`: Hệ thống tin tức và bài viết.
- 💬 `contacts`: Tin nhắn hỗ trợ từ khách hàng.
- 💳 `orders`: Lịch sử giao dịch và trạng thái thanh toán.

---

## 📂 System Architecture & Team Tasks

```
Cloud-Arena/
├── app/                        # Chứa toàn bộ logic backend của ứng dụng
│   ├── config/
│   │   └── config.php          # Cấu hình Database và hằng số (Cả nhóm)
│   ├── core/                   # Thư mục core (Cả nhóm/Leader)
│   │   ├── App.php             # Router: Phân tích URL
│   │   ├── Controller.php      # Controller gốc (Load Model/View)
│   │   └── Database.php        # Wrapper PDO kết nối MySQL
│   ├── helpers/                # Các hàm tiện ích dùng chung
│   │   ├── Pagination.php      # Xử lý phân trang (#2)
│   │   └── Upload.php          # Xử lý upload hình ảnh (#2)
│   ├── controllers/            # Xử lý logic và nhận Request
│   │   ├── Pages.php           # Trang chủ, Liên hệ (#1) | Giới thiệu, Hỏi đáp (#2)
│   │   ├── Products.php        # Hiển thị danh sách & chi tiết sản phẩm (#3)
│   │   ├── Cart.php            # Quản lý giỏ hàng và thanh toán (#3)
│   │   ├── News.php            # Hiển thị danh sách & đọc tin tức (#4)
│   │   ├── Users.php           # Đăng ký / Đăng nhập (Cả nhóm)
│   │   ├── Admin.php           # Dashboard & Cài đặt hệ thống (#1)
│   │   ├── AdminProducts.php   # Quản lý sản phẩm CRUD (#3)
│   │   ├── AdminOrders.php     # Quản lý đơn hàng & trạng thái (#3)
│   │   ├── AdminNews.php       # Quản lý bài viết tin tức CRUD (#4)
│   │   ├── AdminFaqs.php       # Quản lý bộ câu hỏi Hỏi/đáp (#2)
│   │   ├── AdminContacts.php   # Quản lý tin nhắn liên hệ từ khách (#1)
│   │   └── AdminAbout.php      # Quản lý nội dung trang Giới thiệu (#2)
│   ├── models/                 # Chứa các class giao tiếp với Database
│   │   ├── User.php            # Model Thành viên (Cả nhóm)
│   │   ├── Product.php         # Model Sản phẩm (#3)
│   │   ├── Order.php           # Model Đơn hàng (#3)
│   │   ├── NewsModel.php       # Model Bài viết (#4)
│   │   ├── Contact.php         # Model Liên hệ (#1)
│   │   └── ...
│   └── views/                  # Chứa file giao diện HTML/PHP
│       ├── admin/              # GIAO DIỆN ADMIN (SrtDash)
│       └── client/             # GIAO DIỆN KHÁCH (Tailwind CSS - Premium Dark Theme)
├── public/                     # Thư mục công khai (Entry Point)
│   ├── .htaccess               # Cấu hình URL đẹp & Bảo mật
│   └── assets/                 # CSS, JS, Images, Uploads
└── database.sql                # Cấu trúc CSDL gốc
```

---

## 📝 License
Dự án được phát triển bởi nhóm sinh viên cho môn học **Lập trình Web**.
- #1: Giang | #2: Bảo | #3: Thiện | #4: Khoa
