<?php require_once APPROOT . '/views/layouts/admin/header.php'; ?>

<div class="row">
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h4 class="header-title mb-0">Danh sách Sản phẩm</h4>
                <div class="d-flex align-items-center">
                    <form action="<?php echo URLROOT; ?>/admin/products" method="GET" class="mr-3">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" name="search" placeholder="Tìm tên sản phẩm..." value="<?php echo isset($data['keyword']) ? $data['keyword'] : ''; ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary btn-sm" type="submit"><i class="ti-search"></i></button>
                            </div>
                        </div>
                    </form>
                    <a href="<?php echo URLROOT; ?>/admin/products/add" class="btn btn-success btn-sm"><i class="ti-plus"></i> Thêm mới</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-body">
                <div class="single-table">
                    <div class="table-responsive">
                        <table class="table table-hover text-center">
                            <thead class="text-uppercase bg-light">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Hình ảnh</th>
                                    <th scope="col">Tên / Phân loại</th>
                                    <th scope="col">Cấu hình cơ bản</th>
                                    <th scope="col">Giá (VNĐ)</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data['products'])): ?>
                                    <tr><td colspan="7" class="text-center py-4">Không có sản phẩm nào!</td></tr>
                                <?php else: ?>
                                    <?php foreach ($data['products'] as $product) : ?>
                                        <tr>
                                            <th scope="row"><?php echo $product->id; ?></th>
                                            <td>
                                                <?php if(!empty($product->image_url)): ?>
                                                    <?php 
                                                        // Đảm bảo đường dẫn ghép đúng, loại bỏ dấu / thừa ở đầu (nếu có)
                                                        $imgPath = URLROOT . '/uploads/' . ltrim($product->image_url, '/'); 
                                                    ?>
                                                    <img src="<?php echo $imgPath; ?>" alt="img" class="img-fluid rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div style="width: 60px; height: 60px; background-color: #e9ecef; display: inline-flex; align-items: center; justify-content: center; border-radius: .25rem;">
                                                        <i class="ti-image" style="color: #adb5bd; font-size: 24px;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-left">
                                                <strong><?php echo $product->name; ?></strong><br>
                                                <small class="text-muted"><?php echo $product->category_name; ?></small>
                                            </td>
                                            <td class="text-left">
                                                <small>
                                                    <i class="ti-server"></i> CPU: <?php echo $product->cpu_cores; ?> Core<br>
                                                    <i class="ti-harddrives"></i> RAM: <?php echo $product->ram_mb / 1024; ?> GB<br>
                                                    <i class="ti-save"></i> SSD: <?php echo $product->disk_gb; ?> GB
                                                </small>
                                            </td>
                                            <td class="text-danger font-weight-bold"><?php echo number_format($product->price, 0, ',', '.'); ?>đ</td>
                                            <td class="align-middle">
                                                <?php
                                                    $bgColor = $product->status == 'active' ? '#28a745' : '#6c757d'; // Xanh lá cho Hoạt động, Xám cho Đã ẩn
                                                    $textColor = '#ffffff'; // Chữ trắng
                                                    $statusText = $product->status == 'active' ? 'Hoạt động' : 'Đã ẩn';
                                                ?>
                                                <span style="background-color: <?php echo $bgColor; ?>; color: <?php echo $textColor; ?>; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-block; white-space: nowrap;">
                                                    <?php echo $statusText; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <ul class="d-flex justify-content-center list-unstyled mb-0">
                                                    <li class="mr-3"><a href="<?php echo URLROOT; ?>/admin/products/edit/<?php echo $product->id; ?>" class="text-secondary"><i class="fa fa-edit"></i></a></li>
                                                    <li>
                                                        <form action="<?php echo URLROOT; ?>/admin/products/delete/<?php echo $product->id; ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá sản phẩm này?');">
                                                            <button type="submit" class="text-danger border-0 bg-transparent p-0"><i class="ti-trash"></i></button>
                                                        </form>
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
                                    <a href="<?php echo URLROOT; ?>/admin/products?page=<?php echo $i; ?><?php echo !empty($data['keyword']) ? '&search='.$data['keyword'] : ''; ?>"><?php echo $i; ?></a>
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