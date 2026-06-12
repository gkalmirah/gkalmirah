<?php
include('include/dbcon.php');
$res = mysqli_query($con, "DESCRIBE warranty_activations");
if (!$res) {
    echo "Error: " . mysqli_error($con);
} else {
    while($row = mysqli_fetch_assoc($res)) {
        echo $row['Field'] . "\n";
    }
}
?>
