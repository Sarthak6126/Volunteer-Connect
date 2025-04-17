<?php
$showSuccess = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Directory check and creation if needed
    $directory = 'data';
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    
    // Form validation and sanitization
    $organization = [
        "id" => filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT),
        "name" => htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES),
        "primary_category_id" => filter_input(INPUT_POST, 'primary_category_id', FILTER_VALIDATE_INT),
        "categories" => isset($_POST['categories']) ? array_map('intval', $_POST['categories']) : [],
        "location" => htmlspecialchars(trim($_POST['location'] ?? ''), ENT_QUOTES),
        "description" => htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES),
        "mission" => htmlspecialchars(trim($_POST['mission'] ?? ''), ENT_QUOTES),
        "impact" => htmlspecialchars(trim($_POST['impact'] ?? ''), ENT_QUOTES),
        "website" => filter_input(INPUT_POST, 'website', FILTER_SANITIZE_URL),
        "email" => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
        "phone" => htmlspecialchars(trim($_POST['phone'] ?? ''), ENT_QUOTES),
        "founded_year" => htmlspecialchars(trim($_POST['founded_year'] ?? ''), ENT_QUOTES),
        "social_media" => [
            "facebook" => filter_input(INPUT_POST, 'facebook', FILTER_SANITIZE_URL),
            "twitter" => filter_input(INPUT_POST, 'twitter', FILTER_SANITIZE_URL),
            "instagram" => filter_input(INPUT_POST, 'instagram', FILTER_SANITIZE_URL)
        ],
        "date_added" => date('Y-m-d H:i:s')
    ];

    $file = 'data/organizations.json';

    // Read existing data or create new array
    if (file_exists($file)) {
        $json_data = file_get_contents($file);
        $data = json_decode($json_data, true) ?: [];
    } else {
        $data = [];
    }

    $data[] = $organization;
    
    // Save data with proper error handling
    if (file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT))) {
        $showSuccess = true;
    } else {
        $error = "Failed to save data. Please check file permissions.";
    }
}

// Define categories for reuse
$categories = [
    ['id' => 1, 'name' => 'Education'],
    ['id' => 2, 'name' => 'Health'],
    ['id' => 3, 'name' => 'Environment'],
    ['id' => 4, 'name' => 'Animal Welfare'],
    ['id' => 5, 'name' => 'Community Development'],
    ['id' => 6, 'name' => 'Women Empowerment'],
    ['id' => 7, 'name' => 'Disaster Relief'],
    ['id' => 8, 'name' => 'Children & Youth'],
    ['id' => 9, 'name' => 'Elder Care'],
    ['id' => 10, 'name' => 'Arts & Culture']
];

