<!--     -->

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Checking Folder Structure</h2>";
echo "Current directory: " . __DIR__ . "<br><br>";

// Check what exists
$paths_to_check = [
    'config/database.php',
    'config/',
    'Config/database.php',
    'Config/',
    'includes/functions.php',
    'includes/',
    'Includes/functions.php',
    'Includes/',
    './config/database.php',
    './includes/functions.php'
];

foreach ($paths_to_check as $path) {
    $full_path = __DIR__ . '/' . $path;
    if (file_exists($full_path)) {
        echo "✅ FOUND: $path<br>";
    } else {
        echo "❌ MISSING: $path<br>";
    }
}

echo "<br><h3>Current Directory Contents:</h3>";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "$file<br>";
    }
}
?>