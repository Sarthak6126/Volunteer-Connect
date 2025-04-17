<?php
/**
 * Functions for the Volunteer Connect website
 */

/**
 * Start session if not already started
 */
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Get all categories
 * 
 * @return array Categories
 */
function getCategories() {
    try {
        return dbQuery("SELECT * FROM categories ORDER BY name");
    } catch (Exception $e) {
        error_log("Error getting categories: " . $e->getMessage());
        
        // Fallback to JSON file if database query fails
        $categoriesFile = DATA_PATH . 'categories.json';
        if (file_exists($categoriesFile)) {
            $json = file_get_contents($categoriesFile);
            return json_decode($json, true);
        }
        return [];
    }
}

/**
 * Get a category name by ID
 * 
 * @param int $id Category ID
 * @return string Category name
 */
function getCategoryNameById($id) {
    $categories = getCategories();
    foreach ($categories as $category) {
        if ($category['id'] == $id) {
            return $category['name'];
        }
    }
    return 'Unknown';
}

/**
 * Get all locations
 * 
 * @return array Locations
 */
function getLocations() {
    try {
        return dbQuery("SELECT * FROM locations ORDER BY name");
    } catch (Exception $e) {
        error_log("Error getting locations: " . $e->getMessage());
        
        // Fallback to JSON file if database query fails
        $locationsFile = DATA_PATH . 'locations.json';
        if (file_exists($locationsFile)) {
            $json = file_get_contents($locationsFile);
            return json_decode($json, true);
        }
        return [];
    }
}

/**
 * Get a location name by ID
 * 
 * @param int $id Location ID
 * @return string Location name
 */
function getLocationNameById($id) {
    $locations = getLocations();
    foreach ($locations as $location) {
        if ($location['id'] == $id) {
            return $location['name'];
        }
    }
    return 'Unknown';
}

/**
 * Get all volunteer opportunities
 * 
 * @param int|string $categoryId Optional category filter
 * @param int|string $locationId Optional location filter
 * @param string $search Optional search term
 * @param bool $remote Optional remote filter
 * @return array Opportunities
 */
function getOpportunities($categoryId = '', $locationId = '', $search = '', $remote = false) {
    try {
        $params = [];
        $sql = "SELECT * FROM opportunities WHERE 1=1";
        
        // Add category filter
        if (!empty($categoryId)) {
            $sql .= " AND category_id = ?";
            $params[] = $categoryId;
        }
        
        // Add remote filter
        if ($remote) {
            $sql .= " AND is_remote = true";
        }
        
        // Add location filter
        if (!empty($locationId)) {
            // Get location name
            $location = dbQuery("SELECT name FROM locations WHERE id = ?", [$locationId], false);
            if ($location) {
                $sql .= " AND location ILIKE ?";
                $params[] = '%' . $location['name'] . '%';
            }
        }
        
        // Add search filter
        if (!empty($search)) {
            $sql .= " AND (title ILIKE ? OR description ILIKE ? OR location ILIKE ?)";
            $searchPattern = '%' . $search . '%';
            $params[] = $searchPattern;
            $params[] = $searchPattern;
            $params[] = $searchPattern;
        }
        
        // Order by featured first, then by creation date
        $sql .= " ORDER BY is_featured DESC, created_at DESC";
        
        return dbQuery($sql, $params);
    } catch (Exception $e) {
        error_log("Error getting opportunities: " . $e->getMessage());
        
        // Fallback to JSON file if database query fails
        $opportunitiesFile = DATA_PATH . 'opportunities.json';
        if (file_exists($opportunitiesFile)) {
            $json = file_get_contents($opportunitiesFile);
            $opportunities = json_decode($json, true);
            
            // Apply filters
            if (!empty($opportunities)) {
                return array_filter($opportunities, function($opp) use ($categoryId, $locationId, $search, $remote) {
                    // Category filter
                    if (!empty($categoryId) && $opp['category_id'] != $categoryId) {
                        return false;
                    }
                    
                    // Location filter
                    if (!empty($locationId)) {
                        $locations = getLocations();
                        $locationName = '';
                        foreach ($locations as $loc) {
                            if ($loc['id'] == $locationId) {
                                $locationName = $loc['name'];
                                break;
                            }
                        }
                        // Check if the opportunity location contains the location name
                        if (!empty($locationName) && stripos($opp['location'], $locationName) === false) {
                            return false;
                        }
                    }
                    
                    // Search filter
                    if (!empty($search)) {
                        $searchLower = strtolower($search);
                        $titleMatch = stripos($opp['title'], $search) !== false;
                        $descMatch = stripos($opp['description'], $search) !== false;
                        $locationMatch = stripos($opp['location'], $search) !== false;
                        
                        if (!$titleMatch && !$descMatch && !$locationMatch) {
                            return false;
                        }
                    }
                    
                    // Remote filter
                    if ($remote && !$opp['is_remote']) {
                        return false;
                    }
                    
                    return true;
                });
            }
        }
        return [];
    }
}

