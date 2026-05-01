<?php require APPROOT . '/views/layouts/client/header.php'; ?>

<div class="bg-gray-50 py-12">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="md:flex">
        <div class="md:flex-shrink-0 md:w-1/2 p-6 flex items-center justify-center bg-gray-100">
            <?php if($data['product']->image): ?>
                <img src="<?php echo URLROOT; ?>/uploads/<?php echo $data['product']->image; ?>" alt="<?php echo $data['product']->name; ?>" class="w-full h-auto object-cover rounded-md">
            <?php else: ?>
                <i class="fa-solid fa-server text-9xl text-gray-400"></i>
            <?php endif; ?>
        </div>
        <div class="p-8 md:w-1/2">
          <div class="uppercase tracking-wide text-sm text-primary font-semibold">Chi tiết dịch vụ</div>
          <h2 class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
            <?php echo $data['product']->name; ?>
          </h2>
          <p class="mt-4 text-xl text-gray-500">
             <?php echo number_format($data['product']->price, 0, ',', '.'); ?>đ <span class="text-sm">/tháng</span>
          </p>
          
          <div class="mt-6 prose prose-indigo text-gray-500">
            <?php echo nl2br($data['product']->description); ?>
          </div>

          <div class="mt-8">
            <form action="<?php echo URLROOT; ?>/cart/add/<?php echo $data['product']->id; ?>" method="POST" class="flex items-center gap-4">
              <label for="quantity" class="sr-only">Số lượng</label>
              <input type="number" id="quantity" name="quantity" min="1" max="<?php echo $data['product']->stock; ?>" value="1" class="shadow-sm focus:ring-primary focus:border-primary block w-24 sm:text-sm border-gray-300 rounded-md p-2 border">
              <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                <i class="fa-solid fa-cart-plus mr-2"></i> Thêm vào giỏ hàng
              </button>
            </form>
          </div>
          
          <div class="mt-6 border-t border-gray-200 pt-6">
            <h3 class="text-sm font-medium text-gray-900">Thông tin thêm</h3>
            <ul class="mt-4 space-y-2 text-sm text-gray-500">
                <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Khởi tạo tự động ngay lập tức</li>
                <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Hỗ trợ kỹ thuật 24/7</li>
                <li><i class="fa-solid fa-check text-green-500 mr-2"></i> Tình trạng: <?php echo $data['product']->stock > 0 ? 'Còn hàng' : 'Hết hàng'; ?></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require APPROOT . '/views/layouts/client/footer.php'; ?>
