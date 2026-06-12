<?php
include('include/dbcon.php');
$res = mysqli_query($con, "DESC furniture_product");
echo "FIELDS:\n";
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . "\n";
}
?>
