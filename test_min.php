<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = '127.0.0.1';
$user = 'root';
$pass = ''; // Try empty password first
$port = 3307;

echo "<h1>Minimal DB Test</h1>";
echo "Connecting to $host:$port as $user...<br>";

$con = mysqli_connect($host, $user, $pass, '', $port);

if ($con) {
    echo "<p style='color:green;'>SUCCESS: Connected!</p>";
    $res = mysqli_query($con, "SELECT VERSION()");
    $row = mysqli_fetch_row($res);
    echo "Server Version: " . $row[0] . "<br>";
    
    $res = mysqli_query($con, "SHOW DATABASES");
    echo "Databases:<br><ul>";
    while ($row = mysqli_fetch_row($res)) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";

    mysqli_select_db($con, 'furnitureshopmain');
    $res = mysqli_query($con, "SHOW TABLES");
    echo "Tables in 'furnitureshopmain':<br><ul>";
    while ($row = mysqli_fetch_row($res)) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:red;'>FAILED: " . mysqli_connect_error() . " (Error No: " . mysqli_connect_errno() . ")</p>";
}
?>
