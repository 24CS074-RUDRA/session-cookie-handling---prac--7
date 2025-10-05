<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/session.php';
require_once 'includes/functions.php';
require_once 'config/database.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    if (empty($username) || empty($password)) {
        $error = "Username/Email and password are required!";
    } else {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            if (!$db) {
                $error = "Database connection failed!";
            } else {
                $query = "SELECT id, username, password FROM users WHERE username = :username OR email = :username";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                
                if ($stmt->rowCount() == 1) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        
                        if ($remember) {
                            $token = bin2hex(random_bytes(32));
                            $expiry = time() + (30 * 24 * 60 * 60);
                            
                            $updateQuery = "UPDATE users SET remember_token = :token WHERE id = :id";
                            $updateStmt = $db->prepare($updateQuery);
                            $updateStmt->bindParam(':token', $token);
                            $updateStmt->bindParam(':id', $user['id']);
                            $updateStmt->execute();
                            
                            setcookie('remember_token', $token, $expiry, '/');
                        }
                        
                        redirect('dashboard.php');
                    } else {
                        $error = "Invalid password!";
                    }
                } else {
                    $error = "User not found!";
                }
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
    <title>Login</title>
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
        .checkbox-group { display: flex; align-items: center; gap: 10px; margin: 15px 0; }
        .checkbox-group input[type="checkbox"] { transform: scale(1.2); }
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
        .form-footer { text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6; color: #6c757d; }
        .form-footer a { color: #4361ee; text-decoration: none; font-weight: 500; }
        .form-footer a:hover { color: #3a0ca3; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🔐 Login</h2>
            <p>Welcome back! Please sign in to your account</p>
        </div>
        
        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-error">⚠️ <?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">👤 Username or Email:</label>
                    <input type="text" class="form-input" name="username" required placeholder="Enter your username or email">
                </div>
                
                <div class="form-group">
                    <label class="form-label">🔒 Password:</label>
                    <input type="password" class="form-input" name="password" required placeholder="Enter your password">
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember Me</label>
                </div>
                
                <button type="submit" class="btn">Login</button>
            </form>
            
            <div class="form-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>
</body>
</html>