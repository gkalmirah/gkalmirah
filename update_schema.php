<?php
include('include/dbcon.php');

$query = "ALTER TABLE customer_order ADD COLUMN upi_provider VARCHAR(50) DEFAULT NULL AFTER payment_method";
if (mysqli_query($con, $query)) {
    echo "Successfully added upi_provider column.\n";
} else {
    echo "Error adding column: " . mysqli_error($con) . "\n";
}
?>
