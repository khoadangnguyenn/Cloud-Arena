<?php require APPROOT . '/views/layouts/client/header.php'; ?>

<!-- Hero Section -->
<div class="relative overflow-hidden bg-gray-950">
    <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 via-purple-500/10 to-pink-500/10"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(6,182,212,0.1),transparent_50%)]"></div>

    <div class="relative max-w-7xl mx-auto px-4 py-24 sm:px-6 lg:px-8">
        <div class="text-center space-y-10">
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight">
                <span class="bg-gradient-to-r from-cyan-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                    Game Server Hosting
                </span>
                <br />
                <span class="text-white">Cho Mọi Game Thủ</span>
            </h1>

            <p class="text-xl text-gray-400 max-w-3xl mx-auto leading-relaxed">
                Máy chủ game chuyên nghiệp với hiệu năng cao, hỗ trợ modpack và quản lý dễ dàng.
                Khởi động server của bạn chỉ trong vài phút với công nghệ ảo hóa tiên tiến nhất.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="<?php echo URLROOT; ?>/products" class="group relative px-8 py-4 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold rounded-xl hover:shadow-[0_0_40px_rgba(6,182,212,0.5)] transition-all transform hover:scale-105 active:scale-95">
                    Khám phá Gói Server
                    <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="#features" class="px-8 py-4 border border-gray-700 text-gray-300 font-bold rounded-xl hover:bg-white/5 hover:border-gray-500 transition-all">
                    Tìm hiểu thêm
                </a>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-20">
                <div class="p-6 rounded-2xl bg-gray-900/40 border border-white/5 backdrop-blur-md">
                    <div class="text-3xl font-bold text-cyan-400 mb-1">99.9%</div>
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Uptime</div>
                </div>
                <div class="p-6 rounded-2xl bg-gray-900/40 border border-white/5 backdrop-blur-md">
                    <div class="text-3xl font-bold text-purple-400 mb-1">10K+</div>
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Players</div>
                </div>
                <div class="p-6 rounded-2xl bg-gray-900/40 border border-white/5 backdrop-blur-md">
                    <div class="text-3xl font-bold text-pink-400 mb-1">&lt;20ms</div>
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Low Latency</div>
                </div>
                <div class="p-6 rounded-2xl bg-gray-900/40 border border-white/5 backdrop-blur-md">
                    <div class="text-3xl font-bold text-yellow-400 mb-1">24/7</div>
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Support</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div id="features" class="py-24 bg-gray-950 relative border-t border-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 space-y-4">
            <h2 class="text-cyan-400 font-bold tracking-widest uppercase text-sm">Tính năng vượt trội</h2>
            <p class="text-4xl font-bold text-white">Tại sao chọn G-SERVER?</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="group p-8 rounded-3xl bg-gray-900/50 border border-gray-800 hover:border-cyan-500/50 transition-all duration-300">
                <div class="w-14 h-14 bg-cyan-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-bolt-lightning text-cyan-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-4">Hiệu năng tối đa</h3>
                <p class="text-gray-400 leading-relaxed">
                    Sử dụng CPU Intel Core i9 & AMD Ryzen mới nhất, cùng ổ cứng NVMe Gen4 cho tốc độ xử lý vượt trội.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="group p-8 rounded-3xl bg-gray-900/50 border border-gray-800 hover:border-purple-500/50 transition-all duration-300">
                <div class="w-14 h-14 bg-purple-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-shield-halved text-purple-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-4">Anti-DDoS mạnh mẽ</h3>
                <p class="text-gray-400 leading-relaxed">
                    Lớp bảo vệ đa tầng giúp lọc bỏ các cuộc tấn công DDoS lên đến hàng trăm Gbps, giữ server luôn ổn định.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="group p-8 rounded-3xl bg-gray-900/50 border border-gray-800 hover:border-pink-500/50 transition-all duration-300">
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
</div>

<!-- Social proof section -->
<div class="py-16 bg-gray-900/30">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <p class="text-gray-500 font-medium mb-8">HỖ TRỢ ĐA DẠNG CÁC TỰA GAME</p>
        <div class="flex flex-wrap justify-center gap-12 opacity-30 grayscale hover:grayscale-0 transition-all duration-500">
            <i class="fa-brands fa-minecraft text-5xl text-white"></i>
            <i class="fa-solid fa-steam text-5xl text-white"></i>
            <i class="fa-solid fa-gun text-5xl text-white"></i>
            <i class="fa-solid fa-dragon text-5xl text-white"></i>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/client/footer.php'; ?>
