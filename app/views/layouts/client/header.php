<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($data['description']) ? $data['description'] : SITENAME; ?>">
    <title><?php echo isset($data['title']) ? $data['title'] . ' - ' . SITENAME : SITENAME; ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#06b6d4',
                        secondary: '#9333ea',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .glass-nav {
            background: rgba(3, 7, 18, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-gray-950 text-white antialiased flex flex-col min-h-screen">
    
    <!-- Navbar -->
    <nav class="glass-nav sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <a href="<?php echo URLROOT; ?>" class="flex-shrink-0 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-purple-600 rounded-lg flex items-center justify-center shadow-lg shadow-cyan-500/20">
                            <i class="fa-solid fa-server text-white text-xl"></i>
                        </div>
                        <span class="font-bold text-2xl tracking-tighter bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">G-SERVER</span>
                    </a>
                    <div class="hidden md:ml-10 md:flex md:space-x-8">
                        <a href="<?php echo URLROOT; ?>" class="text-gray-300 hover:text-cyan-400 px-1 pt-1 text-sm font-medium transition-colors">
                            Trang chủ
                        </a>
                        <a href="<?php echo URLROOT; ?>/products" class="text-gray-300 hover:text-cyan-400 px-1 pt-1 text-sm font-medium transition-colors">
                            Sản phẩm
                        </a>
                        <a href="<?php echo URLROOT; ?>/news" class="text-gray-300 hover:text-cyan-400 px-1 pt-1 text-sm font-medium transition-colors">
                            Tin tức
                        </a>
                        <a href="<?php echo URLROOT; ?>/contact" class="text-gray-300 hover:text-cyan-400 px-1 pt-1 text-sm font-medium transition-colors">
                            Liên hệ
                        </a>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-6">
                    <?php if(isset($_SESSION['user_id'])) : ?>
                        <div class="flex items-center gap-4">
                            <a href="<?php echo URLROOT; ?>/users/profile" class="text-sm font-medium text-gray-300 hover:text-cyan-400 transition-colors flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gray-800 flex items-center justify-center border border-gray-700">
                                    <i class="fa-solid fa-user text-xs"></i>
                                </div>
                                <?php echo $_SESSION['user_name']; ?>
                            </a>
                            <?php if($_SESSION['user_role'] == 'admin') : ?>
                                <a href="<?php echo URLROOT; ?>/admin" class="text-sm font-medium text-purple-400 hover:text-purple-300">
                                    <i class="fa-solid fa-shield mr-1"></i> Admin
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo URLROOT; ?>/users/logout" class="text-gray-400 hover:text-white transition-colors">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </a>
                        </div>
                    <?php else : ?>
                        <a href="<?php echo URLROOT; ?>/users/login" class="text-sm font-medium text-gray-300 hover:text-white transition-colors">Đăng nhập</a>
                        <a href="<?php echo URLROOT; ?>/users/register" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white px-6 py-2.5 rounded-full text-sm font-semibold transition-all shadow-lg shadow-cyan-500/25 active:scale-95">Tham gia ngay</a>
                    <?php endif; ?>
                    
                    <a href="<?php echo URLROOT; ?>/cart" class="text-gray-400 hover:text-cyan-400 relative p-2 transition-colors">
                        <i class="fa-solid fa-cart-shopping text-xl"></i>
                        <span class="absolute top-0 right-0 bg-gradient-to-r from-pink-500 to-rose-500 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center border-2 border-gray-950">0</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <main class="flex-grow">

