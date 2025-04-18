<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get current user data
$user = getCurrentUser();
if (!$user) {
    // User data not found, log them out
    logoutUser();
    header('Location: login.php');
    exit;
}

// Get user profile data
$profile = isset($user['profile']) ? $user['profile'] : [];

// Process form submission
$success = false;
$error = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    
    if (empty($first_name) || empty($last_name)) {
        $error = "First name and last name are required";
    } else {
        // Update basic user info
        $user['first_name'] = $first_name;
        $user['last_name'] = $last_name;

        // If the password field is set and is not empty, hash it
        if (!empty($_POST['password'])) {
            // Hash the new password (if the user entered a new password)
            $user['password'] = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
        }

        // Update profile data based on user type
        if ($user['user_type'] === 'volunteer') {
            $profile['bio'] = trim($_POST['bio'] ?? '');
            $profile['location'] = trim($_POST['location'] ?? '');
            $profile['availability'] = trim($_POST['availability'] ?? '');
            
            // Handle skills (comma-separated)
            $skills = trim($_POST['skills'] ?? '');
            $profile['skills'] = $skills ? explode(',', $skills) : [];
            
            // Handle interests (array of category IDs)
            $profile['interests'] = isset($_POST['interests']) ? $_POST['interests'] : [];
        } elseif ($user['user_type'] === 'organization') {
            $profile['position'] = trim($_POST['position'] ?? '');
        }
        
        // Save profile data to user record
        $user['profile'] = $profile;
        
        // Save updated user data
        if (saveUser($user)) {
            $success = "Your profile has been updated successfully!";
        } else {
            $error = "There was a problem saving your changes. Please try again.";
        }
    }
}

// Get categories for volunteers to select interests
$categories = getCategories();

$pageTitle = "Edit Profile - Volunteer Connect";
$currentPage = "account";
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex-grow">
        <!-- Hero Section -->
        <section class="bg-teal-600 text-white py-12">
            <div class="container mx-auto px-4">
                <h1 class="text-3xl md:text-4xl font-bold mb-2">Edit Profile</h1>
                <p class="text-xl">
                    Update your personal information
                </p>
            </div>
        </section>

        <!-- Main Content -->
        <main class="py-12">
            <div class="container mx-auto px-4">
                <?php if ($success): ?>
                <div class="mb-8 bg-green-50 border-l-4 border-green-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-green-700 font-medium"><?= $success ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-red-700 font-medium"><?= $error ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="p-6 border-b">
                        <div class="flex justify-between">
                            <h3 class="text-xl font-bold text-gray-800">Edit Profile Information</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <form action="edit-profile.php" method="POST">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                    <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500" required>
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <input type="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50" disabled>
                                    <p class="mt-1 text-sm text-gray-500">Email address cannot be changed</p>
                                </div>
                                
                                <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input type="password" id="password" name="password" placeholder="Enter new password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white" minlength="8">
                                <p class="mt-1 text-sm text-gray-500">Leave blank if you do not want to change your password</p>
                            </div>


                                <div>
                                    <label for="user_type" class="block text-sm font-medium text-gray-700 mb-1">Account Type</label>
                                    <input type="text" id="user_type" value="<?= ucfirst(htmlspecialchars($user['user_type'])) ?>" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50" disabled>
                                </div>
                                
                                <?php if ($user['user_type'] === 'volunteer'): ?>
                                <div class="md:col-span-2">
                                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                                    <textarea id="bio" name="bio" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500"><?= isset($profile['bio']) ? htmlspecialchars($profile['bio']) : '' ?></textarea>
                                    <p class="mt-1 text-sm text-gray-500">Tell us a bit about yourself and your volunteering interests</p>
                                </div>
                                <div>
                                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                    <input type="text" id="location" name="location" value="<?= isset($profile['location']) ? htmlspecialchars($profile['location']) : '' ?>" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500">
                                </div>
                                <div>
                                    <label for="availability" class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                                    <input type="text" id="availability" name="availability" value="<?= isset($profile['availability']) ? htmlspecialchars($profile['availability']) : '' ?>" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500">
                                    <p class="mt-1 text-sm text-gray-500">E.g., "Weekends only", "Tuesday evenings", etc.</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="skills" class="block text-sm font-medium text-gray-700 mb-1">Skills & Interests</label>
                                    <input type="text" id="skills" name="skills" 
                                        value="<?= isset($profile['skills']) && is_array($profile['skills']) ? htmlspecialchars(implode(',', $profile['skills'])) : '' ?>" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500">
                                    <p class="mt-1 text-sm text-gray-500">Enter skills separated by commas (e.g., "teaching, social media, event planning")</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Interest Categories</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                                        <?php foreach ($categories as $category): ?>
                                        <div class="flex items-center">
                                            <input type="checkbox" id="interest_<?= $category['id'] ?>" name="interests[]" value="<?= $category['id'] ?>"
                                                <?= isset($profile['interests']) && is_array($profile['interests']) && in_array($category['id'], $profile['interests']) ? 'checked' : '' ?>
                                                class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                                            <label for="interest_<?= $category['id'] ?>" class="ml-2 block text-sm text-gray-700">
                                                <?= htmlspecialchars($category['name']) ?>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php elseif ($user['user_type'] === 'organization'): ?>
                                <div class="md:col-span-2">
                                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Your Position</label>
                                    <input type="text" id="position" name="position" value="<?= isset($profile['position']) ? htmlspecialchars($profile['position']) : '' ?>" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500">
                                    <p class="mt-1 text-sm text-gray-500">Your role or position in the organization</p>
                                </div>
                                <?php endif; ?>
                                
                                <div class="md:col-span-2">
                                    <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-md shadow-sm transition duration-300">
                                        Save Changes
                                    </button>
                                    <a href="account.php" class="px-4 py-2 ml-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-md shadow-sm transition duration-300">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php if ($success): ?>
    <div class="mb-8 bg-green-50 border-l-4 border-green-500 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-green-700 font-medium"><?= $success ?></p>
            </div>
        </div>
    </div>

    <!-- Add JavaScript alert and redirect -->
    <script>
        alert("Your profile has been updated successfully!");
        window.location.href = "http://localhost/PHPScript/VOLUNTEER%20PROJECTN/profile.php";
    </script>
<?php endif; ?>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
