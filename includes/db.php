<?php
/**
 * Database connection setup for Volunteer Connect
 */

/**
 * Get database connection
 * 
 * @return PDO Database connection
 */
function getDbConnection() {
    static $dbConnection = null;
    
    // If we're skipping database operations, return null without attempting connection
    if (defined('SKIP_DB_OPERATIONS') && SKIP_DB_OPERATIONS) {
        return null;
    }
    
    if ($dbConnection === null) {
        try {
            // Get database URL from environment variable
            $dbUrl = getenv('DATABASE_URL');
            
            if ($dbUrl) {
                // Connect using the full DATABASE_URL
                $dbConnection = new PDO($dbUrl);
            } else {
                // Fallback to individual environment variables if no DATABASE_URL
                $host = getenv('PGHOST');
                $port = getenv('PGPORT') ?: 5432;
                $dbname = getenv('PGDATABASE');
                $user = getenv('PGUSER');
                $password = getenv('PGPASSWORD');
                
                // Create connection string
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
                $dbConnection = new PDO($dsn, $user, $password);
            }
            
            // Set error mode to exception
            $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Use return values as arrays by default
            $dbConnection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            // Handle connection error - this is expected when skipping DB operations
            error_log("Database connection failed: " . $e->getMessage());
            
            // If we already defined SKIP_DB_OPERATIONS, don't throw an exception,
            // just return null
            if (defined('SKIP_DB_OPERATIONS')) {
                return null;
            }
            
            throw new Exception("Database connection failed. Please try again later.");
        }
    }
    
    return $dbConnection;
}

/**
 * Execute a database query
 * 
 * @param string $query SQL query
 * @param array $params Parameters for prepared statement
 * @param bool $fetchAll Whether to fetch all results or just one
 * @return mixed Query result
 */
function dbQuery($query, $params = [], $fetchAll = true) {
    // If we're skipping database operations, return appropriate fallback
    if (defined('SKIP_DB_OPERATIONS') && SKIP_DB_OPERATIONS) {
        if (stripos($query, 'SELECT') === 0) {
            return $fetchAll ? [] : null;
        } elseif (stripos($query, 'INSERT') === 0) {
            return true;
        } else {
            return 0;
        }
    }
    
    try {
        // Get database connection
        $db = getDbConnection();
        if (!$db) {
            throw new Exception("No database connection available");
        }
        
        // Check if there's an active transaction that failed
        try {
            $inFailedTransaction = false;
            $db->query("SELECT 1");
        } catch (PDOException $e) {
            if (stripos($e->getMessage(), 'transaction') !== false) {
                // We're in a failed transaction state, try to roll it back
                $inFailedTransaction = true;
                try {
                    $db->rollBack();
                } catch (Exception $rollbackException) {
                    // Ignore rollback exceptions, we'll create a new connection
                }
            }
        }
        
        // Prepare and execute the statement
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        // Process the result based on the query type
        if (stripos($query, 'SELECT') === 0) {
            return $fetchAll ? $stmt->fetchAll() : $stmt->fetch();
        } elseif (stripos($query, 'INSERT') === 0) {
            // For INSERT queries with manual ID, just return true instead of lastInsertId
            if (stripos($query, 'INSERT INTO') !== false && 
                (stripos($query, 'id') !== false || stripos($query, 'ID') !== false)) {
                return true;
            } else {
                try {
                    return $db->lastInsertId();
                } catch (Exception $e) {
                    // If lastInsertId fails, just return true
                    return true;
                }
            }
        } else {
            return $stmt->rowCount();
        }
    } catch (PDOException $e) {
        error_log("Database query failed: " . $e->getMessage());
        error_log("Query: $query");
        error_log("Params: " . json_encode($params));
        
        // For SELECT queries, return empty result instead of throwing an exception
        if (stripos($query, 'SELECT') === 0) {
            return $fetchAll ? [] : null;
        }
        
        throw new Exception("Database operation failed. Please try again later.");
    }
}

/**
 * Create database tables if they don't exist
 * 
 * @return void
 */
