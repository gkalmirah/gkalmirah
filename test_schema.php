<?php
include('include/dbcon.php');
$query = "DESCRIBE customer_order";
$run = mysqli_query($con, $query);
if($run) {
    while($row = mysqli_fetch_assoc($run)) {
        print_r($row);
    }
} else {
    echo mysqli_error($con);
}
?>