// Define locations
$locations = [
    'Dehradun, Uttarakhand',
    'Delhi, NCR',
    'Mumbai, Maharashtra',
    'Chennai, Tamil Nadu',
    'Shimla, Himachal Pradesh',
    'Bengaluru, Karnataka',
    'Kolkata, West Bengal',
    'Jaipur, Rajasthan',
    'Ahmedabad, Gujarat',
    'Hyderabad, Telangana',
    'Pune, Maharashtra',
    'Lucknow, Uttar Pradesh'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Organization | Volunteer Portal</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50: '#f0f9ff',
              100: '#e0f2fe',
              200: '#bae6fd',
              300: '#7dd3fc',
              400: '#38bdf8',
              500: '#0ea5e9',
              600: '#0284c7',
              700: '#0369a1',
              800: '#075985',
              900: '#0c4a6e',
            },
          }
        }
      }
    }
  </script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-100 min-h-screen flex flex-col items-center justify-center p-4 md:p-8">

  <?php if (isset($error)): ?>
    <div class="w-full max-w-4xl mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow" role="alert">
      <p class="font-medium"><i class="fas fa-exclamation-circle mr-2"></i> Error</p>
      <p><?php echo $error; ?></p>
    </div>
  <?php endif; ?>

  <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl overflow-hidden">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 text-white">
      <h1 class="text-3xl font-bold flex items-center gap-3">
        <i class="fas fa-building"></i> Add New Organization
      </h1>
      <p class="opacity-80 mt-2">Complete the form below to add a new organization to our volunteer network</p>
    </div>
    
    <form method="POST" class="p-6 md:p-10 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="md:col-span-2 p-4 bg-blue-50 rounded-lg border border-blue-100 text-sm text-blue-700">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <i class="fas fa-info-circle mt-0.5"></i>
          </div>
          <div class="ml-3">
            <h3 class="font-semibold">Organization Information</h3>
            <p>Fields marked with an asterisk (*) are required.</p>
          </div>
        </div>
      </div>

      <div class="md:col-span-2">
        <label for="id" class="block text-sm font-medium text-gray-700 mb-1">Organization ID*</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-id-card text-gray-400"></i>
          </div>
          <input type="number" name="id" id="id" placeholder="Unique Identifier" required
                 class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
        </div>
      </div>

      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Organization Name*</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-landmark text-gray-400"></i>
          </div>
          <input type="text" name="name" id="name" placeholder="Full organization name" required
                 class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
        </div>
      </div>

      <div>
        <label for="founded_year" class="block text-sm font-medium text-gray-700 mb-1">Founded Year</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-calendar-alt text-gray-400"></i>
          </div>
          <input type="text" name="founded_year" id="founded_year" placeholder="e.g. 2010" 
                 class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
        </div>
      </div>

      <div>
        <label for="primary_category_id" class="block text-sm font-medium text-gray-700 mb-1">Primary Category*</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-tag text-gray-400"></i>
          </div>
          <select name="primary_category_id" id="primary_category_id" required
                  class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition appearance-none">
            <option value="">Select primary category</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
            <i class="fas fa-chevron-down text-gray-400"></i>
          </div>
        </div>
      </div>

      <div class="md:col-span-2">
        <label for="categories" class="block text-sm font-medium text-gray-700 mb-1">Secondary Categories*</label>
        <div class="relative">
          <div class="absolute top-3 left-3 flex items-center pointer-events-none">
            <i class="fas fa-tags text-gray-400"></i>
          </div>
          <select name="categories[]" id="categories" multiple required
                  class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition h-32">
            <?php foreach ($categories as $category): ?>
              <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
          </select>
          <p class="mt-1 text-sm text-gray-500">Hold Ctrl (or Cmd) to select multiple categories</p>
        </div>
      </div>

      <div class="md:col-span-2">
        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location*</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-map-marker-alt text-gray-400"></i>
          </div>
          <input type="text" name="location" id="location" list="locations" placeholder="City, State" required 
                 class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
          <datalist id="locations">
            <?php foreach ($locations as $location): ?>
              <option value="<?php echo $location; ?>">
            <?php endforeach; ?>
          </datalist>
        </div>
      </div>

      <div class="md:col-span-2">
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description*</label>
        <div class="relative">
          <div class="absolute top-3 left-3 flex items-center pointer-events-none">
            <i class="fas fa-align-left text-gray-400"></i>
          </div>
          <textarea name="description" id="description" placeholder="Provide a brief overview of the organization" required
                    class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition h-24"></textarea>
        </div>
      </div>

      <div class="md:col-span-2">
        <label for="mission" class="block text-sm font-medium text-gray-700 mb-1">Mission*</label>
        <div class="relative">
          <div class="absolute top-3 left-3 flex items-center pointer-events-none">
            <i class="fas fa-bullseye text-gray-400"></i>
          </div>
          <textarea name="mission" id="mission" placeholder="What is the organization's mission statement?" required
                    class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition h-24"></textarea>
        </div>
      </div>

      <div class="md:col-span-2">
        <label for="impact" class="block text-sm font-medium text-gray-700 mb-1">Impact*</label>
        <div class="relative">
          <div class="absolute top-3 left-3 flex items-center pointer-events-none">
            <i class="fas fa-chart-line text-gray-400"></i>
          </div>
          <textarea name="impact" id="impact" placeholder="Describe the organization's impact on the community" required
                    class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition h-24"></textarea>
        </div>
      </div>

      <div class="md:col-span-2 p-4 bg-blue-50 rounded-lg border border-blue-100 text-sm text-blue-700 mt-4">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <i class="fas fa-address-card mt-0.5"></i>
          </div>
          <div class="ml-3">
            <h3 class="font-semibold">Contact Information</h3>
            <p>Add ways for volunteers to contact the organization</p>
          </div>
        </div>
      </div>

      <div>
        <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-globe text-gray-400"></i>
          </div>
          <input type="url" name="website" id="website" placeholder="https://www.example.org" 
                 class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
        </div>
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-envelope text-gray-400"></i>
          </div>
          <input type="email" name="email" id="email" placeholder="contact@example.org" 
                 class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
        </div>
      </div>

      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-phone text-gray-400"></i>
          </div>
          <input type="text" name="phone" id="phone" placeholder="+91 98765 43210" 
                 class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
        </div>
      </div>

      <div class="md:col-span-2 p-4 bg-blue-50 rounded-lg border border-blue-100 text-sm text-blue-700 mt-4">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <i class="fas fa-share-alt mt-0.5"></i>
          </div>
          <div class="ml-3">
            <h3 class="font-semibold">Social Media</h3>
            <p>Share the organization's social media profiles</p>
          </div>
        </div>
      </div>

      <div>
        <label for="facebook" class="block text-sm font-medium text-gray-700 mb-1">Facebook</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fab fa-facebook-f text-gray-400"></i>
          </div>
          <input type="url" name="facebook" id="facebook" placeholder="https://facebook.com/organization" 
                 class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
        </div>
      </div>

      <div>
        <label for="twitter" class="block text-sm font-medium text-gray-700 mb-1">Twitter</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fab fa-twitter text-gray-400"></i>
          </div>
          <input type="url" name="twitter" id="twitter" placeholder="https://twitter.com/organization" 
                 class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
        </div>
      </div>

      <div>
        <label for="instagram" class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fab fa-instagram text-gray-400"></i>
          </div>
          <input type="url" name="instagram" id="instagram" placeholder="https://instagram.com/organization" 
                 class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
        </div>
      </div>

      <div class="md:col-span-2 flex gap-4 pt-6">
        <button type="reset" class="w-1/4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-6 rounded-lg transition-all flex items-center justify-center gap-2">
          <i class="fas fa-undo"></i> Reset
        </button>
        <button type="submit" class="w-3/4 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-semibold py-3 px-6 rounded-lg transition-all flex items-center justify-center gap-2">
          <i class="fas fa-save"></i> Save Organization
        </button>
      </div>
    </form>
  </div>

  <div class="w-full max-w-4xl mt-4 text-center text-gray-600 text-sm">
    <p>© 2025 Volunteer Portal. All rights reserved.</p>
  </div>

  <?php if ($showSuccess): ?>
    <div id="successModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
      <div class="bg-white rounded-lg p-8 shadow-xl max-w-md text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-check text-3xl text-green-500"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Organization Added Successfully!</h3>
        <p class="text-gray-600 mb-6">Your organization has been added to our database.</p>
        <div class="flex justify-center">
          <button onclick="window.location.href='organization-dashboard.php'" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition-all">
            Go to Dashboard
          </button>
        </div>
      </div>
    </div>
    <script>
      // Redirect after showing the success modal briefly
      setTimeout(function() {
        window.location.href = "http://localhost/PHPScript/VOLUNTEER%20PROJECTN/organization-dashboard.php";
      }, 2000);
    </script>
  <?php endif; ?>

</body>
</html>