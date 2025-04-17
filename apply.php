<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get opportunity ID from URL
$id = isset($_GET['id']) ? $_GET['id'] : '';

// If no ID provided, redirect to opportunities page
if (empty($id)) {
    header('Location: opportunities.php');
    exit;
}

// Get opportunity details
$opportunity = getOpportunityById($id);

// If opportunity not found, redirect to opportunities page
if (!$opportunity) {
    header('Location: opportunities.php');
    exit;
}

// Get organization details
$organization = getOrganizationById($opportunity['organization_id']);

$pageTitle = "Apply: " . htmlspecialchars($opportunity['title']) . " - Volunteer Connect";
$currentPage = "opportunities";

// Initialize form variables
$formSubmitted = false;
$formSuccess = false;
$formError = '';
$formData = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'state' => '',
    'zip' => '',
    'experience' => '',
    'motivation' => '',
    'availability' => '',
    'reference_name' => '',
    'reference_contact' => ''
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSubmitted = true;
    
    // Get form data
    $formData = [
        'first_name' => isset($_POST['first_name']) ? trim($_POST['first_name']) : '',
        'last_name' => isset($_POST['last_name']) ? trim($_POST['last_name']) : '',
        'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
        'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
        'address' => isset($_POST['address']) ? trim($_POST['address']) : '',
        'city' => isset($_POST['city']) ? trim($_POST['city']) : '',
        'state' => isset($_POST['state']) ? trim($_POST['state']) : '',
        'zip' => isset($_POST['zip']) ? trim($_POST['zip']) : '',
        'experience' => isset($_POST['experience']) ? trim($_POST['experience']) : '',
        'motivation' => isset($_POST['motivation']) ? trim($_POST['motivation']) : '',
        'availability' => isset($_POST['availability']) ? trim($_POST['availability']) : '',
        'reference_name' => isset($_POST['reference_name']) ? trim($_POST['reference_name']) : '',
        'reference_contact' => isset($_POST['reference_contact']) ? trim($_POST['reference_contact']) : ''
    ];
    
    // Validate form data
    if (empty($formData['first_name'])) {
        $formError = 'Please enter your first name.';
    } elseif (empty($formData['last_name'])) {
        $formError = 'Please enter your last name.';
    } elseif (empty($formData['email'])) {
        $formError = 'Please enter your email address.';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $formError = 'Please enter a valid email address.';
    } elseif (empty($formData['phone'])) {
        $formError = 'Please enter your phone number.';
    } elseif (empty($formData['experience'])) {
        $formError = 'Please tell us about your relevant experience.';
    } elseif (empty($formData['motivation'])) {
        $formError = 'Please tell us why you want to volunteer for this opportunity.';
    } elseif (empty($formData['availability'])) {
        $formError = 'Please tell us about your availability.';
    } else {
        // Submit the application
        $applicationData = $formData;
        $applicationData['opportunity_id'] = $id;
        $applicationData['organization_id'] = $opportunity['organization_id'];
        $applicationData['application_date'] = date('Y-m-d H:i:s');
        
        $result = submitApplication($applicationData);
        
        if ($result) {
            $formSuccess = true;
            // Reset form data after successful submission
            $formData = [
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'phone' => '',
                'address' => '',
                'city' => '',
                'state' => '',
                'zip' => '',
                'experience' => '',
                'motivation' => '',
                'availability' => '',
                'reference_name' => '',
                'reference_contact' => ''
            ];
        } else {
            $formError = 'Sorry, there was a problem submitting your application. Please try again later.';
        }
    }
}
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
                    <a href="opportunity-details.php?id=<?= $opportunity['id'] ?>" class="text-white hover:text-teal-100 transition duration-300">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Opportunity
                    </a>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold mb-2">Apply to Volunteer</h1>
                <p class="text-xl mb-2">
                    <?= htmlspecialchars($opportunity['title']) ?> with <?= htmlspecialchars($organization['name']) ?>
                </p>
            </div>
        </section>

        <!-- Application Form Section -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                        <?php if ($formSubmitted && $formSuccess): ?>
                        <div class="text-center py-8">
                            <div class="mb-4 text-5xl text-green-500">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Application Submitted Successfully!</h2>
                            <p class="text-gray-600 mb-6">
                                Thank you for your interest in volunteering with <?= htmlspecialchars($organization['name']) ?>. 
                                They will review your application and contact you soon.
                            </p>
                            <div class="flex justify-center space-x-4">
                                <a href="opportunities.php" class="bg-teal-600 hover:bg-teal-700 text-white font-bold px-6 py-3 rounded-lg inline-block transition duration-300">
                                    Explore More Opportunities
                                </a>
                                <a href="index.php" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold px-6 py-3 rounded-lg inline-block transition duration-300">
                                    Return Home
                                </a>
                            </div>
                        </div>
                        <?php else: ?>
                        
                        <?php if ($formSubmitted && !$formSuccess && !empty($formError)): ?>
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
                        
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Volunteer Application</h2>
                            <p class="text-gray-600">Please complete the form below to apply for this volunteer opportunity. Fields marked with an asterisk (*) are required.</p>
                        </div>
                        
                        <form id="applicationForm" action="apply.php?id=<?= $opportunity['id'] ?>" method="post">
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-700 mb-3 pb-2 border-b">Personal Information</h3>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($formData['first_name']) ?>" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </div>
                                    <div>
                                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($formData['last_name']) ?>" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-700 mb-3 pb-2 border-b">Contact Information</h3>
                                <div class="grid md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($formData['phone']) ?>" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($formData['address']) ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                </div>
                                <div class="grid md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                        <input type="text" id="city" name="city" value="<?= htmlspecialchars($formData['city']) ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </div>
                                    <div>
                                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                        <input type="text" id="state" name="state" value="<?= htmlspecialchars($formData['state']) ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </div>
                                    <div>
                                        <label for="zip" class="block text-sm font-medium text-gray-700 mb-1">ZIP Code</label>
                                        <input type="text" id="zip" name="zip" value="<?= htmlspecialchars($formData['zip']) ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-700 mb-3 pb-2 border-b">Qualifications & Availability</h3>
                                <div class="mb-4">
                                    <label for="experience" class="block text-sm font-medium text-gray-700 mb-1">Relevant Experience/Skills *</label>
                                    <textarea id="experience" name="experience" rows="4" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50"><?= htmlspecialchars($formData['experience']) ?></textarea>
                                    <p class="mt-1 text-sm text-gray-500">Describe any relevant experience, skills, or qualifications you have for this volunteer position.</p>
                                </div>
                                <div class="mb-4">
                                    <label for="motivation" class="block text-sm font-medium text-gray-700 mb-1">Why You Want to Volunteer *</label>
                                    <textarea id="motivation" name="motivation" rows="4" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50"><?= htmlspecialchars($formData['motivation']) ?></textarea>
                                    <p class="mt-1 text-sm text-gray-500">Tell us why you're interested in this particular volunteer opportunity.</p>
                                </div>
                                <div>
                                    <label for="availability" class="block text-sm font-medium text-gray-700 mb-1">Your Availability *</label>
                                    <textarea id="availability" name="availability" rows="3" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50"><?= htmlspecialchars($formData['availability']) ?></textarea>
                                    <p class="mt-1 text-sm text-gray-500">Please indicate the days and times you are typically available to volunteer.</p>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-700 mb-3 pb-2 border-b">References (Optional)</h3>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="reference_name" class="block text-sm font-medium text-gray-700 mb-1">Reference Name</label>
                                        <input type="text" id="reference_name" name="reference_name" value="<?= htmlspecialchars($formData['reference_name']) ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </div>
                                    <div>
                                        <label for="reference_contact" class="block text-sm font-medium text-gray-700 mb-1">Reference Contact (Email or Phone)</label>
                                        <input type="text" id="reference_contact" name="reference_contact" value="<?= htmlspecialchars($formData['reference_contact']) ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border-t pt-6">
                                <div class="flex items-start mb-6">
                                    <div class="flex items-center h-5">
                                        <input id="terms" name="terms" type="checkbox" required class="focus:ring-teal-500 h-4 w-4 text-teal-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="terms" class="font-medium text-gray-700">I agree to the volunteer terms and conditions *</label>
                                        <p class="text-gray-500">By submitting this application, I confirm that the information provided is accurate and complete.</p>
                                    </div>
                                </div>
                                
                                <div>
                                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                                        Submit Application
                                    </button>
                                </div>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Opportunity Summary -->
                    <?php if (!$formSuccess): ?>
                    <div class="bg-gray-50 rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Opportunity Summary</h3>
                        <div class="mb-4">
                            <h4 class="font-medium text-gray-700"><?= htmlspecialchars($opportunity['title']) ?></h4>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-building mr-1"></i> <?= htmlspecialchars($organization['name']) ?>
                            </p>
                            <p class="text-sm text-gray-600 mb-2">
                                <i class="fas fa-map-marker-alt mr-1"></i> <?= htmlspecialchars($opportunity['location']) ?>
                                <?php if ($opportunity['is_remote']): ?>
                                    <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Remote</span>
                                <?php endif; ?>
                            </p>
                            <div class="mt-2">
                                <span class="px-2 py-1 bg-teal-100 text-teal-800 rounded-full text-xs">
                                    <?= getCategoryNameById($opportunity['category_id']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="text-sm space-y-2">
                            <p><strong>Time Commitment:</strong> <?= htmlspecialchars($opportunity['commitment']) ?></p>
                            <p><strong>Duration:</strong> <?= htmlspecialchars($opportunity['duration']) ?></p>
                            <?php if (!empty($opportunity['start_date'])): ?>
                            <p><strong>Start Date:</strong> <?= htmlspecialchars(date('F j, Y', strtotime($opportunity['start_date']))) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($opportunity['application_deadline'])): ?>
                            <p><strong>Application Deadline:</strong> <?= htmlspecialchars(date('F j, Y', strtotime($opportunity['application_deadline']))) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>
