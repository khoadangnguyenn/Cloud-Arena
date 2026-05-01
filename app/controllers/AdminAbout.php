<?php
class AdminAbout extends Controller {
    public function index() {
        $data = ['title' => 'Quản lý giới thiệu'];
        $this->view('admin/about/index', $data);
    }
}
