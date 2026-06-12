<?php
include('include/dbcon.php');
$q = mysqli_query($con, "SELECT product_id, product_name, product_avail FROM furniture_product LIMIT 10");
while($r = mysqli_fetch_assoc($q)) {
    echo "ID: {$r['product_id']} | Avail: " . var_export($r['product_avail'], true) . "\n";
}
?>
