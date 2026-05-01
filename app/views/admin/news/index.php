<?php require APPROOT . '/views/layouts/admin/header.php'; ?>

<!-- Thêm CKEditor CDN -->
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>

<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">Quản lý Tin Tức</h4>
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                    Thêm bài viết mới
                </button>
                <div class="data-tables">
                    <table class="table text-center">
                        <thead class="bg-light text-capitalize">
                            <tr>
                                <th>ID</th>
                                <th>Hình ảnh</th>
                                <th>Tiêu đề</th>
                                <th>Người đăng</th>
                                <th>Ngày đăng</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['news'] as $article): ?>
                            <tr>
                                <td><?php echo $article->id; ?></td>
                                <td>
                                    <?php if($article->image): ?>
                                        <img src="<?php echo URLROOT; ?>/uploads/<?php echo $article->image; ?>" alt="<?php echo $article->title; ?>" style="width: 80px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="ti-image"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $article->title; ?></td>
                                <td><?php echo $article->author_name; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($article->created_at)); ?></td>
                                <td>
                                    <form action="<?php echo URLROOT; ?>/adminnews/delete/<?php echo $article->id; ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
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

<!-- Modal Thêm Bài viết -->
<div class="modal fade" id="addNewsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo URLROOT; ?>/adminnews/add" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm bài viết mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="title" class="col-form-label">Tiêu đề bài viết</label>
                        <input class="form-control" type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="seo_keyword" class="col-form-label">SEO Keyword</label>
                            <input class="form-control" type="text" id="seo_keyword" name="seo_keyword" placeholder="Từ khóa, cách nhau bằng dấu phẩy">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="seo_description" class="col-form-label">SEO Description</label>
                            <input class="form-control" type="text" id="seo_description" name="seo_description" placeholder="Mô tả ngắn gọn về bài viết">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="image" class="col-form-label">Hình ảnh đại diện</label>
                        <input class="form-control" type="file" id="image" name="image">
                    </div>

                    <div class="form-group mb-3">
                        <label for="content" class="col-form-label">Nội dung bài viết</label>
                        <textarea class="form-control" id="content" name="content" rows="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Đăng bài viết</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Khởi tạo CKEditor cho thẻ textarea có id="content"
    CKEDITOR.replace('content');
</script>

<?php require APPROOT . '/views/layouts/admin/footer.php'; ?>
