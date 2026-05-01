<?php
class AdminFaqs extends Controller {
    public function index() {
        $data = ['title' => 'Quản lý FAQ'];
        $this->view('admin/faqs/index', $data);
    }
}
