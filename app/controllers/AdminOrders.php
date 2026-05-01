<?php
class AdminOrders extends Controller {
    public function index() {
        $data = ['title' => 'Quản lý đơn hàng'];
        $this->view('admin/orders/index', $data);
    }
}
