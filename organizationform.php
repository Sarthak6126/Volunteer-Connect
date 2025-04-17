<?php
$showSuccess = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $contact = htmlspecialchars($_POST['contact']);
    $organizations = isset($_POST['organizations']) ? $_POST['organizations'] : [];

    $entry = [
        'name' => $name,
        'email' => $email,
        'contact' => $contact,
        'organizations' => $organizations,
        'timestamp' => date("Y-m-d H:i:s")
    ];

    $file = 'data/formdata.json';
    if (file_exists($file)) {
        $json_data = file_get_contents($file);
        $data = json_decode($json_data, true);
    } else {
        $data = [];
    }

    $data[] = $entry;
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

    // Set flag for success (used in JavaScript alert)
    $showSuccess = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submission Form</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="bg-white shadow-md rounded-lg p-8 max-w-xl w-full">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Organization Submission Form</h2>

    <form action="" method="POST" class="space-y-5">
      <div>
        <label class="block text-gray-700 font-medium">Name</label>
        <input type="text" name="name" required class="w-full border border-gray-300 rounded px-4 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-400">
      </div>

      <div>
        <label class="block text-gray-700 font-medium">Email</label>
        <input type="email" name="email" required class="w-full border border-gray-300 rounded px-4 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-400">
      </div>

      <div>
        <label class="block text-gray-700 font-medium">Contact Number</label>
        <input type="text" name="contact" required class="w-full border border-gray-300 rounded px-4 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-400">
      </div>

      <div>
        <label class="block text-gray-700 font-medium mb-2">Select the organizations where you want to apply.</label>
        <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
          <label><input type="checkbox" name="organizations[]" value="Red Cross" class="mr-2"> Green India Foundation</label>
          <label><input type="checkbox" name="organizations[]" value="UNICEF" class="mr-2"> Youth Empowerment Foundation of India</label>
          <label><input type="checkbox" name="organizations[]" value="WWF" class="mr-2"> Education First India</label>
          <label><input type="checkbox" name="organizations[]" value="Doctors Without Borders" class="mr-2"> Happy Tails Foundation</label>
          <label><input type="checkbox" name="organizations[]" value="Amnesty International" class="mr-2"> Community Food Bank India</label>
          <label><input type="checkbox" name="organizations[]" value="Greenpeace" class="mr-2"> Senior Support Services India</label>
          <label><input type="checkbox" name="organizations[]" value="Save the Children" class="mr-2"> Trails and Wilderness Coalition India</label>
        </div>
      </div>

      <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">Submit</button>
    </form>
  </div>

  <?php if ($showSuccess): ?>
    <script>
      alert("✅ Submission Successful! We will Contact You Soon");
      window.location.href = "http://localhost/PHPScript/VOLUNTEER%20PROJECTN/organization-details.php";
    </script>
  <?php endif; ?>

</body>
</html>
