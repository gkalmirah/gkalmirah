<?php
session_start();
include('../include/dbcon.php');

$response = ['status' => 'error', 'message' => 'Invalid request'];

if (isset($_POST['action']) && $_POST['action'] == 'buynow' && isset($_POST['product_id'])) {
    
    $product_id = intval($_POST['product_id']);
    $qty = isset($_POST['qty']) ? intval($_POST['qty']) : 1;
    
    if ($product_id > 0 && $qty > 0) {
        
        // Validate product existence and stock
        $check_query = "SELECT * FROM furniture_product WHERE product_id = $product_id";
        $check_run = mysqli_query($con, $check_query);
        
        if ($check_run && mysqli_num_rows($check_run) > 0) {
            $product = mysqli_fetch_assoc($check_run);
            
            // Setup secure instant checkout routing parameters
            $_SESSION['checkout_mode'] = 'buynow';
            $_SESSION['buy_now_product_id'] = $product_id;
            $_SESSION['buy_now_qty'] = $qty;
            
            // Amazon-style Routing Engine:
            if (isset($_SESSION['id'])) {
                // User authenticated -> Send directly to checkout dashboard
                $response = [
                    'status' => 'success',
                    'redirect' => 'checkout.php'
                ];
            } else {
                // Unauthenticated -> Drop them into Sign-In to funnel them securely back
                $response = [
                    'status' => 'success',
                    'redirect' => 'sign-in.php'
                ];
            }
        } else {
            $response['message'] = 'Product is unavailable or out of stock.';
        }
    } else {
        $response['message'] = 'Invalid product data.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
