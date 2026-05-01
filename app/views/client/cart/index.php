<?php require APPROOT . '/views/layouts/client/header.php'; ?>

<div class="bg-gray-50 min-h-[80vh] py-12">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h2 class="text-3xl font-extrabold text-gray-900 mb-8">
      Giỏ hàng của bạn
    </h2>

    <?php if(empty($data['cartItems'])): ?>
      <div class="bg-white p-8 text-center rounded-lg shadow">
        <i class="fa-solid fa-cart-shopping text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-medium text-gray-900">Giỏ hàng trống</h3>
        <p class="mt-2 text-gray-500">Bạn chưa thêm sản phẩm nào vào giỏ hàng.</p>
        <a href="<?php echo URLROOT; ?>/products" class="mt-6 inline-block bg-primary text-white px-6 py-2 rounded hover:bg-blue-700 transition">Tiếp tục mua sắm</a>
      </div>
    <?php else: ?>
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="<?php echo URLROOT; ?>/cart/update" method="POST">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đơn giá</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thành tiền</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php foreach($data['cartItems'] as $item): ?>
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <?php if($item['product']->image): ?>
                            <img class="h-10 w-10 rounded object-cover" src="<?php echo URLROOT; ?>/uploads/<?php echo $item['product']->image; ?>" alt="">
                        <?php else: ?>
                            <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center">
                                <i class="fa-solid fa-server text-gray-400"></i>
                            </div>
                        <?php endif; ?>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900"><?php echo $item['product']->name; ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900"><?php echo number_format($item['product']->price, 0, ',', '.'); ?>đ</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <input type="number" name="quantities[<?php echo $item['product']->id; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="w-20 border-gray-300 rounded p-1 border text-sm text-center">
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <?php echo number_format($item['subtotal'], 0, ',', '.'); ?>đ
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="<?php echo URLROOT; ?>/cart/remove/<?php echo $item['product']->id; ?>" class="text-red-600 hover:text-red-900"><i class="fa-solid fa-trash"></i> Xóa</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-200">
            <div>
              <button type="submit" class="text-sm text-primary hover:text-blue-800"><i class="fa-solid fa-arrows-rotate mr-1"></i> Cập nhật giỏ hàng</button>
            </div>
            <div class="text-right">
              <span class="text-gray-500 mr-4">Tổng cộng:</span>
              <span class="text-2xl font-bold text-gray-900"><?php echo number_format($data['total'], 0, ',', '.'); ?>đ</span>
            </div>
          </div>
        </form>
      </div>
      
      <div class="mt-8 flex justify-end">
        <a href="<?php echo URLROOT; ?>/checkout" class="bg-green-600 border border-transparent rounded-md shadow-sm py-3 px-8 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
          Tiến hành thanh toán
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require APPROOT . '/views/layouts/client/footer.php'; ?>
