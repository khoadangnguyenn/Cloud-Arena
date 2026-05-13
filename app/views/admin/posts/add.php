<?php require APPROOT . '/views/layouts/admin/header.php'; ?>

<div class="content-header">
    <div class="header-left">
        <h1>Thêm tin tức mới</h1>
        <p>Tạo một bài viết mới để đăng lên trang tin tức.</p>
    </div>
    <div class="header-right">
        <a href="<?php echo URLROOT; ?>/admin/posts" class="btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="admin-form-container">
    <form action="<?php echo URLROOT; ?>/admin/posts/add" method="POST" enctype="multipart/form-data" class="admin-form" id="post-form">
        <div class="form-row">
            <div class="form-group full">
                <label for="title">Tiêu đề bài viết <span class="required">*</span></label>
                <input type="text" name="title" id="title" required placeholder="Nhập tiêu đề...">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label for="category_id">Danh mục</label>
                <select name="category_id" id="category_id">
                    <option value="">-- Chọn danh mục --</option>
                    <?php if(!empty($data['categories'])): ?>
                        <?php foreach($data['categories'] as $cat): ?>
                            <option value="<?php echo $cat->id; ?>"><?php echo htmlspecialchars($cat->name); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group half">
                <label for="status">Trạng thái</label>
                <select name="status" id="status">
                    <option value="published">Công khai</option>
                    <option value="draft">Bản nháp</option>
                </select>
            </div>
        </div>

        <div class="form-group full">
            <label for="content">Nội dung bài viết <span class="required">*</span></label>
            <div id="editor-container">
                <textarea name="content" id="content" class="rich-text-editor" placeholder="Nhập nội dung bài viết..."></textarea>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group full">
                <label>Ảnh đại diện (Thumbnail)</label>
                <div id="thumbnail-dropzone" class="dropzone"></div>
                <input type="hidden" name="thumbnail" id="thumbnail-path">
            </div>
        </div>

        <!-- Breaking News & Schedule -->
        <div class="publishing-section">
            <h3><i class="fas fa-bolt"></i> Cấu hình đăng bài</h3>
            <div class="form-row">
                <div class="form-group third">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_breaking" id="is_breaking" value="1">
                        <span class="checkmark"></span>
                        Tin nóng (Breaking News)
                    </label>
                </div>
                <div class="form-group third">
                    <label for="breaking_until">Tin nóng đến</label>
                    <input type="datetime-local" name="breaking_until" id="breaking_until">
                </div>
                <div class="form-group third">
                    <label for="publish_at">Hẹn giờ đăng</label>
                    <input type="datetime-local" name="publish_at" id="publish_at">
                </div>
            </div>
        </div>

        <!-- SEO Section -->
        <div class="seo-section">
            <h3><i class="fas fa-search"></i> Cấu hình SEO</h3>
            <div class="seo-preview" id="seo-preview">
                <div class="seo-preview-title" id="seo-preview-title">Tiêu đề bài viết - <?php echo SITENAME; ?></div>
                <div class="seo-preview-url"><?php echo URLROOT; ?>/posts/show/tieu-de-bai-viet</div>
                <div class="seo-preview-desc" id="seo-preview-desc">Mô tả bài viết sẽ hiển thị tại đây...</div>
            </div>
            <div class="form-group full">
                <label for="meta_keywords">Từ khóa SEO (ngăn cách bởi dấu phẩy)</label>
                <input type="text" name="meta_keywords" id="meta_keywords" placeholder="ví dụ: game, server, hosting...">
            </div>
            <div class="form-group full">
                <label for="meta_description">Mô tả SEO <span class="seo-counter" id="desc-counter">0/160</span></label>
                <textarea name="meta_description" id="meta_description" placeholder="Nhập mô tả ngắn để hiển thị trên Google..." maxlength="200"></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary" id="btn-save">
                <i class="fas fa-save"></i> Lưu bài viết
            </button>
        </div>
    </form>
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
    border: none;
    cursor: pointer;
}

.btn-primary:hover {
    background: #00d2ff;
    transform: translateY(-2px);
}

