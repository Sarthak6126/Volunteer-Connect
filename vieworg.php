<?php
// Read the organizations data
$file = 'data/organizations.json';

if (file_exists($file)) {
    $json_data = file_get_contents($file);
    $data = json_decode($json_data, true);
} else {
    $data = [];
}

// Get the organization ID from the URL (GET parameter)
$organizationId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$organization = null;

// Find the organization by ID
foreach ($data as $org) {
    if ($org['id'] == $organizationId) {
        $organization = $org;
        break;
    }
}

// Category mapping
$categories = [
    1 => 'Education',
    2 => 'Health',
    3 => 'Environment',
    4 => 'Animal Welfare',
    5 => 'Community Development',
    6 => 'Women Empowerment',
    7 => 'Disaster Relief',
    8 => 'Children & Youth',
    9 => 'Elder Care',
    10 => 'Arts & Culture'
];

// Function to get category name from ID
function getCategoryName($id, $categories) {
    return isset($categories[$id]) ? $categories[$id] : 'Unknown';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $organization ? htmlspecialchars($organization['name']) : 'Organization Details' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gradient-to-b from-blue-50 to-indigo-100 min-h-screen">

<!-- Navigation bar -->
<nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="index.php" class="text-blue-700 font-bold text-xl">
                        <i class="fas fa-hands-helping mr-2"></i>Volunteer Portal
                    </a>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="index.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Home
                    </a>
                    <a href="organization-dashboard.php" class="border-brand-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Organizations
                    </a>
                    <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        Volunteer Opportunities
                    </a>
                    <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                        About Us
                    </a>
                </div>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
            </div>
        </div>
    </div>
</nav>

<div class="py-10 px-4">
    <?php if ($organization): ?>
        <!-- Back button -->
        <div class="max-w-5xl mx-auto mb-6">
            <a href="organization-dashboard.php" class="inline-flex items-center text-brand-600 hover:text-brand-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Organizations
            </a>
        </div>

        <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Hero section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 py-8 px-6 md:px-10 text-white">
                <h1 class="text-3xl md:text-4xl font-bold mb-2"><?= htmlspecialchars($organization['name']) ?></h1>
                <div class="flex flex-wrap items-center gap-3 text-blue-100">
                    <span class="flex items-center">
                        <i class="fas fa-map-marker-alt mr-2"></i> <?= htmlspecialchars($organization['location']) ?>
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i> Est. <?= htmlspecialchars($organization['founded_year']) ?>
                    </span>
                </div>
                
                <!-- Categories badges -->
                <div class="mt-4 flex flex-wrap gap-2">
                    <?php if (isset($organization['primary_category_id'])): ?>
                        <span class="bg-white text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                            <?= getCategoryName($organization['primary_category_id'], $categories) ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if (isset($organization['categories']) && is_array($organization['categories'])): ?>
                        <?php foreach($organization['categories'] as $catId): ?>
                            <?php if ($catId != $organization['primary_category_id']): ?>
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                    <?= getCategoryName($catId, $categories) ?>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Content sections -->
            <div class="p-6 md:p-10">
                <!-- Mission section -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-bullseye text-blue-600 mr-3"></i> Our Mission
                    </h2>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <p class="text-gray-700 italic"><?= nl2br(htmlspecialchars($organization['mission'])) ?></p>
                    </div>
                </section>
                
                <!-- About section -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-3"></i> About Us
                    </h2>
                    <p class="text-gray-600 leading-relaxed"><?= nl2br(htmlspecialchars($organization['description'])) ?></p>
                </section>
                
                <!-- Impact section -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line text-blue-600 mr-3"></i> Our Impact
                    </h2>
                    <p class="text-gray-600 leading-relaxed"><?= nl2br(htmlspecialchars($organization['impact'])) ?></p>
                </section>
                
                <!-- Contact & socials -->
                <section class="grid md:grid-cols-2 gap-8">
                    <!-- Contact info -->
                    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-address-card text-blue-600 mr-2"></i> Contact Information
                        </h3>
                        <ul class="space-y-3">
                            <?php if (!empty($organization['email'])): ?>
                                <li class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-full mr-3">
                                        <i class="fas fa-envelope text-blue-600"></i>
                                    </div>
                                    <a href="mailto:<?= htmlspecialchars($organization['email']) ?>" class="text-blue-600 hover:underline">
                                        <?= htmlspecialchars($organization['email']) ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if (!empty($organization['phone'])): ?>
                                <li class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-full mr-3">
                                        <i class="fas fa-phone text-blue-600"></i>
                                    </div>
                                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $organization['phone']) ?>" class="text-gray-700">
                                        <?= htmlspecialchars($organization['phone']) ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if (!empty($organization['website'])): ?>
                                <li class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-full mr-3">
                                        <i class="fas fa-globe text-blue-600"></i>
                                    </div>
                                    <a href="<?= htmlspecialchars($organization['website']) ?>" target="_blank" rel="noopener" class="text-blue-600 hover:underline">
                                        <?= htmlspecialchars(preg_replace('~^https?://~', '', $organization['website'])) ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <!-- Social media -->
                    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-share-alt text-blue-600 mr-2"></i> Follow Us
                        </h3>
                        
                        <?php 
                        $social_media = $organization['social_media'];

                        // Decode only if it's a JSON string
                        if (is_string($social_media)) {
                            $social_media = json_decode($social_media, true);
                        }
                        
                        $hasSocial = !empty($social_media['facebook']) || 
                                     !empty($social_media['twitter']) || 
                                     !empty($social_media['instagram']);
                        ?>
                        
                        <?php if ($hasSocial): ?>
                            <div class="flex flex-wrap gap-4">
                                <?php if (!empty($social_media['facebook'])): ?>
                                    <a href="<?= htmlspecialchars($social_media['facebook']) ?>" target="_blank" rel="noopener" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                                        <i class="fab fa-facebook-f mr-2"></i> Facebook
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($social_media['twitter'])): ?>
                                    <a href="<?= htmlspecialchars($social_media['twitter']) ?>" target="_blank" rel="noopener" 
                                       class="bg-blue-400 hover:bg-blue-500 text-white px-4 py-2 rounded-lg flex items-center">
                                        <i class="fab fa-twitter mr-2"></i> Twitter
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($social_media['instagram'])): ?>
                                    <a href="<?= htmlspecialchars($social_media['instagram']) ?>" target="_blank" rel="noopener" 
                                       class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white px-4 py-2 rounded-lg flex items-center">
                                        <i class="fab fa-instagram mr-2"></i> Instagram
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 italic">No social media profiles available.</p>
                        <?php endif; ?>
                    </div>
                </section>
                
                <!-- Volunteer button -->
                <div class="mt-10 text-center">
                    <a href="#" class="inline-block bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white px-8 py-3 rounded-lg font-bold text-lg shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-hand-holding-heart mr-2"></i> Volunteer with Us
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="max-w-5xl mx-auto bg-white p-10 rounded-xl shadow-lg text-center">
            <div class="text-red-500 text-6xl mb-4">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Organization Not Found</h1>
            <p class="text-gray-600 mb-6">Sorry, we couldn't find the organization you're looking for.</p>
            <a href="organization-dashboard.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Back to Organizations
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer class="bg-gray-800 text-white py-10 mt-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-xl font-semibold mb-4">Volunteer Portal</h3>
                <p class="text-gray-300">Connecting volunteers with organizations making a difference.</p>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-white">Home</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white">Organizations</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white">Volunteer Opportunities</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white">About Us</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-xl font-semibold mb-4">Contact Us</h3>
                <ul class="space-y-2 text-gray-300">
                    <li class="flex items-center"><i class="fas fa-envelope mr-2"></i> info@volunteerportal.org</li>
                    <li class="flex items-center"><i class="fas fa-phone mr-2"></i> +91 98765 43210</li>
                    <li class="flex items-center"><i class="fas fa-map-marker-alt mr-2"></i> Delhi, NCR</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2025 Volunteer Portal. All rights reserved.</p>
        </div>
    </div>
</footer>

</body>
</html>