<?php require APPROOT . '/views/layouts/client/header.php'; ?>

<div class="bg-gray-950 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 space-y-4">
            <h2 class="text-4xl font-bold text-white tracking-tight sm:text-5xl">
                <span class="bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent">Gói Dịch Vụ / Sản Phẩm</span>
            </h2>
            <p class="max-w-2xl mx-auto text-xl text-gray-400">
                Lựa chọn cấu hình tối ưu, sẵn sàng cho những trận game rực lửa.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if(empty($data['products'])): ?>
                <div class="col-span-full text-center py-20 bg-gray-900/50 rounded-3xl border border-dashed border-gray-700">
                    <i class="fa-solid fa-box-open text-6xl text-gray-700 mb-4 block"></i>
                    <p class="text-gray-500">Hiện tại chưa có sản phẩm nào được đăng bán.</p>
                </div>
            <?php else: ?>
                <?php foreach($data['products'] as $product): ?>
                    <div class="group relative bg-gray-900 border border-gray-800 rounded-3xl overflow-hidden hover:border-cyan-500/50 transition-all duration-500 hover:shadow-[0_0_50px_rgba(6,182,212,0.15)] hover:-translate-y-2">
                        <div class="relative h-56 overflow-hidden">
                            <?php if($product->image): ?>
                                <img src="<?php echo URLROOT; ?>/uploads/<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
                                    <i class="fa-solid fa-server text-5xl text-gray-700"></i>
                                </div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent"></div>
                            <div class="absolute bottom-4 left-6">
                                <span class="bg-cyan-500/20 text-cyan-400 text-[10px] font-bold px-3 py-1 rounded-full border border-cyan-500/30 uppercase tracking-widest">Premium</span>
                            </div>
                        </div>
                        
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-white mb-3"><?php echo $product->name; ?></h3>
                            <p class="text-gray-400 text-sm line-clamp-2 mb-6 leading-relaxed">
                                <?php echo $product->description; ?>
                            </p>
                            
                            <div class="flex items-baseline gap-1 mb-8">
                                <span class="text-3xl font-black text-white"><?php echo number_format($product->price, 0, ',', '.'); ?>đ</span>
                                <span class="text-gray-500 text-sm">/tháng</span>
                            </div>

                            <div class="flex gap-3">
                                <a href="<?php echo URLROOT; ?>/products/show/<?php echo $product->id; ?>" class="flex-1 text-center py-3.5 bg-gray-800 text-white text-sm font-bold rounded-xl hover:bg-gray-700 transition-colors">
                                    Chi tiết
                                </a>
                                <a href="<?php echo URLROOT; ?>/cart/add/<?php echo $product->id; ?>" class="flex-none w-12 h-12 flex items-center justify-center bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-xl hover:shadow-lg hover:shadow-cyan-500/30 transition-all active:scale-90">
                                    <i class="fa-solid fa-cart-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/client/footer.php'; ?>

