<div align="center">

# 🎮 CLOUD-ARENA
**Game Server Rental Platform**

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4.svg?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1.svg?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-Styling-38B2AC.svg?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-Logic-F7DF1E.svg?style=flat-square&logo=javascript&logoColor=black)](https://developer.mozilla.org/)

*Nền tảng cho thuê và quản lý máy chủ game tối ưu.*

</div>

<br>

## 📌 Overview

**CLOUD-ARENA** là một nền tảng website chuyên cung cấp dịch vụ cho thuê máy chủ game. Dự án sử dụng kiến trúc Model-View-Controller tùy chỉnh, giúp tối ưu hóa hiệu suất, dễ dàng bảo trì và phân tách rõ ràng giữa logic nghiệp vụ và giao diện người dùng.

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

## 🗄️ Database

Dự án sử dụng **MySQL** làm hệ quản trị cơ sở dữ liệu. Dưới đây là các tables cấu thành nên hệ thống:

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
#1: Giang
#2: Bảo
#3: Thiện
#4: Khoa

Cloud-Arena/
├── app/                        # Chứa toàn bộ logic backend của ứng dụng
│   ├── config/
│   │   └── config.php          # Cấu hình Database và hằng số (Cả nhóm)
│   ├── core/                   # Thư mục nhân hệ thống (Cả nhóm/Leader)
│   │   ├── App.php             # Router: Phân tích URL
│   │   ├── Controller.php      # Controller gốc (Load Model/View)
│   │   └── Database.php        # Wrapper PDO kết nối MySQL
│   ├── helpers/                # [MỚI] Các hàm tiện ích dùng chung
│   │   ├── Pagination.php      # Xử lý phân trang (#2)
│   │   └── Upload.php          # Xử lý upload hình ảnh (#2)
│   ├── controllers/            # Xử lý logic và nhận Request
│   │   ├── Pages.php           # Trang chủ, Liên hệ (#1) | Giới thiệu, Hỏi đáp (#2)
│   │   ├── Products.php        # Hiển thị danh sách & chi tiết sản phẩm (#3)
│   │   ├── Cart.php            # Quản lý giỏ hàng và thanh toán (#3)
│   │   ├── Posts.php           # Hiển thị danh sách & đọc tin tức (#4)
│   │   ├── Users.php           # Đăng ký / Đăng nhập (Cả nhóm)
│   │   ├── Admin.php           # Dashboard & Cài đặt hệ thống (#1)
│   │   ├── AdminProducts.php   # Quản lý sản phẩm CRUD (#3)
│   │   ├── AdminOrders.php     # Quản lý đơn hàng & trạng thái (#3)
│   │   ├── AdminPosts.php      # Quản lý bài viết tin tức CRUD (#4)
│   │   ├── AdminComments.php   # Quản lý bình luận & đánh giá (#4)
│   │   ├── AdminFaqs.php       # Quản lý bộ câu hỏi Hỏi/đáp (#2)
│   │   ├── AdminContacts.php   # Quản lý tin nhắn liên hệ từ khách (#1)
│   │   └── AdminAbout.php      # Quản lý nội dung trang Giới thiệu (#2)
│   ├── models/                 # Chứa các class giao tiếp với Database
│   │   ├── User.php            # Model Thành viên (Cả nhóm)
│   │   ├── Product.php         # Model Sản phẩm (#3)
│   │   ├── Order.php           # Model Đơn hàng (#3)
│   │   ├── Post.php            # Model Bài viết (#4)
│   │   ├── Comment.php         # Model Bình luận (#4)
│   │   ├── Faq.php             # Model Câu hỏi (#2)
│   │   ├── About.php           # Model Giới thiệu (#2)
│   │   ├── Contact.php         # Model Liên hệ (#1)
│   │   └── Setting.php         # Model Cài đặt (Logo, SĐT, Địa chỉ) (#1)
│   └── views/                  # Chứa file giao diện HTML/PHP
│       ├── admin/              # GIAO DIỆN ADMIN (SrtDash)
│       │   ├── settings/       # Sửa Logo, thông tin công ty (#1)
│       │   ├── contacts/       # Danh sách tin nhắn khách hàng (#1)
│       │   ├── products/       # Quản lý sản phẩm (#3)
│       │   ├── orders/         # Quản lý đơn hàng (#3)
│       │   ├── posts/          # Quản lý tin tức (#4)
│       │   ├── comments/       # Quản lý bình luận (#4)
│       │   ├── faqs/           # Quản lý câu hỏi/đáp (#2)
│       │   ├── about/          # Sửa nội dung giới thiệu (#2)
│       │   └── index.php       # Dashboard tổng quan (#1)
│       ├── client/             # GIAO DIỆN KHÁCH (Tailwind CSS)
│       │   ├── index.php       # Trang chủ (#1)
│       │   ├── contact.php     # Trang liên hệ (#1)
│       │   ├── about.php       # Trang giới thiệu (#2)
│       │   ├── faq.php         # Trang hỏi đáp (#2)
│       │   ├── products/       # Danh sách & chi tiết SP (#3)
│       │   ├── cart/           # Giỏ hàng & Thanh toán (#3)
│       │   └── posts/          # Danh sách & đọc bài viết (#4)
│       └── layouts/            # Header và Footer chung (Cả nhóm)
├── public/                     # Thư mục công khai
│   ├── index.php               # Front-controller (Cả nhóm)
│   ├── .htaccess               # Cấu hình URL đẹp (Cả nhóm)
│   ├── css/                    # Custom CSS (Cả nhóm)
│   ├── js/                     # Custom Javascript (Cả nhóm)
│   ├── uploads/                # Nơi lưu trữ ảnh (Xử lý bởi #2, dùng bởi #1, #3, #4)
│   └── admin_assets/           # Assets của template Srtdash (Bảo - #4 quản lý)
└── database.sql                # File SQL cấu trúc các bảng (Cả nhóm)
```

## 🚀 Getting Started
1. Giải nén vào `xampp/htdocs`.
2. Tạo database `game_server_db` trên phpmyadmin và import `database.sql`.
3. Truy cập: `http://localhost/Game%20Server%20Rental%20Platform/public/`.

