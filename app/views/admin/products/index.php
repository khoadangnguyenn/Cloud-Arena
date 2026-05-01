<?php require APPROOT . '/views/layouts/admin/header.php'; ?>

<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Quản lý sản phẩm</h4>
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    Thêm sản phẩm mới
                </button>
                <div class="data-tables">
                    <table class="table text-center">
                        <thead class="bg-light text-capitalize">
                            <tr>
                                <th>ID</th>
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Giá</th>
                                <th>Tồn kho</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['products'] as $product): ?>
                            <tr>
                                <td><?php echo $product->id; ?></td>
                                <td>
                                    <?php if($product->image): ?>
                                        <img src="<?php echo URLROOT; ?>/uploads/<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="ti-image"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $product->name; ?></td>
                                <td><?php echo number_format($product->price, 0, ',', '.'); ?>đ</td>
                                <td><?php echo $product->stock; ?></td>
                                <td>
                                    <form action="<?php echo URLROOT; ?>/adminproducts/delete/<?php echo $product->id; ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                        <button type="submit" class="text-danger border-0 bg-transparent"><i class="ti-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm sản phẩm -->
<div class="modal fade" id="addProductModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo URLROOT; ?>/adminproducts/add" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm sản phẩm mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="name" class="col-form-label">Tên sản phẩm</label>
                        <input class="form-control" type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="price" class="col-form-label">Giá</label>
                        <input class="form-control" type="number" id="price" name="price" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="stock" class="col-form-label">Tồn kho</label>
                        <input class="form-control" type="number" id="stock" name="stock" value="100" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="description" class="col-form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="image" class="col-form-label">Hình ảnh</label>
                        <input class="form-control" type="file" id="image" name="image">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/admin/footer.php'; ?>
