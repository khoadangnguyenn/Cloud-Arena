<?php
  class News extends Controller {
    private $newsModel;

    public function __construct(){
      $this->newsModel = $this->model('NewsModel');
    }

    public function index(){
      $news = $this->newsModel->getNews();

      $data = [
        'title' => 'Tin tức & Cập nhật',
        'description' => 'Tin tức, cập nhật game server và hướng dẫn modpack từ ' . SITENAME . '.',
        'news' => $news
      ];

      $this->view('client/news/index', $data);
    }

    public function show($id){
      $article = $this->newsModel->getNewsById($id);

      if(empty($article)){
        header('Location: ' . URLROOT . '/news');
        exit();
      }

      $description = trim((string) ($article->seo_description ?? ''));
      if ($description === '') {
        $plain = trim(preg_replace('/\s+/u', ' ', strip_tags((string) ($article->content ?? ''))));
        if (function_exists('mb_substr')) {
          $description = $plain !== '' ? mb_substr($plain, 0, 160, 'UTF-8') : '';
        } else {
          $description = $plain !== '' ? substr($plain, 0, 160) : '';
        }
      }

      $metaKeywords = trim((string) ($article->seo_keyword ?? ''));
      $ogImage = '';
      if (!empty($article->image)) {
        $ogImage = rtrim(URLROOT, '/') . '/uploads/' . ltrim((string) $article->image, '/');
      }

      $data = [
        'title' => $article->title,
        'description' => $description,
        'meta_keywords' => $metaKeywords,
        'canonical_url' => rtrim(URLROOT, '/') . '/news/show/' . (int) $article->id,
        'og_type' => 'article',
        'og_image' => $ogImage,
        'article' => $article
      ];

      $this->view('client/news/show', $data);
    }
  }
