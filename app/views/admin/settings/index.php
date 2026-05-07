<?php require APPROOT . '/views/layouts/admin/header.php'; ?>

<?php
$mapUrl = trim($data['settings']['site_map_embed_url'] ?? '');
$showMapPreview = $mapUrl !== '' && filter_var($mapUrl, FILTER_VALIDATE_URL) !== false;
$logoImageFile = basename(trim($data['settings']['site_logo_image'] ?? ''));
$logoImageUrl = $logoImageFile !== '' ? URLROOT . '/uploads/branding/' . rawurlencode($logoImageFile) : '';
?>

<div class="row g-3">
    <div class="col-12">
        <section class="card panel-card">
            <div class="card-body">
                <div class="panel-header">
                    <h2 class="panel-title">Cài đặt thông tin công khai</h2>
                    <span class="badge badge-soft-primary">Thông tin hiển thị</span>
                </div>

                <?php if (!empty($data['flash'])): ?>
                    <div class="alert alert-<?php echo $data['flash']['type'] === 'success' ? 'success' : 'danger'; ?>">
                        <?php echo htmlspecialchars($data['flash']['message']); ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo URLROOT; ?>/admin/settings" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="settings-section-header">
                        <h3>Phần A: Nhận diện thương hiệu</h3>
                        <p>Cấu hình nhận diện thương hiệu cho khu vực công khai.</p>
                    </div>

                    <div class="settings-upload-zone mb-3" id="brandingUploadZone">
                        <input type="file" id="branding_asset" name="branding_asset" accept=".png,.svg,.ico" hidden>
                        <div class="settings-upload-icon"><i class="ti-upload"></i></div>
                        <strong>Kéo thả logo/favicon vào đây</strong>
                        <p>Hỗ trợ PNG/SVG/ICO (tối đa 2MB)</p>
                        <button type="button" class="btn btn-outline-light btn-sm" id="brandingUploadBrowse">Chọn tệp</button>
                        <small id="brandingUploadFilename"></small>
                    </div>
                    <?php if ($logoImageUrl !== ''): ?>
                        <div class="mb-3">
                            <label class="form-label d-block">Logo hiện tại</label>
                            <img src="<?php echo htmlspecialchars($logoImageUrl); ?>" alt="Logo hiện tại" class="settings-logo-preview">
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($data['errors']['branding_asset'])): ?>
                        <div class="alert alert-danger py-2"><?php echo htmlspecialchars($data['errors']['branding_asset']); ?></div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="site_logo_text" class="col-form-label">Tên logo hiển thị</label>
                                <input
                                    class="form-control <?php echo !empty($data['errors']['site_logo_text']) ? 'is-invalid' : ''; ?>"
                                    type="text"
                                    id="site_logo_text"
                                    name="site_logo_text"
                                    value="<?php echo htmlspecialchars($data['settings']['site_logo_text'] ?? ''); ?>"
                                    maxlength="60"
                                    required
                                >
                                <?php if (!empty($data['errors']['site_logo_text'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($data['errors']['site_logo_text']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="site_about_snippet" class="col-form-label">Mô tả ngắn trang công khai</label>
                                <textarea
                                    class="form-control"
                                    id="site_about_snippet"
                                    name="site_about_snippet"
                                    rows="3"
                                    maxlength="300"
                                ><?php echo htmlspecialchars($data['settings']['site_about_snippet'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <hr class="settings-divider">

                    <div class="settings-section-header">
                        <h3>Phần B: Thông tin liên hệ</h3>
                        <p>Cập nhật thông tin liên hệ và vị trí hiển thị.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="site_contact_email" class="col-form-label">Email liên hệ</label>
                                <div class="input-icon-group">
                                    <span><i class="ti-email"></i></span>
                                    <input
                                        class="form-control <?php echo !empty($data['errors']['site_contact_email']) ? 'is-invalid' : ''; ?>"
                                        type="email"
                                        id="site_contact_email"
                                        name="site_contact_email"
                                        value="<?php echo htmlspecialchars($data['settings']['site_contact_email'] ?? ''); ?>"
                                        maxlength="100"
                                        required
                                    >
                                </div>
                                <?php if (!empty($data['errors']['site_contact_email'])): ?>
                                    <div class="invalid-feedback d-block"><?php echo htmlspecialchars($data['errors']['site_contact_email']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="site_hotline" class="col-form-label">Hotline</label>
                                <div class="input-icon-group">
                                    <span><i class="ti-mobile"></i></span>
                                    <input
                                        class="form-control <?php echo !empty($data['errors']['site_hotline']) ? 'is-invalid' : ''; ?>"
                                        type="text"
                                        id="site_hotline"
                                        name="site_hotline"
                                        value="<?php echo htmlspecialchars($data['settings']['site_hotline'] ?? ''); ?>"
                                        maxlength="30"
                                        required
                                    >
                                </div>
                                <?php if (!empty($data['errors']['site_hotline'])): ?>
                                    <div class="invalid-feedback d-block"><?php echo htmlspecialchars($data['errors']['site_hotline']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="site_address" class="col-form-label">Địa chỉ</label>
                        <div class="input-icon-group">
                            <span><i class="ti-location-pin"></i></span>
                            <input
                                class="form-control <?php echo !empty($data['errors']['site_address']) ? 'is-invalid' : ''; ?>"
                                type="text"
                                id="site_address"
                                name="site_address"
                                value="<?php echo htmlspecialchars($data['settings']['site_address'] ?? ''); ?>"
                                maxlength="255"
                                required
                            >
                        </div>
                        <?php if (!empty($data['errors']['site_address'])): ?>
                            <div class="invalid-feedback d-block"><?php echo htmlspecialchars($data['errors']['site_address']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group mb-2">
                        <label for="site_map_embed_url" class="col-form-label">URL nhúng Google Maps</label>
                        <input
                            class="form-control <?php echo !empty($data['errors']['site_map_embed_url']) ? 'is-invalid' : ''; ?>"
                            type="url"
                            id="site_map_embed_url"
                            name="site_map_embed_url"
                            value="<?php echo htmlspecialchars($mapUrl); ?>"
                            placeholder="https://www.google.com/maps?q=...&output=embed"
                            maxlength="500"
                        >
                        <?php if (!empty($data['errors']['site_map_embed_url'])): ?>
                            <div class="invalid-feedback d-block"><?php echo htmlspecialchars($data['errors']['site_map_embed_url']); ?></div>
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
                        <a href="<?php echo URLROOT; ?>/admin/settings" class="btn btn-outline-light">Hủy</a>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<?php require APPROOT . '/views/layouts/admin/footer.php'; ?>
