<?php
  class Admin extends Controller {
    public function __construct(){
      // Protect admin routes
      if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin'){
        header('Location: ' . URLROOT . '/users/login');
        exit();
      }
    }

    public function index(){
      $data = [
        'title' => 'Dashboard'
      ];
     
      $this->view('admin/index', $data);
    }
  }
