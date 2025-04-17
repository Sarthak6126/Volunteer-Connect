<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Set the response header to JSON
header('Content-Type: application/json');

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get application data
$opportunityId = isset($_POST['opportunity_id']) ? $_POST['opportunity_id'] : '';
$firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';
$city = isset($_POST['city']) ? trim($_POST['city']) : '';
$state = isset($_POST['state']) ? trim($_POST['state']) : '';
$zip = isset($_POST['zip']) ? trim($_POST['zip']) : '';
$experience = isset($_POST['experience']) ? trim($_POST['experience']) : '';
$motivation = isset($_POST['motivation']) ? trim($_POST['motivation']) : '';
$availability = isset($_POST['availability']) ? trim($_POST['availability']) : '';
$referenceName = isset($_POST['reference_name']) ? trim($_POST['reference_name']) : '';
$referenceContact = isset($_POST['reference_contact']) ? trim($_POST['reference_contact']) : '';
$terms = isset($_POST['terms']) ? true : false;

// Validate required data
if (empty($opportunityId)) {
    echo json_encode([
        'success' => false,
        'message' => 'Opportunity ID is required'
    ]);
    exit;
}

if (empty($firstName)) {
    echo json_encode([
        'success' => false,
        'message' => 'First name is required'
    ]);
    exit;
}

if (empty($lastName)) {
    echo json_encode([
        'success' => false,
        'message' => 'Last name is required'
    ]);
    exit;
}

if (empty($email)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email is required'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email format'
    ]);
    exit;
}

if (empty($phone)) {
    echo json_encode([
        'success' => false,
        'message' => 'Phone number is required'
    ]);
    exit;
}

if (empty($experience)) {
    echo json_encode([
        'success' => false,
        'message' => 'Experience is required'
    ]);
    exit;
}

if (empty($motivation)) {
    echo json_encode([
        'success' => false,
        'message' => 'Motivation is required'
    ]);
    exit;
}

if (empty($availability)) {
    echo json_encode([
        'success' => false,
        'message' => 'Availability is required'
    ]);
    exit;
}

if (!$terms) {
    echo json_encode([
        'success' => false,
        'message' => 'You must agree to the terms and conditions'
    ]);
    exit;
}

// Get opportunity details to get the organization ID
$opportunity = getOpportunityById($opportunityId);
if (!$opportunity) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid opportunity'
    ]);
    exit;
}

// Prepare application data
$applicationData = [
    'opportunity_id' => $opportunityId,
    'organization_id' => $opportunity['organization_id'],
    'first_name' => $firstName,
    'last_name' => $lastName,
    'email' => $email,
    'phone' => $phone,
    'address' => $address,
    'city' => $city,
    'state' => $state,
    'zip' => $zip,
    'experience' => $experience,
    'motivation' => $motivation,
    'availability' => $availability,
    'reference_name' => $referenceName,
    'reference_contact' => $referenceContact,
    'application_date' => date('Y-m-d H:i:s'),
    'status' => 'pending'
];

// Submit the application
$result = submitApplication($applicationData);

if ($result) {
    echo json_encode([
        'success' => true,
        'message' => 'Application submitted successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to submit application. Please try again later.'
    ]);
}
