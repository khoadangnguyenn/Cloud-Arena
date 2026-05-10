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
      $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
      $perPage = 5;
      $total = count($newsList);
      $lastPage = max(1, (int) ceil($total / $perPage));
      if ($page > $lastPage) {
        $page = $lastPage;
      }
      $offset = ($page - 1) * $perPage;
      $paginatedNews = array_slice($newsList, $offset, $perPage);

      $flash = $_SESSION['admin_news_flash'] ?? null;
      unset($_SESSION['admin_news_flash']);

      $data = [
        'title' => 'Quản lý tin tức',
        'news' => $paginatedNews,
        'pagination' => [
          'page' => $page,
          'last_page' => $lastPage,
          'total' => $total,
          'per_page' => $perPage
        ],
        'flash' => $flash
      ];

      $this->view('admin/news/index', $data);
    }

    private static function sanitizeHtml($html) {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }

        // 1. Strip everything except safe formatting tags CKEditor Standard can produce
        $html = strip_tags($html,
            '<p><br><strong><b><em><i><u><s><ul><ol><li>'
            . '<blockquote><h2><h3><h4><pre><code><a><img><span><hr><sub><sup>'
        );

        // 2. Remove all event-handler attributes (onclick, onerror, onload, …)
        $html = preg_replace('/\s+on\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s\/>]*)/i', '', $html);

        // 3. Neutralize javascript: and data: URIs in href / src
        $html = preg_replace('/(href|src)\s*=\s*["\']?\s*(?:javascript|data):[^"\'>\s]*/i', '$1="#"', $html);

        return $html;
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if (!$this->verifyCsrf('csrf_admin')) {
                $_SESSION['admin_news_flash'] = ['type' => 'danger', 'message' => 'Yêu cầu không hợp lệ.'];
                header('Location: ' . URLROOT . '/adminnews');
                exit();
            }
            $data = [
                'title' => trim($_POST['title']),
                'content' => self::sanitizeHtml($_POST['content'] ?? ''),
                'seo_keyword' => trim($_POST['seo_keyword']),
                'seo_description' => trim($_POST['seo_description']),
                'author_id' => $_SESSION['user_id'],
                'image' => ''
            ];

            if (!empty($_FILES['image']['name'])) {
                $targetDir = APPROOT . '/../public/uploads/';
                $stored = SecureUpload::storeRasterUpload(
                    $_FILES['image'],
                    $targetDir,
                    'n_',
                    SecureUpload::DEFAULT_MAX_BYTES
                );
                if (!$stored['ok']) {
                    $_SESSION['admin_news_flash'] = [
                        'type' => 'danger',
                        'message' => $stored['message'] ?? 'Ảnh bài viết không hợp lệ.'
                    ];
                    header('Location: ' . URLROOT . '/adminnews');
                    exit();
                }
                $data['image'] = $stored['filename'];
            }

            $created = $this->newsModel->addNews($data);
            $_SESSION['admin_news_flash'] = [
              'type' => $created ? 'success' : 'danger',
              'message' => $created ? 'Đã thêm bài viết mới.' : 'Không thể thêm bài viết.'
            ];
            header('Location: ' . URLROOT . '/adminnews');
            exit();
        }
    }

    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if (!$this->verifyCsrf('csrf_admin')) {
                $_SESSION['admin_news_flash'] = ['type' => 'danger', 'message' => 'Yêu cầu không hợp lệ.'];
                header('Location: ' . URLROOT . '/adminnews');
                exit();
            }
            $deleted = $this->newsModel->deleteNews($id);
            $_SESSION['admin_news_flash'] = [
              'type' => $deleted ? 'success' : 'danger',
              'message' => $deleted ? 'Đã xóa bài viết.' : 'Không thể xóa bài viết.'
            ];
            header('Location: ' . URLROOT . '/adminnews');
            exit();
        }
    }
  }
