<?php
  class Users extends Controller {
    private $userModel;

    public function __construct(){
      $this->userModel = $this->model('User');
    }

    public function register(){
      // Check for POST
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Process form
  
        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Init data
        $data =[
          'username' => trim($_POST['username']),
          'email' => trim($_POST['email']),
          'password' => trim($_POST['password']),
          'confirm_password' => trim($_POST['confirm_password']),
          'username_err' => '',
          'email_err' => '',
          'password_err' => '',
          'confirm_password_err' => ''
        ];

        // Validate Email
        if(empty($data['email'])){
          $data['email_err'] = 'Vui lòng nhập email';
        } else {
          // Check email
          if($this->userModel->findUserByEmail($data['email'])){
            $data['email_err'] = 'Email này đã được sử dụng';
          }
        }

        // Validate Username
        if(empty($data['username'])){
          $data['username_err'] = 'Vui lòng nhập tên đăng nhập';
        } else {
            // Check username
            if($this->userModel->findUserByUsername($data['username'])){
              $data['username_err'] = 'Tên đăng nhập này đã được sử dụng';
            }
        }

        // Validate Password
        if(empty($data['password'])){
          $data['password_err'] = 'Vui lòng nhập mật khẩu';
        } elseif(strlen($data['password']) < 6){
          $data['password_err'] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }

        // Validate Confirm Password
        if(empty($data['confirm_password'])){
          $data['confirm_password_err'] = 'Vui lòng xác nhận mật khẩu';
        } else {
          if($data['password'] != $data['confirm_password']){
            $data['confirm_password_err'] = 'Mật khẩu không khớp';
          }
        }

        // Make sure errors are empty
        if(empty($data['email_err']) && empty($data['username_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])){
          // Validated
          
          // Hash Password
          $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

          // Register User
          if($this->userModel->register($data)){
            header('Location: ' . URLROOT . '/users/login');
          } else {
            die('Đã có lỗi xảy ra');
          }

        } else {
          // Load view with errors
          $this->view('client/users/register', $data);
        }

      } else {
        // Init data
        $data =[
          'username' => '',
          'email' => '',
          'password' => '',
          'confirm_password' => '',
          'username_err' => '',
          'email_err' => '',
          'password_err' => '',
          'confirm_password_err' => ''
        ];

        // Load view
        $this->view('client/users/register', $data);
      }
    }

    public function login(){
      // Check for POST
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Process form
        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        
        // Init data
        $data =[
          'username' => trim($_POST['username']),
          'password' => trim($_POST['password']),
          'username_err' => '',
          'password_err' => '',      
        ];

        // Validate Username
        if(empty($data['username'])){
          $data['username_err'] = 'Vui lòng nhập tên đăng nhập';
        }

        // Validate Password
        if(empty($data['password'])){
          $data['password_err'] = 'Vui lòng nhập mật khẩu';
        }

        // Check for user/email
        if($this->userModel->findUserByUsername($data['username'])){
          // User found
        } else {
          // User not found
          $data['username_err'] = 'Không tìm thấy người dùng';
        }

        // Make sure errors are empty
        if(empty($data['username_err']) && empty($data['password_err'])){
          // Validated
          // Check and set logged in user
          $loggedInUser = $this->userModel->login($data['username'], $data['password']);

          if($loggedInUser){
            // Create Session
            $this->createUserSession($loggedInUser);
          } else {
            $data['password_err'] = 'Mật khẩu không chính xác';
            $this->view('client/users/login', $data);
          }
        } else {
          // Load view with errors
          $this->view('client/users/login', $data);
        }

      } else {
        // Init data
        $data =[    
          'username' => '',
          'password' => '',
          'username_err' => '',
          'password_err' => '',        
        ];

        // Load view
        $this->view('client/users/login', $data);
      }
    }

    public function createUserSession($user){
      $_SESSION['user_id'] = $user->id;
      $_SESSION['user_email'] = $user->email;
      $_SESSION['user_name'] = $user->username;
      $_SESSION['user_role'] = $user->role;
      header('Location: ' . URLROOT . '/pages/index');
    }

    public function logout(){
      unset($_SESSION['user_id']);
      unset($_SESSION['user_email']);
      unset($_SESSION['user_name']);
      unset($_SESSION['user_role']);
      session_destroy();
      header('Location: ' . URLROOT . '/users/login');
    }
  }
