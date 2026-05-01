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

      $data = [
        'title' => $article->title,
        'article' => $article
      ];

      $this->view('client/news/show', $data);
    }
  }
