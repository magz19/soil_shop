<?php
/**
 * S-Oil Products Store Installation Script
 * 
 * This script will guide you through setting up the database and initial configuration.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define constants
define('STEP_WELCOME', 1);
define('STEP_REQUIREMENTS', 2);
define('STEP_DATABASE', 3);
define('STEP_INSTALL', 4);
define('STEP_COMPLETE', 5);

// Get current step from session or set to welcome
$step = isset($_SESSION['install_step']) ? $_SESSION['install_step'] : STEP_WELCOME;

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['step'])) {
        switch ($_POST['step']) {
            case STEP_WELCOME:
                $step = STEP_REQUIREMENTS;
                break;
                
            case STEP_REQUIREMENTS:
                $step = STEP_DATABASE;
                break;
                
            case STEP_DATABASE:
                // Validate database details
                $db_host = $_POST['db_host'] ?? '';
                $db_user = $_POST['db_user'] ?? '';
                $db_pass = $_POST['db_pass'] ?? '';
                $db_name = $_POST['db_name'] ?? '';
                
                // Try to connect to database
                $conn = @new mysqli($db_host, $db_user, $db_pass);
                
                if ($conn->connect_error) {
                    $error = "Database connection failed: " . $conn->connect_error;
                } else {
                    // Save database details to session
                    $_SESSION['db_host'] = $db_host;
                    $_SESSION['db_user'] = $db_user;
                    $_SESSION['db_pass'] = $db_pass;
                    $_SESSION['db_name'] = $db_name;
                    
                    $step = STEP_INSTALL;
                }
                break;
                
            case STEP_INSTALL:
                // Get database details from session
                $db_host = $_SESSION['db_host'] ?? '';
                $db_user = $_SESSION['db_user'] ?? '';
                $db_pass = $_SESSION['db_pass'] ?? '';
                $db_name = $_SESSION['db_name'] ?? '';
                
                // Create database connection
                $conn = new mysqli($db_host, $db_user, $db_pass);
                
                if ($conn->connect_error) {
                    $error = "Database connection failed: " . $conn->connect_error;
                    $step = STEP_DATABASE;
                } else {
                    // Create database if it doesn't exist
                    $conn->query("CREATE DATABASE IF NOT EXISTS `$db_name`");
                    $conn->select_db($db_name);
                    
                    // Import database schema
                    $sql_file = file_get_contents('database/soil_shop.sql');
                    
                    // Split SQL file into individual queries
                    $queries = explode(';', $sql_file);
                    
                    // Execute each query
                    $error = false;
                    foreach ($queries as $query) {
                        $query = trim($query);
                        if (!empty($query)) {
                            if (!$conn->query($query)) {
                                $error = "Error executing SQL: " . $conn->error;
                                break;
                            }
                        }
                    }
                    
                    if (!$error) {
                        // Create db_connection.php file
                        $config_content = "<?php
/**
 * Database Connection for S-Oil Products Store
 */

// Define development mode for error handling
define('DEVELOPMENT_MODE', true);

// Database connection details
\$db_host = '$db_host';
\$db_user = '$db_user';
\$db_pass = '$db_pass';
\$db_name = '$db_name';

// Create connection
\$conn = new mysqli(\$db_host, \$db_user, \$db_pass, \$db_name);

// Check connection
if (\$conn->connect_error) {
    // If in development mode, show detailed error
    if (DEVELOPMENT_MODE) {
        die(\"Connection failed: \" . \$conn->connect_error);
    } else {
        // In production, show friendly message
        die(\"We're experiencing technical difficulties. Please try again later.\");
    }
}

// Set charset
\$conn->set_charset(\"utf8mb4\");";
                        
                        // Write config file
                        if (file_put_contents('includes/db_connection.php', $config_content)) {
                            $step = STEP_COMPLETE;
                        } else {
                            $error = "Could not write database configuration file. Please check file permissions.";
                        }
                    }
                }
                break;
        }
        
        // Save step to session
        $_SESSION['install_step'] = $step;
    }
}

// Check system requirements
function checkRequirements() {
    $requirements = [
        'php_version' => [
            'name' => 'PHP Version',
            'minimum' => '7.0.0',
            'current' => PHP_VERSION,
            'status' => version_compare(PHP_VERSION, '7.0.0', '>=')
        ],
        'mysqli' => [
            'name' => 'MySQLi Extension',
            'minimum' => 'Enabled',
            'current' => extension_loaded('mysqli') ? 'Enabled' : 'Disabled',
            'status' => extension_loaded('mysqli')
        ],
        'gd' => [
            'name' => 'GD Extension',
            'minimum' => 'Enabled',
            'current' => extension_loaded('gd') ? 'Enabled' : 'Disabled',
            'status' => extension_loaded('gd')
        ],
        'file_permissions' => [
            'name' => 'File Permissions',
            'minimum' => 'Writable',
            'current' => is_writable('includes') ? 'Writable' : 'Not Writable',
            'status' => is_writable('includes')
        ],
    ];
    
    return $requirements;
}

