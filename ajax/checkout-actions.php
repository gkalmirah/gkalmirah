<?php
session_start();
require_once('../include/dbcon.php');
require_once('../include/discount_logic.php');

$response = ['status' => 'error', 'message' => 'Invalid request'];

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}
$cust_id = $_SESSION['id'];

if (isset($_POST['action'])) {
    
    // SAVE ADDRESS
    if ($_POST['action'] == 'save_address') {
        $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
        $mobile = mysqli_real_escape_string($con, $_POST['mobile']);
        $alt_mobile = mysqli_real_escape_string($con, $_POST['alt_mobile']);
        $house = mysqli_real_escape_string($con, $_POST['house_no']);
        $street = mysqli_real_escape_string($con, $_POST['street']);
        $landmark = mysqli_real_escape_string($con, $_POST['landmark']);
        $city = mysqli_real_escape_string($con, $_POST['city']);
        $state = mysqli_real_escape_string($con, $_POST['state']);
        $pincode = mysqli_real_escape_string($con, $_POST['pincode']);
        
        $query = "INSERT INTO customer_addresses (customer_id, full_name, mobile_number, alt_mobile, house_no, street_address, landmark, city, state, pincode, is_default) 
                  VALUES ($cust_id, '$fullname', '$mobile', '$alt_mobile', '$house', '$street', '$landmark', '$city', '$state', '$pincode', 1)";
                  
        // make other addresses not default
        mysqli_query($con, "UPDATE customer_addresses SET is_default = 0 WHERE customer_id = $cust_id");
        
        if (mysqli_query($con, $query)) {
            $insert_id = mysqli_insert_id($con);
            $_SESSION['checkout_address_id'] = $insert_id;
            $response = ['status' => 'success', 'message' => 'Address saved successfully', 'address_id' => $insert_id];
        } else {
            $response['message'] = 'Database error saving address.';
        }
    }
    
    // APPLY COUPON
    else if ($_POST['action'] == 'apply_coupon') {
        $code = strtoupper(mysqli_real_escape_string($con, trim($_POST['code'])));
        $total_amount = floatval($_POST['cart_total']); // Passed from frontend just for min_order check, backend verifies on place_order
        
        $q = "SELECT * FROM promo_codes WHERE code = '$code' AND is_active = 1 AND expiry_date >= CURDATE()";
        $run = mysqli_query($con, $q);
        
        if ($run && mysqli_num_rows($run) > 0) {
            $promo = mysqli_fetch_assoc($run);
            if ($promo['usage_limit'] !== null && intval($promo['used_count']) >= intval($promo['usage_limit'])) {
                $response['message'] = 'This coupon usage limit has been reached.';
            } else if ($total_amount >= floatval($promo['min_order'])) {
                $_SESSION['checkout_coupon'] = $promo;
                $response = ['status' => 'success', 'message' => 'Coupon applied successfully!'];
            } else {
                $response['message'] = "Minimum order amount for this coupon is ₹" . floatval($promo['min_order']);
            }
        } else {
            $response['message'] = 'Invalid or expired coupon code.';
        }
    }
    
    // REMOVE COUPON
    else if ($_POST['action'] == 'remove_coupon') {
        unset($_SESSION['checkout_coupon']);
        $response = ['status' => 'success', 'message' => 'Coupon removed.'];
    }
    
    // GET SUMMARY
    else if ($_POST['action'] == 'get_summary') {
        // Fetch cart/buynow items
        $sub_total = 0;
        $total_discount = 0;
        $tax_rate = 0.00;
        $tax_q = mysqli_query($con, "SELECT tax_percent FROM tax_settings WHERE is_active = 1 LIMIT 1");
        if ($tax_q && mysqli_num_rows($tax_q) > 0) {
            $tax_row = mysqli_fetch_assoc($tax_q);
            $tax_rate = floatval($tax_row['tax_percent']) / 100.00;
        }
        $is_buy_now = isset($_SESSION['checkout_mode']) && $_SESSION['checkout_mode'] == 'buynow';
        
        if ($is_buy_now && isset($_SESSION['buy_now_product_id'])) {
            $pid = intval($_SESSION['buy_now_product_id']);
            $qty = intval($_SESSION['buy_now_qty']);
            $pr_run = mysqli_query($con, "SELECT * FROM furniture_product WHERE product_id=$pid");
            if(mysqli_num_rows($pr_run) > 0) {
                $p = mysqli_fetch_assoc($pr_run);
                $raw_price = floatval(preg_replace('/[^0-9.]/', '', explode('-', $p['product_price'])[0]));
                $disc = get_active_discount($pid, $raw_price, $con);
                
                $item_total = $raw_price * $qty;
                $sub_total += $item_total;
                if($disc['has_discount']) {
                    $total_discount += ($raw_price - $disc['discounted_price']) * $qty;
                }
            }
        } else {
            $cart_q = "SELECT c.quantity, p.* FROM cart c JOIN furniture_product p ON c.product_id = p.product_id WHERE c.cust_id = $cust_id";
            $cart_run = mysqli_query($con, $cart_q);
            if(mysqli_num_rows($cart_run) > 0) {
                while($c = mysqli_fetch_assoc($cart_run)) {
                    $qty = $c['quantity'];
                    $raw_price = floatval(preg_replace('/[^0-9.]/', '', explode('-', $c['product_price'])[0]));
                    $disc = get_active_discount($c['product_id'], $raw_price, $con);
                    
                    $item_total = $raw_price * $qty;
                    $sub_total += $item_total;
                    if($disc['has_discount']) {
                        $total_discount += ($raw_price - $disc['discounted_price']) * $qty;
                    }
                }
            }
        }
        
        // Shipping
        $shipping = 0;
        if(isset($_POST['delivery_method_id'])) {
            $did = intval($_POST['delivery_method_id']);
            $d_run = mysqli_query($con, "SELECT charge FROM delivery_methods WHERE id=$did");
            if($d_run && mysqli_num_rows($d_run)>0) {
                $shipping = floatval(mysqli_fetch_assoc($d_run)['charge']);
            }
        }
        
        // Coupon
        $coupon_discount = 0;
        $payable_subtotal = $sub_total - $total_discount;
        
        if(isset($_SESSION['checkout_coupon'])) {
            $promo = $_SESSION['checkout_coupon'];
            if($payable_subtotal >= $promo['min_order']) {
                if($promo['discount_type'] == 'percent') {
                    $coupon_discount = ($payable_subtotal * floatval($promo['discount_value'])) / 100;
                } else {
                    $coupon_discount = floatval($promo['discount_value']);
                }
            } else {
                unset($_SESSION['checkout_coupon']); // invalidate if drops below min order
            }
        }
        
        $payable_subtotal -= $coupon_discount;
        $tax = $payable_subtotal * $tax_rate;
        $grand_total = $payable_subtotal + $tax + $shipping;
        
        $response = [
            'status' => 'success',
            'sub_total' => $sub_total,
            'total_discount' => $total_discount,
            'coupon_discount' => $coupon_discount,
            'shipping' => $shipping,
            'tax' => $tax,
            'grand_total' => $grand_total,
            'coupon_active' => isset($_SESSION['checkout_coupon'])
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
