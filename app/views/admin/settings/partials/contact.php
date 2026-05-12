<?php
$s = $data['settings'];
$errors = $data['errors'] ?? [];
$mapUrl = trim($s['site_map_embed_url'] ?? '');
$showMapPreview = $mapUrl !== '' && filter_var($mapUrl, FILTER_VALIDATE_URL) !== false;
?>

<form action="<?php echo URLROOT; ?>/admin/settings/contact" method="POST" novalidate>
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($data['csrf_admin'] ?? ''); ?>">
    <input type="hidden" name="settings_section" value="contact">

    <div class="settings-section-header">
        <h3>Nội dung hiển thị trang Liên hệ</h3>
        <p>Tiêu đề, mô tả và nhãn khối thông tin.</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="contact_page_title" class="col-form-label">Tiêu đề trang</label>
                <input class="form-control <?php echo !empty($errors['contact_page_title']) ? 'is-invalid' : ''; ?>" type="text" id="contact_page_title" name="contact_page_title" value="<?php echo htmlspecialchars($s['contact_page_title'] ?? ''); ?>" maxlength="120">
                <?php if (!empty($errors['contact_page_title'])): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['contact_page_title']); ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="contact_sidebar_title" class="col-form-label">Tiêu đề cột phải (thông tin)</label>
                <input class="form-control <?php echo !empty($errors['contact_sidebar_title']) ? 'is-invalid' : ''; ?>" type="text" id="contact_sidebar_title" name="contact_sidebar_title" value="<?php echo htmlspecialchars($s['contact_sidebar_title'] ?? ''); ?>" maxlength="120">
                <?php if (!empty($errors['contact_sidebar_title'])): ?>
                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['contact_sidebar_title']); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="form-group mb-3">
        <label for="contact_page_intro" class="col-form-label">Đoạn giới thiệu dưới tiêu đề</label>
        <textarea class="form-control <?php echo !empty($errors['contact_page_intro']) ? 'is-invalid' : ''; ?>" id="contact_page_intro" name="contact_page_intro" rows="3" maxlength="500"><?php echo htmlspecialchars($s['contact_page_intro'] ?? ''); ?></textarea>
        <?php if (!empty($errors['contact_page_intro'])): ?>
            <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['contact_page_intro']); ?></div>
        <?php endif; ?>
    </div>

    <hr class="settings-divider">

    <div class="settings-section-header">
        <h3>Thông tin liên hệ &amp; bản đồ</h3>
        <p>Hotline, email, địa chỉ và URL nhúng Google Maps (theo đặc tả trang Liên hệ).</p>
    </div>

    <div class="form-group mb-3">
        <label for="site_about_snippet" class="col-form-label">Mô tả ngắn / meta site (footer)</label>
        <textarea class="form-control" id="site_about_snippet" name="site_about_snippet" rows="3" maxlength="300"><?php echo htmlspecialchars($s['site_about_snippet'] ?? ''); ?></textarea>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="site_contact_email" class="col-form-label">Email liên hệ</label>
                <div class="input-icon-group">
                    <span><i class="ti-email"></i></span>
                    <input class="form-control <?php echo !empty($errors['site_contact_email']) ? 'is-invalid' : ''; ?>" type="email" id="site_contact_email" name="site_contact_email" value="<?php echo htmlspecialchars($s['site_contact_email'] ?? ''); ?>" maxlength="100" required>
                </div>
                <?php if (!empty($errors['site_contact_email'])): ?>
                    <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['site_contact_email']); ?></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="site_hotline" class="col-form-label">Hotline</label>
                <div class="input-icon-group">
                    <span><i class="ti-mobile"></i></span>
                    <input class="form-control <?php echo !empty($errors['site_hotline']) ? 'is-invalid' : ''; ?>" type="text" id="site_hotline" name="site_hotline" value="<?php echo htmlspecialchars($s['site_hotline'] ?? ''); ?>" maxlength="30" required>
                </div>
                <?php if (!empty($errors['site_hotline'])): ?>
                    <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['site_hotline']); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="form-group mb-3">
        <label for="site_address" class="col-form-label">Địa chỉ</label>
        <div class="input-icon-group">
            <span><i class="ti-location-pin"></i></span>
            <input class="form-control <?php echo !empty($errors['site_address']) ? 'is-invalid' : ''; ?>" type="text" id="site_address" name="site_address" value="<?php echo htmlspecialchars($s['site_address'] ?? ''); ?>" maxlength="255" required>
        </div>
        <?php if (!empty($errors['site_address'])): ?>
            <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['site_address']); ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group mb-2">
        <label for="site_map_embed_url" class="col-form-label">URL nhúng Google Maps</label>
        <input class="form-control <?php echo !empty($errors['site_map_embed_url']) ? 'is-invalid' : ''; ?>" type="url" id="site_map_embed_url" name="site_map_embed_url" value="<?php echo htmlspecialchars($mapUrl); ?>" placeholder="https://www.google.com/maps?q=...&output=embed" maxlength="500">
        <?php if (!empty($errors['site_map_embed_url'])): ?>
            <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['site_map_embed_url']); ?></div>
        <?php endif; ?>
    </div>

    <div class="map-preview-block mb-3" id="mapPreviewBlock">
        <div class="map-preview-placeholder <?php echo $showMapPreview ? 'd-none' : ''; ?>" id="mapPreviewPlaceholder">
            <i class="ti-map"></i>
            <p>Bản xem trước sẽ hiển thị tại đây khi URL hợp lệ.</p>
        </div>
        <iframe
            id="mapPreviewFrame"
            class="<?php echo $showMapPreview ? '' : 'd-none'; ?>"
            src="<?php echo $showMapPreview ? htmlspecialchars($mapUrl) : ''; ?>"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Xem trước Google Maps"
        ></iframe>
    </div>

    <div class="settings-actions">
        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="<?php echo URLROOT; ?>/admin/settings/contact" class="btn btn-outline-light settings-actions-cancel">Hủy</a>
    </div>
</form>
