<?php require APPROOT . '/views/layouts/admin/header.php'; ?>

<div class="main-content-inner">
    <?php if(!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger my-2"><?php echo htmlspecialchars($_SESSION['flash_error']); ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if(!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success my-2"><?php echo htmlspecialchars($_SESSION['flash_success']); ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h4 class="card_title">Quản lý FAQ</h4>
                    <form action="<?php echo URLROOT; ?>/admin/faqs" method="post" class="d-flex gap-2">
                        <input type="hidden" name="__action" value="createFaq">
                        <input type="text" name="question" placeholder="Câu hỏi" class="form-control" style="min-width:300px;">
                        <?php if(!empty($data['categories'])): ?>
                            <select name="category" class="form-control" style="max-width:260px">
                                <option value="">(Không chọn)</option>
                                <?php foreach($data['categories'] as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c->slug); ?>"><?php echo htmlspecialchars($c->title); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="text" name="category" placeholder="Category (tùy chọn)" class="form-control" style="max-width:220px">
                        <?php endif; ?>
                        <input type="hidden" name="answer" value="-">
                        <button class="btn btn-primary">Thêm nhanh</button>
                    </form>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h4 class="card_title">Quản lý Category FAQ</h4>
                    <form action="<?php echo URLROOT; ?>/admin/faqs" method="post" enctype="multipart/form-data" class="d-flex gap-2">
                        <input type="hidden" name="__action" value="createCategory">
                        <input type="text" name="title" placeholder="Tiêu đề category" class="form-control" style="min-width:240px;">
                        <input type="file" name="image" accept="image/*" class="form-control" style="max-width:320px">
                        <button class="btn btn-success">Thêm category</button>
                    </form>
                </div>
                <div class="card-body">
                    <?php if(!empty($data['categories'])): ?>
                        <div class="row">
                            <?php foreach($data['categories'] as $cat): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <?php if(!empty($cat->image)): ?><img src="<?php echo htmlspecialchars($cat->image); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($cat->title); ?>"><?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($cat->title); ?></h5>
                                            <p class="text-muted">Slug: <?php echo htmlspecialchars($cat->slug); ?></p>
                                            <form action="<?php echo URLROOT; ?>/admin/faqs" method="post" enctype="multipart/form-data" class="d-flex gap-2">
                                                <input type="hidden" name="__action" value="updateCategory">
                                                <input type="hidden" name="id" value="<?php echo $cat->id; ?>">
                                                <input type="text" name="title" value="<?php echo htmlspecialchars($cat->title); ?>" class="form-control">
                                                <input type="file" name="image" accept="image/*" class="form-control">
                                                <button class="btn btn-primary">Lưu</button>
                                            </form>
                                            <div class="mt-2 text-end">
                                                <form action="<?php echo URLROOT; ?>/admin/faqs" method="post" style="display:inline-block" onsubmit="return confirm('Xóa category? Các FAQ liên quan sẽ bị bỏ category.')">
                                                    <input type="hidden" name="__action" value="deleteCategory">
                                                    <input type="hidden" name="id" value="<?php echo $cat->id; ?>">
                                                    <button class="btn btn-sm btn-danger">Xóa</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Chưa có category FAQ nào.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <label for="admin_category_filter" class="mb-0">Lọc danh mục:</label>
                        <form id="admin-category-form" method="get" action="<?php echo URLROOT; ?>/admin/faqs">
                            <select id="admin_category_filter" name="category" onchange="document.getElementById('admin-category-form').submit()" class="form-control" style="max-width:260px; display:inline-block">
                                <option value="">-- Chọn danh mục --</option>
                                <?php if(!empty($data['categories'])): foreach($data['categories'] as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c->slug); ?>" <?php echo (isset($data['current_category']) && $data['current_category'] === $c->slug) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c->title); ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </form>
                    </div>
                    <?php if(!empty($data['faqs'])): ?>
                        <div class="list-group">
                            <?php foreach($data['faqs'] as $faq): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold">#<?php echo $faq->id; ?> — <?php echo htmlspecialchars($faq->question); ?></div>
                                            <small class="text-muted">Trạng thái: <?php echo htmlspecialchars($faq->status); ?><?php echo isset($faq->category) && $faq->category ? ' · ' . htmlspecialchars($faq->category) : ''; ?></small>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-toggle" data-id="<?php echo $faq->id; ?>">Sửa</button>
                                            <form action="<?php echo URLROOT; ?>/admin/faqs" method="post" style="display:inline-block" onsubmit="return confirm('Xác nhận xóa?')">
                                                <input type="hidden" name="__action" value="deleteFaq">
                                                <input type="hidden" name="id" value="<?php echo $faq->id; ?>">
                                                <button class="btn btn-sm btn-danger">Xóa</button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="faq-edit mt-3" id="faq-edit-<?php echo $faq->id; ?>" style="display:none;">
                                        <form action="<?php echo URLROOT; ?>/admin/faqs" method="post" class="d-flex flex-column gap-2">
                                            <input type="hidden" name="__action" value="updateFaq">
                                            <input type="hidden" name="id" value="<?php echo $faq->id; ?>">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <input type="text" name="question" value="<?php echo htmlspecialchars($faq->question); ?>" class="form-control" placeholder="Câu hỏi">
                                                </div>
                                                <div class="col-md-4">
                                                    <?php if(!empty($data['categories'])): ?>
                                                        <select name="category" class="form-control">
                                                            <option value="">(Không chọn)</option>
                                                            <?php foreach($data['categories'] as $c): ?>
                                                                <option value="<?php echo htmlspecialchars($c->slug); ?>" <?php echo (isset($faq->category) && $faq->category === $c->slug) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c->title); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <input type="text" name="category" value="<?php echo htmlspecialchars($faq->category); ?>" class="form-control" placeholder="Category (tùy chọn)">
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div>
                                                <textarea name="answer" rows="4" class="form-control" placeholder="Đáp án (answer)"><?php echo htmlspecialchars($faq->answer); ?></textarea>
                                            </div>
                                            <div class="d-flex gap-2 justify-content-end">
                                                <select name="status" class="form-control" style="max-width:160px;">
                                                    <option value="active" <?php echo ($faq->status === 'active') ? 'selected' : ''; ?>>Active</option>
                                                    <option value="inactive" <?php echo ($faq->status === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                                <button class="btn btn-primary">Lưu</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Chưa có FAQ nào.</p>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-end">
                    <?php echo $data['pagination'] ?? ''; ?>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h5>Tin nhắn từ người dùng <?php if(!empty($data['new_messages'])): ?><span class="badge bg-danger"><?php echo $data['new_messages']; ?></span><?php endif; ?></h5>
                    <?php if(!empty($data['messages'])): ?>
                        <div class="list-group mt-3">
                            <?php foreach($data['messages'] as $m): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong><?php echo htmlspecialchars($m->name ?: 'Khách'); ?></strong>
                                            <?php if(!empty($m->email)): ?><small class="text-muted"> &lt;<?php echo htmlspecialchars($m->email); ?>&gt;</small><?php endif; ?>
                                            <?php if(!empty($m->category)): ?><span class="badge bg-secondary ms-2"><?php echo htmlspecialchars($m->category); ?></span><?php endif; ?>
                                        </div>
                                        <div><small class="text-muted"><?php echo htmlspecialchars($m->created_at); ?></small></div>
                                    </div>
                                    <div class="mt-2"><?php echo nl2br(htmlspecialchars($m->message)); ?></div>
                                    <?php if(!empty($m->reply)): ?>
                                        <div class="mt-3 p-3 bg-light text-dark rounded">Trả lời: <?php echo nl2br(htmlspecialchars($m->reply)); ?> <br><small>Bởi: <?php echo htmlspecialchars($m->reply_by); ?></small></div>
                                    <?php else: ?>
                                        <form action="<?php echo URLROOT; ?>/admin/faqs" method="post" class="mt-3 d-flex gap-2">
                                            <input type="hidden" name="__action" value="replyMessage">
                                            <input type="hidden" name="id" value="<?php echo $m->id; ?>">
                                            <textarea name="reply" class="form-control" placeholder="Viết trả lời..." required></textarea>
                                            <button class="btn btn-primary">Gửi</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Chưa có tin nhắn nào.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/admin/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.btn-edit-toggle').forEach(function(btn){
        btn.addEventListener('click', function(){
            var id = btn.getAttribute('data-id');
            var el = document.getElementById('faq-edit-' + id);
            if(!el) return;
            if(el.style.display === 'none' || el.style.display === ''){
                el.style.display = 'block';
                btn.textContent = 'Đóng';
            } else {
                el.style.display = 'none';
                btn.textContent = 'Sửa';
            }
        });
    });
});
</script>
