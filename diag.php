<?php
mysqli_report(MYSQLI_REPORT_OFF);
$hosts = ['127.0.0.1', 'localhost', '[::1]'];
$ports = [3306, 3307];
$user = 'root';
$pass = '';

echo "DB Connectivity Test\n";
echo "====================\n";

foreach ($hosts as $host) {
    foreach ($ports as $port) {
        echo "Testing $host:$port... ";
        $conn = @mysqli_connect($host, $user, $pass, '', $port);
        if ($conn) {
            echo "SUCCESS\n";
            $res = mysqli_query($conn, "SHOW DATABASES");
            while ($row = mysqli_fetch_row($res)) {
                echo "  - {$row[0]}\n";
            }
            mysqli_close($conn);
        } else {
            echo "FAILED: " . mysqli_connect_error() . " (Err: " . mysqli_connect_errno() . ")\n";
        }
    }
}
?>
