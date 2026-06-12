<?php
include 'include/dbcon.php';

$queries = [
    "ALTER TABLE furniture_product ADD COLUMN product_subtitle VARCHAR(255) DEFAULT '' AFTER product_name",
    "ALTER TABLE furniture_product ADD COLUMN product_mrp DECIMAL(10,2) DEFAULT 0 AFTER product_price",
    "ALTER TABLE warranty_activations ADD COLUMN invoice_file VARCHAR(255) DEFAULT '' AFTER serial_number",
    "ALTER TABLE warranty_activations ADD COLUMN product_id INT(11) DEFAULT 0 AFTER id"
];

foreach ($queries as $query) {
    if (mysqli_query($con, $query)) {
        echo "Success: $query\n";
    } else {
        echo "Error: " . mysqli_error($con) . "\n";
    }
}
?>
