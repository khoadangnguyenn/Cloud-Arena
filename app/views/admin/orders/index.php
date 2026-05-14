<?php require_once APPROOT . '/views/layouts/admin/header.php'; ?>

<div class="row">
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-4">Quản lý Đơn hàng</h4>
                
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="text-center table table-hover">
                            <thead class="text-uppercase bg-light">
                                <tr>
                                    <th>Mã Đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Ngày đặt</th>
                                    <th>Trạng thái</th>
                                    <th>Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data['orders'])): ?>
                                    <tr><td colspan="6" class="py-4">Chưa có đơn hàng nào.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($data['orders'] as $order) : ?>
                                        <tr>
                                            <td class="font-weight-bold text-primary align-middle">#<?php echo $order->id; ?></td>
                                            <td class="align-middle text-left pl-4">
                                                <strong><?php echo $order->username; ?></strong><br>
                                                <small class="text-muted"><?php echo $order->email; ?></small>
                                            </td>
                                            <td class="text-danger font-weight-bold align-middle"><?php echo number_format($order->total_amount, 0, ',', '.'); ?>đ</td>
                                            <td class="align-middle"><?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></td>
                                            
                                            <td class="align-middle">
                                                <form action="<?php echo URLROOT; ?>/adminorders/updateStatus/<?php echo $order->id; ?>" method="POST" class="d-flex align-items-center justify-content-center gap-2 mb-0">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $data['csrf_admin']; ?>">
                                                    
                                                    <select name="status" class="form-control form-control-sm" style="width: 130px; border-radius: 8px; font-size: 13px;">
                                                        <option value="pending" <?php echo $order->status == 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                                                        <option value="processing" <?php echo $order->status == 'processing' ? 'selected' : ''; ?>>Đang thiết lập</option>
                                                        <option value="completed" <?php echo $order->status == 'completed' ? 'selected' : ''; ?>>Hoàn tất</option>
                                                        <option value="cancelled" <?php echo $order->status == 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                                                    </select>
                                                    
                                                    <button type="submit" class="btn btn-sm btn-primary" style="padding: 4px 8px;" title="Lưu thay đổi">
                                                        <i class="ti-save"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            
                                            <td class="align-middle text-center">
                                                <a href="<?php echo URLROOT; ?>/adminorders/show/<?php echo $order->id; ?>" class="text-info" title="Xem chi tiết đơn hàng" style="font-size: 22px;">
                                                    <i class="ti-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if (isset($data['totalPages']) && $data['totalPages'] > 1) : ?>
                    <div class="pagination_area pull-right mt-4">
                        <ul>
                            <?php for ($i = 1; $i <= $data['totalPages']; $i++) : ?>
                                <li class="<?php echo $data['currentPage'] == $i ? 'active' : ''; ?>">
                                    <a href="<?php echo URLROOT; ?>/adminorders?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/layouts/admin/footer.php'; ?>