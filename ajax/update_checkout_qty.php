<?php
session_start();
include('../include/dbcon.php');

$response = ['status' => 'error'];

if(isset($_POST['product_id']) && isset($_POST['qty'])) {
    $pid = intval($_POST['product_id']);
    $qty = intval($_POST['qty']);
    if($qty < 1) $qty = 1;
    if($qty > 10) $qty = 10;
    
    $is_buy_now = isset($_SESSION['checkout_mode']) && $_SESSION['checkout_mode'] == 'buynow';
    $customer_id = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
    
    if(!$is_buy_now && $customer_id == 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Not logged in']);
        exit;
    }
    
    // Update Quantity
    if($is_buy_now && isset($_SESSION['buy_now_product_id']) && $_SESSION['buy_now_product_id'] == $pid) {
        $_SESSION['buy_now_qty'] = $qty;
    } else if(!$is_buy_now && $customer_id > 0) {
        $update = "UPDATE cart SET quantity = $qty WHERE product_id = $pid AND cust_id = $customer_id";
        mysqli_query($con, $update);
    }
    
    // Recalculate Totals
    $base_sub_total = 0;
    
    $checkout_items = [];
    if($is_buy_now && isset($_SESSION['buy_now_product_id'])) {
        $pqty = isset($_SESSION['buy_now_qty']) ? intval($_SESSION['buy_now_qty']) : 1;
        $pr_query  = "SELECT * FROM furniture_product WHERE product_id=$pid";
        $pr_run    = mysqli_query($con,$pr_query);
        if(mysqli_num_rows($pr_run) > 0){
            $checkout_items[] = [
                'product' => mysqli_fetch_array($pr_run),
                'quantity' => $pqty
            ];
        }
    } else if(!$is_buy_now && $customer_id > 0) {
        $cart = "SELECT * FROM cart WHERE cust_id='$customer_id'";
        $run  = mysqli_query($con,$cart);
        if(mysqli_num_rows($run) > 0){
            while($cart_row = mysqli_fetch_array($run)){
                $db_pro_id  = $cart_row['product_id'];
                $db_pro_qty  = $cart_row['quantity'];
                $pr_query  = "SELECT * FROM furniture_product WHERE product_id=$db_pro_id";
                $pr_run    = mysqli_query($con,$pr_query);
                if(mysqli_num_rows($pr_run) > 0){
                    $checkout_items[] = [
                        'product' => mysqli_fetch_array($pr_run),
                        'quantity' => $db_pro_qty
                    ];
                }
            }
        }
    }
    
    foreach($checkout_items as $item) {
        $pr_row = $item['product'];
        $item_qty = intval($item['quantity']);
        $raw_price_str = strval($pr_row['product_price']);
        $parts = explode('-', $raw_price_str);
        $price = floatval(preg_replace('/[^0-9.]/', '', trim($parts[0])));
        
        $base_sub_total += $price * $item_qty;
    }
    
    $response = [
        'status' => 'success',
        'baseSubtotal' => $base_sub_total
    ];
}

echo json_encode($response);
?>
