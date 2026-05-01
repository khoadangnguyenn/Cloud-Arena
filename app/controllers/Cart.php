<?php
class Cart extends Controller {
    public function index() {
        $data = ['title' => 'Giỏ hàng'];
        $this->view('client/cart/index', $data);
    }
}
