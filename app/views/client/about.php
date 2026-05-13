<?php require APPROOT . '/views/layouts/client/header.php'; ?>

<?php
$ps = $data['public_settings'] ?? [];
$headingPrefix = trim((string) ($ps['about_heading_prefix'] ?? 'Về'));
$headingHighlight = trim((string) ($ps['about_heading_highlight'] ?? 'Chúng Tôi'));
$p1 = trim((string) ($ps['about_para1'] ?? ''));
$p2 = trim((string) ($ps['about_para2'] ?? ''));
?>

<div class="bg-gray-950 py-24 min-h-[70vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center space-y-8">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white">
                <?php echo htmlspecialchars($headingPrefix); ?> <span class="bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent"><?php echo htmlspecialchars($headingHighlight); ?></span>
            </h1>
            <div class="max-w-3xl mx-auto space-y-6 text-lg text-gray-400 leading-relaxed">
                <?php if ($p1 !== ''): ?>
                    <p><?php echo htmlspecialchars($p1); ?></p>
                <?php endif; ?>
                <?php if ($p2 !== ''): ?>
                    <p><?php echo htmlspecialchars($p2); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/client/footer.php'; ?>
