<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
$pageTitle = "Home - Volunteer Connect";
$currentPage = "home";
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex-grow ">
        <!-- Hero Section -->
        <section class="relative bg-teal-600 text-white py-20">
            <div class="container mx-auto px-4 "> 
                <div class="md:w-2/3">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">Make a Difference in Your Community</h1>
                    <p class="text-xl mb-8">Connect with local organizations and find volunteer opportunities that match your skills and interests.</p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="opportunities.php" class="bg-white text-teal-600 hover:bg-gray-100 font-semibold px-6 py-3 rounded-lg inline-block transition duration-300 text-center hover:shadow-lg hover:shadow-green-500/50">Find Opportunities</a>
                        <a href="organization-details.php" class="bg-transparent border-2 border-white hover:bg-white hover:text-teal-600 text-white font-semibold px-6 py-3 rounded-lg inline-block transition duration-300 text-center hover:shadow-lg hover:shadow-green-500/50">View Organizations</a>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-0 right-0 w-full h-16 bg-white" style="clip-path: polygon(100% 0, 0% 100%, 100% 100%);"></div>
        </section>

        <!-- Quick Search Section -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Find Your Perfect Volunteer Opportunity</h2>
                <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg p-6">
                    <form id="quickSearchForm" class="grid md:grid-cols-2 gap-4" action="opportunities.php" method="get">
                        <div>
                            <label for="quickCategory" class="block text-sm font-medium text-gray-700 mb-1">Interest Area</label>
                            <select id="quickCategory" name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                <option value="">All Categories</option>
                                <?php
                                $categories = getCategories();
                                foreach ($categories as $category) {
                                    echo "<option value=\"{$category['id']}\">{$category['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label for="quickLocation" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <select id="quickLocation" name="location" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                <option value="">All Locations</option>
                                <?php
                                $locations = getLocations();
                                foreach ($locations as $location) {
                                    echo "<option value=\"{$location['id']}\">{$location['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">Search Opportunities</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Featured Opportunities -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-800">Featured Opportunities</h2>
                    <a href="opportunities.php" class="text-teal-600 hover:text-teal-800 font-medium">View All <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    $featuredOpportunities = getFeaturedOpportunities(3);
                    foreach ($featuredOpportunities as $opportunity) {
                        $org = getOrganizationById($opportunity['organization_id']);
                    ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <span class="px-3 py-1 bg-teal-100 text-teal-800 rounded-full text-sm font-medium"><?= getCategoryNameById($opportunity['category_id']) ?></span>
                                <?php if ($opportunity['is_remote']) { ?>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">Remote</span>
                                <?php } ?>
                            </div>
                            <h3 class="mt-3 text-xl font-semibold text-gray-800"><?= htmlspecialchars($opportunity['title']) ?></h3>
                            <p class="mt-2 text-sm text-gray-600">
                                <i class="fas fa-building mr-2"></i> <?= htmlspecialchars($org['name']) ?>
                            </p>
                            <p class="mt-1 text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt mr-2"></i> <?= htmlspecialchars($opportunity['location']) ?>
                            </p>
                            <p class="mt-3 text-gray-700 line-clamp-3"><?= htmlspecialchars(substr($opportunity['description'], 0, 120)) ?>...</p>
                            <div class="mt-4">
                                <a href="opportunity-details.php?id=<?= $opportunity['id'] ?>" class="inline-block bg-teal-600 hover:bg-teal-700 text-white font-medium px-4 py-2 rounded-md transition duration-300">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </section>

        <!-- Impact Stats -->
        <section class="py-16 bg-teal-600 text-white">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">Our Community Impact</h2>
                <div class="grid md:grid-cols-3 gap-8 text-center">
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg">
                        <div class="text-4xl md:text-5xl font-bold mb-2"><?= getTotalOpportunities() ?></div>
                        <p class="text-xl">Volunteer Opportunities</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg">
                        <div class="text-4xl md:text-5xl font-bold mb-2"><?= getTotalOrganizations() ?></div>
                        <p class="text-xl">Partner Organizations</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg">
                        <div class="text-4xl md:text-5xl font-bold mb-2"><?= getTotalApplications() ?>+</div>
                        <p class="text-xl">Volunteer Applications</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">How It Works</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="bg-teal-100 mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-full">
                            <i class="fas fa-search text-2xl text-teal-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Search</h3>
                        <p class="text-gray-600">Browse opportunities that match your interests, skills, and availability.</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-teal-100 mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-full">
                            <i class="fas fa-paper-plane text-2xl text-teal-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Apply</h3>
                        <p class="text-gray-600">Submit your application directly through our platform.</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-teal-100 mx-auto mb-4 w-16 h-16 flex items-center justify-center rounded-full">
                            <i class="fas fa-hands-helping text-2xl text-teal-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Volunteer</h3>
                        <p class="text-gray-600">Make a difference in your community and gain valuable experience.</p>
                    </div>
                </div>
                <div class="text-center mt-10">
                    <a href="about.php" class="inline-block bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300">Learn More</a>
                </div>
            </div>
        </section>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>
