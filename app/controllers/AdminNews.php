<?php
  class AdminNews extends Controller {
    private $newsModel;

    public function __construct(){
      // Protect admin routes
      if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin'){
        header('Location: ' . URLROOT . '/users/login');
        exit();
      }
      $this->newsModel = $this->model('NewsModel');
    }

    public function index(){
      $newsList = $this->newsModel->getNews();

      $data = [
        'title' => 'Quản lý tin tức',
        'news' => $newsList
      ];

      $this->view('admin/news/index', $data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // We do NOT sanitize content fully because WYSIWYG sends HTML.
            // But we should protect against XSS ideally. For this assignment, we allow HTML.
            $data = [
                'title' => trim($_POST['title']),
                'content' => $_POST['content'], // Contains HTML from WYSIWYG
                'seo_keyword' => trim($_POST['seo_keyword']),
                'seo_description' => trim($_POST['seo_description']),
                'author_id' => $_SESSION['user_id'],
                'image' => ''
            ];

            // Image Upload Logic
            if(!empty($_FILES['image']['name'])){
                $target_dir = APPROOT . '/../public/uploads/';
                $imageFileType = strtolower(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION));
                $new_filename = uniqid() . '_news.' . $imageFileType;
                $target_file = $target_dir . $new_filename;

                if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $data['image'] = $new_filename;
                }
            }

            if($this->newsModel->addNews($data)){
                header('Location: ' . URLROOT . '/adminnews');
            } else {
                die('Lỗi thêm tin tức');
            }
        }
    }

    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->newsModel->deleteNews($id)){
                header('Location: ' . URLROOT . '/adminnews');
            } else {
                die('Lỗi xóa tin tức');
            }
        }
    }
  }
