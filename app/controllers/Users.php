<?php
class Users extends Controller {
    public function login() {
        $data = ['title' => 'Đăng nhập'];
        $this->view('client/users/login', $data);
    }
}