// Function to display header
function displayHeader() {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>S-Oil Products Store - Installation</title>
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        
        <style>
            body {
                background-color: #f8f9fa;
                padding-top: 40px;
                padding-bottom: 40px;
            }
            
            .install-container {
                max-width: 700px;
                margin: 0 auto;
                padding: 30px;
            }
            
            .install-header {
                text-align: center;
                margin-bottom: 30px;
            }
            
            .step-header {
                display: flex;
                justify-content: space-between;
                margin-bottom: 20px;
                border-bottom: 1px solid #dee2e6;
                padding-bottom: 10px;
            }
            
            .step-indicator {
                display: flex;
                justify-content: center;
                margin-bottom: 30px;
            }
            
            .step-indicator .step {
                flex: 1;
                text-align: center;
                position: relative;
                padding-bottom: 10px;
            }
            
            .step-indicator .step:not(:last-child)::after {
                content: '';
                position: absolute;
                top: 15px;
                right: -50%;
                width: 100%;
                height: 2px;
                background-color: #dee2e6;
                z-index: 0;
            }
            
            .step-indicator .step.active::after {
                background-color: #ffc107;
            }
            
            .step-indicator .step.completed::after {
                background-color: #28a745;
            }
            
            .step-indicator .step .step-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                background-color: #f8f9fa;
                border: 2px solid #dee2e6;
                color: #6c757d;
                font-weight: bold;
                position: relative;
                z-index: 1;
            }
            
            .step-indicator .step.active .step-icon {
                background-color: #fff;
                border-color: #ffc107;
                color: #ffc107;
            }
            
            .step-indicator .step.completed .step-icon {
                background-color: #28a745;
                border-color: #28a745;
                color: #fff;
            }
            
            .step-indicator .step .step-label {
                font-size: 0.8rem;
                color: #6c757d;
                margin-top: 5px;
            }
            
            .step-indicator .step.active .step-label {
                color: #ffc107;
                font-weight: 600;
            }
            
            .step-indicator .step.completed .step-label {
                color: #28a745;
                font-weight: 600;
            }
        </style>
    </head>
    <body>
        <div class="install-container">
            <div class="install-header">
                <h1><span class="text-warning">S-Oil</span> Products Store</h1>
                <p class="text-muted">Installation Wizard</p>
            </div>
    <?php
}

// Function to display step indicator
function displayStepIndicator($currentStep) {
    $steps = [
        STEP_WELCOME => 'Welcome',
        STEP_REQUIREMENTS => 'Requirements',
        STEP_DATABASE => 'Database',
        STEP_INSTALL => 'Installation',
        STEP_COMPLETE => 'Complete'
    ];
    
    echo '<div class="step-indicator">';
    
    foreach ($steps as $step => $label) {
        $class = '';
        
        if ($step < $currentStep) {
            $class = 'completed';
            $icon = '<i class="fas fa-check"></i>';
        } elseif ($step == $currentStep) {
            $class = 'active';
            $icon = $step;
        } else {
            $icon = $step;
        }
        
        echo '<div class="step ' . $class . '">';
        echo '<span class="step-icon">' . $icon . '</span>';
        echo '<div class="step-label">' . $label . '</div>';
        echo '</div>';
    }
    
    echo '</div>';
}

// Function to display footer
function displayFooter() {
    ?>
            <div class="mt-5 text-center text-muted small">
                <p>© <?php echo date('Y'); ?> S-Oil Products Store. All rights reserved.</p>
            </div>
        </div>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
}

// Display header
displayHeader();

// Display step indicator
displayStepIndicator($step);

