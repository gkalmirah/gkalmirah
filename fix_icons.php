<?php
include('include/dbcon.php');

// Array of ID => Icon Class
$updates = [
    1 => 'fa-home',         // Household
    2 => 'fa-briefcase',    // Office
    4 => 'fa-lock',         // Locker
    5 => 'fa-star'          // Best Seller
];

foreach ($updates as $id => $icon) {
    $q = "UPDATE categories SET fontawesome_icon = '$icon' WHERE id = $id";
    if(mysqli_query($con, $q)){
        echo "Updated ID $id to $icon<br>";
    } else {
        echo "Error handling ID $id: " . mysqli_error($con) . "<br>";
    }
}
?>
