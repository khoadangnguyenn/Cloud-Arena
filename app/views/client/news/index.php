<?php require APPROOT . '/views/layouts/client/header.php'; ?>

<div class="bg-gray-50 py-12">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
      <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
        Tin tức & Cập nhật
      </h2>
      <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
        Những thông tin mới nhất về dịch vụ, khuyến mãi và các thay đổi hệ thống.
      </p>
    </div>

    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
      <?php if(empty($data['news'])): ?>
        <p class="text-center col-span-3 text-gray-500">Chưa có bài viết nào.</p>
      <?php else: ?>
        <?php foreach($data['news'] as $article): ?>
          <div class="flex flex-col rounded-lg shadow-lg overflow-hidden bg-white hover:shadow-xl transition">
            <div class="flex-shrink-0">
                <?php if($article->image): ?>
                    <img class="h-48 w-full object-cover" src="<?php echo URLROOT; ?>/uploads/<?php echo $article->image; ?>" alt="<?php echo $article->title; ?>">
                <?php else: ?>
                    <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
                        <i class="fa-solid fa-newspaper text-5xl text-gray-400"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex-1 p-6 flex flex-col justify-between">
              <div class="flex-1">
                <p class="text-sm font-medium text-primary">
                  Tin tức
                </p>
                <a href="<?php echo URLROOT; ?>/news/show/<?php echo $article->id; ?>" class="block mt-2">
                  <p class="text-xl font-semibold text-gray-900 line-clamp-2">
                    <?php echo $article->title; ?>
                  </p>
                  <p class="mt-3 text-base text-gray-500 line-clamp-3">
                    <?php echo strip_tags($article->content); ?>
                  </p>
                </a>
              </div>
              <div class="mt-6 flex items-center">
                <div class="flex-shrink-0">
                    <span class="inline-block h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold">
                        <?php echo strtoupper(substr($article->author_name, 0, 1)); ?>
                    </span>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">
                    <?php echo $article->author_name; ?>
                  </p>
                  <div class="flex space-x-1 text-sm text-gray-500">
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
