<?php
require_once '../app/init.php';

// --- BẮT ĐẦU AUTO LOGIN (REMEMBER ME) ---
if (!isset($_SESSION['user_id']) && isset($_COOKIE['cloud_arena_remember'])) {
    $parts = explode('|', $_COOKIE['cloud_arena_remember']);
    
    if (count($parts) === 2) {
        $userId = $parts[0];
        $hash = $parts[1];
        
        // Gọi Model User để check DB
        require_once '../app/models/User.php';
        $userModel = new User();
        $user = $userModel->getById($userId);
        
        if ($user) {
            // Xác thực chữ ký token xem có bị giả mạo không
            $expectedHash = md5($user->username . $user->password . 'CloudArenaSecret');
            
            if ($hash === $expectedHash) {
                // Khôi phục Session thành công
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->full_name ?: $user->username;
                $_SESSION['user_role'] = $user->role ?? 'member';
                if (!empty($user->avatar)) {
                    $_SESSION['user_avatar'] = $user->avatar;
                }
                $_SESSION['user_credit'] = isset($user->credit) ? (int)$user->credit : 0;
            } else {
                // Xóa luôn cookie nếu phát hiện giả mạo
                setcookie('cloud_arena_remember', '', time() - 3600, '/');
            }
        }
    }
}

$init = new App;