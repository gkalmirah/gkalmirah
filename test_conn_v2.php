<?php
mysqli_report(MYSQLI_REPORT_OFF);
$hosts = ['127.0.0.1', 'localhost'];
$ports = [3306, 3307];
$user = 'root';
$pass = '';
$db = 'furnitureshopmain';

foreach ($hosts as $host) {
    foreach ($ports as $port) {
        echo "Testing $host:$port... ";
        $con = @mysqli_connect($host, $user, $pass, $db, $port);
        if ($con) {
            echo "SUCCESS\n";
            mysqli_close($con);
        } else {
            echo "FAIL: " . mysqli_connect_error() . "\n";
        }
    }
}
?>
