<?php
mysqli_report(MYSQLI_REPORT_OFF);
$con = @mysqli_connect('127.0.0.1', 'root', '', '', 3306);
if ($con) {
    echo "SUCCESS_3306_ROOT_NO_PASS\n";
    mysqli_close($con);
} else {
    echo "FAIL_3306_ROOT_NO_PASS: " . mysqli_connect_error() . "\n";
}

$con = @mysqli_connect('localhost', 'root', '', '', 3306);
if ($con) {
    echo "SUCCESS_3306_ROOT_NO_PASS_LOCALHOST\n";
    mysqli_close($con);
} else {
    echo "FAIL_3306_ROOT_NO_PASS_LOCALHOST: " . mysqli_connect_error() . "\n";
}
?>