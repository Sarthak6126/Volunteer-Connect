<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Initialize variables
$formSubmitted = false;
$formSuccess = false;
$formError = '';
$formData = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => '',
    'user_type' => 'volunteer',
    'organization_id' => ''
];

// Process signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSubmitted = true;
    
    // Get form data
    $formData = [
        'first_name' => isset($_POST['first_name']) ? trim($_POST['first_name']) : '',
        'last_name' => isset($_POST['last_name']) ? trim($_POST['last_name']) : '',
        'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
        'password' => isset($_POST['password']) ? $_POST['password'] : '',
        'confirm_password' => isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '',
        'user_type' => isset($_POST['user_type']) ? $_POST['user_type'] : 'volunteer',
        'organization_id' => isset($_POST['organization_id']) ? $_POST['organization_id'] : ''
    ];
    
    // Validate form data
    if (empty($formData['first_name'])) {
        $formError = 'Please enter your first name.';
    } elseif (empty($formData['last_name'])) {
        $formError = 'Please enter your last name.';
    } elseif (empty($formData['email'])) {
        $formError = 'Please enter your email address.';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $formError = 'Please enter a valid email address.';
    } elseif (empty($formData['password'])) {
        $formError = 'Please enter a password.';
    } elseif (strlen($formData['password']) < 8) {
        $formError = 'Password must be at least 8 characters long.';
    } elseif ($formData['password'] !== $formData['confirm_password']) {
        $formError = 'Passwords do not match.';
    } elseif ($formData['user_type'] === 'organization' && empty($formData['organization_id'])) {
        $formError = 'Please select an organization.';
    } else {
        // Check if email already exists
        $existingUser = getUserByEmail($formData['email']);
        if ($existingUser) {
            $formError = 'Email address is already registered.';
        } else {
            // Create user data array
            $userData = [
                'email' => $formData['email'],
                'password' => $formData['password'],
                'first_name' => $formData['first_name'],
                'last_name' => $formData['last_name'],
                'user_type' => $formData['user_type'],
                'profile' => []
            ];
            
            // Add organization ID if user is an organization member
            if ($formData['user_type'] === 'organization') {
                $userData['profile']['organization_id'] = $formData['organization_id'];
            }
            
            // Register user
            $result = registerUser($userData);
            
            if ($result) {
                // Auto-login the user
                $user = authenticateUser($formData['email'], $formData['password']);
                
                if ($user) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_type'] = $user['user_type'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    
                    // Redirect to appropriate page based on user type
                    if ($user['user_type'] === 'organization') {
                        header('Location: organization-dashboard.php');
                    } else {
                        header('Location: profile.php?first_login=1');
                    }
                    exit;
                } else {
                    $formSuccess = true; // Show success message
                }
            } else {
                $formError = 'An error occurred during registration. Please try again.';
            }
        }
    }
}

// Get organizations for dropdown
$organizations = getOrganizations();

$pageTitle = "Sign Up - Volunteer Connect";
$currentPage = "signup";
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex-grow">
        <!-- Main Content -->
        <main class="py-12">
            <div class="container mx-auto px-4">
                <div class="max-w-xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="py-4 px-6 bg-teal-600 text-white text-center">
                        <h2 class="text-2xl font-bold">Create an Account</h2>
                    </div>
                    
                    <div class="p-6">
                        <?php if ($formSubmitted && $formSuccess): ?>
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-green-700">
                                        Your account has been created successfully! You can now <a href="login.php" class="font-medium underline">login</a>.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php elseif ($formSubmitted && !empty($formError)): ?>
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-500"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-red-700">
                                        <?= htmlspecialchars($formError) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!$formSuccess): ?>
                        <form action="signup.php" method="post">
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-700 mb-3 pb-2 border-b">Account Type</h3>
                                <div class="flex space-x-6">
                                    <div class="flex items-center">
                                        <input type="radio" id="volunteer" name="user_type" value="volunteer" <?= ($formData['user_type'] === 'volunteer') ? 'checked' : '' ?> class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300">
                                        <label for="volunteer" class="ml-2 block text-sm font-medium text-gray-700">
                                            I want to volunteer (Individual)
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" id="organization" name="user_type" value="organization" <?= ($formData['user_type'] === 'organization') ? 'checked' : '' ?> class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300">
                                        <label for="organization" class="ml-2 block text-sm font-medium text-gray-700">
                                            I represent an organization
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="organization-select" class="mb-6 <?= ($formData['user_type'] === 'organization') ? '' : 'hidden' ?>">
                                <label for="organization_id" class="block text-sm font-medium text-gray-700 mb-1">Select Your Organization</label>
                                <select id="organization_id" name="organization_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    <option value="">-- Select Organization --</option>
                                    <?php foreach ($organizations as $org): ?>
                                    <option value="<?= $org['id'] ?>" <?= ($formData['organization_id'] == $org['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($org['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="mt-1 text-sm text-gray-500">Don't see your organization? <a href="contact.php" class="text-teal-600 hover:underline">Contact us</a> to get it added.</p>
                            </div>
                            
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-700 mb-3 pb-2 border-b">Personal Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($formData['first_name']) ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50" required>
                                    </div>
                                    <div>
                                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($formData['last_name']) ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-700 mb-3 pb-2 border-b">Account Information</h3>
                                <div class="mb-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50" required>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                        <input type="password" id="password" name="password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50" required>
                                        <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                                    </div>
                                    <div>
                                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                        <input type="password" id="confirm_password" name="confirm_password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <div class="flex items-center">
                                    <input type="checkbox" id="terms" name="terms" required class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    <label for="terms" class="ml-2 block text-sm text-gray-700">
                                        I agree to the <a href="#" class="text-teal-600 hover:underline">Terms of Service</a> and <a href="#" class="text-teal-600 hover:underline">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                                    Create Account
                                </button>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                    
                    <div class="py-4 px-6 bg-gray-50 border-t text-center">
                        <p class="text-gray-700">Already have an account? <a href="login.php" class="text-teal-600 hover:text-teal-800 font-medium">Login here</a></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show/hide organization select based on account type
        const volunteerRadio = document.getElementById('volunteer');
        const organizationRadio = document.getElementById('organization');
        const organizationSelect = document.getElementById('organization-select');
        
        function toggleOrganizationSelect() {
            if (organizationRadio.checked) {
                organizationSelect.classList.remove('hidden');
            } else {
                organizationSelect.classList.add('hidden');
            }
        }
        
        volunteerRadio.addEventListener('change', toggleOrganizationSelect);
        organizationRadio.addEventListener('change', toggleOrganizationSelect);
    });
    </script>
</body>
</html>