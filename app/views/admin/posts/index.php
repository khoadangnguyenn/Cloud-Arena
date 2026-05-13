<?php require APPROOT . '/views/layouts/admin/header.php'; ?>

<div class="content-header">
    <div class="header-left">
        <h1>Quản lý tin tức</h1>
        <p>Xem, thêm, sửa và xóa các bài viết tin tức trên hệ thống.</p>
    </div>
    <div class="header-right">
        <a href="<?php echo URLROOT; ?>/admin/posts/add" class="btn-primary">
            <i class="fas fa-plus"></i> Thêm bài viết mới
        </a>
    </div>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Thumbnail</th>
                <th>Tiêu đề</th>
                <th>Tác giả</th>
                <th>Trạng thái</th>
                <th>Lượt xem</th>
                <th>Ngày đăng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($data['news'])): ?>
                <tr>
                    <td colspan="8" class="text-center">Chưa có bài viết nào.</td>
                </tr>
            <?php else: ?>
                <?php foreach($data['news'] as $article): ?>
                    <tr>
                        <td><?php echo $article->id; ?></td>
                        <td>
                            <?php if($article->thumbnail): ?>
                                <img src="<?php echo URLROOT; ?>/public/uploads/<?php echo $article->thumbnail; ?>" class="table-thumb" alt="">
                            <?php else: ?>
                                <span class="no-thumb">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo $article->title; ?></strong>
                            <div class="small-slug"><?php echo $article->slug; ?></div>
                        </td>
                        <td><?php echo $article->author_name; ?></td>
                        <td>
                            <span class="status-badge <?php echo $article->status; ?>">
                                <?php echo $article->status == 'published' ? 'Đã đăng' : 'Bản nháp'; ?>
                            </span>
                        </td>
                        <td><?php echo $article->views_count; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($article->created_at)); ?></td>
                        <td class="actions">
                            <a href="<?php echo URLROOT; ?>/posts/show/<?php echo $article->slug; ?>" target="_blank" class="btn-icon view" title="Xem bài viết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo URLROOT; ?>/admin/posts/edit/<?php echo $article->id; ?>" class="btn-icon edit" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo URLROOT; ?>/admin/posts/delete/<?php echo $article->id; ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')">
                                <button type="submit" class="btn-icon delete" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.header-left h1 {
    font-size: 1.8rem;
    margin-bottom: 5px;
}

.header-left p {
    color: #888;
}

.btn-primary {
    background: #3a7bd5;
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary:hover {
    background: #00d2ff;
    transform: translateY(-2px);
}

.admin-table-container {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th {
    text-align: left;
    padding: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    color: #aaa;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
}

.admin-table td {
    padding: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    vertical-align: middle;
}

.table-thumb {
    width: 60px;
    height: 40px;
    object-fit: cover;
    border-radius: 4px;
}

.no-thumb {
    font-size: 0.7rem;
    color: #555;
    background: #222;
    padding: 5px 10px;
    border-radius: 4px;
}

.small-slug {
    font-size: 0.75rem;
    color: #666;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-badge.published {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.status-badge.draft {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
}

.actions {
    display: flex;
    gap: 10px;
}

.btn-icon {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    background: #222;
    color: #888;
}

.btn-icon:hover {
    color: #fff;
}

.btn-icon.view:hover { background: #17a2b8; }
.btn-icon.edit:hover { background: #3a7bd5; }
.btn-icon.delete:hover { background: #dc3545; }

.text-center { text-align: center; }
</style>

<?php require APPROOT . '/views/layouts/admin/footer.php'; ?>
