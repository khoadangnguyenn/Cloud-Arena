<?php
class AdminComments extends Controller {
    public function index() {
        $data = ['title' => 'Quản lý bình luận'];
        $this->view('admin/comments/index', $data);
    }
}
