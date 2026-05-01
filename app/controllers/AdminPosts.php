<?php
class AdminPosts extends Controller {
    public function index() {
        $data = ['title' => 'Quản lý bài viết'];
        $this->view('admin/posts/index', $data);
    }
}
