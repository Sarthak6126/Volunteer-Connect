<?php
/**
 * Configuration settings for the Volunteer Connect website
 */

// Set error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base paths
define('BASE_PATH', dirname(__DIR__) . '/');
define('DATA_PATH', BASE_PATH . 'data/');

// Set timezone
date_default_timezone_set('America/New_York');

// Configuration options
$config = [
    'site_name' => 'Volunteer Connect',
    'site_description' => 'Connecting volunteers with organizations',
    'admin_email' => 'admin@volunteerconnect.org',
    'results_per_page' => 9,
    'featured_limit' => 3,
    'related_opportunities_limit' => 3,
    'similar_organizations_limit' => 3
];

// Initialize session
require_once 'functions.php';
startSession();

// Define a constant to control database operations
// Set to true to disable database operations and use JSON files instead
// Temporarily setting to true while we resolve connection issues
define('SKIP_DB_OPERATIONS', true);

// Include database file
require_once 'db.php';

// Initialize database tables if they don't exist and we're not skipping database operations
if (!SKIP_DB_OPERATIONS) {
    initializeDatabase();
}

// Migrate data from JSON to database (only if not already migrated)
if (!SKIP_DB_OPERATIONS) {
    try {
        // Check if any data exists in the categories table
        $result = dbQuery("SELECT COUNT(*) as count FROM categories");
        $categoriesCount = $result[0]['count'];
        
        // Only run migration if the database is empty
        if ($categoriesCount == 0) {
            migrateDataFromJson();
        }
    } catch (Exception $e) {
        // If there's an error, log it but continue
        error_log("Data migration check failed: " . $e->getMessage());
    }
}
