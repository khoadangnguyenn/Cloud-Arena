# Cloud-Arena
 Game Server Rental Platform providing servers with high performance, modpack support, and easy management.
---

## 1. Cấu trúc thư mục (MVC Architecture)

Dự án tuân theo luồng xử lý: **Người dùng truy cập -> Route (index.php) -> Controller -> Model (CSDL) -> View (Giao diện HTML)**.

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

---

## 2. Hướng dẫn phân chia công việc cho 4 thành viên

Theo yêu cầu đề bài, phần **Công việc chung** (Kiến trúc MVC, Database, Đăng nhập/Đăng ký, Phân quyền) hiện đã được tôi thiết lập cơ bản. Dưới đây là phân chia các module cho từng người:

### 👤 Thành viên #1
**Nhiệm vụ Client:**
- **Trang chủ & Trang Liên hệ:**
  - File cần sửa: `app/views/client/pages/index.php` (Đã có sườn cơ bản, cần chau chuốt thêm).
  - Tạo Controller `Contacts.php` và View `app/views/client/contacts/index.php` cho form liên hệ.
  
**Nhiệm vụ Admin:**
- **Quản lý Thông tin hệ thống:** Tạo Controller `AdminSettings.php` (hoặc cấu hình trong `Admin.php`) để thay đổi Số điện thoại, Email, Logo dưới Footer. 
- **Quản lý Liên hệ:** Tạo Controller `AdminContacts.php` để lấy danh sách liên hệ từ bảng `contacts`, chức năng (đánh dấu đã đọc, xoá liên hệ).

---

### 👤 Thành viên #2
**Nhiệm vụ Client:**
- **Trang Giới thiệu & Hỏi/Đáp (FAQ):**
  - File cần tạo: `app/views/client/pages/about.php` và `app/views/client/pages/faq.php`. Cần truy xuất bảng `faqs` để hiển thị.
  
**Nhiệm vụ Admin:**
- **Quản lý Hỏi/Đáp:**
  - Tạo Controller `AdminFaqs.php`, viết chức năng Thêm / Sửa / Xoá các câu hỏi đáp lưu vào DB.
- **Cập nhật nội dung Giới thiệu:** Kết hợp với thành viên #1 để lưu thông tin trang Giới thiệu vào bảng `settings` (hoặc tạo file config tĩnh).

---

### 👤 Thành viên #3 (Module Sản phẩm/Giỏ hàng - Đã làm mẫu 80%)
**Nhiệm vụ Client:**
- **Trang danh sách & Chi tiết sản phẩm:** Đã làm sẵn ở Controller `Products.php`. Nhiệm vụ của bạn là thêm **Thanh tìm kiếm (Search)** theo từ khóa và **Phân trang (Pagination)**.
- **Giỏ hàng / Checkout:** Đã làm sẵn Controller `Cart.php`. Cần làm thêm chức năng **Checkout** (Lưu giỏ hàng vào bảng `orders` và `order_details`).
  
**Nhiệm vụ Admin:**
- **Quản lý sản phẩm:** Đã làm sẵn tại `AdminProducts.php`. Bạn cần bổ sung tính năng **Sửa sản phẩm** (hiện tại mới có Thêm, Xem, Xoá) và **Phân trang**.
- **Quản lý đơn hàng:** Tạo Controller `AdminOrders.php` để xem danh sách `orders`, và cập nhật trạng thái đơn hàng (Pending -> Completed).

---

### 👤 Thành viên #4
**Nhiệm vụ Client:**
- **Trang Tin tức & Đọc bài viết:**
  - Tạo Controller `News.php` và Model `News.php`.
  - Làm trang danh sách tin tức (có tìm kiếm và phân trang) và trang đọc chi tiết.
- **Bình luận:** Thêm Form bình luận ở cuối bài viết và dưới chi tiết sản phẩm. Lưu vào bảng `comments`.
  
**Nhiệm vụ Admin:**
- **Quản lý Tin tức:**
  - Tạo Controller `AdminNews.php` để Thêm / Sửa / Xoá bài viết, có tích hợp **Trình soạn thảo WYSIWYG** (như CKEditor hoặc TinyMCE) theo đúng yêu cầu đồ án. Nhớ thêm trường cho thẻ meta SEO.
- **Quản lý Bình luận:** Tạo Controller `AdminComments.php` để xem, duyệt, hoặc xoá các comment thô tục.

---

### 💡 Lưu ý chung cho tất cả thành viên:
1. **Kiểm tra dữ liệu đầu vào:** Ở mỗi Form (Thêm/Sửa, Đăng ký), các bạn phải dùng cả JS để chặn phía Client và dùng PHP để kiểm tra ở Controller (Server-side validation) để tránh điểm trừ.
2. **Framework JS/CSS nâng cao:** Theo yêu cầu: *"tìm hiểu và ứng dụng carousel, wysiwyg, drag & drop, animations"*, các bạn nên rải đều các thư viện này vào chức năng của mình (VD: Thành viên #4 dùng WYSIWYG, Thành viên #1 làm Carousel banner Trang chủ).
3. **Bảo mật:** Sử dụng Prepared Statement (`$this->db->bind()`) của class Database để chống SQL Injection (như tôi đã làm trong code mẫu).
4. **Phân trang (Pagination):** Yêu cầu bắt buộc đối với các danh sách (Sản phẩm, Tin tức, Đơn hàng) trong Admin. Bạn truyền parameter `?page=2` lên URL và dùng `LIMIT`, `OFFSET` trong MySQL.