/**
 * Get featured opportunities
 * 
 * @param int $limit Number of opportunities to return
 * @return array Featured opportunities
 */
function getFeaturedOpportunities($limit = 3) {
    $opportunities = getOpportunities();
    
    // Filter to only include featured opportunities
    $featured = array_filter($opportunities, function($opp) {
        return isset($opp['is_featured']) && $opp['is_featured'] == true;
    });
    
    // If we don't have enough featured, get the most recent ones
    if (count($featured) < $limit) {
        // Sort by created date descending
        usort($opportunities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        // Take the first $limit opportunities
        return array_slice($opportunities, 0, $limit);
    }
    
    // Shuffle the featured opportunities and return $limit
    shuffle($featured);
    return array_slice($featured, 0, $limit);
}

/**
 * Get a specific opportunity by ID
 * 
 * @param int $id Opportunity ID
 * @return array|null Opportunity details or null if not found
 */
function getOpportunityById($id) {
    try {
        $opportunity = dbQuery(
            "SELECT * FROM opportunities WHERE id = ?",
            [$id],
            false
        );
        
        return $opportunity ?: null;
    } catch (Exception $e) {
        error_log("Error getting opportunity by ID: " . $e->getMessage());
        
        // Fallback to JSON file if database query fails
        $opportunitiesFile = DATA_PATH . 'opportunities.json';
        if (file_exists($opportunitiesFile)) {
            $json = file_get_contents($opportunitiesFile);
            $opportunities = json_decode($json, true);
            
            foreach ($opportunities as $opportunity) {
                if ($opportunity['id'] == $id) {
                    return $opportunity;
                }
            }
        }
        return null;
    }
}

/**
 * Get related opportunities based on category and excluding the current opportunity
 * 
 * @param int $currentId Current opportunity ID to exclude
 * @param int $categoryId Category ID to match
 * @param int $limit Number of opportunities to return
 * @return array Related opportunities
 */
function getRelatedOpportunities($currentId, $categoryId, $limit = 3) {
    $opportunities = getOpportunities();
    
    // Filter to only include opportunities in the same category but not the current one
    $related = array_filter($opportunities, function($opp) use ($currentId, $categoryId) {
        return $opp['id'] != $currentId && $opp['category_id'] == $categoryId;
    });
    
    // If we don't have enough related by category, add some random ones
    if (count($related) < $limit) {
        $others = array_filter($opportunities, function($opp) use ($currentId, $related) {
            return $opp['id'] != $currentId && !in_array($opp, $related);
        });
        
        // Shuffle the other opportunities
        shuffle($others);
        
        // Add enough to reach the limit
        $related = array_merge($related, array_slice($others, 0, $limit - count($related)));
    }
    
    // Shuffle the related opportunities
    shuffle($related);
    return array_slice($related, 0, $limit);
}

/**
 * Get all organizations
 * 
 * @param int|string $categoryId Optional category filter
 * @param int|string $locationId Optional location filter
 * @param string $search Optional search term
 * @return array Organizations
 */
function getOrganizations($categoryId = '', $locationId = '', $search = '') {
    $organizationsFile = DATA_PATH . 'organizations.json';
    if (file_exists($organizationsFile)) {
        $json = file_get_contents($organizationsFile);
        $organizations = json_decode($json, true);
        
        // Apply filters
        if (!empty($organizations)) {
            return array_filter($organizations, function($org) use ($categoryId, $locationId, $search) {
                // Category filter
                if (!empty($categoryId)) {
                    $orgCategories = getOrganizationCategories($org['id']);
                    if (!in_array($categoryId, $orgCategories) && $org['primary_category_id'] != $categoryId) {
                        return false;
                    }
                }
                
                // Location filter
                if (!empty($locationId)) {
                    $locations = getLocations();
                    $locationName = '';
                    foreach ($locations as $loc) {
                        if ($loc['id'] == $locationId) {
                            $locationName = $loc['name'];
                            break;
                        }
                    }
                    // Check if the organization location contains the location name
                    if (!empty($locationName) && stripos($org['location'], $locationName) === false) {
                        return false;
                    }
                }
                
                // Search filter
                if (!empty($search)) {
                    $searchLower = strtolower($search);
                    $nameMatch = stripos($org['name'], $search) !== false;
                    $descMatch = stripos($org['description'], $search) !== false;
                    $locationMatch = stripos($org['location'], $search) !== false;
                    
                    if (!$nameMatch && !$descMatch && !$locationMatch) {
                        return false;
                    }
                }
                
                return true;
            });
        }
    }
    return [];
}

/**
 * Get a specific organization by ID
 * 
 * @param int $id Organization ID
 * @return array|null Organization details or null if not found
 */
function getOrganizationById($id) {
    $organizationsFile = DATA_PATH . 'organizations.json';
    if (file_exists($organizationsFile)) {
        $json = file_get_contents($organizationsFile);
        $organizations = json_decode($json, true);
        
        foreach ($organizations as $organization) {
            if ($organization['id'] == $id) {
                return $organization;
            }
        }
    }
    return null;
}

/**
 * Get all categories for a specific organization
 * 
 * @param int $orgId Organization ID
 * @return array Category IDs
 */
function getOrganizationCategories($orgId) {
    $organization = getOrganizationById($orgId);
    
    if ($organization && isset($organization['categories'])) {
        return $organization['categories'];
    } elseif ($organization && isset($organization['primary_category_id'])) {
        // If no categories array but has primary category
        return [$organization['primary_category_id']];
    }
    
    return [];
}

/**
 * Get opportunities for a specific organization
 * 
 * @param int $orgId Organization ID
 * @return array Opportunities
 */
function getOrganizationOpportunities($orgId) {
    $opportunities = getOpportunities();
    
    return array_filter($opportunities, function($opp) use ($orgId) {
        return $opp['organization_id'] == $orgId;
    });
}

/**
 * Get similar organizations based on category and excluding the current organization
 * 
 * @param int $currentId Current organization ID to exclude
 * @param int $categoryId Category ID to match
 * @param int $limit Number of organizations to return
 * @return array Similar organizations
 */
function getSimilarOrganizations($currentId, $categoryId, $limit = 3) {
    $organizations = getOrganizations();
    
    // Filter to only include organizations in the same category but not the current one
    $similar = array_filter($organizations, function($org) use ($currentId, $categoryId) {
        $orgCategories = getOrganizationCategories($org['id']);
        return $org['id'] != $currentId && 
               ($org['primary_category_id'] == $categoryId || in_array($categoryId, $orgCategories));
    });
    
    // If we don't have enough similar by category, add some random ones
    if (count($similar) < $limit) {
        $others = array_filter($organizations, function($org) use ($currentId, $similar) {
            return $org['id'] != $currentId && !in_array($org, $similar);
        });
        
        // Shuffle the other organizations
        shuffle($others);
        
        // Add enough to reach the limit
        $similar = array_merge($similar, array_slice($others, 0, $limit - count($similar)));
    }
    
    // Shuffle the similar organizations
    shuffle($similar);
    return array_slice($similar, 0, $limit);
}

/**
 * Get total number of opportunities
 * 
 * @return int Total opportunities
 */
function getTotalOpportunities() {
    $opportunities = getOpportunities();
    return count($opportunities);
}

/**
 * Get total number of organizations
 * 
 * @return int Total organizations
 */
function getTotalOrganizations() {
    $organizations = getOrganizations();
    return count($organizations);
}

/**
 * Get total number of applications
 * 
 * @return int Total applications
 */
function getTotalApplications() {
    $applicationsFile = DATA_PATH . 'applications.json';
    if (file_exists($applicationsFile)) {
        $json = file_get_contents($applicationsFile);
        $applications = json_decode($json, true);
        return count($applications);
    }
    return 0;
}

/**
 * Submit a volunteer application
 * 
 * @param array $data Application data
 * @return bool Success or failure
 */
function submitApplication($data) {
    $applicationsFile = DATA_PATH . 'applications.json';
    
    // Load existing applications
    if (file_exists($applicationsFile)) {
        $json = file_get_contents($applicationsFile);
        $applications = json_decode($json, true);
    } else {
        $applications = [];
    }
    
    // Generate a unique ID for the application
    $data['id'] = count($applications) > 0 ? max(array_column($applications, 'id')) + 1 : 1;
    
    // Add application submission date if not set
    if (!isset($data['application_date'])) {
        $data['application_date'] = date('Y-m-d H:i:s');
    }
    
    // Add status if not set
    if (!isset($data['status'])) {
        $data['status'] = 'pending';
    }
    
    // Add the new application
    $applications[] = $data;
    
    // Save back to file
    return file_put_contents($applicationsFile, json_encode($applications, JSON_PRETTY_PRINT)) !== false;
}

/**
 * Submit a contact form
 * 
 * @param array $data Form data
 * @return bool Success or failure
 */
function submitContactForm($data) {
    $contactFile = DATA_PATH . 'contact_submissions.json';
    
    // Load existing submissions
    if (file_exists($contactFile)) {
        $json = file_get_contents($contactFile);
        $submissions = json_decode($json, true);
    } else {
        $submissions = [];
    }
    
    // Generate a unique ID for the submission
    $data['id'] = count($submissions) > 0 ? max(array_column($submissions, 'id')) + 1 : 1;
    
    // Add submission date
    $data['submission_date'] = date('Y-m-d H:i:s');
    
    // Add status
    $data['status'] = 'new';
    
    // Add the new submission
    $submissions[] = $data;
    
    // Save back to file
    return file_put_contents($contactFile, json_encode($submissions, JSON_PRETTY_PRINT)) !== false;
}

/**
 * Search opportunities with AJAX
 * 
 * @param string $query Search query
 * @param string $category Category ID
 * @param string $location Location ID
 * @param bool $remote Remote only filter
 * @return array Search results
 */
function searchOpportunities($query, $category, $location, $remote) {
    return getOpportunities($category, $location, $query, $remote);
}

/**
 * Search organizations with AJAX
 * 
 * @param string $query Search query
 * @param string $category Category ID
 * @param string $location Location ID
 * @return array Search results
 */
function searchOrganizations($query, $category, $location) {
    return getOrganizations($category, $location, $query);
}

/**
 * Get all users
 * 
 * @return array Users
 */
function getUsers() {
    try {
        return dbQuery("SELECT * FROM users");
    } catch (Exception $e) {
        error_log("Error getting users: " . $e->getMessage());
        
        // Fallback to JSON file if database query fails
        $usersFile = DATA_PATH . 'users.json';
        if (file_exists($usersFile)) {
            $json = file_get_contents($usersFile);
            return json_decode($json, true);
        }
        return [];
    }
}

/**
 * Get all users from JSON file (used for fallback mode)
 * 
 * @return array Users
 */
function getAllUsers() {
    // Get users directly from JSON file
    $usersFile = DATA_PATH . 'users.json';
    if (file_exists($usersFile)) {
        $json = file_get_contents($usersFile);
        return json_decode($json, true);
    }
    return [];
}

/**
 * Get user by email
 * 
 * @param string $email User email
 * @return array|null User data or null if not found
 */
function getUserByEmail($email) {
    // If database operations are disabled, go straight to JSON
    if (defined('SKIP_DB_OPERATIONS') && SKIP_DB_OPERATIONS) {
        // Get user directly from JSON
        $users = getAllUsers();
        foreach ($users as $user) {
            if (strtolower($user['email']) === strtolower($email)) {
                return $user;
            }
        }
        return null;
    }
    
    try {
        $user = dbQuery("SELECT * FROM users WHERE email = ?", [$email], false);
        
        if ($user) {
            // Get user profile if available
            try {
                $profile = dbQuery(
                    "SELECT * FROM user_profiles WHERE user_id = ?",
                    [$user['id']],
                    false
                );
                
                if ($profile) {
                    // Convert JSON fields back to arrays
                    if (!empty($profile['skills'])) {
                        $profile['skills'] = json_decode($profile['skills'], true);
                    }
                    if (!empty($profile['interests'])) {
                        $profile['interests'] = json_decode($profile['interests'], true);
                    }
                    
                    $user['profile'] = $profile;
                }
            } catch (Exception $e) {
                error_log("Error getting user profile: " . $e->getMessage());
            }
            
            return $user;
        }
        
        return null;
    } catch (Exception $e) {
        error_log("Error getting user by email: " . $e->getMessage());
        
        // Fallback to JSON file if database query fails
        $users = getAllUsers();
        foreach ($users as $user) {
            if (strtolower($user['email']) === strtolower($email)) {
                return $user;
            }
        }
        return null;
    }
}

/**
 * Get user by ID
 * 
 * @param int $id User ID
 * @return array|null User data or null if not found
 */
function getUserById($id) {
    // If database operations are disabled, go straight to JSON
    if (defined('SKIP_DB_OPERATIONS') && SKIP_DB_OPERATIONS) {
        // Get user directly from JSON
        $users = getAllUsers();
        foreach ($users as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }
        return null;
    }
    
    try {
        $user = dbQuery("SELECT * FROM users WHERE id = ?", [$id], false);
        
        if ($user) {
            // Get user profile if available
            try {
                $profile = dbQuery(
                    "SELECT * FROM user_profiles WHERE user_id = ?",
                    [$user['id']],
                    false
                );
                
                if ($profile) {
                    // Convert JSON fields back to arrays
                    if (!empty($profile['skills'])) {
                        $profile['skills'] = json_decode($profile['skills'], true);
                    }
                    if (!empty($profile['interests'])) {
                        $profile['interests'] = json_decode($profile['interests'], true);
                    }
                    
                    $user['profile'] = $profile;
                }
            } catch (Exception $e) {
                error_log("Error getting user profile: " . $e->getMessage());
            }
            
            return $user;
        }
        
        return null;
    } catch (Exception $e) {
        error_log("Error getting user by ID: " . $e->getMessage());
        
        // Fallback to JSON file if database query fails
        $users = getAllUsers();
        foreach ($users as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }
        return null;
    }
}

/**
 * Register a new user
 * 
 * @param array $userData User data
 * @return bool|int User ID on success, false on failure
 */
function registerUser($userData) {
    try {
        // Check if email already exists
        $existingUser = getUserByEmail($userData['email']);
        if ($existingUser) {
            return false; // Email already registered
        }
        
        // Hash password
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Check if we're using database or JSON
        if (defined('SKIP_DB_OPERATIONS') && SKIP_DB_OPERATIONS) {
            // Use JSON files
            $users = getAllUsers();
            
            // Get new user ID
            $maxId = 0;
            foreach ($users as $user) {
                if ($user['id'] > $maxId) {
                    $maxId = $user['id'];
                }
            }
            $userId = $maxId + 1;
            
            // Create new user
            $newUser = [
                'id' => $userId,
                'email' => $userData['email'],
                'password' => $hashedPassword,
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'user_type' => $userData['user_type'],
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Add to users array
            $users[] = $newUser;
            
            // Save to file
            file_put_contents(DATA_PATH . 'users.json', json_encode($users, JSON_PRETTY_PRINT));
        } else {
            // Use database
            $db = getDbConnection();
            if ($db) {
                $db->beginTransaction();
            }
            
            $userId = dbQuery(
                "INSERT INTO users (
                    email, password, first_name, last_name, user_type, created_at
                ) VALUES (?, ?, ?, ?, ?, ?)",
                [
                    $userData['email'],
                    $hashedPassword,
                    $userData['first_name'],
                    $userData['last_name'],
                    $userData['user_type'],
                    date('Y-m-d H:i:s')
                ]
            );
        }
        
        // If profile data exists, process it
        if (isset($userData['profile']) && is_array($userData['profile'])) {
            $profile = $userData['profile'];
            
            if (defined('SKIP_DB_OPERATIONS') && SKIP_DB_OPERATIONS) {
                // Update user in JSON with profile data
                if (!isset($newUser['profile'])) {
                    $newUser['profile'] = [];
                }
                
                // Add profile fields to user object
                foreach ($profile as $key => $value) {
                    $newUser['profile'][$key] = $value;
                }
                
                // Update user in the array and save file
                foreach ($users as $key => $user) {
                    if ($user['id'] === $userId) {
                        $users[$key] = $newUser;
                        break;
                    }
                }
                
                // Save updated users to file
                file_put_contents(DATA_PATH . 'users.json', json_encode($users, JSON_PRETTY_PRINT));
            } else {
                // Use database for profile
                // Prepare profile data
                $bio = isset($profile['bio']) ? $profile['bio'] : null;
                $location = isset($profile['location']) ? $profile['location'] : null;
                $skills = isset($profile['skills']) ? json_encode($profile['skills']) : null;
                $interests = isset($profile['interests']) ? json_encode($profile['interests']) : null;
                $availability = isset($profile['availability']) ? $profile['availability'] : null;
                $organizationId = isset($profile['organization_id']) ? $profile['organization_id'] : null;
                $position = isset($profile['position']) ? $profile['position'] : null;
                
                dbQuery(
                    "INSERT INTO user_profiles (
                        user_id, bio, location, skills, interests, availability,
                        organization_id, position
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $userId,
                        $bio,
                        $location,
                        $skills,
                        $interests,
                        $availability,
                        $organizationId,
                        $position
                    ]
                );
            }
        }
        
        // Commit database transaction if using database
        if (!defined('SKIP_DB_OPERATIONS') || !SKIP_DB_OPERATIONS) {
            if (isset($db) && $db && $db->inTransaction()) {
                $db->commit();
            }
        }
        
        return $userId;
    } catch (Exception $e) {
        // Rollback transaction on error
        if (isset($db) && $db && method_exists($db, 'inTransaction') && $db->inTransaction()) {
            $db->rollBack();
        }
        
        error_log("Error registering user: " . $e->getMessage());
        
        // Fallback to JSON file if database operation fails
        $usersFile = DATA_PATH . 'users.json';
        
        // Load existing users
        if (file_exists($usersFile)) {
            $json = file_get_contents($usersFile);
            $users = json_decode($json, true);
        } else {
            $users = [];
        }
        
        // Check if email already exists
        foreach ($users as $user) {
            if ($user['email'] === $userData['email']) {
                return false; // Email already registered
            }
        }
        
        // Generate a unique ID for the user
        $userData['id'] = count($users) > 0 ? max(array_column($users, 'id')) + 1 : 1;
        
        // Add created_at timestamp
        $userData['created_at'] = date('Y-m-d H:i:s');
        
        // Hash password
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Add the new user
        $users[] = $userData;
        
        // Save back to file
        $result = file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
        
        if ($result !== false) {
            return $userData['id'];
        }
        
        return false;
    }
}

/**
 * Authenticate user
 * 
 * @param string $email User email
 * @param string $password User password
 * @return bool|array User data on success, false on failure
 */
function authenticateUser($email, $password) {
    // Debug info
    error_log("Authentication attempt for email: " . $email);
    
    // Get user by email
    $user = getUserByEmail($email);
    
    // Debug if user was found
    if ($user) {
        error_log("User found, verifying password");
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            error_log("Password verified successfully");
            
            // Remove password before returning
            unset($user['password']);
            return $user;
        } else {
            error_log("Password verification failed");
        }
    } else {
        error_log("User not found with email: " . $email);
    }
    
    return false;
}

/**
 * Check if user is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged in user
 * 
 * @return array|null User data or null if not logged in
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        $user = getUserById($_SESSION['user_id']);
        if ($user) {
            // Remove password before returning
            unset($user['password']);
            return $user;
        }
    }
    return null;
}

/**
 * Logout current user
 * 
 * @return void
 */
function logoutUser() {
    startSession();
    $_SESSION = array();
    session_destroy();
}
