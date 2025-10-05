<!-- THIS CODE IS ONLY FOR CHECKING THE CODE RUNNING SUCCESS.... -->

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";

try {
    require_once 'config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "✅ <strong>Database Connected Successfully!</strong><br>";
        echo "Database Name: login_system<br>";
        echo "Host: localhost<br><br>";
        
        // Check if users table exists and show data
        $query = "SHOW TABLES LIKE 'users'";
        $stmt = $db->query($query);
        
        if ($stmt->rowCount() > 0) {
            echo "✅ <strong>Users table exists!</strong><br>";
            
            // Show table structure
            echo "<h3>Table Structure:</h3>";
            $stmt = $db->query("DESCRIBE users");
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$row['Field']}</td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Null']}</td>";
                echo "<td>{$row['Key']}</td>";
                echo "<td>{$row['Default']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Show existing users
            echo "<h3>Existing Users:</h3>";
            $stmt = $db->query("SELECT id, username, email, created_at FROM users");
            if ($stmt->rowCount() > 0) {
                echo "<table border='1' cellpadding='5'>";
                echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Created At</th></tr>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>{$row['id']}</td>";
                    echo "<td>{$row['username']}</td>";
                    echo "<td>{$row['email']}</td>";
                    echo "<td>{$row['created_at']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No users registered yet.";
            }
        } else {
            echo "❌ Users table does not exist. Please create it in PHPMyAdmin.";
        }
    }
} catch (PDOException $e) {
    echo "❌ <strong>Database Error:</strong> " . $e->getMessage();
}
?>

