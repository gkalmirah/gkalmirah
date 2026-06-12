<?php
include('include/dbcon.php');
$query = "SELECT co.*, fp.product_name, fp.product_img1, fp.image 
          FROM customer_order co 
          JOIN furniture_product fp ON co.product_id = fp.product_id 
          LIMIT 1";
$run = mysqli_query($con, $query);
if(!$run) {
    echo mysqli_error($con);
} else {
    echo "Query successful!";
}
?>
