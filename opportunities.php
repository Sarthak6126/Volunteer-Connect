<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
$pageTitle = "Volunteer Opportunities - Volunteer Connect";
$currentPage = "opportunities";

// Get filter parameters
$categoryId = isset($_GET['category']) ? $_GET['category'] : '';
$locationId = isset($_GET['location']) ? $_GET['location'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$remote = isset($_GET['remote']) ? ($_GET['remote'] === 'on' || $_GET['remote'] === '1') : false;

// Load opportunities with filters
$opportunities = getOpportunities($categoryId, $locationId, $search, $remote);
$categories = getCategories();
$locations = getLocations();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex-grow">
        <!-- Hero Section -->
        <section class="bg-teal-600 text-white py-12">
            <div class="container mx-auto px-4">
                <h1 class="text-3xl md:text-4xl font-bold mb-4">Volunteer Opportunities</h1>
                <p class="text-xl mb-4">Find the perfect opportunity to make a difference in your community.</p>
            </div>
        </section>

        <!-- Search and Filter Section -->
        <section class="bg-white py-8 shadow-md">
            <div class="container mx-auto px-4">
                <form id="filterForm" class="grid md:grid-cols-4 gap-4" action="opportunities.php" method="get">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" id="search" name="search" placeholder="Search by keyword" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select id="category" name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category) { 
                                $selected = $categoryId == $category['id'] ? 'selected' : '';
                            ?>
                                <option value="<?= $category['id'] ?>" <?= $selected ?>><?= htmlspecialchars($category['name']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <select id="location" name="location" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                            <option value="">All Locations</option>
                            <?php foreach ($locations as $location) { 
                                $selected = $locationId == $location['id'] ? 'selected' : '';
                            ?>
                                <option value="<?= $location['id'] ?>" <?= $selected ?>><?= htmlspecialchars($location['name']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <div class="mr-4">
                            <label for="remote" class="flex items-center">
                                <input type="checkbox" id="remote" name="remote" class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50" <?= $remote ? 'checked' : '' ?>>
                                <span class="ml-2 text-sm text-gray-700">Remote Only</span>
                            </label>
                        </div>
                        <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-medium px-4 py-2 rounded-md transition duration-300 h-10">Filter</button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Opportunities Listing -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <div class="mb-6 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">
                        <?= count($opportunities) ?> Opportunities Found
                    </h2>
                    <div>
                        <button id="resetFilters" class="text-teal-600 hover:text-teal-800 text-sm font-medium">
                            <i class="fas fa-redo mr-1"></i> Reset Filters
                        </button>
                    </div>
                </div>

                <?php if (empty($opportunities)): ?>
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="text-5xl text-gray-300 mb-4">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No opportunities found</h3>
                    <p class="text-gray-600 mb-4">Try adjusting your search filters or check back later for new opportunities.</p>
                    <button id="clearFilters" class="bg-teal-600 hover:bg-teal-700 text-white font-medium px-4 py-2 rounded-md transition duration-300">Clear Filters</button>
                </div>
                <?php else: ?>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($opportunities as $opportunity): 
                        $org = getOrganizationById($opportunity['organization_id']);
                    ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <span class="px-3 py-1 bg-teal-100 text-teal-800 rounded-full text-sm font-medium"><?= getCategoryNameById($opportunity['category_id']) ?></span>
                                <?php if ($opportunity['is_remote']): ?>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">Remote</span>
                                <?php endif; ?>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($opportunity['title']) ?></h3>
                            <p class="text-sm text-gray-600 mb-1">
                                <i class="fas fa-building mr-2"></i> <?= htmlspecialchars($org['name']) ?>
                            </p>
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

        <!-- Call to Action -->
        <section class="py-12 bg-teal-600 text-white">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold mb-4">Can't Find What You're Looking For?</h2>
                <p class="text-xl mb-8 max-w-2xl mx-auto">
                    New volunteer opportunities are added regularly. Contact us to discuss your interests and we'll help you find the perfect match.
                </p>
                <a href="contact.php" class="bg-white text-teal-600 hover:bg-gray-100 font-semibold px-6 py-3 rounded-lg inline-block transition duration-300">Contact Us</a>
            </div>
        </section>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <script src="js/search.js"></script>
</body>
</html>
