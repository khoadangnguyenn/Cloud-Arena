<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Admin Dashboard - <?php echo SITENAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?php echo URLROOT; ?>/admin_assets/images/icon/logo.png">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/admin_assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/admin_assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/admin_assets/css/themify-icons.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/admin_assets/css/metismenujs.min.css">
    <!-- others css -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/admin_assets/css/typography.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/admin_assets/css/default-css.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/admin_assets/css/styles.css">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/admin_assets/css/responsive.css">
</head>

<body>
    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- page container area start -->
    <div class="page-container">
        <!-- sidebar menu area start -->
        <div class="sidebar-menu">
            <div class="sidebar-header">
                <div class="logo">
                    <a href="<?php echo URLROOT; ?>/admin"><img src="<?php echo URLROOT; ?>/admin_assets/images/icon/logo.png" alt="logo"></a>
                </div>
            </div>
            <div class="main-menu">
                <div class="menu-inner">
                    <nav>
                        <ul class="metismenu" id="menu">
                            <li class="active">
                                <a href="<?php echo URLROOT; ?>/admin" aria-expanded="true"><i class="ti-dashboard"></i><span>Dashboard</span></a>
                            </li>
                            <li>
                                <a href="<?php echo URLROOT; ?>/admin/users"><i class="ti-user"></i><span>Quản lý thành viên</span></a>
                            </li>
                            <li>
                                <a href="<?php echo URLROOT; ?>/admin/products"><i class="ti-package"></i><span>Quản lý sản phẩm</span></a>
                            </li>
                            <li>
                                <a href="<?php echo URLROOT; ?>/admin/orders"><i class="ti-shopping-cart"></i><span>Quản lý đơn hàng</span></a>
                            </li>
                            <li>
                                <a href="<?php echo URLROOT; ?>/admin/news"><i class="ti-file"></i><span>Quản lý tin tức</span></a>
                            </li>
                            <li>
                                <a href="<?php echo URLROOT; ?>/admin/contacts"><i class="ti-email"></i><span>Quản lý liên hệ</span></a>
                            </li>
                            <li>
                                <a href="<?php echo URLROOT; ?>/admin/settings"><i class="ti-settings"></i><span>Cài đặt hệ thống</span></a>
                            </li>
                            <li>
                                <a href="<?php echo URLROOT; ?>/pages/index"><i class="ti-home"></i><span>Quay về trang chủ</span></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <!-- sidebar menu area end -->
        <!-- main content area start -->
        <div class="main-content">
            <!-- header area start -->
            <div class="header-area">
                <div class="row align-items-center">
                    <!-- nav and search button -->
                    <div class="col-md-6 col-sm-8 clearfix">
                        <div class="nav-btn pull-left">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <div class="search-box pull-left">
                            <form action="#">
                                <input type="text" name="search" placeholder="Search..." required>
                                <i class="ti-search"></i>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- header area end -->
            <!-- page title area start -->
            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Dashboard</h4>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <div class="user-profile pull-right">
                            <h4 class="user-name dropdown-toggle" data-bs-toggle="dropdown"><?php echo $_SESSION['user_name'] ?? 'Admin'; ?> <i class="fa fa-angle-down"></i></h4>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?php echo URLROOT; ?>/users/logout">Đăng xuất</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- page title area end -->
            <div class="main-content-inner">
