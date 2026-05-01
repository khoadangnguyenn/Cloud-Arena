<?php
class AdminContacts extends Controller {
    public function index() {
        $data = ['title' => 'Quản lý liên hệ'];
        $this->view('admin/contacts/index', $data);
    }
}
