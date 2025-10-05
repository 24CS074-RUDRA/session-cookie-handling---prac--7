<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/session.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

class Database {
    private $host = "localhost";
    private $db_name = "login_system";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

$user_data = [];
try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT username, email, created_at FROM users WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching user data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh; padding: 20px;
            display: flex; align-items: center; justify-content: center;
        }
        .container {
            max-width: 500px; width: 100%; background: white;
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
        .alert {
            padding: 15px 20px; border-radius: 8px; margin-bottom: 20px;
            border-left: 4px solid; animation: slideIn 0.5s ease;
        }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        .alert-error { background: #f8d7da; border-color: #f72585; color: #721c24; }
        .dashboard-info {
            background: #f8f9fa; padding: 25px; border-radius: 10px;
            margin: 25px 0; border-left: 4px solid #4361ee;
        }
        .dashboard-info h3 { color: #2b2d42; margin-bottom: 15px; font-size: 20px; }
        .dashboard-info p { margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #dee2e6; }
        .dashboard-info p:last-child { border-bottom: none; }
        .dashboard-info strong { color: #4361ee; min-width: 120px; display: inline-block; }
        .btn {
            width: 100%; padding: 15px; border: none; border-radius: 8px;
            font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;
            text-decoration: none; display: inline-block; text-align: center;
            background: linear-gradient(135deg, #f72585, #b5179e); color: white;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(247, 37, 133, 0.3); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📊 Dashboard</h2>
            <p>Welcome to your personal dashboard</p>
        </div>
        
        <div class="form-container">
            <?php if (isset($error)): ?>
                <div class="alert alert-error">⚠️ <?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="dashboard-info">
                <h3>👤 User Information</h3>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user_data['username'] ?? 'N/A'); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email'] ?? 'N/A'); ?></p>
                <p><strong>Member Since:</strong> <?php echo htmlspecialchars($user_data['created_at'] ?? 'N/A'); ?></p>
                <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
            </div>
            
            <a href="logout.php" class="btn">🚪 Logout</a>
        </div>
    </div>
</body>
</html>