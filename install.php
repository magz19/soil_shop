<?php
$step = isset($_GET['step']) ? $_GET['step'] : 'welcome';
$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 'database':
            // Process database setup
            $host = $_POST['db_host'] ?? '';
            $username = $_POST['db_username'] ?? '';
            $password = $_POST['db_password'] ?? '';
            $database = $_POST['db_name'] ?? '';
            
            // Validate inputs
            if (empty($host) || empty($username) || empty($database)) {
                $error = 'All fields except password are required.';
            } else {
                // Test database connection
                try {
                    $conn = new mysqli($host, $username, $password);
                    if ($conn->connect_error) {
                        throw new Exception("Connection failed: " . $conn->connect_error);
                    }
                    
                    // Create database if it doesn't exist
                    $sql = "CREATE DATABASE IF NOT EXISTS `$database`";
                    if ($conn->query($sql) !== TRUE) {
                        throw new Exception("Error creating database: " . $conn->error);
                    }
                    
                    // Select database
                    $conn->select_db($database);
                    
                    // Create a config file
                    $configContent = "<?php
// Database configuration
define('DB_HOST', '$host');
define('DB_USERNAME', '$username');
define('DB_PASSWORD', '$password');
define('DB_NAME', '$database');
?>";
                    
                    if (!file_put_contents('includes/config.php', $configContent)) {
                        throw new Exception("Failed to create config file. Check permissions.");
                    }
                    
                    // Import the SQL schema
                    $sql = file_get_contents('database/soil_shop.sql');
                    
                    if ($conn->multi_query($sql)) {
                        // Process all result sets to clear them
                        do {
                            if ($result = $conn->store_result()) {
                                $result->free();
                            }
                        } while ($conn->more_results() && $conn->next_result());
                        
                        $success = 'Database setup completed successfully!';
                        header('Location: install.php?step=complete');
                        exit;
                    } else {
                        throw new Exception("Error importing database schema: " . $conn->error);
                    }
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S-Oil Products Store Installation</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .install-container {
            max-width: 700px;
            margin: 0 auto;
        }
        .install-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .install-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .step.active .step-number {
            background-color: #007bff;
            color: white;
        }
        .step.completed .step-number {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container install-container">
        <div class="install-header">
            <h1><span class="text-warning">S-Oil</span> Products Store</h1>
            <p>Installation Wizard</p>
        </div>
        
        <!-- Installation Steps -->
        <div class="install-steps">
            <div class="step <?php echo in_array($step, ['welcome', 'database', 'complete']) ? 'active' : ''; ?> <?php echo in_array($step, ['database', 'complete']) ? 'completed' : ''; ?>">
                <div class="step-number">1</div>
                <div>Welcome</div>
            </div>
            <div class="step <?php echo in_array($step, ['database', 'complete']) ? 'active' : ''; ?> <?php echo $step == 'complete' ? 'completed' : ''; ?>">
                <div class="step-number">2</div>
                <div>Database Setup</div>
            </div>
            <div class="step <?php echo $step == 'complete' ? 'active' : ''; ?>">
                <div class="step-number">3</div>
                <div>Complete</div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($step == 'welcome'): ?>
                    <h2 class="card-title">Welcome to the Installation Wizard</h2>
                    <p>This wizard will guide you through the installation process of the S-Oil Products Store.</p>
                    <p>Before continuing, please make sure you have the following information ready:</p>
                    <ul>
                        <li>MySQL database host (usually "localhost")</li>
                        <li>MySQL username and password</li>
                        <li>Database name</li>
                    </ul>
                    <p>The installation wizard will create the necessary database tables and configuration files.</p>
                    <div class="d-grid gap-2 col-6 mx-auto mt-4">
                        <a href="install.php?step=database" class="btn btn-primary">Continue</a>
                    </div>
                
                <?php elseif ($step == 'database'): ?>
                    <h2 class="card-title">Database Setup</h2>
                    <p>Please enter your database connection details:</p>
                    
                    <form action="install.php?step=database" method="post">
                        <div class="mb-3">
                            <label for="db_host" class="form-label">Database Host</label>
                            <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                            <div class="form-text">Usually "localhost"</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="db_username" class="form-label">Database Username</label>
                            <input type="text" class="form-control" id="db_username" name="db_username" value="root" required>
                            <div class="form-text">Usually "root" for local installations</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="db_password" class="form-label">Database Password</label>
                            <input type="password" class="form-control" id="db_password" name="db_password">
                            <div class="form-text">Leave empty if no password is set</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="db_name" class="form-label">Database Name</label>
                            <input type="text" class="form-control" id="db_name" name="db_name" value="soil_shop" required>
                            <div class="form-text">The database will be created if it doesn't exist</div>
                        </div>
                        
                        <div class="d-grid gap-2 col-6 mx-auto mt-4">
                            <button type="submit" class="btn btn-primary">Install Database</button>
                        </div>
                    </form>
                
                <?php elseif ($step == 'complete'): ?>
                    <h2 class="card-title text-success">Installation Complete!</h2>
                    <p>The S-Oil Products Store has been successfully installed on your server.</p>
                    <p>You can now access your store:</p>
                    
                    <div class="d-grid gap-2 col-8 mx-auto mt-4">
                        <a href="index.php" class="btn btn-primary">Go to S-Oil Products Store</a>
                        <a href="utility/make_admin.php" class="btn btn-warning">Set Current User as Admin</a>
                    </div>
                    
                    <div class="alert alert-warning mt-4">
                        <strong>Important:</strong> For security reasons, please remove the <code>install.php</code> file from your server once installation is complete.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>