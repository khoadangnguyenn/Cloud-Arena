<?php require APPROOT . '/views/layouts/admin/header.php'; ?>

<div class="content-header">
    <div class="header-left">
        <h1>Thêm Quảng cáo mới</h1>
        <p>Tạo banner quảng cáo mới để hiển thị trên website</p>
    </div>
    <div class="header-right">
        <a href="<?php echo URLROOT; ?>/admin/ads" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="admin-form-container">
    <form action="<?php echo URLROOT; ?>/admin/ads/add" method="POST" enctype="multipart/form-data" class="admin-form">
        <div class="form-row">
            <div class="form-group full">
                <label for="title">Tiêu đề quảng cáo <span class="required">*</span></label>
                <input type="text" name="title" id="title" required placeholder="Ví dụ: Khuyến mãi mùa hè">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group full">
                <label for="link_url">Đường dẫn liên kết (Link URL) <span class="required">*</span></label>
                <input type="url" name="link_url" id="link_url" required placeholder="https://example.com/promo">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label for="position">Vị trí hiển thị</label>
                <select name="position" id="position">
                    <option value="sticky-sidebar">Thanh bên (Sticky Sidebar)</option>
                    <option value="header-top">Phía trên đầu (Header Top)</option>
                    <option value="footer-bottom">Phía dưới cùng (Footer Bottom)</option>
                </select>
            </div>
            <div class="form-group half">
                <label for="status">Trạng thái</label>
                <select name="status" id="status">
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Tạm dừng</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label for="start_at">Ngày bắt đầu</label>
                <input type="datetime-local" name="start_at" id="start_at">
            </div>
            <div class="form-group half">
                <label for="end_at">Ngày kết thúc</label>
                <input type="datetime-local" name="end_at" id="end_at">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group full">
                <label for="image">Hình ảnh banner <span class="required">*</span></label>
                <input type="file" name="image" id="image" required accept="image/*">
                <p class="form-help">Kích thước gợi ý: 300x600px cho Sidebar</p>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fas fa-plus"></i> Tạo quảng cáo
            </button>
        </div>
    </form>
</div>

<style>
.admin-form-container {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 30px;
}
.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}
.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.form-group.full { width: 100%; }
.form-group.half { width: 50%; }
.form-group label {
    font-weight: 600;
    color: #aaa;
}
.form-group input, .form-group select {
    background: #222;
    border: 1px solid rgba(255,255,255,0.1);
    padding: 12px;
    border-radius: 8px;
    color: #fff;
    outline: none;
}
.form-group input:focus {
    border-color: #3a7bd5;
}
.form-help {
    font-size: 0.75rem;
    color: #666;
    margin-top: 5px;
}
.required { color: #ef4444; }
.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}
</style>

<?php require APPROOT . '/views/layouts/admin/footer.php'; ?>
