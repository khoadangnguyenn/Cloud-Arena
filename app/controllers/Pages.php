<?php
  class Pages extends Controller {
    public function __construct(){
     
    }

    public function index(){
      $data = [
        'title' => 'Trang chủ',
        'description' => 'Chào mừng đến với Game Server Rental Platform'
      ];
     
      $this->view('client/pages/index', $data);
    }

    public function about(){
      $data = [
        'title' => 'Giới thiệu',
        'description' => 'Thông tin về chúng tôi'
      ];

      $this->view('client/pages/about', $data);
    }
  }
