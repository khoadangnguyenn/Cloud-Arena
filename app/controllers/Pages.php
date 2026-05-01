<?php
class Pages extends Controller {
    public function __construct() {
        // Load models here if needed
    }

    public function index() {
        $data = ['title' => 'Trang chủ'];
        $this->view('client/pages/index', $data);
    }

    public function about() {
        $data = ['title' => 'Giới thiệu'];
        $this->view('client/about', $data);
    }

    public function contact() {
        $data = ['title' => 'Liên hệ'];
        $this->view('client/contact', $data);
    }

    public function faq() {
        $data = ['title' => 'Hỏi đáp'];
        $this->view('client/faq', $data);
    }
}
