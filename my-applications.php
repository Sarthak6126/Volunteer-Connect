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

// Redirect organization users
if ($user['user_type'] !== 'volunteer') {
    header('Location: profile.php');
    exit;
}

// Get user's applications
$applications = [];
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

$pageTitle = "My Applications - Volunteer Connect";
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
                <h1 class="text-3xl md:text-4xl font-bold mb-2">My Applications</h1>
                <p class="text-xl">
                    Track the status of your volunteer applications
                </p>
            </div>
        </section>

        <!-- Main Content -->
        <main class="py-12">
            <div class="container mx-auto px-4">
                <!-- Applications List -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="p-6 border-b">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold text-gray-800">My Volunteer Applications</h2>
                            <a href="opportunities.php" class="text-teal-600 hover:text-teal-800 font-medium">
                                <i class="fas fa-search mr-1"></i> Find More Opportunities
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <?php if (empty($applications)): ?>
                        <div class="text-center py-8">
                            <div class="text-5xl text-gray-300 mb-4">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-700 mb-2">No Applications Yet</h3>
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
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
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
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="application-details.php?id=<?= $application['id'] ?>" class="text-teal-600 hover:text-teal-800 mr-4">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <?php if ($application['status'] === 'pending'): ?>
                                            <a href="#" class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-times-circle"></i> Withdraw
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-8 pt-6 border-t">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Application Status Legend</h3>
                            <div class="flex flex-wrap gap-4">
                                <div class="flex items-center">
                                    <span class="px-3 py-1 mr-2 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Pending</span>
                                    <span class="text-sm text-gray-600">Your application is being reviewed</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="px-3 py-1 mr-2 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Approved</span>
                                    <span class="text-sm text-gray-600">You've been accepted for this opportunity</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="px-3 py-1 mr-2 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Rejected</span>
                                    <span class="text-sm text-gray-600">Your application was not selected</span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Application Tips -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-bold text-gray-800">Tips for Successful Applications</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid md:grid-cols-3 gap-6">
                            <div class="bg-gray-50 p-5 rounded-lg">
                                <div class="text-3xl text-teal-600 mb-3">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <h3 class="text-lg font-semibold mb-2">Complete Your Profile</h3>
                                <p class="text-gray-700">
                                    Organizations are more likely to approve volunteers with complete profiles. Add your skills, experience, and availability.
                                </p>
                            </div>
                            <div class="bg-gray-50 p-5 rounded-lg">
                                <div class="text-3xl text-teal-600 mb-3">
                                    <i class="fas fa-align-left"></i>
                                </div>
                                <h3 class="text-lg font-semibold mb-2">Be Specific</h3>
                                <p class="text-gray-700">
                                    When applying, mention specific skills and experiences that make you a good fit for the particular opportunity.
                                </p>
                            </div>
                            <div class="bg-gray-50 p-5 rounded-lg">
                                <div class="text-3xl text-teal-600 mb-3">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <h3 class="text-lg font-semibold mb-2">Follow Up</h3>
                                <p class="text-gray-700">
                                    If your application has been pending for more than two weeks, consider sending a polite follow-up message to the organization.
                                </p>
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