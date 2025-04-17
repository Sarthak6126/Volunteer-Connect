<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($id === 'LPU' && $password === 'LPU123') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admindashboard.php');
        exit;
    } else {
        $error = 'Invalid ID or Password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded shadow-md w-full max-w-md">
  <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">🔐 Admin Login</h2>
  
  <?php if ($error): ?>
    <p class="text-red-500 text-sm mb-4"><?= $error ?></p>
  <?php endif; ?>

  <form method="POST">
    <label class="block mb-2 font-medium">ID</label>
    <input type="text" name="id" required class="w-full p-2 border border-gray-300 rounded mb-4">

    <label class="block mb-2 font-medium">Password</label>
    <input type="password" name="password" required class="w-full p-2 border border-gray-300 rounded mb-6">

    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Login</button>
  </form>
</div>

</body>
</html>
