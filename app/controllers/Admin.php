<?php
class Admin extends Controller {
    public function __construct() {
        // Check if logged in as admin
    }

    public function index() {
        $data = ['title' => 'Dashboard'];
        $this->view('admin/index', $data);
    }

    public function settings() {
        $data = ['title' => 'Cài đặt hệ thống'];
        $this->view('admin/settings/index', $data);
    }
}
