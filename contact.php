<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
$pageTitle = "Contact Us - Volunteer Connect";
$currentPage = "contact";

// Check if we're contacting a specific organization
$orgId = isset($_GET['org']) ? $_GET['org'] : '';
$organization = null;

if (!empty($orgId)) {
    $organization = getOrganizationById($orgId);
}

// Initialize form variables
$formSubmitted = false;
$formSuccess = false;
$formError = '';
$formData = [
    'name' => '',
    'email' => '',
    'subject' => '',
    'message' => '',
    'organization_id' => $orgId
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formSubmitted = true;
    
    // Get form data
    $formData = [
        'name' => isset($_POST['name']) ? trim($_POST['name']) : '',
        'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
        'subject' => isset($_POST['subject']) ? trim($_POST['subject']) : '',
        'message' => isset($_POST['message']) ? trim($_POST['message']) : '',
        'organization_id' => isset($_POST['organization_id']) ? $_POST['organization_id'] : ''
    ];
    
    // Validate form data
    if (empty($formData['name'])) {
        $formError = 'Please enter your name.';
    } elseif (empty($formData['email'])) {
        $formError = 'Please enter your email address.';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $formError = 'Please enter a valid email address.';
    } elseif (empty($formData['subject'])) {
        $formError = 'Please enter a subject.';
    } elseif (empty($formData['message'])) {
        $formError = 'Please enter your message.';
    } else {
        // Submit the contact form
        $result = submitContactForm($formData);
        
        if ($result) {
            $formSuccess = true;
            // Reset form data after successful submission
            $formData = [
                'name' => '',
                'email' => '',
                'subject' => '',
                'message' => '',
                'organization_id' => $orgId
            ];
        } else {
            $formError = 'Sorry, there was a problem submitting your message. Please try again later.';
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
                <h1 class="text-3xl md:text-4xl font-bold mb-4">
                    <?php if ($organization): ?>
                        Contact <?= htmlspecialchars($organization['name']) ?>
                    <?php else: ?>
                        Contact Us
                    <?php endif; ?>
                </h1>
                <p class="text-xl mb-4">
                    <?php if ($organization): ?>
                        Get in touch with <?= htmlspecialchars($organization['name']) ?> directly.
                    <?php else: ?>
                        Have questions or feedback? We'd love to hear from you.
                    <?php endif; ?>
                </p>
            </div>
        </section>

        <!-- Contact Form Section -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Main Form Column -->
                    <div class="md:col-span-2">
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6">Send Us a Message</h2>
                            
                            <?php if ($formSubmitted && $formSuccess): ?>
                            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-green-700">
                                            Your message has been sent successfully! We'll get back to you as soon as possible.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
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
                            
                            <form id="contactForm" action="contact.php<?= $organization ? '?org=' . $organization['id'] : '' ?>" method="post">
                                <input type="hidden" name="organization_id" value="<?= htmlspecialchars($orgId) ?>">
                                
                                <div class="grid md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Your Name *</label>
                                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($formData['name']) ?>" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                                    <input type="text" id="subject" name="subject" value="<?= htmlspecialchars($formData['subject']) ?>" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                </div>
                                
                                <div class="mb-6">
                                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                                    <textarea id="message" name="message" rows="6" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50"><?= htmlspecialchars($formData['message']) ?></textarea>
                                </div>
                                
                                <div>
                                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                                        Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Sidebar Column -->
                    <div>
                        <?php if ($organization): ?>
                        <!-- Organization Contact Info -->
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Organization Details</h3>
                            <ul class="space-y-4">
                                <li class="flex items-start">
                                    <div class="text-teal-600 mr-3 mt-1">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-700">Name</h4>
                                        <p class="text-gray-600"><?= htmlspecialchars($organization['name']) ?></p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <div class="text-teal-600 mr-3 mt-1">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-700">Location</h4>
                                        <p class="text-gray-600"><?= htmlspecialchars($organization['location']) ?></p>
                                    </div>
                                </li>
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
                            </ul>
                        </div>
                        <?php else: ?>
                        <!-- General Contact Info -->
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Contact Information</h3>
                            <ul class="space-y-4">
                                <li class="flex items-start">
                                    <div class="text-teal-600 mr-3 mt-1">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-700">Address</h4>
                                        <p class="text-gray-600">Jalandhar-Delhi G.T. Road, Phagwara, Punjab (India) - 144411. </p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <div class="text-teal-600 mr-3 mt-1">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-700">Phone</h4>
                                        <p class="text-gray-600">6266928927, 8708755231</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <div class="text-teal-600 mr-3 mt-1">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-700">Email</h4>
                                        <a href="mailto:info@volunteerconnect.org" class="text-teal-600 hover:text-teal-800 transition duration-300">lpuvolunteer.org</a>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <div class="text-teal-600 mr-3 mt-1">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-700">Office Hours</h4>
                                        <p class="text-gray-600">Monday - Friday: 9:00 AM - 5:00 PM</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Social Media -->
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Connect With Us</h3>
                            <div class="flex space-x-4">
                                <a href="#" class="text-teal-600 hover:text-teal-800 transition duration-300" title="Facebook">
                                    <i class="fab fa-facebook-square text-2xl"></i>
                                </a>
                                <a href="#" class="text-teal-600 hover:text-teal-800 transition duration-300" title="Twitter">
                                    <i class="fab fa-twitter-square text-2xl"></i>
                                </a>
                                <a href="https://www.instagram.com/mr__ashish__2208/?next=%2Fmr__ashish__2208%2F" class="text-teal-600 hover:text-teal-800 transition duration-300" title="Instagram">
                                    <i class="fab fa-instagram-square text-2xl"></i>
                                </a>
                                <a href="https://www.linkedin.com/feed/?trk=guest_homepage-basic_google-one-tap-submit" class="text-teal-600 hover:text-teal-800 transition duration-300" title="LinkedIn">
                                    <i class="fab fa-linkedin text-2xl"></i>
                                </a>
                            </div>
                        </div>
                        
                        <!-- FAQ or Resources -->
                        <div class="bg-teal-50 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-teal-800 mb-4">Quick Links</h3>
                            <ul class="space-y-2">
                                <li>
                                    <a href="opportunities.php" class="text-teal-600 hover:text-teal-800 transition duration-300">
                                        <i class="fas fa-search mr-2"></i> Find Volunteer Opportunities
                                    </a>
                                </li>
                                <li>
                                    <a href="organizations.php" class="text-teal-600 hover:text-teal-800 transition duration-300">
                                        <i class="fas fa-building mr-2"></i> Browse Organizations
                                    </a>
                                </li>
                                <li>
                                    <a href="about.php" class="text-teal-600 hover:text-teal-800 transition duration-300">
                                        <i class="fas fa-info-circle mr-2"></i> About Volunteer Connect
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>
