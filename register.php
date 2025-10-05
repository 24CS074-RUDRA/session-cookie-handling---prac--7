<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$paths_tried = [];

if (file_exists(__DIR__ . '/Config/database.php')) {
    require_once __DIR__ . '/Config/database.php';
    require_once __DIR__ . '/Includes/session.php';
    require_once __DIR__ . '/Includes/functions.php';
    $paths_tried[] = 'Capitalized folders (Config, Includes)';
} 
elseif (file_exists(__DIR__ . '/config/database.php')) {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/includes/session.php';
    require_once __DIR__ . '/includes/functions.php';
    $paths_tried[] = 'Lowercase folders (config, includes)';
} 
elseif (file_exists(__DIR__ . '/database.php')) {
    require_once 'database.php';
    require_once 'session.php';
    require_once 'functions.php';
    $paths_tried[] = 'Files in main directory';
} else {
    die("❌ Could not find required files. Please check folder structure.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } else {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if ($db) {
                $query = "SELECT id FROM users WHERE username = :username OR email = :email";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $error = "Username or email already exists!";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $hashed_password);
                    
                    if ($stmt->execute()) {
                        $success = "Registration successful! <a href='login.php'>Login here</a>";
                    } else {
                        $error = "Registration failed. Please try again.";
                    }
                }
            } else {
                $error = "Cannot connect to database!";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh; padding: 20px;
            display: flex; align-items: center; justify-content: center;
        }
        .container {
            max-width: 450px; width: 100%; background: white;
            border-radius: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden; animation: fadeIn 0.6s ease;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .header {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white; padding: 30px 20px; text-align: center;
        }
        .header h2 { font-size: 28px; margin-bottom: 10px; }
        .header p { opacity: 0.9; font-size: 16px; }
        .form-container { padding: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; color: #2b2d42; font-weight: 600; font-size: 14px; }
        .form-input {
            width: 100%; padding: 15px; border: 2px solid #dee2e6; border-radius: 8px;
            font-size: 16px; transition: all 0.3s ease; background: #f8f9fa;
        }
        .form-input:focus {
            outline: none; border-color: #4361ee; background: white;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1); transform: translateY(-2px);
        }
        .btn {
            width: 100%; padding: 15px; border: none; border-radius: 8px;
            font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;
            background: linear-gradient(135deg, #4361ee, #3a0ca3); color: white;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3); }
        .alert {
            padding: 15px 20px; border-radius: 8px; margin-bottom: 20px;
            border-left: 4px solid; animation: slideIn 0.5s ease;
        }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        .alert-error { background: #f8d7da; border-color: #f72585; color: #721c24; }
        .alert-success { background: #d4edda; border-color: #4cc9f0; color: #155724; }
        .form-footer { text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6; color: #6c757d; }
        .form-footer a { color: #4361ee; text-decoration: none; font-weight: 500; }
        .form-footer a:hover { color: #3a0ca3; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>👤 Register</h2>
            <p>Create your account to get started</p>
        </div>
        
        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-error">⚠️ <?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">👤 Username:</label>
                    <input type="text" class="form-input" name="username" required placeholder="Choose a username">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email:</label>
                    <input type="email" class="form-input" name="email" required placeholder="Enter your email">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password:</label>
                    <input type="password" class="form-input" name="password" required placeholder="Create a password (min. 6 characters)">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Confirm Password:</label>
                    <input type="password" class="form-input" name="confirm_password" required placeholder="Confirm your password">
                </div>
                
                <button type="submit" class="btn">Register</button>
            </form>
            
            <div class="form-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>
</html>