function initializeDatabase() {
    // Skip initialization if database operations are disabled
    if (defined('SKIP_DB_OPERATIONS') && SKIP_DB_OPERATIONS) {
        return true;
    }
    
    // Define SQL for creating tables
    $createTablesSQL = [
        // Categories table
        "CREATE TABLE IF NOT EXISTS categories (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT
        )",
        
        // Locations table
        "CREATE TABLE IF NOT EXISTS locations (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT
        )",
        
        // Organizations table
        "CREATE TABLE IF NOT EXISTS organizations (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            primary_category_id INTEGER REFERENCES categories(id),
            location VARCHAR(255) NOT NULL,
            description TEXT,
            mission TEXT,
            impact TEXT,
            website VARCHAR(255),
            email VARCHAR(255),
            phone VARCHAR(50),
            founded_year VARCHAR(20),
            social_media JSONB,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // Organization categories (many-to-many)
        "CREATE TABLE IF NOT EXISTS organization_categories (
            organization_id INTEGER REFERENCES organizations(id),
            category_id INTEGER REFERENCES categories(id),
            PRIMARY KEY (organization_id, category_id)
        )",
        
        // Opportunities table
        "CREATE TABLE IF NOT EXISTS opportunities (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            organization_id INTEGER REFERENCES organizations(id),
            category_id INTEGER REFERENCES categories(id),
            location VARCHAR(255),
            is_remote BOOLEAN DEFAULT FALSE,
            description TEXT,
            responsibilities TEXT,
            requirements TEXT,
            benefits TEXT,
            commitment VARCHAR(100),
            duration VARCHAR(100),
            start_date DATE,
            application_deadline DATE,
            is_featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // Users table
        "CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            user_type VARCHAR(20) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // User profiles table
        "CREATE TABLE IF NOT EXISTS user_profiles (
            user_id INTEGER PRIMARY KEY REFERENCES users(id),
            bio TEXT,
            location VARCHAR(255),
            skills JSONB,
            interests JSONB,
            availability TEXT,
            organization_id INTEGER REFERENCES organizations(id),
            position VARCHAR(255),
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        // Applications table
        "CREATE TABLE IF NOT EXISTS applications (
            id SERIAL PRIMARY KEY,
            opportunity_id INTEGER REFERENCES opportunities(id),
            organization_id INTEGER REFERENCES organizations(id),
            user_id INTEGER REFERENCES users(id),
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50),
            address VARCHAR(255),
            city VARCHAR(100),
            state VARCHAR(50),
            zip VARCHAR(20),
            experience TEXT,
            motivation TEXT,
            availability TEXT,
            reference_name VARCHAR(255),
            reference_contact VARCHAR(255),
            application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status VARCHAR(20) DEFAULT 'pending'
        )",
        
        // Contact submissions table
        "CREATE TABLE IF NOT EXISTS contact_submissions (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            organization_id INTEGER REFERENCES organizations(id),
            submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status VARCHAR(20) DEFAULT 'new'
        )"
    ];
    
    try {
        $db = getDbConnection();
        
        // Start transaction
        $db->beginTransaction();
        
        // Create tables
        foreach ($createTablesSQL as $sql) {
            $db->exec($sql);
        }
        
        // Commit transaction
        $db->commit();
        
        return true;
    } catch (PDOException $e) {
        // Rollback transaction on error
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        
        error_log("Database initialization failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Migrate data from JSON files to database
 * 
 * @return bool Success or failure
 */
function migrateDataFromJson() {
    // Skip migration if database operations are disabled
    if (defined('SKIP_DB_OPERATIONS') && SKIP_DB_OPERATIONS) {
        return true;
    }
    
    try {
        $db = getDbConnection();
        
        // Start transaction
        $db->beginTransaction();
        
        // Migrate categories
        $categoriesFile = DATA_PATH . 'categories.json';
        if (file_exists($categoriesFile)) {
            $categories = json_decode(file_get_contents($categoriesFile), true);
            
            // Check if categories table is empty
            $result = dbQuery("SELECT COUNT(*) as count FROM categories");
            if ($result[0]['count'] == 0) {
                foreach ($categories as $category) {
                    dbQuery(
                        "INSERT INTO categories (id, name, description) VALUES (?, ?, ?)",
                        [$category['id'], $category['name'], $category['description']]
                    );
                }
            }
        }
        
        // Migrate locations
        $locationsFile = DATA_PATH . 'locations.json';
        if (file_exists($locationsFile)) {
            $locations = json_decode(file_get_contents($locationsFile), true);
            
            // Check if locations table is empty
            $result = dbQuery("SELECT COUNT(*) as count FROM locations");
            if ($result[0]['count'] == 0) {
                foreach ($locations as $location) {
                    dbQuery(
                        "INSERT INTO locations (id, name, description) VALUES (?, ?, ?)",
                        [$location['id'], $location['name'], $location['description']]
                    );
                }
            }
        }
        
        // Migrate organizations
        $organizationsFile = DATA_PATH . 'organizations.json';
        if (file_exists($organizationsFile)) {
            $organizations = json_decode(file_get_contents($organizationsFile), true);
            
            // Check if organizations table is empty
            $result = dbQuery("SELECT COUNT(*) as count FROM organizations");
            if ($result[0]['count'] == 0) {
                foreach ($organizations as $organization) {
                    // Insert organization
                    $orgId = dbQuery(
                        "INSERT INTO organizations (
                            id, name, primary_category_id, location, description, 
                            mission, impact, website, email, phone, founded_year, social_media
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                        [
                            $organization['id'],
                            $organization['name'],
                            $organization['primary_category_id'],
                            $organization['location'],
                            $organization['description'],
                            $organization['mission'],
                            $organization['impact'],
                            $organization['website'],
                            $organization['email'],
                            $organization['phone'],
                            $organization['founded_year'],
                            json_encode($organization['social_media'])
                        ]
                    );
                    
                    // Insert organization categories
                    if (isset($organization['categories']) && is_array($organization['categories'])) {
                        foreach ($organization['categories'] as $categoryId) {
                            dbQuery(
                                "INSERT INTO organization_categories (organization_id, category_id)
                                VALUES (?, ?)",
                                [$organization['id'], $categoryId]
                            );
                        }
                    }
                }
            }
        }
        
        // Migrate opportunities
        $opportunitiesFile = DATA_PATH . 'opportunities.json';
        if (file_exists($opportunitiesFile)) {
            $opportunities = json_decode(file_get_contents($opportunitiesFile), true);
            
            // Check if opportunities table is empty
            $result = dbQuery("SELECT COUNT(*) as count FROM opportunities");
            if ($result[0]['count'] == 0) {
                foreach ($opportunities as $opportunity) {
                    dbQuery(
                        "INSERT INTO opportunities (
                            id, title, organization_id, category_id, location, is_remote,
                            description, responsibilities, requirements, benefits,
                            commitment, duration, start_date, application_deadline,
                            is_featured, created_at
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                        [
                            $opportunity['id'],
                            $opportunity['title'],
                            $opportunity['organization_id'],
                            $opportunity['category_id'],
                            $opportunity['location'],
                            $opportunity['is_remote'],
                            $opportunity['description'],
                            $opportunity['responsibilities'],
                            $opportunity['requirements'],
                            $opportunity['benefits'],
                            $opportunity['commitment'],
                            $opportunity['duration'],
                            !empty($opportunity['start_date']) ? $opportunity['start_date'] : null,
                            !empty($opportunity['application_deadline']) ? $opportunity['application_deadline'] : null,
                            $opportunity['is_featured'],
                            $opportunity['created_at']
                        ]
                    );
                }
            }
        }
        
        // Migrate users
        $usersFile = DATA_PATH . 'users.json';
        if (file_exists($usersFile)) {
            $users = json_decode(file_get_contents($usersFile), true);
            
            // Check if users table is empty
            $result = dbQuery("SELECT COUNT(*) as count FROM users");
            if ($result[0]['count'] == 0) {
                foreach ($users as $user) {
                    // Insert user
                    $userId = dbQuery(
                        "INSERT INTO users (
                            id, email, password, first_name, last_name, user_type, created_at
                        ) VALUES (?, ?, ?, ?, ?, ?, ?)",
                        [
                            $user['id'],
                            $user['email'],
                            $user['password'],
                            $user['first_name'],
                            $user['last_name'],
                            $user['user_type'],
                            $user['created_at']
                        ]
                    );
                    
                    // Insert user profile
                    if (isset($user['profile'])) {
                        $profile = $user['profile'];
                        
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
                                $user['id'],
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
            }
        }
        
        // Migrate applications
        $applicationsFile = DATA_PATH . 'applications.json';
        if (file_exists($applicationsFile)) {
            $applications = json_decode(file_get_contents($applicationsFile), true);
            
            // Check if applications table is empty
            $result = dbQuery("SELECT COUNT(*) as count FROM applications");
            if ($result[0]['count'] == 0) {
                foreach ($applications as $application) {
                    // Find user ID by email
                    $user = dbQuery(
                        "SELECT id FROM users WHERE email = ?",
                        [$application['email']],
                        false
                    );
                    
                    $userId = $user ? $user['id'] : null;
                    
                    dbQuery(
                        "INSERT INTO applications (
                            id, opportunity_id, organization_id, user_id, first_name, last_name,
                            email, phone, address, city, state, zip, experience, motivation,
                            availability, reference_name, reference_contact, application_date, status
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                        [
                            $application['id'],
                            $application['opportunity_id'],
                            $application['organization_id'],
                            $userId,
                            $application['first_name'],
                            $application['last_name'],
                            $application['email'],
                            $application['phone'],
                            $application['address'],
                            $application['city'],
                            $application['state'],
                            $application['zip'],
                            $application['experience'],
                            $application['motivation'],
                            $application['availability'],
                            $application['reference_name'],
                            $application['reference_contact'],
                            $application['application_date'],
                            $application['status']
                        ]
                    );
                }
            }
        }
        
        // Migrate contact submissions
        $contactFile = DATA_PATH . 'contact_submissions.json';
        if (file_exists($contactFile)) {
            $submissions = json_decode(file_get_contents($contactFile), true);
            
            // Check if contact_submissions table is empty
            $result = dbQuery("SELECT COUNT(*) as count FROM contact_submissions");
            if ($result[0]['count'] == 0) {
                foreach ($submissions as $submission) {
                    dbQuery(
                        "INSERT INTO contact_submissions (
                            id, name, email, subject, message, organization_id, submission_date, status
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                        [
                            $submission['id'],
                            $submission['name'],
                            $submission['email'],
                            $submission['subject'],
                            $submission['message'],
                            !empty($submission['organization_id']) ? $submission['organization_id'] : null,
                            $submission['submission_date'],
                            $submission['status']
                        ]
                    );
                }
            }
        }
        
        // Commit transaction
        $db->commit();
        
        return true;
    } catch (PDOException $e) {
        // Rollback transaction on error
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        
        error_log("Data migration failed: " . $e->getMessage());
        return false;
    }
}