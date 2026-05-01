<?php require APPROOT . '/views/layouts/client/header.php'; ?>

<div class="bg-gray-950 min-h-[80vh] py-24">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h2 class="text-4xl font-extrabold text-white mb-12 tracking-tight">
      Giỏ hàng <span class="text-cyan-400">của bạn</span>
    </h2>

    <?php if(empty($data['cartItems'])): ?>
      <div class="bg-gray-900/50 backdrop-blur-xl border border-white/5 p-16 text-center rounded-3xl">
        <div class="w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-8">
            <i class="fa-solid fa-cart-shopping text-4xl text-gray-600"></i>
        </div>
        <h3 class="text-2xl font-bold text-white mb-4">Giỏ hàng trống</h3>
        <p class="text-gray-400 mb-10">Bạn chưa thêm sản phẩm nào vào giỏ hàng của mình.</p>
        <a href="<?php echo URLROOT; ?>/products" class="inline-block bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white px-10 py-4 rounded-xl font-bold transition-all shadow-lg shadow-cyan-500/25">Khám phá sản phẩm</a>
      </div>
    <?php else: ?>
      <div class="bg-gray-900/50 backdrop-blur-xl border border-white/5 rounded-3xl overflow-hidden shadow-2xl">
        <form action="<?php echo URLROOT; ?>/cart/update" method="POST">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800">
              <thead class="bg-gray-800/50">
                <tr>
                  <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Sản phẩm</th>
                  <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Đơn giá</th>
                  <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Số lượng</th>
                  <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Thành tiền</th>
                  <th scope="col" class="px-8 py-5 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Thao tác</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-800">
                <?php foreach($data['cartItems'] as $item): ?>
                  <tr class="hover:bg-white/5 transition-colors">
                    <td class="px-8 py-6 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 rounded-xl overflow-hidden border border-gray-700">
                          <?php if($item['product']->image): ?>
                              <img class="h-full w-full object-cover" src="<?php echo URLROOT; ?>/uploads/<?php echo $item['product']->image; ?>" alt="">
                          <?php else: ?>
                              <div class="h-full w-full bg-gray-800 flex items-center justify-center">
                                  <i class="fa-solid fa-server text-gray-600"></i>
                              </div>
                          <?php endif; ?>
                        </div>
                        <div class="ml-4">
                          <div class="text-base font-bold text-white"><?php echo $item['product']->name; ?></div>
                        </div>
                      </div>
                    </td>
                    <td class="px-8 py-6 whitespace-nowrap">
                      <div class="text-sm text-gray-300 font-medium"><?php echo number_format($item['product']->price, 0, ',', '.'); ?>đ</div>
                    </td>
                    <td class="px-8 py-6 whitespace-nowrap">
                      <input type="number" name="quantities[<?php echo $item['product']->id; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="w-16 bg-gray-800 border border-gray-700 rounded-lg py-1.5 px-2 text-sm text-white text-center focus:ring-2 focus:ring-cyan-500/50 outline-none">
                    </td>
                    <td class="px-8 py-6 whitespace-nowrap text-sm font-bold text-cyan-400">
                      <?php echo number_format($item['subtotal'], 0, ',', '.'); ?>đ
                    </td>
                    <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                      <a href="<?php echo URLROOT; ?>/cart/remove/<?php echo $item['product']->id; ?>" class="text-rose-500 hover:text-rose-400 transition-colors"><i class="fa-solid fa-trash-can"></i></a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="bg-gray-800/30 px-8 py-6 flex flex-col md:flex-row items-center justify-between gap-6 border-t border-gray-800">
            <div>
              <button type="submit" class="text-sm font-bold text-cyan-400 hover:text-cyan-300 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-arrows-rotate"></i> Cập nhật giỏ hàng
              </button>
            </div>
            <div class="flex items-center gap-8">
              <div class="text-right">
                <span class="text-gray-500 text-sm font-medium uppercase tracking-wider block mb-1">Tổng cộng</span>
                <span class="text-3xl font-black text-white"><?php echo number_format($data['total'], 0, ',', '.'); ?>đ</span>
              </div>
              <a href="<?php echo URLROOT; ?>/checkout" class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white px-8 py-4 rounded-xl font-bold transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                Thanh toán ngay
              </a>
            </div>
          </div>
        </form>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require APPROOT . '/views/layouts/client/footer.php'; ?>
