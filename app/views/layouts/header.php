<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<header class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between">
        <h1 class="text-xl font-bold">Cloud Arena</h1>
        <nav>
            <a href="<?php echo URLROOT; ?>" class="px-2">Home</a>
            <a href="<?php echo URLROOT; ?>/pages/about" class="px-2">About</a>
            <a href="<?php echo URLROOT; ?>/products" class="px-2">Products</a>
            <a href="<?php echo URLROOT; ?>/pages/contact" class="px-2">Contact</a>
        </nav>
    </div>
</header>
<main class="container mx-auto py-8">
