<?php require APPROOT . '/views/layouts/client/header.php'; ?>

<div class="bg-gray-950 py-24 min-h-[80vh]">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-16 space-y-4">
      <h2 class="text-4xl font-extrabold text-white tracking-tight sm:text-5xl">
        <span class="bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent">Tin tức & Cập nhật</span>
      </h2>
      <p class="max-w-2xl mx-auto text-xl text-gray-400">
        Những thông tin mới nhất về dịch vụ, khuyến mãi và các thay đổi hệ thống.
      </p>
    </div>

    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
      <?php if(empty($data['news'])): ?>
        <div class="col-span-full text-center py-20 bg-gray-900/50 rounded-3xl border border-dashed border-gray-700">
          <p class="text-gray-500">Chưa có bài viết nào được đăng tải.</p>
        </div>
      <?php else: ?>
        <?php foreach($data['news'] as $article): ?>
          <div class="flex flex-col rounded-3xl shadow-2xl overflow-hidden bg-gray-900 border border-gray-800 hover:border-cyan-500/50 transition-all duration-300 group">
            <div class="flex-shrink-0 h-48 overflow-hidden">
                <?php if($article->image): ?>
                    <img class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500" src="<?php echo URLROOT; ?>/uploads/<?php echo $article->image; ?>" alt="<?php echo $article->title; ?>">
                <?php else: ?>
                    <div class="h-full w-full bg-gray-800 flex items-center justify-center">
                        <i class="fa-solid fa-newspaper text-5xl text-gray-700"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex-1 p-8 flex flex-col justify-between">
              <div class="flex-1">
                <p class="text-xs font-bold text-cyan-400 uppercase tracking-widest mb-3">
                  Cập nhật
                </p>
                <a href="<?php echo URLROOT; ?>/news/show/<?php echo $article->id; ?>" class="block">
                  <h3 class="text-xl font-bold text-white mb-4 line-clamp-2 hover:text-cyan-400 transition-colors">
                    <?php echo $article->title; ?>
                  </h3>
                  <p class="text-gray-400 text-sm leading-relaxed line-clamp-3">
                    <?php echo strip_tags($article->content); ?>
                  </p>
                </a>
              </div>
              <div class="mt-8 flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-cyan-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-lg shadow-cyan-500/20">
                        <?php echo strtoupper(substr($article->author_name, 0, 1)); ?>
                    </div>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-bold text-white">
                    <?php echo $article->author_name; ?>
                  </p>
                  <div class="text-xs text-gray-500">
                    <time datetime="<?php echo $article->created_at; ?>">
                      <?php echo date('d/m/Y', strtotime($article->created_at)); ?>
                    </time>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require APPROOT . '/views/layouts/client/footer.php'; ?>