.admin-form-container {
    background: #1a1a1a;
    border-radius: 12px;
    padding: 30px;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.admin-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: flex;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group.full { width: 100%; }
.form-group.half { width: 50%; }
.form-group.third { width: 33.33%; }

.form-group label {
    font-size: 0.9rem;
    color: #aaa;
    font-weight: 600;
}

.required { color: #ef4444; }

.form-group input, 
.form-group select, 
.form-group textarea {
    background: #222;
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 12px;
    border-radius: 8px;
    color: #fff;
    outline: none;
    transition: all 0.3s ease;
}

.form-group input:focus, 
.form-group select:focus, 
.form-group textarea:focus {
    border-color: #3a7bd5;
    box-shadow: 0 0 10px rgba(58, 123, 213, 0.1);
}

.form-group textarea {
    min-height: 150px;
    resize: vertical;
}

.form-group textarea.rich-text-editor {
    min-height: 400px;
}

/* Checkbox */
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    color: #ccc;
    padding-top: 28px;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #3a7bd5;
}

/* Publishing section */
.publishing-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.publishing-section h3 {
    margin-bottom: 20px;
    font-size: 1.1rem;
    color: #f59e0b;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* SEO Section */
.seo-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.seo-section h3 {
    margin-bottom: 20px;
    font-size: 1.1rem;
    color: #3a7bd5;
    display: flex;
    align-items: center;
    gap: 8px;
}

.seo-preview {
    background: #222;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.seo-preview-title {
    color: #8ab4f8;
    font-size: 1.1rem;
    margin-bottom: 4px;
}

.seo-preview-url {
    color: #bdc1c6;
    font-size: 0.8rem;
    margin-bottom: 4px;
}

.seo-preview-desc {
    color: #969ba1;
    font-size: 0.85rem;
    line-height: 1.5;
}

.seo-counter {
    float: right;
    font-weight: 400;
    font-size: 0.75rem;
}

.form-actions {
    margin-top: 30px;
    display: flex;
    justify-content: flex-end;
}

.btn-secondary {
    background: #333;
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-secondary:hover {
    background: #444;
}

.dropzone {
    background: #222 !important;
    border: 2px dashed rgba(255,255,255,0.1) !important;
    border-radius: 12px !important;
    color: #aaa !important;
    min-height: 150px !important;
}
.dz-message {
    font-weight: 600 !important;
}

/* CKEditor dark theme override */
.ck.ck-editor__main > .ck-editor__editable {
    background: #222 !important;
    color: #e0e0e0 !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    min-height: 350px;
}
.ck.ck-toolbar {
    background: #2a2a2a !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}
.ck.ck-button, .ck.ck-button.ck-on {
    color: #ccc !important;
}
</style>

<!-- CKEditor 5 CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<!-- Dropzone CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>

<script>
    // Initialize CKEditor 5
    let editorInstance = null;
    ClassicEditor
        .create(document.querySelector('#content'), {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo' ]
        })
        .then(editor => {
            editorInstance = editor;
        })
        .catch(error => {
            console.error('CKEditor error:', error);
        });

    // Initialize Dropzone
    Dropzone.autoDiscover = false;
    const myDropzone = new Dropzone("#thumbnail-dropzone", {
        url: "<?php echo URLROOT; ?>/admin/posts/uploadImage",
        paramName: "file",
        maxFilesize: 2, // MB
        maxFiles: 1,
        acceptedFiles: "image/*",
        addRemoveLinks: true,
        dictDefaultMessage: "<i class='fas fa-cloud-upload-alt' style='font-size:2rem;margin-bottom:10px;display:block;color:#666'></i> Kéo thả ảnh vào đây hoặc click để chọn",
        success: function(file, response) {
            try {
                const res = typeof response === 'string' ? JSON.parse(response) : response;
                if(res.success) {
                    document.getElementById('thumbnail-path').value = res.filename;
                }
            } catch(e) {
                console.error('Dropzone parse error:', e);
            }
        },
        error: function(file, response) {
            alert("Lỗi tải ảnh: " + (typeof response === 'string' ? response : response.message || 'Unknown error'));
        }
    });

    // SEO Preview
    const titleInput = document.getElementById('title');
    const descInput = document.getElementById('meta_description');
    const descCounter = document.getElementById('desc-counter');
    
    if (titleInput) {
        titleInput.addEventListener('input', function() {
            document.getElementById('seo-preview-title').textContent = this.value || 'Tiêu đề bài viết' + ' - <?php echo SITENAME; ?>';
        });
    }
    
    if (descInput) {
        descInput.addEventListener('input', function() {
            document.getElementById('seo-preview-desc').textContent = this.value || 'Mô tả bài viết sẽ hiển thị tại đây...';
            descCounter.textContent = this.value.length + '/160';
            descCounter.style.color = this.value.length > 160 ? '#ef4444' : (this.value.length >= 120 ? '#22c55e' : '#888');
        });
    }

    // Breaking news toggle
    const breakingCheckbox = document.getElementById('is_breaking');
    const breakingUntil = document.getElementById('breaking_until');
    if (breakingCheckbox && breakingUntil) {
        breakingUntil.parentElement.style.opacity = '0.4';
        breakingCheckbox.addEventListener('change', function() {
            breakingUntil.parentElement.style.opacity = this.checked ? '1' : '0.4';
            breakingUntil.disabled = !this.checked;
        });
    }
</script>

<?php require APPROOT . '/views/layouts/admin/footer.php'; ?>
