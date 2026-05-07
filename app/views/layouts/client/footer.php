<?php
$publicSettings = $data['public_settings'] ?? [];
$siteLogoText = $publicSettings['site_logo_text'] ?? 'G-SERVER';
$siteLogoImageFile = basename((string) ($publicSettings['site_logo_image'] ?? ''));
$siteLogoImageUrl = $siteLogoImageFile !== '' ? URLROOT . '/uploads/branding/' . rawurlencode($siteLogoImageFile) : '';
$siteHotline = $publicSettings['site_hotline'] ?? '0123 456 789';
$siteEmail = $publicSettings['site_contact_email'] ?? 'contact@gameserver.vn';
$siteAddress = $publicSettings['site_address'] ?? '268 Lý Thường Kiệt, Q10, TP.HCM';
$siteAboutSnippet = $publicSettings['site_about_snippet'] ?? 'Nền tảng cho thuê Game Server hàng đầu Việt Nam. Cung cấp máy chủ chất lượng cao, ổn định và bảo mật tối đa cho cộng đồng game thủ.';
?>
    </main>
    <footer class="bg-gray-900 border-t border-gray-800 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="col-span-1 md:col-span-1">
                    <a href="<?php echo URLROOT; ?>" class="flex items-center gap-3 mb-6">
                        <?php if ($siteLogoImageUrl !== ''): ?>
                            <img src="<?php echo htmlspecialchars($siteLogoImageUrl); ?>" alt="Logo thương hiệu" class="w-8 h-8 rounded object-cover">
                        <?php else: ?>
                            <div class="w-8 h-8 bg-gradient-to-br from-cyan-500 to-purple-600 rounded flex items-center justify-center">
                                <i class="fa-solid fa-server text-white text-sm"></i>
                            </div>
                        <?php endif; ?>
                        <span class="font-bold text-xl tracking-tighter text-white"><?php echo htmlspecialchars($siteLogoText); ?></span>
                    </a>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        <?php echo htmlspecialchars($siteAboutSnippet); ?>
                    </p>
                    <div class="flex gap-4 mt-6">
                        <a href="#" class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-cyan-500 hover:text-white transition-all"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-cyan-500 hover:text-white transition-all"><i class="fa-brands fa-discord"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-cyan-500 hover:text-white transition-all"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-6">Liên kết nhanh</h3>
                    <ul class="space-y-4 text-sm text-gray-400">
                        <li><a href="<?php echo URLROOT; ?>" class="hover:text-cyan-400 transition-colors">Trang chủ</a></li>
                        <li><a href="<?php echo URLROOT; ?>/products" class="hover:text-cyan-400 transition-colors">Sản phẩm</a></li>
                        <li><a href="<?php echo URLROOT; ?>/news" class="hover:text-cyan-400 transition-colors">Tin tức</a></li>
                        <li><a href="<?php echo URLROOT; ?>/pages/faq" class="hover:text-cyan-400 transition-colors">Hỏi đáp</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-6">Dịch vụ</h3>
                    <ul class="space-y-4 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-cyan-400 transition-colors">Minecraft Hosting</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors">GTA V Roleplay</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors">CS:GO Server</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition-colors">Dịch vụ Anti-DDoS</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-6">Liên hệ</h3>
                    <ul class="space-y-4 text-sm text-gray-400">
                        <li class="flex items-start gap-3">
                            <i class="fa-solid fa-location-dot text-cyan-500 mt-1"></i>
                            <span><?php echo htmlspecialchars($siteAddress); ?></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-phone text-cyan-500"></i>
                            <span><?php echo htmlspecialchars($siteHotline); ?></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-solid fa-envelope text-cyan-500"></i>
                            <span><?php echo htmlspecialchars($siteEmail); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-16 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITENAME; ?>. All rights reserved.</p>
                <div class="flex gap-8">
                    <a href="#" class="hover:text-gray-300">Chính sách bảo mật</a>
                    <a href="#" class="hover:text-gray-300">Điều khoản sử dụng</a>
                </div>
            </div>
        </div>
    </footer>
    <script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>
</html>

