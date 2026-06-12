<?php
include 'include/dbcon.php';

$queries = [
    "ALTER TABLE furniture_product ADD COLUMN product_short_desc TEXT AFTER product_subtitle",
    "ALTER TABLE furniture_product ADD COLUMN product_tax_inc VARCHAR(50) DEFAULT 'Included' AFTER product_mrp",
    "ALTER TABLE furniture_product ADD COLUMN product_360 TEXT AFTER product_img6"
];

foreach ($queries as $query) {
    if (mysqli_query($con, $query)) {
        echo "Success: $query\n";
    } else {
        echo "Error: " . mysqli_error($con) . "\n";
    }
}
?>
