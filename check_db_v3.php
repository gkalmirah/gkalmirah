
<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$port = 3306;

echo "<h1>Checking MySQL/MariaDB Status</h1>";

$con = @mysqli_connect($host, $user, $pass, '', $port);

if (!$con) {
    die("<p style='color:red;'>Could not connect to MySQL on port $port: " . mysqli_connect_error() . "</p>");
}

echo "<p style='color:green;'>Connected to MySQL on port $port!</p>";

echo "<h2>Databases:</h2><ul>";
$result = mysqli_query($con, "SHOW DATABASES");
while ($row = mysqli_fetch_row($result)) {
    echo "<li>" . $row[0] . "</li>";
}
echo "</ul>";

$dbname = 'furnitureshopmain';
if (mysqli_select_db($con, $dbname)) {
    echo "<p style='color:green;'>Database '$dbname' exists.</p>";
    echo "<h2>Tables in '$dbname':</h2><ul>";
    $result = mysqli_query($con, "SHOW TABLES");
    while ($row = mysqli_fetch_row($result)) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:red;'>Database '$dbname' does NOT exist.</p>";
}

mysqli_close($con);
?>
