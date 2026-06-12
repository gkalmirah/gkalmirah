<?php 
mysqli_report(MYSQLI_REPORT_OFF);
$start = microtime(true);
$con = mysqli_connect('127.0.0.1', 'root', '', 'furnitureshopmain', 3306);
$time1 = microtime(true) - $start;
echo "Port 3306 connect time: " . round($time1 * 1000, 2) . " ms. Success: " . ($con ? "YES" : "NO") . "\n";

$start = microtime(true);
$con = mysqli_connect('127.0.0.1', 'root', '', 'furnitureshopmain', 3307);
$time2 = microtime(true) - $start;
echo "Port 3307 connect time: " . round($time2 * 1000, 2) . " ms. Success: " . ($con ? "YES" : "NO") . "\n";
?>
