<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Set the response header to JSON
header('Content-Type: application/json');

// Get search parameters
$type = isset($_GET['type']) ? $_GET['type'] : 'opportunities';
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$remote = isset($_GET['remote']) ? ($_GET['remote'] === 'true' || $_GET['remote'] === '1') : false;

// Default response
$response = [
    'success' => false,
    'message' => 'Invalid request',
    'data' => []
];

// Perform search based on type
if ($type === 'opportunities') {
    $results = searchOpportunities($query, $category, $location, $remote);
    
    // Format results for display
    $formattedResults = [];
    foreach ($results as $opportunity) {
        $org = getOrganizationById($opportunity['organization_id']);
        $formattedResults[] = [
            'id' => $opportunity['id'],
            'title' => $opportunity['title'],
            'organization' => $org ? $org['name'] : 'Unknown Organization',
            'location' => $opportunity['location'],
            'category' => getCategoryNameById($opportunity['category_id']),
            'commitment' => $opportunity['commitment'],
            'is_remote' => $opportunity['is_remote'],
            'description' => substr($opportunity['description'], 0, 150) . '...',
            'url' => 'opportunity-details.php?id=' . $opportunity['id']
        ];
    }
    
    $response = [
        'success' => true,
        'message' => count($formattedResults) . ' opportunities found',
        'data' => $formattedResults
    ];
} elseif ($type === 'organizations') {
    $results = searchOrganizations($query, $category, $location);
    
    // Format results for display
    $formattedResults = [];
    foreach ($results as $organization) {
        $formattedResults[] = [
            'id' => $organization['id'],
            'name' => $organization['name'],
            'location' => $organization['location'],
            'primary_category' => getCategoryNameById($organization['primary_category_id']),
            'description' => substr($organization['description'], 0, 150) . '...',
            'url' => 'organization-details.php?id=' . $organization['id']
        ];
    }
    
    $response = [
        'success' => true,
        'message' => count($formattedResults) . ' organizations found',
        'data' => $formattedResults
    ];
}

// Output the response
echo json_encode($response);
