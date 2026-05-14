<?php require APPROOT . '/views/layouts/admin/header.php'; ?>

<div class="row g-3">
    <div class="col-12">
        <section class="card panel-card">
            <div class="card-body">
                <div class="admin-module-header">
                    <div>
                        <h1 class="admin-module-title">Chỉnh sửa quảng cáo</h1>
                        <p class="admin-module-lead">Cập nhật thông tin banner: <?php echo htmlspecialchars((string) $data['ad']->title, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <a href="<?php echo URLROOT; ?>/admin/ads" class="btn btn-outline-light btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
                </div>

                <form action="<?php echo URLROOT; ?>/admin/ads/edit/<?php echo (int) $data['ad']->id; ?>" method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="title" required value="<?php echo htmlspecialchars((string) $data['ad']->title, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-12">
                            <label for="link_url" class="form-label">Link URL <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" name="link_url" id="link_url" required value="<?php echo htmlspecialchars((string) $data['ad']->link_url, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-12">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" name="status" id="status" data-admin-custom-select="true">
                                <option value="active" <?php echo $data['ad']->status === 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                                <option value="inactive" <?php echo $data['ad']->status === 'inactive' ? 'selected' : ''; ?>>Tạm dừng</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="start_at" class="form-label">Ngày bắt đầu</label>
                            <input type="datetime-local" class="form-control" name="start_at" id="start_at" value="<?php echo $data['ad']->start_at ? date('Y-m-d\TH:i', strtotime($data['ad']->start_at)) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="end_at" class="form-label">Ngày kết thúc</label>
                            <input type="datetime-local" class="form-control" name="end_at" id="end_at" value="<?php echo $data['ad']->end_at ? date('Y-m-d\TH:i', strtotime($data['ad']->end_at)) : ''; ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Hình ảnh banner hiện tại</label>
                            <div class="mb-3">
                                <img src="<?php echo htmlspecialchars(URLROOT . $data['ad']->image_url, ENT_QUOTES, 'UTF-8'); ?>" alt="Current Banner" class="rounded border" style="max-width: 300px; display: block;">
                            </div>
                            <label for="image" class="form-label">Thay đổi hình ảnh (Để trống nếu giữ nguyên)</label>
                            <input type="file" class="form-control" name="image" id="image" accept="image/*">
                            <p class="form-text panel-muted mb-0">Gợi ý: 300×600px cho sidebar.</p>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật quảng cáo</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<?php require APPROOT . '/views/layouts/admin/footer.php'; ?>
