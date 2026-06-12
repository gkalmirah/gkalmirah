<?php
include 'include/dbcon.php';
$res = mysqli_query($con, 'SELECT COUNT(*) as total FROM furniture_product');
$row = mysqli_fetch_assoc($res);
echo "TOTAL_ROWS:" . $row['total'];
?>
