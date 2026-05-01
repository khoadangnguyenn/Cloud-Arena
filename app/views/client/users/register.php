<?php require APPROOT . '/views/layouts/client/header.php'; ?>
<div class="min-h-[80vh] flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
  <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
    <div>
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Đăng ký tài khoản
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Đã có tài khoản?
        <a href="<?php echo URLROOT; ?>/users/login" class="font-medium text-primary hover:text-blue-500">
          Đăng nhập ngay
        </a>
      </p>
    </div>
    <form class="mt-8 space-y-6" action="<?php echo URLROOT; ?>/users/register" method="POST">
      <div class="rounded-md shadow-sm -space-y-px">
        <div class="mb-4">
          <label for="username" class="sr-only">Tên đăng nhập</label>
          <input id="username" name="username" type="text" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm <?php echo (!empty($data['username_err'])) ? 'border-red-500' : ''; ?>" placeholder="Tên đăng nhập" value="<?php echo $data['username']; ?>">
          <span class="text-xs text-red-500"><?php echo $data['username_err']; ?></span>
        </div>
        <div class="mb-4">
          <label for="email" class="sr-only">Địa chỉ Email</label>
          <input id="email" name="email" type="email" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm <?php echo (!empty($data['email_err'])) ? 'border-red-500' : ''; ?>" placeholder="Địa chỉ Email" value="<?php echo $data['email']; ?>">
          <span class="text-xs text-red-500"><?php echo $data['email_err']; ?></span>
        </div>
        <div class="mb-4">
          <label for="password" class="sr-only">Mật khẩu</label>
          <input id="password" name="password" type="password" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm <?php echo (!empty($data['password_err'])) ? 'border-red-500' : ''; ?>" placeholder="Mật khẩu" value="<?php echo $data['password']; ?>">
          <span class="text-xs text-red-500"><?php echo $data['password_err']; ?></span>
        </div>
        <div>
          <label for="confirm_password" class="sr-only">Xác nhận mật khẩu</label>
          <input id="confirm_password" name="confirm_password" type="password" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm <?php echo (!empty($data['confirm_password_err'])) ? 'border-red-500' : ''; ?>" placeholder="Xác nhận mật khẩu" value="<?php echo $data['confirm_password']; ?>">
          <span class="text-xs text-red-500"><?php echo $data['confirm_password_err']; ?></span>
        </div>
      </div>

      <div>
        <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
          Đăng ký
        </button>
      </div>
    </form>
  </div>
</div>
<?php require APPROOT . '/views/layouts/client/footer.php'; ?>
