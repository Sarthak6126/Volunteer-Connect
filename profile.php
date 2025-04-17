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

// Check for first login
$firstLogin = isset($_GET['first_login']) && $_GET['first_login'] == 1;

// Get user profile data
$profile = isset($user['profile']) ? $user['profile'] : [];

// Get user's applications if user is a volunteer
$applications = [];
if ($user['user_type'] === 'volunteer') {
    $applicationsFile = DATA_PATH . 'applications.json';
    if (file_exists($applicationsFile)) {
        $json = file_get_contents($applicationsFile);
        $allApplications = json_decode($json, true);
        
        // Filter to only include this user's applications
        foreach ($allApplications as $app) {
            if ($app['email'] === $user['email']) {
                // Get opportunity and organization details
                $opportunity = getOpportunityById($app['opportunity_id']);
                $organization = getOrganizationById($app['organization_id']);
                
                $app['opportunity_title'] = $opportunity ? $opportunity['title'] : 'Unknown Opportunity';
                $app['organization_name'] = $organization ? $organization['name'] : 'Unknown Organization';
                
                $applications[] = $app;
            }
        }
    }
}

// Get organization details if user is organization admin
$organization = null;
if ($user['user_type'] === 'organization' && isset($profile['organization_id'])) {
    $organization = getOrganizationById($profile['organization_id']);
}

