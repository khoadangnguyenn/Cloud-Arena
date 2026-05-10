<?php require APPROOT . '/views/layouts/client/header.php'; ?>

<?php
$featuredProducts = is_array($data['featured_products'] ?? null) ? $data['featured_products'] : [];
$featuredReview   = $data['featured_review'] ?? null;

$reviewAuthor      = 'Khách hàng G-SERVER';
$reviewAvatarUrl   = '';
$reviewProductName = 'Gói dịch vụ gần đây';
$reviewComment     = 'Hệ thống ổn định, tốc độ tốt và hỗ trợ kỹ thuật rất nhanh.';
$reviewRating      = 5;

if ($featuredReview) {
    $candidateAuthor = trim((string) ($featuredReview->full_name ?? ''));
    if ($candidateAuthor === '') {
        $candidateAuthor = trim((string) ($featuredReview->username ?? ''));
    }
    if ($candidateAuthor !== '') {
        $reviewAuthor = $candidateAuthor;
    }

    $candidateProductName = trim((string) ($featuredReview->product_name ?? ''));
    if ($candidateProductName !== '') {
        $reviewProductName = $candidateProductName;
    }

    $candidateComment = trim((string) ($featuredReview->comment ?? ''));
    if ($candidateComment !== '') {
        $reviewComment = $candidateComment;
    }

    $reviewRating = max(1, min(5, (int) ($featuredReview->rating ?? 5)));

    $avatarRaw = trim((string) ($featuredReview->avatar ?? ''));
    if ($avatarRaw !== '') {
        if (strpos($avatarRaw, 'http://') === 0 || strpos($avatarRaw, 'https://') === 0) {
            $reviewAvatarUrl = $avatarRaw;
        } elseif (strpos($avatarRaw, '/uploads/') === 0) {
            $reviewAvatarUrl = URLROOT . $avatarRaw;
        } elseif (strpos($avatarRaw, 'uploads/') === 0) {
            $reviewAvatarUrl = URLROOT . '/' . ltrim($avatarRaw, '/');
        } else {
            $reviewAvatarUrl = URLROOT . '/uploads/avatars/' . ltrim($avatarRaw, '/');
        }
    }
}

if (function_exists('mb_strlen') && mb_strlen($reviewComment) > 140) {
    $reviewComment = mb_substr($reviewComment, 0, 137) . '...';
} elseif (strlen($reviewComment) > 140) {
    $reviewComment = substr($reviewComment, 0, 137) . '...';
}

