<?php
include('include/dbcon.php');
$query = "ALTER TABLE customer_order ADD COLUMN payment_method VARCHAR(50) DEFAULT 'COD' AFTER order_status";
if(mysqli_query($con, $query)) {
    echo "payment_method added successfully.\n";
} else {
    echo "Error: " . mysqli_error($con) . "\n";
}
?>
