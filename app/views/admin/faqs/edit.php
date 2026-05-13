<?php require APPROOT . '/views/layouts/admin/header.php'; ?>

<div class="container p-6">
    <h1 class="text-2xl font-bold mb-4">Sửa FAQ</h1>

    <?php
        $isNew = empty($data['faq']);
    ?>
        <form action="<?php echo URLROOT; ?>/admin/AdminFaqs" method="post">
            <input type="hidden" name="__action" value="<?php echo $isNew ? 'createFaq' : 'updateFaq'; ?>">
            <?php if(!$isNew): ?><input type="hidden" name="id" value="<?php echo $data['faq']->id; ?>"><?php endif; ?>
        <div class="mb-2">
            <label for="faq_question" class="block mb-1">Câu hỏi</label>
            <input id="faq_question" type="text" name="question" class="w-full p-2 border rounded" value="<?php echo htmlspecialchars($data['faq']->question ?? ''); ?>">
        </div>
        <div class="mb-2">
            <label for="faq_category" class="block mb-1">Category (tùy chọn)</label>
            <?php if(!empty($data['categories'])): ?>
                <select id="faq_category" name="category" class="w-full p-2 border rounded">
                    <option value="">(Không chọn)</option>
                    <?php foreach($data['categories'] as $c): ?>
                        <option value="<?php echo htmlspecialchars($c->slug); ?>" <?php echo (!empty($data['faq']->category) && $data['faq']->category === $c->slug) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c->title); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input type="text" name="category" class="w-full p-2 border rounded" value="<?php echo isset($data['faq']->category) ? htmlspecialchars($data['faq']->category) : ''; ?>">
            <?php endif; ?>
        </div>
        <div class="mb-2">
            <label for="faq_answer" class="block mb-1">Trả lời</label>
            <textarea id="faq_answer" name="answer" rows="6" class="w-full p-2 border rounded"><?php echo htmlspecialchars($data['faq']->answer ?? ''); ?></textarea>
        </div>
        <div class="mb-2">
            <label class="inline-flex items-center"><input type="checkbox" name="status" value="active" <?php echo (!empty($data['faq']) && $data['faq']->status === 'active') || $isNew ? 'checked' : ''; ?>> Hiển thị</label>
        </div>
        <div>
            <button class="btn btn-primary">Lưu</button>
            <a href="<?php echo URLROOT; ?>/admin/AdminFaqs" class="btn">Huỷ</a>
        </div>
    </form>
</div>

<?php require APPROOT . '/views/layouts/admin/footer.php'; ?>
