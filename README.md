# Cloud-Arena
 Game Server Rental Platform providing servers with high performance, modpack support, and easy management.
---

## 1. Cấu trúc thư mục (MVC Architecture)

Luồng xử lý: **Client truy cập -> Route (index.php) -> Controller -> Model (CSDL) -> View (Giao diện HTML)**.

```text
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

