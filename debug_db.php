<?php
mysqli_report(MYSQLI_REPORT_OFF);

function test_conn($host, $port, $user, $pass)
{
    echo "Testing $host:$port with user '$user'...\n";
    $conn = @mysqli_connect($host, $user, $pass, '', $port);
    if ($conn) {
        echo "Success on $host:$port\n";
        $res = mysqli_query($conn, "SHOW DATABASES");
        echo "Databases:\n";
        while ($row = mysqli_fetch_row($res)) {
            echo " - " . $row[0] . "\n";
        }
        mysqli_close($conn);
        return true;
    } else {
        echo "Failed on $host:$port: " . mysqli_connect_error() . "\n";
        return false;
    }
}

echo "--- CONNECTION TESTS ---\n";
test_conn('127.0.0.1', 3306, 'root', '');
test_conn('localhost', 3306, 'root', '');
test_conn('127.0.0.1', 3307, 'root', '');
test_conn('localhost', 3307, 'root', '');
?>