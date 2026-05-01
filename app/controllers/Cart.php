<?php
  class Cart extends Controller {
    private $productModel;

    public function __construct(){
      $this->productModel = $this->model('Product');
    }

    public function index(){
      $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
      $cartItems = [];
      $total = 0;

      foreach($cart as $id => $quantity){
        $product = $this->productModel->getProductById($id);
        if($product){
          $subtotal = $product->price * $quantity;
          $total += $subtotal;
          $cartItems[] = [
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $subtotal
          ];
        }
      }

      $data = [
        'title' => 'Giỏ hàng của bạn',
        'cartItems' => $cartItems,
        'total' => $total
      ];

      $this->view('client/cart/index', $data);
    }

    public function add($id){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        $product = $this->productModel->getProductById($id);
        if($product){
          if(isset($_SESSION['cart'][$id])){
            $_SESSION['cart'][$id] += $quantity;
          } else {
            $_SESSION['cart'][$id] = $quantity;
          }
        }
        header('Location: ' . URLROOT . '/cart');
      } else {
        header('Location: ' . URLROOT . '/products');
      }
    }

    public function update(){
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $quantities = $_POST['quantities'];
        if(is_array($quantities)){
          foreach($quantities as $id => $quantity){
            $quantity = (int)$quantity;
            if($quantity <= 0){
              unset($_SESSION['cart'][$id]);
            } else {
              $_SESSION['cart'][$id] = $quantity;
            }
          }
        }
      }
      header('Location: ' . URLROOT . '/cart');
    }

    public function remove($id){
      if(isset($_SESSION['cart'][$id])){
        unset($_SESSION['cart'][$id]);
      }
      header('Location: ' . URLROOT . '/cart');
    }
  }
