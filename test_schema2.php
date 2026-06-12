<?php
include('include/dbcon.php');
$tables = ['customer_order', 'customer_addresses', 'furniture_product', 'customer'];
foreach($tables as $t) {
    echo "TABLE: $t\n";
    $run = mysqli_query($con, "DESCRIBE $t");
    if($run) {
        while($row = mysqli_fetch_assoc($run)) {
            echo "{$row['Field']} ({$row['Type']})\n";
        }
    } else {
        echo mysqli_error($con) . "\n";
    }
    echo "---------------------------\n";
}
?>
