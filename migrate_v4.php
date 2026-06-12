<?php
include 'include/dbcon.php';

function columnExists($con, $table, $column) {
    $res = mysqli_query($con, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    return mysqli_num_rows($res) > 0;
}

$columns = [
    'product_short_desc' => 'TEXT AFTER product_subtitle',
    'product_tax_inc' => "VARCHAR(50) DEFAULT 'Included' AFTER product_mrp",
    'product_360' => 'TEXT AFTER product_img6'
];

foreach ($columns as $col => $def) {
    if (!columnExists($con, 'furniture_product', $col)) {
        $q = "ALTER TABLE furniture_product ADD COLUMN `$col` $def";
        if (mysqli_query($con, $q)) {
            echo "Added $col\n";
        } else {
            echo "Error adding $col: " . mysqli_error($con) . "\n";
        }
    } else {
        echo "$col already exists\n";
    }
}
?>
