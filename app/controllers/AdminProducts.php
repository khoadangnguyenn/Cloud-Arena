<?php
  class AdminProducts extends Controller {
    private $productModel;

    public function __construct(){
      // Protect admin routes
      if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin'){
        header('Location: ' . URLROOT . '/users/login');
        exit();
      }
      $this->productModel = $this->model('Product');
    }

    public function index(){
      $products = $this->productModel->getProducts();

      $data = [
        'title' => 'Quản lý sản phẩm',
        'products' => $products
      ];

      $this->view('admin/products/index', $data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Sanitize
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'name' => trim($_POST['name']),
                'price' => trim($_POST['price']),
                'description' => trim($_POST['description']),
                'stock' => trim($_POST['stock']),
                'image' => '' // Handle image upload here
            ];

            // Image Upload Logic
            if(!empty($_FILES['image']['name'])){
                $target_dir = APPROOT . '/../public/uploads/';
                $imageFileType = strtolower(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION));
                $new_filename = uniqid() . '.' . $imageFileType;
                $target_file = $target_dir . $new_filename;

                if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $data['image'] = $new_filename;
                }
            }

            if($this->productModel->addProduct($data)){
                header('Location: ' . URLROOT . '/adminproducts');
            } else {
                die('Lỗi thêm sản phẩm');
            }
        }
    }

    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->productModel->deleteProduct($id)){
                header('Location: ' . URLROOT . '/adminproducts');
            } else {
                die('Lỗi xóa sản phẩm');
            }
        }
    }
  }
