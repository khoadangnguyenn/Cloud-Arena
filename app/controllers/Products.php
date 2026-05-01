<?php
  class Products extends Controller {
    private $productModel;

    public function __construct(){
      $this->productModel = $this->model('Product');
    }

    public function index(){
      $products = $this->productModel->getProducts();

      $data = [
        'title' => 'Sản phẩm',
        'products' => $products
      ];

      $this->view('client/products/index', $data);
    }

    public function show($id){
      $product = $this->productModel->getProductById($id);

      if(empty($product)){
        header('Location: ' . URLROOT . '/products');
        exit();
      }

      $data = [
        'title' => $product->name,
        'product' => $product
      ];

      $this->view('client/products/show', $data);
    }
  }