$tones = [
    ['chip' => 'text-cyan-300 border-cyan-500/40 bg-cyan-500/10',    'css' => 'product-card--cyan'],
    ['chip' => 'text-violet-300 border-violet-500/40 bg-violet-500/10', 'css' => 'product-card--violet'],
    ['chip' => 'text-pink-300 border-pink-500/40 bg-pink-500/10',    'css' => 'product-card--pink'],
    ['chip' => 'text-emerald-300 border-emerald-500/40 bg-emerald-500/10', 'css' => 'product-card--emerald'],
];
?>
<div class="landing-scene">
     <div class="landing-scene__image"></div>
     <div class="landing-scene__fade"></div>
        <!-- Hero Section -->
        <section class="relative overflow-hidden hero-scene" id="hero-parallax">
            <div class="parallax-layer" data-speed="0.08"></div>
            <div class="absolute -top-32 -left-16 w-80 h-80 rounded-full bg-cyan-500/10 blur-3xl parallax-layer" data-speed="0.22"></div>
            <div class="absolute top-20 -right-16 w-80 h-80 rounded-full bg-violet-500/10 blur-3xl parallax-layer" data-speed="0.16"></div>

            <div class="relative max-w-7xl mx-auto px-4 pt-32 pb-24 sm:px-6 lg:px-8">

                <!-- Title + CTA — full width, centered -->
                <div class="text-center space-y-7 max-w-3xl mx-auto">
                    <h1 class="text-4xl md:text-5xl xl:text-6xl 2xl:text-7xl font-extrabold tracking-tight">
                        <span class="bg-gradient-to-r from-cyan-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                            Game Server Hosting
                        </span>
                        <br />
                        <span class="text-white">Cho Mọi Game Thủ</span>
                    </h1>

                    <p class="text-lg text-gray-300 max-w-xl mx-auto leading-relaxed">
                        Máy chủ game chuyên nghiệp với hiệu năng cao, hỗ trợ modpack và quản lý dễ dàng.
                        Khởi động Server chỉ trong vài phút với công nghệ ảo hóa tiên tiến nhất.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a href="<?php echo URLROOT; ?>/products"
                        class="group px-8 py-4 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold rounded-xl hover:shadow-[0_0_40px_rgba(6,182,212,0.5)] transition-all transform hover:scale-105 active:scale-95">
                            Khám phá Gói Server
                            <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="#about-us"
                        class="px-8 py-4 border border-gray-700 text-gray-200 font-bold rounded-xl hover:bg-white/5 hover:border-cyan-400/40 transition-all">
                            Tìm hiểu thêm
                        </a>
                    </div>

                    <!-- Quick Resource Search -->
                    <form id="quick-resource-search" class="flex flex-col sm:flex-row items-center gap-3 max-w-xl mx-auto">
                        <div class="flex-shrink-0">
                            <select id="resource_type" data-admin-custom-select="true">
                                <option value="products">Sản phẩm</option>
                                <option value="news">Tin tức</option>
                            </select>
                        </div>
                        <div class="relative flex-1 w-full">
                            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input id="resource_keyword" type="text" maxlength="100"
                                   placeholder="Tìm kiếm sản phẩm, tin tức..."
                                   class="w-full bg-gray-900 border border-gray-700 text-gray-200 text-sm rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:border-cyan-500 placeholder-gray-500">
                        </div>
                        <button type="submit" class="flex-shrink-0 px-5 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity">
                            Tìm kiếm
                        </button>
                    </form>
                </div>

                <!-- Floating cards: absolute on desktop, stacked on mobile -->
                <div class="hero-cards-wrapper mt-8 grid grid-cols-1 sm:grid-cols-2 gap-6">

                    <!-- Card trái: Năng lực công nghệ -->
                    <aside class="hero-floating-card hero-card-left">
                        <p class="text-xs uppercase tracking-widest font-semibold text-cyan-300">Năng lực công nghệ</p>
                        <div class="mt-3 rounded-xl border border-cyan-500/20 bg-black/60 p-3">
                            <div class="flex items-center justify-between text-[11px] text-slate-400">
                                <span>CPU / RAM Monitor</span>
                                <span class="text-cyan-300">● Online</span>
                            </div>
                            <div class="mt-2 space-y-1.5">
                                <div class="h-1.5 rounded-full bg-slate-800 overflow-hidden">
                                    <span class="block h-full w-[74%] bg-gradient-to-r from-cyan-400 to-cyan-300 rounded-full"></span>
                                </div>
                                <div class="h-1.5 rounded-full bg-slate-800 overflow-hidden">
                                    <span class="block h-full w-[61%] bg-gradient-to-r from-violet-400 to-violet-300 rounded-full"></span>
                                </div>
                            </div>
                            <div class="mt-2.5 rounded-lg bg-black border border-slate-800 px-3 py-2 text-[11px] text-slate-300 font-mono leading-snug">
                                <p><span class="text-cyan-400">&gt;</span> start minecraft-node-04</p>
                                <p class="text-emerald-300">Server started in 1.9s ✓</p>
                            </div>
                        </div>
                    </aside>

                    <!-- Card phải: Social Proof -->
                    <aside class="hero-floating-card hero-floating-card--delayed hero-card-right">
                        <p class="text-xs uppercase tracking-widest font-semibold text-violet-300">Review gần đây</p>
                        <div class="flex items-center gap-1 mt-3">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fa-solid fa-star text-sm <?php echo $i <= $reviewRating ? 'text-yellow-400' : 'text-gray-700'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="mt-3 text-sm text-gray-300 leading-relaxed">"<?php echo htmlspecialchars($reviewComment); ?>"</p>
                        <div class="mt-4 flex items-center gap-3">
                            <?php if ($reviewAvatarUrl !== ''): ?>
                                <img src="<?php echo htmlspecialchars($reviewAvatarUrl); ?>"
                                    alt="Avatar khách hàng"
                                    class="w-9 h-9 rounded-full object-cover border border-gray-700 flex-shrink-0">
                            <?php else: ?>
                                <div class="w-9 h-9 rounded-full bg-gray-800 border border-gray-700 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-user text-xs text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <p class="text-sm font-semibold text-white leading-tight"><?php echo htmlspecialchars($reviewAuthor); ?></p>
                                <p class="text-xs text-gray-400 mt-0.5"><?php echo htmlspecialchars($reviewProductName); ?></p>
                            </div>
                        </div>
                    </aside>

                </div>

                <!-- Product preview cards — full width, 4 columns -->
                <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
                    <?php if (empty($featuredProducts)): ?>
                        <div class="sm:col-span-2 xl:col-span-4 p-6 rounded-2xl bg-gray-900 border border-dashed border-gray-700 text-gray-400 text-center">
                            Chưa có gói server để hiển thị.
                        </div>
                    <?php else: ?>
                        <?php foreach ($featuredProducts as $index => $product): ?>
                            <?php
                            $tone      = $tones[$index % count($tones)];
                            $detailUrl = URLROOT . '/products?keyword=' . rawurlencode((string) $product->name);
                            $ramGb     = round((int) $product->ram_mb / 1024, 1);
                            ?>
                            <article class="group bg-gray-900 border border-gray-800 rounded-2xl p-5 flex flex-col <?php echo $tone['css']; ?> transition-all duration-300 hover:-translate-y-1.5">
                                <span class="inline-flex self-start px-2.5 py-1 rounded-full text-[11px] font-semibold border <?php echo $tone['chip']; ?>">
                                    Gói Server
                                </span>
                                <h3 class="mt-3 text-base font-bold text-white leading-tight"><?php echo htmlspecialchars((string) $product->name); ?></h3>
                                <p class="mt-1.5 text-xs text-gray-300 leading-relaxed line-clamp-2 flex-1"><?php echo htmlspecialchars((string) $product->description); ?></p>
                                <div class="mt-3 text-xl font-black text-white">
                                    <?php echo number_format((float) $product->price, 0, ',', '.'); ?>đ
                                    <span class="text-xs text-gray-300 font-medium">/tháng</span>
                                </div>
                                <div class="mt-2.5 grid grid-cols-3 gap-2 text-[12px] text-gray-300 font-medium border-t border-gray-700 pt-2.5 text-left">
                                    <span><?php echo (int) $product->cpu_cores; ?> vCPU</span>
                                    <span><?php echo $ramGb; ?> GB RAM</span>
                                    <span><?php echo (int) $product->disk_gb; ?> GB SSD</span>
                                </div>
                                <a href="<?php echo htmlspecialchars($detailUrl); ?>" class="product-detail-btn">
                                    Chi tiết
                                </a>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </section>

        <!-- About Us / Features Section -->
        <section id="about-us" class="py-24 relative border-t border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 space-y-4">
                    <p class="text-cyan-400 font-bold tracking-widest uppercase text-sm">Tính năng vượt trội</p>
                    <h2 class="text-4xl font-bold text-white">Tại sao chọn G-SERVER?</h2>
                    <p class="text-gray-400 max-w-2xl mx-auto leading-relaxed">
                        Nền tảng tập trung cho cộng đồng game thủ: triển khai nhanh, bảo mật cao và vận hành ổn định xuyên suốt.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="group p-8 rounded-3xl bg-white/[0.03] border border-white/8 hover:border-cyan-500/40 transition-all duration-300">
                        <div class="w-14 h-14 bg-cyan-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-bolt-lightning text-cyan-400 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-4">Hiệu năng tối đa</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Sử dụng CPU Intel Core i9 & AMD Ryzen mới nhất, cùng ổ cứng NVMe Gen4 cho tốc độ xử lý vượt trội.
                        </p>
                    </div>

                    <div class="group p-8 rounded-3xl bg-white/[0.03] border border-white/8 hover:border-purple-500/40 transition-all duration-300">
                        <div class="w-14 h-14 bg-purple-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-shield-halved text-purple-400 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-4">Anti-DDoS mạnh mẽ</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Lớp bảo vệ đa tầng giúp lọc bỏ các cuộc tấn công DDoS lên đến hàng trăm Gbps, giữ server luôn ổn định.
                        </p>
                    </div>

                    <div class="group p-8 rounded-3xl bg-white/[0.03] border border-white/8 hover:border-pink-500/40 transition-all duration-300">
                        <div class="w-14 h-14 bg-pink-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-clock-rotate-left text-pink-400 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-4">Backup tự động</h3>
                        <p class="text-gray-400 leading-relaxed">
                            Dữ liệu của bạn luôn an toàn với hệ thống sao lưu tự động hàng ngày. Khôi phục nhanh chóng khi cần thiết.
                        </p>
                    </div>
                </div>
            </div>
        </section>
</div>
<?php require APPROOT . '/views/layouts/client/footer.php'; ?>
