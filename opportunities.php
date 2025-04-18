<?php
$pageTitle = "Volunteer Opportunities - Volunteer Connect";
$currentPage = "opportunities";

// Filter params (Get values from the URL)
$categoryId = isset($_GET['category']) ? $_GET['category'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$remote = isset($_GET['remote']) ? ($_GET['remote'] === 'on' || $_GET['remote'] === '1') : false;

// Load JSON data
$jsonFilePath = 'data/opportunities.json'; // Adjust the path as needed
if (file_exists($jsonFilePath)) {
    $jsonData = file_get_contents($jsonFilePath); // Assuming the file is in the same directory
    $allOpportunities = json_decode($jsonData, true); // Decode JSON data into an associative array
} else {
    echo "<p>Error: The file 'opportunities.json' could not be found.</p>";
    exit; // Stop the script if the file is not found
}

// Filter the data
$opportunities = [];
foreach ($allOpportunities as $op) {
    $matchCategory = !$categoryId || $op['category_id'] == $categoryId;
    $matchLocation = !$location || stripos($op['location'], $location) !== false;
    $matchSearch = !$search || stripos($op['title'], $search) !== false || stripos($op['description'], $search) !== false;
    $matchRemote = !$remote || $op['is_remote'];

    // Add to the result if all conditions match
    if ($matchCategory && $matchLocation && $matchSearch && $matchRemote) {
        $opportunities[] = $op;
    }
}

// Categories and Locations for dropdowns (you can hardcode or fetch from other sources)
$categories = [
    1 => "Youth Services",
    2 => "Community Support",
    3 => "Arts & Culture",
    4 => "Technology",
    5 => "Environment",
    6 => "Animal Welfare"
];

$locations = [
    'Downtown', 'Westside', 'Eastside', 'Northwest Area', 'Virtual/Remote', 'City-wide'
];

// Get Organization Name based on ID
$organizations = [
    1 => "GreenThumb Initiative",
    2 => "Youth Empowerment Network",
    3 => "Virtual Tutors United",
    4 => "Happy Tails Animal Shelter",
    5 => "Community Food Bank",
    6 => "Senior Support Services",
    7 => "Nature Trail Conservancy",
    8 => "Tech for Good",
    9 => "City Art Museum",
    10 => "Disaster Relief Agency"
];

// Include the header
include 'OpporHead.php';
?>

<!-- Custom CSS for animations -->
<style>
    .hover-float {
        transition: transform 0.3s ease;
    }
    .hover-float:hover {
        transform: translateY(-8px);
    }
    .fade-in {
        animation: fadeIn 0.6s ease-in;
    }
    @keyframes fadeIn {
        0% { opacity: 0; transform: translateY(10px); }
        100% { opacity: 1; transform: translateY(0); }
    }
</style>

<!-- Hero Section with Parallax Effect -->
<section class="relative bg-gradient-to-br from-teal-700 via-teal-600 to-teal-500 text-white py-24">
    <div class="absolute inset-0 overflow-hidden opacity-10">
        <div class="absolute inset-0" style="background-image: url('https://images.unsplash.com/photo-1544027993-37dbfe43562a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;"></div>
    </div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">Make a Difference Through Volunteering</h1>
            <p class="text-xl md:text-2xl font-light mb-8 max-w-xl">Discover opportunities that match your skills and passion in your community.</p>
            <div class="flex flex-wrap gap-4">
                <a href="#opportunities" class="bg-white text-teal-700 hover:bg-gray-100 font-medium px-6 py-3 rounded-lg inline-block transition-all duration-300 shadow-lg hover:shadow-xl">
                    Browse Opportunities
                </a>
                <a href="#filter" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-teal-700 font-medium px-6 py-3 rounded-lg inline-block transition-all duration-300">
                    Filter Results
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Search and Filter Section with Card Design -->
<section id="filter" class="bg-white py-12 shadow-lg rounded-t-3xl -mt-8 relative z-20">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Find Your Perfect Opportunity</h2>
        <form id="filterForm" class="bg-gray-50 rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow duration-300 max-w-5xl mx-auto" action="opportunities.php" method="get">
            <div class="grid md:grid-cols-4 gap-6">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Keywords</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="search" name="search" placeholder="Search by keyword" class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50" value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-tags text-gray-400"></i>
                        </div>
                        <select id="category" name="category" class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $id => $name): ?>
                                <option value="<?= $id ?>" <?= $categoryId == $id ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-map-marker-alt text-gray-400"></i>
                        </div>
                        <select id="location" name="location" class="w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                            <option value="">All Locations</option>
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?= $loc ?>" <?= $location == $loc ? 'selected' : '' ?>><?= $loc ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col justify-end">
                    <div class="mb-4">
                        <label for="remote" class="flex items-center">
                            <input type="checkbox" id="remote" name="remote" class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50" <?= $remote ? 'checked' : '' ?>>
                            <span class="ml-2 text-sm text-gray-700">Remote Only</span>
                        </label>
                    </div>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-medium px-4 py-3 rounded-lg transition duration-300 w-full flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Opportunities Listing -->
