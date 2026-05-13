<?php require APPROOT . '/views/layouts/admin/header.php'; ?>

<div class="content-header">
    <div class="header-left">
        <h1>Quản lý Quảng cáo</h1>
        <p>Hiển thị và quản lý các banner quảng cáo trên website</p>
    </div>
    <div class="header-right">
        <a href="<?php echo URLROOT; ?>/admin/ads/add" class="btn-primary">
            <i class="fas fa-plus"></i> Thêm quảng cáo mới
        </a>
    </div>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Banner</th>
                <th>Thông tin</th>
                <th>Vị trí</th>
                <th>Trạng thái</th>
                <th>Thời gian</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($data['ads'])): ?>
                <?php foreach($data['ads'] as $ad): ?>
                <tr>
                    <td>
                        <div class="ad-thumbnail">
                            <img src="<?php echo URLROOT . $ad->image_url; ?>" alt="<?php echo $ad->title; ?>" style="max-width: 150px; border-radius: 8px;">
                        </div>
                    </td>
                    <td>
                        <div class="ad-info">
                            <div class="ad-title"><?php echo htmlspecialchars($ad->title); ?></div>
                            <div class="ad-link"><a href="<?php echo $ad->link_url; ?>" target="_blank" class="text-info"><?php echo $ad->link_url; ?></a></div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-outline"><?php echo $ad->position; ?></span>
                    </td>
                    <td>
                        <span class="status-badge <?php echo $ad->status === 'active' ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo $ad->status === 'active' ? 'Hoạt động' : 'Tạm dừng'; ?>
                        </span>
                    </td>
                    <td>
                        <div class="ad-time">
                            <div>Bắt đầu: <?php echo $ad->start_at ? date('d/m/Y', strtotime($ad->start_at)) : 'N/A'; ?></div>
                            <div>Kết thúc: <?php echo $ad->end_at ? date('d/m/Y', strtotime($ad->end_at)) : 'N/A'; ?></div>
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?php echo URLROOT; ?>/admin/ads/edit/<?php echo $ad->id; ?>" class="btn-icon" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo URLROOT; ?>/admin/ads/delete/<?php echo $ad->id; ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa quảng cáo này?')">
                                <button type="submit" class="btn-icon btn-delete" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center py-4">Chưa có quảng cáo nào được tạo.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.ad-thumbnail img {
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}
.ad-title {
    font-weight: 600;
    margin-bottom: 5px;
}
.ad-link {
    font-size: 0.8rem;
    opacity: 0.7;
}
.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status-active {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}
.status-inactive {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}
.badge-outline {
    border: 1px solid rgba(255,255,255,0.1);
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 0.7rem;
}
.ad-time {
    font-size: 0.75rem;
    color: #888;
}
</style>

<?php require APPROOT . '/views/layouts/admin/footer.php'; ?>
