<?php
class Cart extends Controller {
    public function index() {
        $data = [
            'title' => 'Giỏ hàng',
            'description' => 'Xem và chỉnh sửa giỏ hàng dịch vụ tại ' . SITENAME . '.',
        ];
        $this->view('client/cart/index', $data);
    }
}
