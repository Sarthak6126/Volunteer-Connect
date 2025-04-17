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
$email = '';

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSubmitted = true;
    
    // Get form data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $rememberMe = isset($_POST['remember_me']) ? true : false;
    
    // Validate form data
    if (empty($email)) {
        $formError = 'Please enter your email address.';
    } elseif (empty($password)) {
        $formError = 'Please enter your password.';
    } else {
        // Attempt to authenticate user
        $user = authenticateUser($email, $password);
        
        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            
            // If remember me is checked, set a cookie
            if ($rememberMe) {
                setcookie('user_email', $email, time() + (86400 * 30), "/"); // 30 days
            }
            
            // Redirect to appropriate page based on user type
            if ($user['user_type'] === 'organization') {
                header('Location: organization-dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $formError = 'Invalid email or password.';
        }
    }
}

$pageTitle = "Login - Volunteer Connect";
$currentPage = "login";
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex-grow">
        <!-- Main Content -->
        <main class="py-12">
            <div class="container mx-auto px-4">
                <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="py-4 px-6 bg-teal-600 text-white text-center">
                        <h2 class="text-2xl font-bold">Login to Your Account</h2>
                    </div>
                    
                    <div class="p-6">
                        <?php if ($formSubmitted && !empty($formError)): ?>
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
                        
                        <form action="login.php" method="post">
                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input type="password" id="password" name="password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50" required>
                            </div>
                            
                            <div class="mb-6 flex items-center">
                                <input type="checkbox" id="remember_me" name="remember_me" class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                                    Remember me
                                </label>
                            </div>
                            
                            <div class="mb-6">
                                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                                    Login
                                </button>
                            </div>
                            
                            <div class="text-center text-sm">
                                <a href="#" class="text-teal-600 hover:text-teal-800">Forgot your password?</a>
                            </div>
                        </form>
                    </div>
                    
                    <div class="py-4 px-6 bg-gray-50 border-t text-center">
                        <p class="text-gray-700">Don't have an account? <a href="signup.php" class="text-teal-600 hover:text-teal-800 font-medium">Sign up now</a></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Focus on email field when page loads
        document.getElementById('email').focus();
    });
    </script>
</body>
</html>