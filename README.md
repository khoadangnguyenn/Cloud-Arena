```
**Đề tài:** Game Server Rental Platform (G-SERVER)
**Kiến trúc:** Model-View-Controller (MVC) Custom
**Ngôn ngữ:** PHP 8.x, MySQL, Tailwind CSS, JavaScript
```
---

## 1. MVC Architecture

### 1.1. Cấu trúc thư mục
- **/app**: Logic nghiệp vụ (Controller, Model, Core). Không thể truy cập từ URL.
- **/public**: Tài nguyên tĩnh (CSS, JS, Images) và file `index.php` (Entry point).
- **Core Classes**: 
    - `App.php`: Routing URL thông minh.
    - `Controller.php`: Lớp cơ sở cung cấp phương thức `view()` và `model()`.
    - `Database.php`: Wrapper PDO chống SQL Injection.

### 1.2. Workflow
Luồng xử lý: **Client truy cập -> Route (index.php) -> Controller -> Model (CSDL) -> View (Giao diện HTML)**.
1. **Request**: Trình duyệt gọi URL -> `.htaccess` chuyển về `index.php`.
2. **Route**: `App.php` xác định Controller/Action.
3. **Action**: Controller nhận yêu cầu, lấy dữ liệu từ Model.
4. **Response**: Controller truyền dữ liệu vào View và hiển thị kết quả cho người dùng.

---

## 2. DATABASE
Hệ thống sử dụng MySQL với các bảng chính:
- `users`: Quản lý tài khoản và phân quyền (Admin/Member).
- `products`: Các gói dịch vụ server (RAM, CPU, Giá).
- `news`: Tin tức và bài viết hướng dẫn.
- `contacts`: Phản hồi khách hàng.
- `user_servers` (Mở rộng): Quản lý các máy chủ thực tế mà khách đã thuê (Status: Running/Stopped).
- `orders`: Lịch sử giao dịch và thanh toán.

---
```
Game Server Rental Platform/
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

## 7. HƯỚNG DẪN CÀI ĐẶT
1. Giải nén vào `xampp/htdocs`.
2. Tạo database `game_server_db` trên phpmyadmin và import `database.sql`.
3. Truy cập: `http://localhost/Game%20Server%20Rental%20Platform/public/`.

