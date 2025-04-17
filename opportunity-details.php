<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get organization ID from URL
$id = isset($_GET['id']) ? $_GET['id'] : '';

// If no ID provided, redirect to organizations page
if (empty($id)) {
    header('Location: organizations.php');
    exit;
}

// Get organization details
$organization = getOrganizationById($id);

// If organization not found, redirect to organizations page
if (!$organization) {
    header('Location: organizations.php');
    exit;
}

// Get organization opportunities
$opportunities = getOrganizationOpportunities($id);

$pageTitle = htmlspecialchars($organization['name']) . " - Volunteer Connect";
$currentPage = "organizations";

// Get organization categories
$orgCategories = getOrganizationCategories($id);
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex-grow">
        <!-- Hero Section -->
        <section class="bg-teal-600 text-white py-12">
            <div class="container mx-auto px-4">
                <div class="mb-2">
                    <a href="organizations.php" class="text-white hover:text-teal-100 transition duration-300">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Organizations
                    </a>
                </div>
                <div class="flex flex-col md:flex-row md:items-center justify-between">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold mb-2"><?= htmlspecialchars($organization['name']) ?></h1>
                        <p class="text-xl mb-2">
                            <i class="fas fa-map-marker-alt mr-2"></i> <?= htmlspecialchars($organization['location']) ?>
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <a href="contact.php?org=<?= $organization['id'] ?>" class="bg-white text-teal-600 hover:bg-teal-50 font-bold px-6 py-3 rounded-lg inline-block transition duration-300">
                            Contact Organization
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Organization Details -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="md:col-span-2">
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <div class="flex flex-wrap gap-2 mb-6">
                                <?php foreach ($orgCategories as $catId): ?>
                                <span class="px-3 py-1 bg-teal-100 text-teal-800 rounded-full text-sm font-medium">
                                    <?= getCategoryNameById($catId) ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                            
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">About the Organization</h2>
                            <div class="text-gray-700 mb-8 space-y-4">
                                <?php 
                                // Split paragraphs and render them
                                $paragraphs = explode("\n", $organization['description']);
                                foreach ($paragraphs as $paragraph) {
                                    if (trim($paragraph) !== '') {
                                        echo "<p>" . htmlspecialchars($paragraph) . "</p>";
                                    }
                                }
                                ?>
                            </div>
                            
                            <?php if (!empty($organization['mission'])): ?>
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Our Mission</h2>
                            <div class="text-gray-700 mb-8 space-y-4">
                                <?php 
                                // Split paragraphs and render them
                                $missionParagraphs = explode("\n", $organization['mission']);
                                foreach ($missionParagraphs as $paragraph) {
                                    if (trim($paragraph) !== '') {
                                        echo "<p>" . htmlspecialchars($paragraph) . "</p>";
                                    }
                                }
                                ?>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($organization['impact'])): ?>
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Our Impact</h2>
                            <div class="text-gray-700 mb-8 space-y-4">
                                <?php 
                                // Split paragraphs and render them
                                $impactParagraphs = explode("\n", $organization['impact']);
                                foreach ($impactParagraphs as $paragraph) {
                                    if (trim($paragraph) !== '') {
                                        echo "<p>" . htmlspecialchars($paragraph) . "</p>";
                                    }
                                }
                                ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mt-8">
                                <a href="contact.php?org=<?= $organization['id'] ?>" class="bg-teal-600 hover:bg-teal-700 text-white font-bold px-6 py-3 rounded-lg inline-block transition duration-300">
                                    Contact Organization
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sidebar -->
                    <div>
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Details</h3>
                            <ul class="space-y-4">
                                <li class="flex items-start">
                                    <div class="text-teal-600 mr-3 mt-1">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-700">Location</h4>
                                        <p class="text-gray-600"><?= htmlspecialchars($organization['location']) ?></p>
                                    </div>
                                </li>
                                <?php if (!empty($organization['website'])): ?>
                                <li class="flex items-start">
                                    <div class="text-teal-600 mr-3 mt-1">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-700">Website</h4>
                                        <a href="<?= htmlspecialchars($organization['website']) ?>" target="_blank" class="text-teal-600 hover:text-teal-800 transition duration-300">
                                            <?= htmlspecialchars(preg_replace('#^https?://#', '', $organization['website'])) ?>
                                        </a>
                                    </div>
                                </li>
                                <?php endif; ?>
                                <?php if (!empty($organization['phone'])): ?>
                                <li class="flex items-start">
                                    <div class="text-teal-600 mr-3 mt-1">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-700">Phone</h4>
                                        <p class="text-gray-600"><?= htmlspecialchars($organization['phone']) ?></p>
                                    </div>
                                </li>
                                <?php endif; ?>
                                <?php if (!empty($organization['email'])): ?>
                                <li class="flex items-start">
                                    <div class="text-teal-600 mr-3 mt-1">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-700">Email</h4>
                                        <a href="mailto:<?= htmlspecialchars($organization['email']) ?>" class="text-teal-600 hover:text-teal-800 transition duration-300">
                                            <?= htmlspecialchars($organization['email']) ?>
                                        </a>
                                    </div>
                                </li>
                                <?php endif; ?>
                                <?php if (!empty($organization['founded_year'])): ?>
                                <li class="flex items-start">
                                    <div class="text-teal-600 mr-3 mt-1">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-700">Founded</h4>
                                        <p class="text-gray-600"><?= htmlspecialchars($organization['founded_year']) ?></p>
                                    </div>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <?php if (!empty($organization['social_media'])): ?>
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Follow Us</h3>
                            <div class="flex space-x-4">
                                <?php 
                                $socialMedia = json_decode($organization['social_media'], true);
                                if (!empty($socialMedia['facebook'])): 
                                ?>
                                <a href="<?= htmlspecialchars($socialMedia['facebook']) ?>" target="_blank" class="text-teal-600 hover:text-teal-800 transition duration-300" title="Facebook">
                                    <i class="fab fa-facebook-square text-2xl"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($socialMedia['twitter'])): ?>
                                <a href="<?= htmlspecialchars($socialMedia['twitter']) ?>" target="_blank" class="text-teal-600 hover:text-teal-800 transition duration-300" title="Twitter">
                                    <i class="fab fa-twitter-square text-2xl"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($socialMedia['instagram'])): ?>
                                <a href="<?= htmlspecialchars($socialMedia['instagram']) ?>" target="_blank" class="text-teal-600 hover:text-teal-800 transition duration-300" title="Instagram">
                                    <i class="fab fa-instagram-square text-2xl"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($socialMedia['linkedin'])): ?>
                                <a href="<?= htmlspecialchars($socialMedia['linkedin']) ?>" target="_blank" class="text-teal-600 hover:text-teal-800 transition duration-300" title="LinkedIn">
                                    <i class="fab fa-linkedin text-2xl"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Volunteer Opportunities -->
        <section class="py-12 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl font-bold text-gray-800 mb-8">Volunteer Opportunities</h2>
                
                <?php if (empty($opportunities)): ?>
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="text-5xl text-gray-300 mb-4">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No opportunities currently available</h3>
                    <p class="text-gray-600 mb-4">This organization doesn't have any active volunteer opportunities at the moment. Check back later or contact them directly for more information.</p>
                    <a href="contact.php?org=<?= $organization['id'] ?>" class="bg-teal-600 hover:bg-teal-700 text-white font-medium px-4 py-2 rounded-md transition duration-300">Contact Organization</a>
                </div>
                <?php else: ?>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($opportunities as $opportunity): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <span class="px-3 py-1 bg-teal-100 text-teal-800 rounded-full text-sm font-medium">
                                    <?= getCategoryNameById($opportunity['category_id']) ?>
                                </span>
                                <?php if ($opportunity['is_remote']): ?>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">Remote</span>
                                <?php endif; ?>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($opportunity['title']) ?></h3>
                            <p class="text-sm text-gray-600 mb-1">
                                <i class="fas fa-map-marker-alt mr-2"></i> <?= htmlspecialchars($opportunity['location']) ?>
                            </p>
                            <p class="text-sm text-gray-600 mb-3">
                                <i class="fas fa-clock mr-2"></i> <?= htmlspecialchars($opportunity['commitment']) ?>
                            </p>
                            <p class="text-gray-700 mb-4 line-clamp-3"><?= htmlspecialchars(substr($opportunity['description'], 0, 120)) ?>...</p>
                            <div class="mt-2">
                                <a href="opportunity-details.php?id=<?= $opportunity['id'] ?>" class="inline-block bg-teal-600 hover:bg-teal-700 text-white font-medium px-4 py-2 rounded-md transition duration-300">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Similar Organizations -->
        <section class="py-12 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl font-bold text-gray-800 mb-8">Similar Organizations</h2>
                
                <div class="grid md:grid-cols-3 gap-6">
                    <?php 
                    $similarOrganizations = getSimilarOrganizations($id, $organization['primary_category_id']);
                    foreach ($similarOrganizations as $similarOrg): 
                    ?>
                    <div class="bg-gray-50 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <span class="px-3 py-1 bg-teal-100 text-teal-800 rounded-full text-sm font-medium">
                                    <?= getCategoryNameById($similarOrg['primary_category_id']) ?>
                                </span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($similarOrg['name']) ?></h3>
                            <p class="text-sm text-gray-600 mb-3">
                                <i class="fas fa-map-marker-alt mr-2"></i> <?= htmlspecialchars($similarOrg['location']) ?>
                            </p>
                            <p class="text-gray-700 mb-4 line-clamp-3"><?= htmlspecialchars(substr($similarOrg['description'], 0, 120)) ?>...</p>
                            <div class="mt-2">
                                <a href="organization-details.php?id=<?= $similarOrg['id'] ?>" class="inline-block bg-teal-600 hover:bg-teal-700 text-white font-medium px-4 py-2 rounded-md transition duration-300">View Profile</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>
