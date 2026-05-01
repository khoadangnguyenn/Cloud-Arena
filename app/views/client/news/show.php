<?php require APPROOT . '/views/layouts/client/header.php'; ?>

<div class="bg-white py-12">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-10">
      <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
        <?php echo $data['article']->title; ?>
      </h1>
      <div class="mt-4 flex items-center justify-center space-x-4 text-sm text-gray-500">
        <span class="flex items-center">
            <i class="fa-solid fa-user mr-2"></i> <?php echo $data['article']->author_name; ?>
        </span>
        <span class="flex items-center">
            <i class="fa-solid fa-calendar mr-2"></i> <?php echo date('d/m/Y', strtotime($data['article']->created_at)); ?>
        </span>
      </div>
    </div>

    <?php if($data['article']->image): ?>
        <div class="mb-10">
            <img class="w-full h-auto rounded-lg shadow-md object-cover max-h-[500px]" src="<?php echo URLROOT; ?>/uploads/<?php echo $data['article']->image; ?>" alt="<?php echo $data['article']->title; ?>">
        </div>
    <?php endif; ?>

    <div class="prose prose-indigo prose-lg text-gray-700 mx-auto">
      <!-- In HTML content directly, assuming it was saved via WYSIWYG editor -->
      <?php echo $data['article']->content; ?>
    </div>

    <div class="mt-12 pt-8 border-t border-gray-200">
      <a href="<?php echo URLROOT; ?>/news" class="text-primary hover:text-blue-800 font-medium">
        <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại danh sách tin tức
      </a>
    </div>
  </div>
</div>

<?php require APPROOT . '/views/layouts/client/footer.php'; ?>
