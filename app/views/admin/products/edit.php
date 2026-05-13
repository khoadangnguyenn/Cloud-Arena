<?php require APPROOT . '/views/layouts/admin/header.php'; ?>

<div class="row">
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="header-title mb-0">Cập nhật Sản phẩm: <span class="text-primary"><?php echo $data['name']; ?></span></h4>
                    <a href="<?php echo URLROOT; ?>/admin/products" class="btn btn-secondary btn-sm"><i class="ti-arrow-left"></i> Quay lại</a>
                </div>
                
                <form action="<?php echo URLROOT; ?>/admin/products/edit/<?php echo $data['id']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="col-form-label">Tên package <span class="text-danger">*</span></label>
                                <input class="form-control <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''; ?>" type="text" name="name" value="<?php echo $data['name']; ?>">
                                <span class="invalid-feedback"><?php echo $data['name_err']; ?></span>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-form-label">Danh mục (Category) <span class="text-danger">*</span></label>
                                <select class="custom-select" name="category_id">
                                    <?php foreach($data['categories'] as $category) : ?>
                                        <option value="<?php echo $category->id; ?>" <?php echo ($data['category_id'] == $category->id) ? 'selected' : ''; ?>><?php echo $category->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="price" class="col-form-label">Mức giá / Tháng (VNĐ) <span class="text-danger">*</span></label>
                                <input class="form-control <?php echo (!empty($data['price_err'])) ? 'is-invalid' : ''; ?>" type="number" name="price" value="<?php echo $data['price']; ?>">
                                <span class="invalid-feedback"><?php echo $data['price_err']; ?></span>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-form-label">Trạng thái bán</label>
                                <select class="custom-select" name="status">
                                    <option value="active" <?php echo ($data['status'] == 'active') ? 'selected' : ''; ?>>Đang triển khai (Active)</option>
                                    <option value="hidden" <?php echo ($data['status'] == 'hidden') ? 'selected' : ''; ?>>Ẩn (Hidden)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cpu_cores" class="col-form-label">Số Core CPU <span class="text-danger">*</span></label>
                                <input class="form-control <?php echo (!empty($data['cpu_err'])) ? 'is-invalid' : ''; ?>" type="number" name="cpu_cores" value="<?php echo $data['cpu_cores']; ?>">
                                <span class="invalid-feedback"><?php echo $data['cpu_err']; ?></span>
                            </div>

                            <div class="form-group">
                                <label for="ram_mb" class="col-form-label">Dung lượng RAM (MB) <span class="text-danger">*</span></label>
                                <input class="form-control <?php echo (!empty($data['ram_mb_err'])) ? 'is-invalid' : ''; ?>" type="number" name="ram_mb" value="<?php echo $data['ram_mb']; ?>">
                                <span class="invalid-feedback"><?php echo $data['ram_mb_err']; ?></span>
                            </div>

                            <div class="form-group">
                                <label for="disk_gb" class="col-form-label">Dung lượng ổ Disk SSD (GB) <span class="text-danger">*</span></label>
                                <input class="form-control <?php echo (!empty($data['disk_err'])) ? 'is-invalid' : ''; ?>" type="number" name="disk_gb" value="<?php echo $data['disk_gb']; ?>">
                                <span class="invalid-feedback"><?php echo $data['disk_err']; ?></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Cập nhật hình ảnh đại diện</label>
                                <div class="mb-3">
                                    <?php if (!empty($data['image_url'])): ?>
                                        <?php 
                                            // Căn chỉnh lại đường dẫn upload cho chuẩn
                                            $imgPath = URLROOT . '/uploads/' . ltrim($data['image_url'], '/'); 
                                        ?>
                                        <img src="<?php echo $imgPath; ?>" alt="Ảnh hiện tại" class="img-thumbnail" style="max-height: 100px;">
                                    <?php else: ?>
                                        <div style="width: 150px; height: 100px; background-color: #f8f9fa; border: 1px dashed #ced4da; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <span style="color: #6c757d; font-size: 13px;">Chưa có ảnh</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <input type="file" class="form-control-file <?php echo (!empty($data['image_err'])) ? 'is-invalid' : ''; ?>" id="image" name="image">
                                <small class="form-text text-muted">Bỏ trống nếu bạn muốn giữ nguyên ảnh cũ.</small>
                                <div class="text-danger mt-1" style="font-size: 80%;"><?php echo $data['image_err']; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mt-3">
                        <label for="description" class="col-form-label">Mô tả chi tiết</label>
                        <textarea class="form-control" name="description" rows="4"><?php echo $data['description']; ?></textarea>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary px-5"><i class="ti-save"></i> Cập nhật thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/admin/footer.php'; ?>