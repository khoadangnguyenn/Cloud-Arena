<?php require APPROOT . '/views/layouts/client/header.php'; ?>

<div class="bg-gray-950 py-24 min-h-[80vh]">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-12">
      <a href="<?php echo URLROOT; ?>/news" class="inline-flex items-center text-cyan-400 hover:text-cyan-300 transition-colors font-bold text-sm uppercase tracking-widest mb-8">
        <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại tin tức
      </a>
      <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight leading-tight">
        <?php echo $data['article']->title; ?>
      </h1>
      <div class="mt-8 flex items-center gap-6 text-sm text-gray-500 border-b border-gray-800 pb-8">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-full bg-gray-800 flex items-center justify-center text-cyan-400">
                <i class="fa-solid fa-user text-xs"></i>
            </div>
            <span class="font-bold text-gray-400"><?php echo $data['article']->author_name; ?></span>
        </div>
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-full bg-gray-800 flex items-center justify-center text-purple-400">
                <i class="fa-solid fa-calendar text-xs"></i>
            </div>
            <span class="font-bold text-gray-400"><?php echo date('d/m/Y', strtotime($data['article']->created_at)); ?></span>
        </div>
      </div>
    </div>

    <?php if($data['article']->image): ?>
        <div class="mb-12 rounded-3xl overflow-hidden shadow-2xl border border-white/5">
            <img class="w-full h-auto object-cover max-h-[600px]" src="<?php echo URLROOT; ?>/uploads/<?php echo $data['article']->image; ?>" alt="<?php echo $data['article']->title; ?>">
        </div>
    <?php endif; ?>

    <div class="prose prose-invert prose-cyan max-w-none text-gray-300 leading-relaxed text-lg">
      <?php echo $data['article']->content; ?>
    </div>
  </div>
</div>

<?php require APPROOT . '/views/layouts/client/footer.php'; ?>
