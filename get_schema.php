<?php
include('include/dbcon.php');
$q = mysqli_query($con, 'DESCRIBE customer_order;');
while($r = mysqli_fetch_assoc($q)) {
    print_r($r);
}
?>
