<?php require APPROOT . '/views/layouts/client/header.php'; ?>

<div class="bg-gray-950 py-24 min-h-[80vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gray-900/50 backdrop-blur-xl border border-white/5 rounded-3xl p-8 md:p-12">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-white mb-4">Liên Hệ</h1>
                <p class="text-gray-400">Gửi tin nhắn cho chúng tôi, chúng tôi sẽ phản hồi sớm nhất có thể.</p>
            </div>
            
            <form action="<?php echo URLROOT; ?>/pages/contact" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Họ tên</label>
                        <input type="text" name="name" class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-cyan-500/50" placeholder="Nhập họ tên">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                        <input type="email" name="email" class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-cyan-500/50" placeholder="example@email.com">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Nội dung</label>
                    <textarea rows="5" name="message" class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-cyan-500/50" placeholder="Gửi tin nhắn của bạn..."></textarea>
                </div>
                <button type="submit" class="w-full py-4 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-cyan-500/25 active:scale-[0.98]">
                    Gửi tin nhắn ngay
                </button>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/client/footer.php'; ?>
