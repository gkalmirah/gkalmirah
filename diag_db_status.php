<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'furnitureshopmain';

$ports = [3307, 3306];
$con = null;

foreach ($ports as $port) {
    echo "Testing port $port... ";
    $con = @mysqli_connect($host, $user, $pass, '', $port);
    if ($con) {
        echo "SUCCESS\n";
        break;
    }
    echo "FAILED\n";
}

if (!$con) {
    die("Could not connect to MySQL on any port.");
}

$db_check = mysqli_query($con, "SHOW DATABASES LIKE '$dbname'");
if (mysqli_num_rows($db_check) > 0) {
    echo "Database '$dbname' exists.\n";
    mysqli_select_db($con, $dbname);
    $table_check = mysqli_query($con, "SHOW TABLES");
    echo "Tables in '$dbname':\n";
    while ($row = mysqli_fetch_array($table_check)) {
        echo "- " . $row[0] . "\n";
    }
} else {
    echo "Database '$dbname' does NOT exist.\n";
}

mysqli_close($con);
?>
