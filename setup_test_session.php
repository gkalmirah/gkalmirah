<?php
session_start();
require_once 'include/dbcon.php';

// Test user: Surbhi (from SQL dump)
$email = 'surbhi@gmail.com';
$query = "SELECT * FROM customer WHERE cust_email='$email'";
$run = mysqli_query($con, $query);

if (mysqli_num_rows($run) > 0) {
    $row = mysqli_fetch_array($run);
    $_SESSION['id'] = $row['cust_id'];
    $_SESSION['name'] = $row['cust_name'];
    $_SESSION['email'] = $row['cust_email'];
    $_SESSION['add'] = $row['cust_add'];
    $_SESSION['city'] = $row['cust_city'];
    $_SESSION['pcode'] = $row['cust_postalcode'];
    $_SESSION['number'] = $row['cust_number'];

    // Ensure there's at least one item in the cart
    $cust_id = $_SESSION['id'];
    $check_cart = mysqli_query($con, "SELECT * FROM cart WHERE cust_id = $cust_id");
    if (mysqli_num_rows($check_cart) == 0) {
        $product_query = mysqli_query($con, "SELECT product_id FROM furniture_product LIMIT 1");
        if ($product_row = mysqli_fetch_array($product_query)) {
            $pid = $product_row['product_id'];
            mysqli_query($con, "INSERT INTO cart (cust_id, product_id, quantity) VALUES ($cust_id, $pid, 1)");
        }
    }

    echo "Session created for " . $_SESSION['name'] . ". <a href='checkout.php'>Go to Checkout</a>";
} else {
    echo "Test user not found.";
}
?>