<section id="opportunities" class="py-16 bg-gradient-to-b from-gray-50 to-white">
    <div class="container mx-auto px-4">
        <div class="mb-10 flex flex-wrap justify-between items-center">
            <div class="mb-4 md:mb-0">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">
                    <span class="text-teal-600"><?= count($opportunities) ?></span> Opportunities Found
                </h2>
                <p class="text-gray-600">Matching your search criteria</p>
            </div>
            
            <?php if (!empty($opportunities) && ($search || $categoryId || $location || $remote)): ?>
            <div class="text-gray-600">
                <button id="clearFilters" class="flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition duration-300">
                    <i class="fas fa-times-circle mr-2 text-teal-600"></i> Clear All Filters
                </button>
            </div>
            <?php endif; ?>
        </div>

        <?php if (empty($opportunities)): ?>
            <div class="bg-white rounded-2xl shadow-md p-12 text-center max-w-2xl mx-auto border border-gray-100 fade-in">
                <div class="text-6xl text-gray-300 mb-6">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-700 mb-3">No opportunities found</h3>
                <p class="text-gray-600 mb-8">Try adjusting your search filters or check back later for new opportunities.</p>
                <button id="clearFilters" class="bg-teal-600 hover:bg-teal-700 text-white font-medium px-6 py-3 rounded-lg transition duration-300 shadow-md">
                    <i class="fas fa-redo mr-2"></i> Reset Filters
                </button>
            </div>
        <?php else: ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($opportunities as $index => $opportunity): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover-float fade-in" style="animation-delay: <?= $index * 0.1 ?>s">
                    <div class="relative">
                        <?php 
                        // Generate a background color based on category
                        $bgColors = [
                            1 => "bg-blue-500",
                            2 => "bg-purple-500",
                            3 => "bg-pink-500",
                            4 => "bg-cyan-500",
                            5 => "bg-lime-500",
                            6 => "bg-amber-500"
                        ];
                        $bgColor = $bgColors[$opportunity['category_id']] ?? "bg-teal-500";
                        ?>
                        <div class="h-3 <?= $bgColor ?>"></div>
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <span class="px-3 py-1 bg-teal-100 text-teal-800 rounded-full text-sm font-medium">
                                    <?= $categories[$opportunity['category_id']] ?>
                                </span>
                                <?php if ($opportunity['is_remote']): ?>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium flex items-center">
                                        <i class="fas fa-laptop-house mr-1"></i> Remote
                                    </span>
                                <?php endif; ?>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-3 line-clamp-2">
                                <?= htmlspecialchars($opportunity['title']) ?>
                            </h3>
                            <div class="space-y-2 mb-4">
                                <p class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-building mr-2 text-teal-600"></i> 
                                    <span class="font-medium"><?= htmlspecialchars($organizations[$opportunity['organization_id']] ?? 'Unknown') ?></span>
                                </p>
                                <p class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2 text-teal-600"></i> 
                                    <?= htmlspecialchars($opportunity['location']) ?>
                                </p>
                                <p class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-clock mr-2 text-teal-600"></i> 
                                    <?= htmlspecialchars($opportunity['commitment']) ?>
                                </p>
                            </div>
                            <div class="border-t border-gray-100 pt-4 mt-2">
                                <p class="text-gray-700 mb-5 line-clamp-3">
                                    <?= htmlspecialchars(substr($opportunity['description'], 0, 150)) ?>...
                                </p>
                                <div class="flex items-center justify-between">
                                    <a href="opportunity-details.php?id=<?= $opportunity['id'] ?>" 
                                       class="inline-flex items-center bg-teal-600 hover:bg-teal-700 text-white font-medium px-5 py-2 rounded-lg transition duration-300 shadow-sm">
                                        View Details
                                        <i class="fas fa-arrow-right ml-2"></i>
                                    </a>
                                    <button class="text-teal-600 hover:text-teal-800 transition duration-300" title="Save for later">
                                        <i class="far fa-heart text-xl"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination (could be connected later) -->
            <?php if (count($opportunities) > 9): ?>
            <div class="mt-12 flex justify-center">
                <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Previous</span>
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                    <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-teal-600 text-sm font-medium text-white hover:bg-teal-700">
                        1
                    </a>
                    <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                        2
                    </a>
                    <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                        3
                    </a>
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                        ...
                    </span>
                    <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                        8
                    </a>
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Next</span>
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                </nav>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Feature Grid -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Why Volunteer With Us</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-all duration-300 text-center">
                <div class="w-16 h-16 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-handshake text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-800">Make an Impact</h3>
                <p class="text-gray-600">Contribute to meaningful causes and create real change in your community.</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-all duration-300 text-center">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-800">Build Connections</h3>
                <p class="text-gray-600">Meet like-minded individuals and expand your personal and professional network.</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-all duration-300 text-center">
                <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-brain text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-800">Develop Skills</h3>
                <p class="text-gray-600">Gain valuable experience and develop new skills that enhance your resume.</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-all duration-300 text-center">
                <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-heart text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-gray-800">Feel Good</h3>
                <p class="text-gray-600">Experience the satisfaction and wellbeing that comes from helping others.</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial Section -->
