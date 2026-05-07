<?php require APPROOT . '/views/layouts/client/header.php'; ?>

<?php
$user = $data['user'] ?? null;
$errors = $data['errors'] ?? [];
$avatarRaw = trim((string) ($user->avatar ?? ''));
$avatarUrl = '';
if ($avatarRaw !== '') {
    if (strpos($avatarRaw, 'http://') === 0 || strpos($avatarRaw, 'https://') === 0) {
        $avatarUrl = $avatarRaw;
    } elseif (strpos($avatarRaw, '/uploads/') === 0) {
        $avatarUrl = URLROOT . $avatarRaw;
    } elseif (strpos($avatarRaw, 'uploads/') === 0) {
        $avatarUrl = URLROOT . '/' . ltrim($avatarRaw, '/');
    } else {
        $avatarUrl = URLROOT . '/uploads/avatars/' . ltrim($avatarRaw, '/');
    }
}
$displayName = $user ? ($user->full_name ?: $user->username) : '';
?>

<div class="bg-gray-950 min-h-[80vh] py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">Profile Settings</h1>
            <p class="text-gray-400 mt-2">Manage your account information and security</p>
        </div>

        <?php if (!empty($data['success_message'])): ?>
            <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 text-green-300 rounded-xl">
                <?php echo htmlspecialchars($data['success_message']); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-white mb-5">Profile Picture</h2>
                    <div class="flex flex-col items-center">
                        <?php if ($avatarUrl !== ''): ?>
                            <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Avatar" class="w-32 h-32 rounded-full object-cover border border-gray-700 mb-4">
                        <?php else: ?>
                            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-cyan-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold mb-4">
                                <?php echo strtoupper(substr($displayName, 0, 1)); ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?php echo URLROOT; ?>/users/profile" method="POST" enctype="multipart/form-data" class="w-full text-center">
                            <input type="hidden" name="action" value="upload_avatar">
                            <label class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded-lg cursor-pointer transition">
                                <i class="fa-solid fa-upload"></i>
                                Upload New
                                <input type="file" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
                            </label>
                            <p class="text-xs text-gray-500 mt-2">JPG, PNG, GIF, WEBP. Max 2MB.</p>
                            <?php if (!empty($errors['avatar'])): ?>
                                <p class="text-xs text-red-400 mt-2"><?php echo htmlspecialchars($errors['avatar']); ?></p>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-white mb-5">Personal Information</h2>
                    <form action="<?php echo URLROOT; ?>/users/profile" method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="profile_info">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Display Name</label>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user->full_name ?? ''); ?>" class="w-full px-4 py-3 bg-gray-800 border <?php echo !empty($errors['full_name']) ? 'border-red-500' : 'border-gray-700'; ?> rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-cyan-500/50">
                            <?php if (!empty($errors['full_name'])): ?><p class="text-xs text-red-400 mt-1"><?php echo htmlspecialchars($errors['full_name']); ?></p><?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user->email ?? ''); ?>" class="w-full px-4 py-3 bg-gray-800 border <?php echo !empty($errors['email']) ? 'border-red-500' : 'border-gray-700'; ?> rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-cyan-500/50">
                            <?php if (!empty($errors['email'])): ?><p class="text-xs text-red-400 mt-1"><?php echo htmlspecialchars($errors['email']); ?></p><?php endif; ?>
                        </div>
                        <button type="submit" class="px-6 py-3 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl transition">Save Changes</button>
                    </form>
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-white mb-5">Change Password</h2>
                    <form action="<?php echo URLROOT; ?>/users/profile" method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="change_password">
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Current Password</label>
                            <input type="password" name="current_password" class="w-full px-4 py-3 bg-gray-800 border <?php echo !empty($errors['current_password']) ? 'border-red-500' : 'border-gray-700'; ?> rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-cyan-500/50">
                            <?php if (!empty($errors['current_password'])): ?><p class="text-xs text-red-400 mt-1"><?php echo htmlspecialchars($errors['current_password']); ?></p><?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">New Password</label>
                            <input type="password" name="new_password" class="w-full px-4 py-3 bg-gray-800 border <?php echo !empty($errors['new_password']) ? 'border-red-500' : 'border-gray-700'; ?> rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-cyan-500/50">
                            <?php if (!empty($errors['new_password'])): ?><p class="text-xs text-red-400 mt-1"><?php echo htmlspecialchars($errors['new_password']); ?></p><?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-2">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="w-full px-4 py-3 bg-gray-800 border <?php echo !empty($errors['confirm_password']) ? 'border-red-500' : 'border-gray-700'; ?> rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-cyan-500/50">
                            <?php if (!empty($errors['confirm_password'])): ?><p class="text-xs text-red-400 mt-1"><?php echo htmlspecialchars($errors['confirm_password']); ?></p><?php endif; ?>
                        </div>
                        <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl transition">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/client/footer.php'; ?>
