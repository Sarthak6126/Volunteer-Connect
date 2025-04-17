<?php
$allowed_files = [
  'applications', 'categories', 'contact_submissions','formdata',
  'locations', 'opportunities', 'organizations', 'users'
];

$file = $_GET['file'] ?? '';
if (!in_array($file, $allowed_files)) {
  die("Invalid file requested.");
}

$file = $_GET['file'] ?? '';
if (!in_array($file, $allowed_files)) {
  die("Invalid file requested.");
}

$filepath = __DIR__ . "/data/$file.json";
if (!file_exists($filepath)) {
  die("File not found.");
}

$json = file_get_contents($filepath);
$data = json_decode($json, true);
if (!$data) {
  die("Invalid JSON format.");
}
if (array_keys($data) !== range(0, count($data) - 1)) {
  $data = [$data]; // wrap object
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View <?= htmlspecialchars(ucfirst($file)) ?> Data</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

<div class="container mx-auto px-6 py-10">
  <div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-blue-700"><?= ucfirst($file) ?> Data</h1>
    <a href="index.php" class="text-blue-500 hover:underline">← Back to Dashboard</a>
  </div>

  <?php if (empty($data)): ?>
    <p class="text-gray-500">No data found in <?= $file ?>.</p>
  <?php else: ?>
    <div class="overflow-auto rounded-lg shadow">
      <table class="min-w-full bg-white border">
        <thead class="bg-blue-600 text-white text-sm">
          <tr>
            <?php foreach (array_keys($data[0]) as $key): ?>
              <th class="px-4 py-2 text-left border"><?= htmlspecialchars($key) ?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody class="text-sm text-gray-700">
          <?php foreach ($data as $row): ?>
            <tr class="border-t hover:bg-gray-100">
              <?php foreach ($row as $value): ?>
                <td class="px-4 py-2 border">
                  <?php
                    if (is_array($value)) {
                      echo "<pre class='whitespace-pre-wrap'>" . htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT)) . "</pre>";
                    } else {
                      echo htmlspecialchars($value);
                    }
                  ?>
                </td>
              <?php endforeach; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

</body>
</html>
