<?php
class Products extends Controller {
    public function index() {
        $data = [
            'title' => 'Danh sách sản phẩm',
            'description' => 'Gói máy chủ game, RAM/CPU/băng thông và giá thuê minh bạch — ' . SITENAME . '.',
        ];
        $this->view('client/products/index', $data);
    }

    public function show($id) {
        $product = $this->model('ProductModel')->getProductById($id);
        $data = [
            'title' => 'Chi tiết sản phẩm',
            'product' => $product
        ];
        $this->view('client/products/show', $data);
    }
}
