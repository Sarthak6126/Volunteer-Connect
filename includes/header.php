<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Volunteer Connect' ?></title>
    
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
    <nav class="container mx-auto px-4 py-4 ">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center ">
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
                        <a href="index.php" class="block py-2 text-gray-700 hover:text-teal-600 transition duration-300 <?= ($currentPage == 'home') ? 'text-teal-600 font-semibold' : '' ?>">Home</a>
                    </li>
                    <li>
                        <a href="opportunities.php" class="block py-2 text-gray-700 hover:text-teal-600 transition duration-300 <?= ($currentPage == 'opportunities') ? 'text-teal-600 font-semibold' : '' ?>">Opportunities</a>
                    </li>
                    <li>
                        <a href="organization-details.php" class="block py-2 text-gray-700 hover:text-teal-600 transition duration-300 <?= ($currentPage == 'organizations') ? 'text-teal-600 font-semibold' : '' ?>">Organizations</a>
                    </li>
                    <li>
                        <a href="about.php" class="block py-2 text-gray-700 hover:text-teal-600 transition duration-300 <?= ($currentPage == 'about') ? 'text-teal-600 font-semibold' : '' ?>">About</a>
                    </li>
                    <li>
                        <a href="contact.php" class="block py-2 text-gray-700 hover:text-teal-600 transition duration-300 <?= ($currentPage == 'contact') ? 'text-teal-600 font-semibold' : '' ?>">Contact</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                    <li class="relative group">
                        <a href="#" class="block py-2 text-gray-700 hover:text-teal-600 transition duration-300 <?= ($currentPage == 'account') ? 'text-teal-600 font-semibold' : '' ?>">
                            <i class="fas fa-user-circle mr-1"></i> Account
                        </a>
                        <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                            <a href="profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">My Profile</a>
                            <a href="my-applications.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">My Applications</a>
                            <div class="border-t border-gray-100"></div>
                            <a href="logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
                        </div>
                    </li>
                    <?php else: ?>
                    <li class="md:ml-4">
                        <a href="login.php" class="inline-block py-2 px-4 border border-teal-600 text-teal-600 rounded hover:bg-teal-600 hover:text-white transition duration-300 <?= ($currentPage == 'login') ? 'bg-teal-600 text-white' : '' ?>">Login</a>
                    </li>
                    <li class="md:ml-2">
                        <a href="signup.php" class="inline-block py-2 px-4 bg-teal-600 text-white rounded hover:bg-teal-700 transition duration-300 <?= ($currentPage == 'signup') ? 'bg-teal-700' : '' ?>">Sign Up</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <!-- <button class="border-2 border-white rounded-full px-4 py-1 text-white bg-gray-800 w-fit ml-4" id="mod-lap ">Dark</button> -->
            </div>
        </div>
    </nav>
</header>

