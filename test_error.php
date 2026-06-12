<?php
include('include/dbcon.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Let's get the latest invoice
$q = mysqli_query($con, "SELECT invoice_no, customer_id FROM customer_order ORDER BY order_id DESC LIMIT 1");
if(!$q || mysqli_num_rows($q) == 0) {
    die("No orders found");
}
$row = mysqli_fetch_assoc($q);
$invoice = $row['invoice_no'];
$customer_id = $row['customer_id'];

echo "Invoice: $invoice, Customer ID: $customer_id\n";

$query = "SELECT co.*, fp.product_name, fp.product_img1, fp.product_mrp, fp.product_model, c.cust_number as alt_number 
          FROM customer_order co 
          LEFT JOIN furniture_product fp ON co.product_id = fp.product_id 
          LEFT JOIN customer c ON co.customer_id = c.cust_id
          WHERE co.invoice_no = '$invoice' AND co.customer_id = $customer_id";

$run = mysqli_query($con, $query);

if(!$run) {
    echo "SQL ERROR: " . mysqli_error($con) . "\n";
} else {
    echo "Rows found: " . mysqli_num_rows($run) . "\n";
    if(mysqli_num_rows($run) > 0) {
        $first = mysqli_fetch_assoc($run);
        print_r($first);
    }
}
?>