<section class="py-20 bg-gradient-to-br from-teal-600 to-teal-800 text-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl font-bold mb-12">What Our Volunteers Say</h2>
            <div class="relative">
                <div class="text-5xl absolute -top-6 left-0 opacity-30">
                    <i class="fas fa-quote-left"></i>
                </div>
                <p class="text-xl md:text-2xl font-light italic mb-8">
                    Volunteering through this platform has been one of the most rewarding experiences of my life. 
                    I've met amazing people and developed skills I never knew I had. The team made it so easy to find 
                    opportunities that matched my interests and schedule.
                </p>
                <div class="text-5xl absolute -bottom-6 right-0 opacity-30">
                    <i class="fas fa-quote-right"></i>
                </div>
            </div>
            <div class="mt-8">
                <div class="w-16 h-16 rounded-full overflow-hidden mx-auto mb-3 border-2 border-white shadow-md">
                    <img src="https://media.istockphoto.com/id/1306460911/vector/volunteers-needed-symbol-volunteering-service-sign-vector.jpg?s=612x612&w=0&k=20&c=BXOz799EABnRKB4Is8AUmCdGRhoq33D1_-EENxl_mHY=" alt="Testimonial" class="w-full h-full object-cover">
                </div>
                <p class="font-semibold text-lg">Sarah Johnson</p>
                <p class="text-teal-200">Environmental Volunteer</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="bg-gradient-to-r from-teal-700 to-teal-500 rounded-2xl p-12 text-white text-center shadow-xl overflow-hidden relative">
            <div class="absolute inset-0 bg-black opacity-10 pattern-diagonal-lines-sm"></div>
            <div class="relative z-10">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Can't Find What You're Looking For?</h2>
                <p class="text-xl mb-8 max-w-2xl mx-auto">
                    New volunteer opportunities are added regularly. Contact us to discuss your interests and we'll help you find the perfect match.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="contact.php" class="bg-white text-teal-600 hover:bg-gray-100 font-semibold px-8 py-4 rounded-lg inline-block transition-all duration-300 shadow-md hover:shadow-lg">
                        Contact Us
                    </a>
                    <a href="organizations.php" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-teal-600 font-semibold px-8 py-4 rounded-lg inline-block transition-all duration-300">
                        Partner Organizations
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for the clear filters button and other enhancements -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Clear filters functionality
    const clearFiltersBtn = document.querySelectorAll('#clearFilters');
    clearFiltersBtn.forEach(function(btn) {
        btn.addEventListener('click', function() {
            window.location.href = 'opportunities.php';
        });
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    
    // Add heart toggle functionality
    document.querySelectorAll('.fa-heart').forEach(heart => {
        heart.parentElement.addEventListener('click', function() {
            heart.classList.toggle('far');
            heart.classList.toggle('fas');
            heart.classList.toggle('text-red-500');
        });
    });
});
</script>

<?php
// Include the footer
include 'OpporFooter.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Optional: Link to your own stylesheets -->
    <link rel="stylesheet" href="css/styles.css">  <!-- Your custom CSS file -->
</head>