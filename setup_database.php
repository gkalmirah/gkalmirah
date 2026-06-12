<?php
/**
 * DATABASE SETUP SCRIPT
 * This script automates the creation of the database and tables for the G.K. Almirah project.
 */

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'furnitureshopmain';
$sql_file = 'database_backup_21_products.sql';

echo "<h1>🚀 Project Database Setup</h1>";

// 1. Try connecting to MySQL
$ports = [3307, 3306];
$con = null;
$connected_port = null;

foreach ($ports as $port) {
    echo "Testing connection on port $port... ";
    $temp_con = @mysqli_connect($host, $user, $pass, '', $port);
    if ($temp_con) {
        $con = $temp_con;
        $connected_port = $port;
        echo "<span style='color:green;'>SUCCESS</span><br>";
        break;
    }
    echo "<span style='color:red;'>FAILED</span><br>";
}

if (!$con) {
    die("<h2 style='color:red;'>❌ Setup Failed: Could not connect to MySQL. Is XAMPP running?</h2>");
}

// 2. Create Database if not exists
echo "Checking for database <strong>$dbname</strong>... ";
$db_check = mysqli_query($con, "CREATE DATABASE IF NOT EXISTS `$dbname` ");
if ($db_check) {
    echo "<span style='color:green;'>DONE</span><br>";
} else {
    die("<span style='color:red;'>FAILED: " . mysqli_error($con) . "</span>");
}

// 3. Select Database
mysqli_select_db($con, $dbname);

// 4. Check if tables exist
$table_check = mysqli_query($con, "SHOW TABLES LIKE 'furniture_product'");
if (mysqli_num_rows($table_check) > 0) {
    echo "<h2 style='color:blue;'>ℹ️ Database already contains data. Skipping import.</h2>";
    echo "<a href='index.php' style='padding:10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Back to Homepage</a>";
    exit;
}

// 5. Import SQL file
if (file_exists($sql_file)) {
    echo "Importing database from <strong>$sql_file</strong>...<br>";
    
    // Read the SQL file
    $sql = file_get_contents($sql_file);
    
    // Execute multi-query (Warning: might fail if file is huge, but here it's 23KB)
    if (mysqli_multi_query($con, $sql)) {
        do {
            /* store first result set */
            if ($result = mysqli_store_result($con)) {
                mysqli_free_result($result);
            }
        } while (mysqli_more_results($con) && mysqli_next_result($con));
        
        echo "<h2 style='color:green;'>✅ Setup Completed Successfully!</h2>";
        echo "<p>All 21 products and categories have been imported.</p>";
        echo "<a href='index.php' style='padding:10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Click Here to View Project</a>";
    } else {
        echo "<span style='color:red;'>Error during import: " . mysqli_error($con) . "</span>";
    }
} else {
    echo "<h2 style='color:red;'>❌ Setup Failed: SQL file '$sql_file' not found in htdocs.</h2>";
}

mysqli_close($con);
?>
