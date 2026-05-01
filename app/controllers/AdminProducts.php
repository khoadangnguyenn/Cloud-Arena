<?php
class AdminProducts extends Controller {
    public function index() {
        $data = ['title' => 'Quản lý sản phẩm'];
        $this->view('admin/products/index', $data);
    }

    public function add() {
        $data = ['title' => 'Thêm sản phẩm'];
        $this->view('admin/products/add', $data);
    }
}
