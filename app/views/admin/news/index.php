<?php require APPROOT . '/views/layouts/admin/header.php'; ?>

<!-- Thêm CKEditor CDN -->
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>

<div class="row g-3">
    <div class="col-12">
        <section class="card panel-card">
            <div class="card-body">
                <div class="panel-header">
                    <div>
                        <h2 class="panel-title">News Management</h2>
                        <p class="panel-muted">Create and manage blog posts</p>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                        <i class="ti-plus me-1"></i> Thêm bài viết mới
                    </button>
                </div>
                <?php if (!empty($data['flash'])): ?>
                    <div class="alert alert-<?php echo $data['flash']['type'] === 'success' ? 'success' : 'danger'; ?>">
                        <?php echo htmlspecialchars($data['flash']['message']); ?>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
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
                            <?php if (empty($data['news'])): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Chưa có bài viết nào.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($data['news'] as $article): ?>
                                <tr>
                                    <td>#<?php echo $article->id; ?></td>
                                    <td>
                                        <?php if($article->image): ?>
                                            <img src="<?php echo URLROOT; ?>/uploads/<?php echo $article->image; ?>" alt="<?php echo $article->title; ?>" style="width: 80px; height: 50px; object-fit: cover; border-radius:8px;">
                                        <?php else: ?>
                                            <span class="badge badge-soft-primary">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($article->title); ?></td>
                                    <td><?php echo htmlspecialchars($article->author_name); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($article->created_at)); ?></td>
                                    <td>
                                        <form action="<?php echo URLROOT; ?>/adminnews/delete/<?php echo $article->id; ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($data['csrf_admin'] ?? ''); ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="ti-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php $pagination = $data['pagination'] ?? ['page' => 1, 'last_page' => 1, 'total' => count($data['news'] ?? [])]; ?>
                <?php if ((int) $pagination['last_page'] > 1): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <p class="mb-0 text-muted small">Total articles: <?php echo (int) $pagination['total']; ?></p>
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($p = 1; $p <= (int) $pagination['last_page']; $p++): ?>
                                <li class="page-item <?php echo (int) $pagination['page'] === $p ? 'active' : ''; ?>">
                                    <a class="page-link bg-transparent border-secondary text-light" href="<?php echo URLROOT; ?>/adminnews?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<!-- Modal Thêm Bài viết -->
<div class="modal fade" id="addNewsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo URLROOT; ?>/adminnews/add" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($data['csrf_admin'] ?? ''); ?>">
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
