<?php
include('include/dbcon.php');
$res = mysqli_query($con, "DESC furniture_product");
while($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}
?>
