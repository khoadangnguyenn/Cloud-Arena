<?php require APPROOT . '/views/layouts/client/header.php'; ?>

<div class="bg-gray-950 py-24 min-h-[80vh]">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-12">
        <a href="<?php echo URLROOT; ?>/products" class="inline-flex items-center text-cyan-400 hover:text-cyan-300 transition-colors font-bold text-sm uppercase tracking-widest">
            <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại sản phẩm
        </a>
    </div>

    <div class="bg-gray-900/50 backdrop-blur-xl border border-white/5 rounded-[40px] overflow-hidden shadow-2xl">
      <div class="lg:flex">
        <div class="lg:w-1/2 p-8 lg:p-12 flex items-center justify-center bg-gray-800/30">
            <?php if(isset($data['product']->image) && $data['product']->image): ?>
                <img src="<?php echo htmlspecialchars(URLROOT, ENT_QUOTES, 'UTF-8'); ?>/uploads/<?php echo htmlspecialchars($data['product']->image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($data['product']->name, ENT_QUOTES, 'UTF-8'); ?>" class="w-full h-auto object-cover rounded-[32px] shadow-2xl transition-transform hover:scale-105 duration-700">
            <?php else: ?>
                <div class="w-full aspect-square bg-gray-800/50 rounded-[32px] flex items-center justify-center border border-white/5">
                    <i class="fa-solid fa-server text-9xl text-gray-700"></i>
                </div>
            <?php endif; ?>
        </div>
        <div class="p-8 lg:p-16 lg:w-1/2">
          <div class="inline-block px-4 py-1 bg-cyan-500/10 border border-cyan-500/20 rounded-full text-[10px] font-bold text-cyan-400 uppercase tracking-[0.2em] mb-6">
            Dịch vụ lưu trữ
          </div>
          <h2 class="text-4xl lg:text-5xl font-black text-white mb-6 tracking-tight leading-tight">
            <?php echo htmlspecialchars($data['product']->name, ENT_QUOTES, 'UTF-8'); ?>
          </h2>
          
          <div class="flex items-baseline gap-2 mb-10">
              <span class="text-4xl font-black text-white"><?php echo number_format($data['product']->price, 0, ',', '.'); ?>đ</span>
              <span class="text-gray-500 font-medium">/tháng</span>
          </div>
          
          <div class="prose prose-invert prose-cyan text-gray-400 leading-relaxed mb-10">
            <?php echo nl2br(htmlspecialchars($data['product']->description, ENT_QUOTES, 'UTF-8')); ?>
          </div>

          <div class="flex flex-col sm:flex-row items-center gap-4 mb-10">
            <form action="<?php echo htmlspecialchars(URLROOT, ENT_QUOTES, 'UTF-8'); ?>/cart/add/<?php echo htmlspecialchars($data['product']->id, ENT_QUOTES, 'UTF-8'); ?>" method="POST" class="w-full flex gap-4">
              <input type="number" name="quantity" min="1" max="100" value="1" class="w-24 bg-gray-800 border border-gray-700 rounded-xl px-4 py-4 text-white text-center font-bold focus:ring-2 focus:ring-cyan-500/50 outline-none">
              <button type="submit" class="flex-1 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold py-4 px-8 rounded-xl transition-all shadow-lg shadow-cyan-500/25 active:scale-[0.98] flex items-center justify-center gap-3">
                <i class="fa-solid fa-cart-plus"></i> Thuê ngay bây giờ
              </button>
            </form>
          </div>
          
          <div class="grid grid-cols-2 gap-4 border-t border-gray-800 pt-10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <i class="fa-solid fa-check text-xs"></i>
                </div>
                <span class="text-sm text-gray-400 font-medium">Khởi tạo tức thì</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <i class="fa-solid fa-check text-xs"></i>
                </div>
                <span class="text-sm text-gray-400 font-medium">Hỗ trợ 24/7</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require APPROOT . '/views/layouts/client/footer.php'; ?>
