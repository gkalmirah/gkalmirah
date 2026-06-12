<?php
include('include/dbcon.php');

$db_pro_id = 4;
$qty = 1;

$stock_query = mysqli_query($con, "SELECT product_avail, product_name FROM furniture_product WHERE product_id = $db_pro_id");
if (!$stock_query) {
    die("Query failed: " . mysqli_error($con));
}

$stock_row = mysqli_fetch_assoc($stock_query);

echo "Row fetched: " . print_r($stock_row, true) . "\n";

$is_stock_tracked = ($stock_row['product_avail'] !== null && $stock_row['product_avail'] !== '' && intval($stock_row['product_avail']) != -1);
echo "Is stock tracked? " . ($is_stock_tracked ? 'Yes' : 'No') . "\n";

if ($is_stock_tracked) {
    if (intval($stock_row['product_avail']) < $qty) {
        echo "Insufficient stock!\n";
    } else {
        echo "Stock is sufficient!\n";
    }
}
?>
