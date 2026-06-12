<?php
include 'include/dbcon.php';
$res = mysqli_query($con, "DESCRIBE furniture_product");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
