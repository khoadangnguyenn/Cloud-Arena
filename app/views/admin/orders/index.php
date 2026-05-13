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
                                    <th>Thao tác</th>
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
                                            <?php
                                                $bgColor = '#6c757d'; // Màu xám mặc định
                                                $textColor = '#ffffff'; // Chữ trắng

                                                if ($order->status == 'completed') { 
                                                    $bgColor = '#28a745'; // Xanh lá
                                                } elseif ($order->status == 'pending') { 
                                                    $bgColor = '#ffc107'; // Vàng
                                                    $textColor = '#000000'; // Chữ đen cho dễ đọc
                                                } elseif ($order->status == 'processing') { 
                                                    $bgColor = '#17a2b8'; // Xanh dương
                                                } elseif ($order->status == 'cancelled') { 
                                                    $bgColor = '#dc3545'; // Đỏ
                                                }
                                            ?>
                                            <span style="background-color: <?php echo $bgColor; ?>; color: <?php echo $textColor; ?>; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-block;">
                                                <?php echo strtoupper($order->status); ?>
                                            </span>
                                        </td>
                                            <td class="align-middle">
                                                <ul class="d-flex justify-content-center align-items-center list-unstyled mb-0">
                                                    <li class="mr-3">
                                                        <form action="<?php echo URLROOT; ?>/admin/orders/updateStatus/<?php echo $order->id; ?>" method="POST" class="mb-0">
                                                            <select name="status" class="form-control form-control-sm shadow-none border-secondary" onchange="this.form.submit()" style="height: 30px; padding: 2px 10px; cursor: pointer; border-radius: 4px;">
                                                                <option value="pending" <?php echo ($order->status == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="processing" <?php echo ($order->status == 'processing') ? 'selected' : ''; ?>>Processing</option>
                                                                <option value="completed" <?php echo ($order->status == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                                                <option value="cancelled" <?php echo ($order->status == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                                            </select>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <a href="<?php echo URLROOT; ?>/admin/orders/show/<?php echo $order->id; ?>" class="text-info" title="Xem chi tiết đơn hàng" style="font-size: 22px;">
                                                            <i class="ti-eye"></i>
                                                        </a>
                                                    </li>
                                                </ul>
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
                                    <a href="<?php echo URLROOT; ?>/admin/orders?page=<?php echo $i; ?>"><?php echo $i; ?></a>
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