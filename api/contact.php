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

// Get contact form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$organizationId = isset($_POST['organization_id']) ? $_POST['organization_id'] : '';

// Validate required data
if (empty($name)) {
    echo json_encode([
        'success' => false,
        'message' => 'Name is required'
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

if (empty($subject)) {
    echo json_encode([
        'success' => false,
        'message' => 'Subject is required'
    ]);
    exit;
}

if (empty($message)) {
    echo json_encode([
        'success' => false,
        'message' => 'Message is required'
    ]);
    exit;
}

// Prepare contact form data
$contactData = [
    'name' => $name,
    'email' => $email,
    'subject' => $subject,
    'message' => $message,
    'organization_id' => $organizationId,
    'submission_date' => date('Y-m-d H:i:s'),
    'status' => 'new'
];

// Submit the contact form
$result = submitContactForm($contactData);

if ($result) {
    echo json_encode([
        'success' => true,
        'message' => 'Your message has been sent successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send message. Please try again later.'
    ]);
}
