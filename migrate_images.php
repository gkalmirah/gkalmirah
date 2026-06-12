<?php
include 'include/dbcon.php';
$queries = [
    "ALTER TABLE furniture_product ADD COLUMN `product_img4` VARCHAR(100) NOT NULL DEFAULT '' AFTER `product_img3` ",
    "ALTER TABLE furniture_product ADD COLUMN `product_img5` VARCHAR(100) NOT NULL DEFAULT '' AFTER `product_img4` ",
    "ALTER TABLE furniture_product ADD COLUMN `product_img6` VARCHAR(100) NOT NULL DEFAULT '' AFTER `product_img5` "
];

foreach ($queries as $q) {
    if(!mysqli_query($con, $q)) {
        echo "Error: " . mysqli_error($con) . "\n";
    } else {
        echo "Query Success: $q \n";
    }
}
?>
