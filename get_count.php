<?php
include('include/dbcon.php');
$res = mysqli_query($con, "SELECT COUNT(*) FROM warranty_activations");
$row = mysqli_fetch_row($res);
echo "WARRANTY_COUNT:" . $row[0];
?>
