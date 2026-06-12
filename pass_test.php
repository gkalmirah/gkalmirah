<?php
mysqli_report(MYSQLI_REPORT_OFF);
$passwords = ['', 'root', 'admin', 'password', '123456'];
$host = '127.0.0.1';
$user = 'root';
$port = 3306;

echo "Password Test\n";
echo "=============\n";

foreach ($passwords as $pass) {
    echo "Testing with password: '" . ($pass ?: '(empty)') . "'... ";
    $conn = @mysqli_connect($host, $user, $pass, '', $port);
    if ($conn) {
        echo "SUCCESS!\n";
        mysqli_close($conn);
        break;
    } else {
        echo "FAILED\n";
    }
}
?>
