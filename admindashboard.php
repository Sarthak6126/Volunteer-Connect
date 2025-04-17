<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: adminlogin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

<div class="container mx-auto px-6 py-10">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-blue-600">📁 Admin Dashboard</h1>
    <a href="adminlogout.php" class="text-sm text-red-500 hover:underline">Logout</a>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
    <?php
      $files = [
        'applications' => 'Applications',
        'categories' => 'Categories',
        'contact_submissions' => 'Contact Submissions',
        'locations' => 'Locations',
        'opportunities' => 'Opportunities',
        'organizations' => 'Organizations',
        'users' => 'Users',
        'formdata' => 'Organization Form Submission'  // New Module
      ];

      foreach ($files as $key => $label):
    ?>
      <a href="view.php?file=<?= $key ?>" class="block p-6 bg-white rounded-lg shadow-md hover:bg-blue-100 transition">
        <h2 class="text-xl font-semibold text-blue-700"><?= $label ?></h2>
        <p class="text-sm text-gray-500 mt-2">View all <?= strtolower($label) ?> data</p>
      </a>
    <?php endforeach; ?>
  </div>
</div>

</body>
</html>
