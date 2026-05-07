<?php
$statusLabels = [
    'unread' => 'Chưa đọc',
    'read' => 'Đã đọc',
    'replied' => 'Đã phản hồi'
];
$priorityLabels = [
    'low' => 'Thấp',
    'normal' => 'Bình thường',
    'high' => 'Cao',
    'urgent' => 'Khẩn cấp'
];
?>

<?php if (!$selectedContact): ?>
    <div class="panel-header">
        <h2 class="panel-title">Chi tiết ticket</h2>
    </div>
    <p class="panel-muted mb-0">Chọn một ticket từ danh sách để xem nội dung chi tiết.</p>
<?php else: ?>
    <div class="panel-header align-items-start ticket-detail-header">
        <div>
            <h2 class="panel-title">Chi tiết ticket</h2>
        </div>
        <form action="<?php echo URLROOT; ?>/admincontacts/delete/<?php echo (int) $selectedContact->user_id; ?>/<?php echo (int) $selectedContact->contact_id; ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa ticket này?');">
            <input type="hidden" name="redirect_query" value="<?php echo htmlspecialchars($queryString); ?>">
            <button type="submit" class="btn btn-sm btn-danger ticket-delete-btn" title="Xóa ticket" aria-label="Xóa ticket">
                <i class="ti-trash"></i>
            </button>
        </form>
    </div>

    <div class="ticket-user-meta mb-3">
        <strong><?php echo htmlspecialchars($selectedContact->name); ?></strong>
        <small>
            <?php echo htmlspecialchars($selectedContact->email); ?>
            | <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($selectedContact->created_at))); ?>
        </small>
    </div>

    <div class="ticket-conversation mb-3">
        <div class="ticket-bubble ticket-bubble-customer">
            <div class="ticket-bubble-author">Khách hàng</div>
            <div class="ticket-bubble-content"><?php echo nl2br(htmlspecialchars($selectedContact->message)); ?></div>
        </div>
        <?php if (!empty($selectedContact->admin_reply)): ?>
            <div class="ticket-bubble ticket-bubble-admin">
                <div class="ticket-bubble-author">Quản trị viên phản hồi</div>
                <div class="ticket-bubble-content"><?php echo nl2br(htmlspecialchars($selectedContact->admin_reply)); ?></div>
                <?php if (!empty($selectedContact->replied_at)): ?>
                    <small class="d-block mt-2">
                        <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($selectedContact->replied_at))); ?>
                    </small>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-md-6">
            <form
                action="<?php echo URLROOT; ?>/admincontacts/updateStatus/<?php echo (int) $selectedContact->user_id; ?>/<?php echo (int) $selectedContact->contact_id; ?>"
                method="POST"
                class="ticket-auto-save-form"
                data-admin-autosave="true"
                data-toast-success="Trạng thái đã cập nhật (tự động)."
            >
                <input type="hidden" name="redirect_query" value="<?php echo htmlspecialchars($queryString); ?>">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select" data-admin-autosave-input="true" required>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?php echo htmlspecialchars($status); ?>" <?php echo $selectedContact->status === $status ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($statusLabels[$status] ?? ucfirst($status)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <div class="col-md-6">
            <form
                action="<?php echo URLROOT; ?>/admincontacts/updatePriority/<?php echo (int) $selectedContact->user_id; ?>/<?php echo (int) $selectedContact->contact_id; ?>"
                method="POST"
                class="ticket-auto-save-form"
                data-admin-autosave="true"
                data-toast-success="Ưu tiên đã cập nhật (tự động)."
            >
                <input type="hidden" name="redirect_query" value="<?php echo htmlspecialchars($queryString); ?>">
                <label class="form-label">Ưu tiên</label>
                <select name="priority" class="form-select" data-admin-autosave-input="true" required>
                    <?php foreach ($priorities as $priority): ?>
                        <option value="<?php echo htmlspecialchars($priority); ?>" <?php echo $selectedContact->priority === $priority ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($priorityLabels[$priority] ?? ucfirst($priority)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <form action="<?php echo URLROOT; ?>/admincontacts/reply/<?php echo (int) $selectedContact->user_id; ?>/<?php echo (int) $selectedContact->contact_id; ?>" method="POST" class="mb-1">
        <input type="hidden" name="redirect_query" value="<?php echo htmlspecialchars($queryString); ?>">
        <label for="reply_message" class="form-label">Phản hồi quản trị viên</label>
        <textarea
            id="reply_message"
            name="reply_message"
            rows="4"
            class="form-control"
            placeholder="Nhập nội dung phản hồi..."
            required
        ><?php echo htmlspecialchars($selectedContact->admin_reply ?? ''); ?></textarea>
        <button type="submit" class="btn btn-primary mt-2">
            Gửi phản hồi
        </button>
    </form>
<?php endif; ?>
