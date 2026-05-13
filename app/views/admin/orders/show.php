<?php require_once APPROOT . '/views/layouts/admin/header.php'; ?>

<div class="row">
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="header-title mb-0">Chi tiết Đơn hàng: <span class="text-primary">#<?php echo $data['order']->id; ?></span></h4>
                    <a href="<?php echo URLROOT; ?>/admin/orders" class="btn btn-secondary btn-sm"><i class="ti-arrow-left"></i> Quay lại</a>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded border">
                            <h5 class="mb-3 border-bottom pb-2"><i class="ti-user"></i> Thông tin Khách hàng</h5>
                            <p><strong>Tài khoản:</strong> <?php echo $data['order']->username; ?></p>
                            <p><strong>Email:</strong> <?php echo $data['order']->email; ?></p>
                            <p><strong>Điện thoại:</strong> <?php echo isset($data['order']->phone) ? $data['order']->phone : 'Không có'; ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded border">
                            <h5 class="mb-3 border-bottom pb-2"><i class="ti-info-alt"></i> Thông tin Đơn hàng</h5>
                            <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i:s', strtotime($data['order']->created_at)); ?></p>
                            <p><strong>Trạng thái:</strong> <span class="badge badge-primary p-1 text-uppercase"><?php echo $data['order']->status; ?></span></p>
                            <p><strong>Ghi chú / Địa chỉ:</strong> <?php echo isset($data['order']->address) ? $data['order']->address : 'Không có'; ?></p>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3"><i class="ti-shopping-cart"></i> Danh sách Sản phẩm</h5>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="bg-light">
                            <tr>
                                <th>Hình ảnh</th>
                                <th>Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th>Thời hạn</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($data['items'])): ?>
                                <?php foreach($data['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <?php if(isset($item->image_url)): ?>
                                            <img src="<?php echo URLROOT . '/' . $item->image_url; ?>" width="50" class="img-thumbnail">
                                        <?php else: ?>
                                            <i class="ti-server text-muted" style="font-size: 24px;"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-left font-weight-bold"><?php echo isset($item->name) ? $item->name : 'Sản phẩm ID: '.$item->product_id; ?></td>
                                    <td><?php echo number_format($item->price, 0, ',', '.'); ?>đ</td>
                                    <td>x<?php echo $item->quantity; ?></td>
                                    <td><?php echo isset($item->duration_months) ? $item->duration_months : 1; ?> Tháng</td>
                                    <td class="text-danger font-weight-bold"><?php echo number_format($item->price * $item->quantity * (isset($item->duration_months) ? $item->duration_months : 1), 0, ',', '.'); ?>đ</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6">Không tải được chi tiết sản phẩm.</td></tr>
                            <?php endif; ?>
                            <tr class="bg-light">
                                <td colspan="5" class="text-right font-weight-bold text-uppercase">Tổng thanh toán:</td>
                                <td class="text-danger font-weight-bold" style="font-size: 1.2rem;"><?php echo number_format($data['order']->total_amount, 0, ',', '.'); ?>đ</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/admin/footer.php'; ?>