$pageTitle = "My Profile - Volunteer Connect";
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
                <h1 class="text-3xl md:text-4xl font-bold mb-2">My Profile</h1>
                <p class="text-xl">
                    Manage your account and track your volunteer activities
                </p>
            </div>
        </section>

        <!-- Main Content -->
        <main class="py-12">
            <div class="container mx-auto px-4">
                <!-- First Login Welcome Message -->
                <?php if ($firstLogin): ?>
                <div class="mb-8 bg-teal-50 border-l-4 border-teal-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-teal-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-teal-700 font-medium">
                                Welcome to Volunteer Connect! Your account has been created successfully.
                            </p>
                            <p class="text-teal-700 mt-1">
                                Complete your profile to help match you with the perfect volunteer opportunities.
                            </p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Sidebar -->
                    <div class="md:col-span-1">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6 border-b">
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-32 h-32 rounded-full bg-teal-100 flex items-center justify-center text-5xl text-teal-600 mb-4">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <h2 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h2>
                                    <p class="text-gray-600"><?= htmlspecialchars($user['email']) ?></p>
                                    <div class="mt-2">
                                        <span class="px-3 py-1 bg-<?= ($user['user_type'] === 'volunteer') ? 'blue' : 'green' ?>-100 text-<?= ($user['user_type'] === 'volunteer') ? 'blue' : 'green' ?>-800 rounded-full text-sm font-medium">
                                            <?= ucfirst(htmlspecialchars($user['user_type'])) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <nav class="flex flex-col">
                                    <a href="#profile" class="px-4 py-3 hover:bg-gray-50 font-medium text-teal-600 border-l-4 border-teal-600">Profile Information</a>
                                    <?php if ($user['user_type'] === 'volunteer'): ?>
                                    <a href="#applications" class="px-4 py-3 hover:bg-gray-50 font-medium">My Applications</a>
                                    <a href="#saved" class="px-4 py-3 hover:bg-gray-50 font-medium">Saved Opportunities</a>
                                    <?php elseif ($user['user_type'] === 'organization'): ?>
                                    <a href="#organization" class="px-4 py-3 hover:bg-gray-50 font-medium">Organization Details</a>
                                    <a href="#opportunities" class="px-4 py-3 hover:bg-gray-50 font-medium">Our Opportunities</a>
                                    <a href="#applications" class="px-4 py-3 hover:bg-gray-50 font-medium">Volunteer Applications</a>
                                    <?php endif; ?>
                                    <a href="#settings" class="px-4 py-3 hover:bg-gray-50 font-medium">Account Settings</a>
                                </nav>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Main Content Area -->
                    <div class="md:col-span-2">
                        <!-- Profile Information -->
                        <div id="profile" class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                            <div class="p-6 border-b">
                                <div class="flex justify-between">
                                    <h3 class="text-xl font-bold text-gray-800">Profile Information</h3>
                                    <a href="#" class="text-teal-600 hover:text-teal-800">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">First Name</h4>
                                        <p class="text-gray-800"><?= htmlspecialchars($user['first_name']) ?></p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Last Name</h4>
                                        <p class="text-gray-800"><?= htmlspecialchars($user['last_name']) ?></p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Email Address</h4>
                                        <p class="text-gray-800"><?= htmlspecialchars($user['email']) ?></p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Account Type</h4>
                                        <p class="text-gray-800"><?= ucfirst(htmlspecialchars($user['user_type'])) ?></p>
                                    </div>
                                    
                                    <?php if ($user['user_type'] === 'volunteer'): ?>
                                    <div class="md:col-span-2">
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Bio</h4>
                                        <p class="text-gray-800">
                                            <?= isset($profile['bio']) ? htmlspecialchars($profile['bio']) : '<span class="text-gray-400">No bio provided yet. Click Edit to add your bio.</span>' ?>
                                        </p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Location</h4>
                                        <p class="text-gray-800">
                                            <?= isset($profile['location']) ? htmlspecialchars($profile['location']) : '<span class="text-gray-400">Not specified</span>' ?>
                                        </p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Availability</h4>
                                        <p class="text-gray-800">
                                            <?= isset($profile['availability']) ? htmlspecialchars($profile['availability']) : '<span class="text-gray-400">Not specified</span>' ?>
                                        </p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Skills & Interests</h4>
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <?php if (isset($profile['skills']) && is_array($profile['skills'])): ?>
                                                <?php foreach ($profile['skills'] as $skill): ?>
                                                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm"><?= htmlspecialchars($skill) ?></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                            <span class="text-gray-400">No skills specified</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="md:col-span-2">
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Interest Categories</h4>
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <?php 
                                            if (isset($profile['interests']) && is_array($profile['interests'])): 
                                                $categories = getCategories();
                                                foreach ($profile['interests'] as $categoryId):
                                                    foreach ($categories as $category):
                                                        if ($category['id'] == $categoryId):
                                            ?>
                                            <span class="px-3 py-1 bg-teal-100 text-teal-800 rounded-full text-sm"><?= htmlspecialchars($category['name']) ?></span>
                                            <?php 
                                                        endif;
                                                    endforeach;
                                                endforeach;
                                            else: 
                                            ?>
                                            <span class="text-gray-400">No interests specified</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php elseif ($user['user_type'] === 'organization' && $organization): ?>
                                    <div class="md:col-span-2">
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Position</h4>
                                        <p class="text-gray-800">
                                            <?= isset($profile['position']) ? htmlspecialchars($profile['position']) : '<span class="text-gray-400">Not specified</span>' ?>
                                        </p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Organization</h4>
                                        <p class="text-gray-800">
                                            <?= htmlspecialchars($organization['name']) ?>
                                        </p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="md:col-span-2">
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Member Since</h4>
                                        <p class="text-gray-800">
                                            <?= date('F j, Y', strtotime($user['created_at'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($user['user_type'] === 'volunteer'): ?>
                        <!-- My Applications -->
                        <div id="applications" class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                            <div class="p-6 border-b">
                                <h3 class="text-xl font-bold text-gray-800">My Applications</h3>
                            </div>
                            <div class="p-6">
                                <?php if (empty($applications)): ?>
                                <div class="text-center py-8">
                                    <div class="text-5xl text-gray-300 mb-4">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <h4 class="text-lg font-medium text-gray-700 mb-2">No Applications Yet</h4>
                                    <p class="text-gray-600 mb-4">You haven't applied to any volunteer opportunities yet.</p>
                                    <a href="opportunities.php" class="bg-teal-600 hover:bg-teal-700 text-white font-medium px-4 py-2 rounded-md transition duration-300">
                                        Browse Opportunities
                                    </a>
                                </div>
                                <?php else: ?>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Opportunity
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Organization
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Date Applied
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <?php foreach ($applications as $application): ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="opportunity-details.php?id=<?= $application['opportunity_id'] ?>" class="text-teal-600 hover:text-teal-800 font-medium">
                                                        <?= htmlspecialchars($application['opportunity_title']) ?>
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="organization-details.php?id=<?= $application['organization_id'] ?>" class="text-gray-700 hover:text-teal-600">
                                                        <?= htmlspecialchars($application['organization_name']) ?>
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                                    <?= date('M j, Y', strtotime($application['application_date'])) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php
                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                    if ($application['status'] === 'approved') {
                                                        $statusClass = 'bg-green-100 text-green-800';
                                                    } elseif ($application['status'] === 'rejected') {
                                                        $statusClass = 'bg-red-100 text-red-800';
                                                    } elseif ($application['status'] === 'pending') {
                                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                                    }
                                                    ?>
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                                        <?= ucfirst(htmlspecialchars($application['status'])) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php elseif ($user['user_type'] === 'organization' && $organization): ?>
                        <!-- Organization Details -->
                        <div id="organization" class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                            <div class="p-6 border-b">
                                <div class="flex justify-between">
                                    <h3 class="text-xl font-bold text-gray-800">Organization Details</h3>
                                    <a href="#" class="text-teal-600 hover:text-teal-800">
                                        <i class="fas fa-external-link-alt"></i> View Public Profile
                                    </a>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2">
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Organization Name</h4>
                                        <p class="text-gray-800"><?= htmlspecialchars($organization['name']) ?></p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Primary Category</h4>
                                        <p class="text-gray-800"><?= getCategoryNameById($organization['primary_category_id']) ?></p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Location</h4>
                                        <p class="text-gray-800"><?= htmlspecialchars($organization['location']) ?></p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Website</h4>
                                        <p class="text-gray-800">
                                            <a href="<?= htmlspecialchars($organization['website']) ?>" target="_blank" class="text-teal-600 hover:text-teal-800">
                                                <?= htmlspecialchars($organization['website']) ?>
                                            </a>
                                        </p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Email</h4>
                                        <p class="text-gray-800">
                                            <a href="mailto:<?= htmlspecialchars($organization['email']) ?>" class="text-teal-600 hover:text-teal-800">
                                                <?= htmlspecialchars($organization['email']) ?>
                                            </a>
                                        </p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Phone</h4>
                                        <p class="text-gray-800"><?= htmlspecialchars($organization['phone']) ?></p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Founded</h4>
                                        <p class="text-gray-800"><?= htmlspecialchars($organization['founded_year']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Account Settings (placeholder) -->
                        <div id="settings" class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6 border-b">
                                <h3 class="text-xl font-bold text-gray-800">Account Settings</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div>
                                        <h4 class="font-medium text-gray-700 mb-2">Change Password</h4>
                                        <a href="#" class="text-teal-600 hover:text-teal-800 font-medium">
                                            <i class="fas fa-key mr-1"></i> Update Password
                                        </a>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-700 mb-2">Email Preferences</h4>
                                        <a href="#" class="text-teal-600 hover:text-teal-800 font-medium">
                                            <i class="fas fa-envelope mr-1"></i> Manage Email Notifications
                                        </a>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-700 mb-2">Account Removal</h4>
                                        <a href="#" class="text-red-600 hover:text-red-800 font-medium">
                                            <i class="fas fa-user-times mr-1"></i> Delete Account
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>