// Display error if any
if (isset($error)) {
    echo '<div class="alert alert-danger">' . $error . '</div>';
}
?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <?php if ($step == STEP_WELCOME): ?>
            <div class="step-header">
                <h3>Welcome</h3>
            </div>
            
            <p>Welcome to the S-Oil Products Store installation wizard. This wizard will guide you through the installation process to set up your online store.</p>
            
            <div class="alert alert-info">
                <p class="mb-0"><strong>Note:</strong> Before continuing, make sure you have the following information ready:</p>
                <ul class="mb-0 mt-2">
                    <li>Database host (usually "localhost")</li>
                    <li>Database username</li>
                    <li>Database password</li>
                    <li>Database name</li>
                </ul>
            </div>
            
            <form method="post">
                <input type="hidden" name="step" value="<?php echo STEP_WELCOME; ?>">
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        Continue <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
            
        <?php elseif ($step == STEP_REQUIREMENTS): ?>
            <div class="step-header">
                <h3>System Requirements</h3>
            </div>
            
            <p>The system will check if your server meets the requirements for running S-Oil Products Store.</p>
            
            <?php
            $requirements = checkRequirements();
            $allRequirementsMet = true;
            ?>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Requirement</th>
                        <th>Minimum</th>
                        <th>Current</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requirements as $requirement): ?>
                        <?php if (!$requirement['status']) $allRequirementsMet = false; ?>
                        <tr>
                            <td><?php echo $requirement['name']; ?></td>
                            <td><?php echo $requirement['minimum']; ?></td>
                            <td><?php echo $requirement['current']; ?></td>
                            <td>
                                <?php if ($requirement['status']): ?>
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Passed</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="fas fa-times"></i> Failed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <form method="post">
                <input type="hidden" name="step" value="<?php echo STEP_REQUIREMENTS; ?>">
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <?php if (!$allRequirementsMet): ?>
                        <div class="alert alert-danger mb-0 me-auto">
                            <i class="fas fa-exclamation-triangle me-2"></i> Your server does not meet all requirements. Please fix the issues and try again.
                        </div>
                        <button type="submit" class="btn btn-primary" disabled>
                            Continue <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    <?php else: ?>
                        <div class="alert alert-success mb-0 me-auto">
                            <i class="fas fa-check-circle me-2"></i> Your server meets all requirements!
                        </div>
                        <button type="submit" class="btn btn-primary">
                            Continue <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </form>
            
        <?php elseif ($step == STEP_DATABASE): ?>
            <div class="step-header">
                <h3>Database Configuration</h3>
            </div>
            
            <p>Please enter your database connection details below. If you're not sure about these, contact your hosting provider.</p>
            
            <form method="post">
                <input type="hidden" name="step" value="<?php echo STEP_DATABASE; ?>">
                
                <div class="mb-3">
                    <label for="db_host" class="form-label">Database Host</label>
                    <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                    <div class="form-text">Usually this is "localhost".</div>
                </div>
                
                <div class="mb-3">
                    <label for="db_user" class="form-label">Database Username</label>
                    <input type="text" class="form-control" id="db_user" name="db_user" value="root" required>
                    <div class="form-text">For XAMPP, this is usually "root".</div>
                </div>
                
                <div class="mb-3">
                    <label for="db_pass" class="form-label">Database Password</label>
                    <input type="password" class="form-control" id="db_pass" name="db_pass" value="">
                    <div class="form-text">For XAMPP, this is usually blank.</div>
                </div>
                
                <div class="mb-3">
                    <label for="db_name" class="form-label">Database Name</label>
                    <input type="text" class="form-control" id="db_name" name="db_name" value="soil_shop" required>
                    <div class="form-text">The database will be created if it doesn't exist.</div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        Continue <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
            
        <?php elseif ($step == STEP_INSTALL): ?>
            <div class="step-header">
                <h3>Installing S-Oil Products Store</h3>
            </div>
            
            <p>The system is ready to install S-Oil Products Store. Click the button below to start the installation process.</p>
            
            <div class="alert alert-warning">
                <p class="mb-0"><strong>Warning:</strong> This will create the necessary database tables and may overwrite existing data. If you're upgrading, please make sure you have a backup of your database.</p>
            </div>
            
            <form method="post">
                <input type="hidden" name="step" value="<?php echo STEP_INSTALL; ?>">
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        Install Now <i class="fas fa-download ms-2"></i>
                    </button>
                </div>
            </form>
            
        <?php elseif ($step == STEP_COMPLETE): ?>
            <div class="step-header">
                <h3>Installation Complete</h3>
            </div>
            
            <div class="text-center mb-4">
                <i class="fas fa-check-circle text-success fa-5x mb-3"></i>
                <h4>S-Oil Products Store has been successfully installed!</h4>
            </div>
            
            <div class="alert alert-success">
                <p class="mb-0"><strong>Success!</strong> You can now start using your S-Oil Products Store.</p>
            </div>
            
            <div class="alert alert-info">
                <h5 class="alert-heading">Admin Login Details</h5>
                <p>You can log in to the admin panel with the following credentials:</p>
                <ul class="mb-0">
                    <li><strong>Username:</strong> admin</li>
                    <li><strong>Password:</strong> admin123</li>
                </ul>
            </div>
            
            <div class="mt-4 text-center">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i> Go to Homepage
                </a>
                <a href="login.php" class="btn btn-warning ms-2">
                    <i class="fas fa-user-cog me-2"></i> Admin Login
                </a>
            </div>
            
            <?php
            // Clear installation session data
            unset($_SESSION['install_step']);
            unset($_SESSION['db_host']);
            unset($_SESSION['db_user']);
            unset($_SESSION['db_pass']);
            unset($_SESSION['db_name']);
            ?>
            
        <?php endif; ?>
    </div>
</div>

<?php
// Display footer
displayFooter();
?>