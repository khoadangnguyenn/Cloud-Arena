<?php require APPROOT . '/views/layouts/client/header.php'; ?>

<?php
$publicSettings = $data['public_settings'] ?? [];
$siteHotline = $publicSettings['site_hotline'] ?? '0123 456 789';
$siteEmail = $publicSettings['site_contact_email'] ?? 'contact@gameserver.vn';
$siteAddress = $publicSettings['site_address'] ?? '268 Lý Thường Kiệt, Q10, TP.HCM';
$siteMapEmbedUrl = $publicSettings['site_map_embed_url'] ?? 'https://www.google.com/maps?q=268+Ly+Thuong+Kiet+Q10+TPHCM&output=embed';
?>

<div class="bg-gray-950 py-20 min-h-[80vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-gray-900/50 backdrop-blur-xl border border-white/5 rounded-3xl p-8 md:p-10">
                <div class="mb-10">
                    <h1 class="text-4xl font-extrabold text-white mb-3">Liên Hệ</h1>
                    <p class="text-gray-400">Gửi ticket hỗ trợ cho chúng tôi. Đội ngũ sẽ phản hồi sớm nhất có thể.</p>
                </div>

                <?php if (!empty($data['success_message'])): ?>
                    <div class="mb-6 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-emerald-300">
                        <?php echo htmlspecialchars($data['success_message']); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($data['errors']['general'])): ?>
                    <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-red-300">
                        <?php echo htmlspecialchars($data['errors']['general']); ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo URLROOT; ?>/pages/contact" method="POST" class="space-y-6" novalidate>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Họ tên</label>
                            <input
                                type="text"
                                name="name"
                                value="<?php echo htmlspecialchars($data['form']['name'] ?? ''); ?>"
                                class="w-full px-4 py-3 bg-gray-800/50 border <?php echo !empty($data['errors']['name']) ? 'border-red-500' : 'border-gray-700'; ?> rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-cyan-500/50"
                                placeholder="Nhập họ tên"
                                maxlength="100"
                                required
                            >
                            <?php if (!empty($data['errors']['name'])): ?>
                                <p class="mt-2 text-sm text-red-400"><?php echo htmlspecialchars($data['errors']['name']); ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                            <input
                                type="email"
                                name="email"
                                value="<?php echo htmlspecialchars($data['form']['email'] ?? ''); ?>"
                                class="w-full px-4 py-3 bg-gray-800/50 border <?php echo !empty($data['errors']['email']) ? 'border-red-500' : 'border-gray-700'; ?> rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-cyan-500/50"
                                placeholder="example@email.com"
                                maxlength="100"
                                required
                            >
                            <?php if (!empty($data['errors']['email'])): ?>
                                <p class="mt-2 text-sm text-red-400"><?php echo htmlspecialchars($data['errors']['email']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Chủ đề</label>
                        <input
                            type="text"
                            name="subject"
                            value="<?php echo htmlspecialchars($data['form']['subject'] ?? ''); ?>"
                            class="w-full px-4 py-3 bg-gray-800/50 border <?php echo !empty($data['errors']['subject']) ? 'border-red-500' : 'border-gray-700'; ?> rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-cyan-500/50"
                            placeholder="Ví dụ: Cần tư vấn gói server Minecraft"
                            maxlength="255"
                        >
                        <?php if (!empty($data['errors']['subject'])): ?>
                            <p class="mt-2 text-sm text-red-400"><?php echo htmlspecialchars($data['errors']['subject']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Nội dung</label>
                        <textarea
                            rows="6"
                            name="message"
                            class="w-full px-4 py-3 bg-gray-800/50 border <?php echo !empty($data['errors']['message']) ? 'border-red-500' : 'border-gray-700'; ?> rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-cyan-500/50"
                            placeholder="Mô tả chi tiết vấn đề của bạn..."
                            minlength="10"
                            required
                        ><?php echo htmlspecialchars($data['form']['message'] ?? ''); ?></textarea>
                        <?php if (!empty($data['errors']['message'])): ?>
                            <p class="mt-2 text-sm text-red-400"><?php echo htmlspecialchars($data['errors']['message']); ?></p>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-cyan-500/25 active:scale-[0.98]">
                        Gửi ticket ngay
                    </button>
                </form>
            </div>

            <div class="space-y-6">
                <div class="bg-gray-900/40 border border-white/5 rounded-3xl p-6">
                    <h2 class="text-xl font-bold text-white mb-5">Thông tin liên hệ</h2>
                    <ul class="space-y-4 text-sm text-gray-300">
                        <li class="flex gap-3">
                            <i class="fa-solid fa-location-dot text-cyan-400 mt-1"></i>
                            <span><?php echo htmlspecialchars($siteAddress); ?></span>
                        </li>
                        <li class="flex gap-3">
                            <i class="fa-solid fa-phone text-cyan-400 mt-1"></i>
                            <span><?php echo htmlspecialchars($siteHotline); ?></span>
                        </li>
                        <li class="flex gap-3">
                            <i class="fa-solid fa-envelope text-cyan-400 mt-1"></i>
                            <span><?php echo htmlspecialchars($siteEmail); ?></span>
                        </li>
                    </ul>
                </div>

                <div class="bg-gray-900/40 border border-white/5 rounded-3xl p-3">
                    <iframe
                        src="<?php echo htmlspecialchars($siteMapEmbedUrl); ?>"
                        class="w-full h-72 rounded-2xl border-0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Bản đồ văn phòng Cloud Arena"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/client/footer.php'; ?>
