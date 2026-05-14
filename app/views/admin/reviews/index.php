<?php require_once APPROOT . '/views/layouts/admin/header.php'; ?>

<div class="row">
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-4">Quản lý Đánh giá (Reviews)</h4>
                
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="text-center table table-hover">
                            <thead class="text-uppercase bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Khách hàng</th>
                                    <th>Sản phẩm</th>
                                    <th>Đánh giá</th>
                                    <th>Nội dung</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data['reviews'])): ?>
                                    <tr><td colspan="7" class="py-4">Chưa có đánh giá nào.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($data['reviews'] as $review) : ?>
                                        <tr>
                                            <td class="align-middle text-primary font-weight-bold">#<?php echo $review->id; ?></td>
                                            <td class="align-middle"><strong><?php echo $review->full_name ?: $review->username; ?></strong></td>
                                            <td class="align-middle text-info"><?php echo $review->product_name; ?></td>
                                            <td class="align-middle text-warning">
                                                <?php for($i=1; $i<=5; $i++): ?>
                                                    <i class="fa fa-star <?php echo $i <= $review->rating ? '' : 'text-light'; ?>"></i>
                                                <?php endfor; ?>
                                            </td>
                                            <td class="align-middle text-left" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                <?php echo htmlspecialchars($review->comment); ?>
                                            </td>
                                            <td class="align-middle">
                                                <form action="<?php echo URLROOT; ?>/adminreviews/updateStatus/<?php echo $review->id; ?>" method="POST" class="d-flex justify-content-center">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_admin'] ?? ''; ?>">
                                                    
                                                    <select name="status" class="form-control form-control-sm" style="width: 110px;" onchange="this.form.submit()">
                                                        <option value="pending" <?php echo $review->status == 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                                                        <option value="approved" <?php echo $review->status == 'approved' ? 'selected' : ''; ?>>Đã duyệt</option>
                                                        <option value="hidden" <?php echo $review->status == 'hidden' ? 'selected' : ''; ?>>Đã ẩn</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="align-middle">
                                                <form action="<?php echo URLROOT; ?>/adminreviews/delete/<?php echo $review->id; ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                                                    <button type="submit" class="btn btn-xs btn-danger"><i class="ti-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/admin/footer.php'; ?>