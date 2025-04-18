<?php
$pageTitle = "Organization Dashboard";

// Load organizations from JSON file
$orgDataPath = 'organizations.json';
$organizations = [];

if (file_exists($orgDataPath)) {
    $jsonData = file_get_contents($orgDataPath);
    $organizations = json_decode($jsonData, true);
}
require_once 'includes/config.php';
require_once 'includes/functions.php';
$pageTitle = "Organizations - Volunteer Connect";
$currentPage = "organizations";

// Get filter parameters
$categoryId = isset($_GET['category']) ? $_GET['category'] : '';
$locationId = isset($_GET['location']) ? $_GET['location'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Load organizations with filters
$organizations = getOrganizations($categoryId, $locationId, $search);
$categories = getCategories();
$locations = getLocations();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

<!-- Header -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Connect</title>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom styles -->
    <link rel="stylesheet" href="css/custom.css">
    
    <style>
        /* Tailwind CSS customization */
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        teal: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </style>
</head>

<!-- Navigation -->
<header class="bg-white shadow-lg shadow-green-500/50">
    <nav class="container mx-auto px-4 py-4">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center">
            <div class="flex items-center justify-between">
                <a href="index.php" class="text-2xl font-bold text-teal-600 shadow-lg shadow-green-500/50">
                    <i class="fas fa-hands-helping mr-2"></i> Volunteer Connect
                </a>
                <button id="mobileMenuBtn" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            
            <div id="mobileMenu" class="md:flex items-center mt-4 md:mt-0 hidden md:block">
                <ul class="flex flex-col md:flex-row md:items-center md:space-x-8">
                    <li>
                        <a href="about.php" class="block py-2 text-gray-700 hover:text-teal-600 transition duration-300">About</a>
                    </li>
                    <li>
                        <a href="contact.php" class="block py-2 text-gray-700 hover:text-teal-600 transition duration-300">Contact</a>
                    </li>
                    <!-- Change this section based on login status -->
                    <!-- Assuming the user is logged in, adjust this part manually -->
                    <li class="relative group">
                        <a href="#" class="block py-2 text-gray-700 hover:text-teal-600 transition duration-300">
                            <i class="fas fa-user-circle mr-1"></i> Account
                        </a>
                        <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                            <a href="profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">My Profile</a>
                            <div class="border-t border-gray-100"></div>
                            <a href="logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100">Logout</a>
                        </div>
                    </li>
                    <!-- If user is not logged in, display login/signup buttons -->
                    <!-- <li class="md:ml-4">
                        <a href="login.php" class="inline-block py-2 px-4 border border-teal-600 text-teal-600 rounded hover:bg-teal-600 hover:text-white transition duration-300">Login</a>
                    </li>
                    <li class="md:ml-2">
                        <a href="signup.php" class="inline-block py-2 px-4 bg-teal-600 text-white rounded hover:bg-teal-700 transition duration-300">Sign Up</a>
                    </li> -->
                </ul>
            </div>
        </div>
    </nav>
</header>


<!-- Main -->
<main class="flex-grow container mx-auto px-4 py-10">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Organization Dashboard</h1>
        <a href="addorg.php" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-6 py-2 rounded-lg transition duration-300">
            <i class="fas fa-plus mr-2"></i> Add New Organization
        </a>
    </div>

    <?php if (!empty($organizations)) : ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($organizations as $org) : ?>
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($org['name']) ?></h2>
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-map-marker-alt mr-1"></i> <?= htmlspecialchars($org['location']) ?>
                    </p>
                    <p class="text-gray-700 line-clamp-3 mb-4">
                        <?= htmlspecialchars(substr($org['description'], 0, 120)) ?>...
                    </p>
                    <a href="vieworg.php?id=<?= $org['id'] ?? '' ?>" class="inline-block bg-teal-600 hover:bg-teal-700 text-white font-medium px-4 py-2 rounded-md transition duration-300">
                        View Details
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p class="text-gray-600">No organizations found.</p>
    <?php endif; ?>
    
</main>

<!-- Footer -->
<!-- Footer -->
<footer class="bg-teal-600 text-white py-12">
    <div class="container mx-auto px-4">
        <!-- Footer Content -->
        <div class="grid md:grid-cols-4 gap-8">
            <!-- Column 1 - About -->
            <div>
                <h3 class="text-xl font-bold mb-4">Volunteer Connect</h3>
                <p class="text-gray-400 mb-4">
                    Connecting passionate volunteers with organizations making a difference in our community.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-white hover:text-white transition duration-300">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-white hover:text-white transition duration-300">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.instagram.com/mr__ashish__2208/?next=%2Fmr__ashish__2208%2F" class="text-gray-400 hover:text-white transition duration-300">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.linkedin.com/feed/?trk=guest_homepage-basic_google-one-tap-submit" class="text-gray-400 hover:text-white transition duration-300">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
            
            <!-- Column 2 - Quick Links -->
            
            
            <!-- Column 3 - Categories -->
            <div>
                <ul class="space-y-2">
                    <?php
                    $footerCategories = getCategories();
                    $displayCount = min(count($footerCategories), 5);
                    for ($i = 0; $i < $displayCount; $i++) {
                        echo '<li><a href="opportunities.php?category=' . $footerCategories[$i]['id'] . '" class="text-gray-400 hover:text-white transition duration-300">' . htmlspecialchars($footerCategories[$i]['name']) . '</a></li>';
                    }
                    ?>
                </ul>
            </div>
            
            <!-- Column 4 - Contact Info -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                <ul class="space-y-2 text-gray-400">
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-3"></i>
                        <span>Jalandhar-Delhi G.T. Road, Phagwara, Punjab (India) - 144411. </span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-phone mt-1 mr-3"></i>
                        <span>6266928927,8708755231</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-envelope mt-1 mr-3"></i>
                        <a href="mailto:info@volunteerconnect.org" class="hover:text-white transition duration-300">lpuvolunteer.org</a>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-clock mt-1 mr-3"></i>
                        <span>Mon-Fri: 9:00 AM - 5:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Bottom Footer -->
        <div class="border-t border-gray-700 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-400 text-sm mb-4 md:mb-0">
                &copy; <?= date('Y') ?> Volunteer Connect. All rights reserved.
            </p>
            <div class="flex space-x-6">
                <a href="#" class="text-gray-400 hover:text-white text-sm transition duration-300">Privacy Policy</a>
                <a href="#" class="text-gray-400 hover:text-white text-sm transition duration-300">Terms of Service</a>
                <a href="#" class="text-gray-400 hover:text-white text-sm transition duration-300">Accessibility</a>
            </div>
        </div>
    </div>
</footer>


</body>
</html>
