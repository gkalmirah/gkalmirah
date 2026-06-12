<?php
// checkout_action.php
ob_start();
session_start();
require_once('include/dbcon.php');
require_once('include/discount_logic.php');
require_once('include/notification_service.php');

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit();
}

$action = $_GET['action'] ?? '';
$customer_id = $_SESSION['id'];
$customer_email = $_SESSION['email'];
$customer_name = $_SESSION['name'];

if ($action === 'confirm') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
        exit();
    }

    $address_id = intval($_POST['selected_address_id']);
    $delivery_id = intval($_POST['selected_delivery_id']);
    $payment_method = mysqli_real_escape_string($con, trim($_POST['selected_payment_method']));
    $upi_id = mysqli_real_escape_string($con, trim($_POST['upi_id'] ?? ''));
    $upi_provider = mysqli_real_escape_string($con, trim($_POST['upi_provider'] ?? ''));

    // Validate Address
    $add_q = mysqli_query($con, "SELECT * FROM customer_addresses WHERE id = $address_id AND customer_id = $customer_id");
    if (mysqli_num_rows($add_q) == 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid address selected.']);
        exit();
    }
    $add_row = mysqli_fetch_assoc($add_q);
    $fullname = $add_row['full_name'];
    $address = $add_row['house_no'] . ", " . $add_row['street_address'] . ($add_row['landmark'] ? ", " . $add_row['landmark'] : "");
    $city = $add_row['city'];
    $code = $add_row['pincode'];
    $number = $add_row['mobile_number'];

    // Validate Delivery
    $shipping_cost = 0;
    if ($delivery_id > 0) {
        $del_q = mysqli_query($con, "SELECT * FROM delivery_methods WHERE id = $delivery_id");
        if ($d_row = mysqli_fetch_assoc($del_q)) {
            $shipping_cost = floatval($d_row['charge']);
        }
    }

    if ($payment_method === 'UPI') {
        if (empty($upi_provider)) {
            echo json_encode(['success' => false, 'message' => 'Please select a UPI provider.']);
            exit();
        }
        if (!preg_match('/^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/', $upi_id)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid UPI ID (e.g., name@bank).']);
            exit();
        }
    }

    // Get Items
    $checkout_items = [];
    $is_buy_now = isset($_SESSION['checkout_mode']) && $_SESSION['checkout_mode'] == 'buynow';
    if ($is_buy_now && isset($_SESSION['buy_now_product_id'])) {
        $pid = intval($_SESSION['buy_now_product_id']);
        $pqty = isset($_SESSION['buy_now_qty']) ? intval($_SESSION['buy_now_qty']) : 1;
        $pr_query = "SELECT * FROM furniture_product WHERE product_id=$pid";
        $pr_run = mysqli_query($con, $pr_query);
        if (mysqli_num_rows($pr_run) > 0) {
            $checkout_items[] = ['product' => mysqli_fetch_array($pr_run), 'quantity' => $pqty];
        }
    } else {
        $cart = "SELECT * FROM cart WHERE cust_id='$customer_id'";
        $run = mysqli_query($con, $cart);
        if (mysqli_num_rows($run) > 0) {
            while ($cart_row = mysqli_fetch_array($run)) {
                $db_pro_id = $cart_row['product_id'];
                $db_pro_qty = $cart_row['quantity'];
                $pr_query = "SELECT * FROM furniture_product WHERE product_id=$db_pro_id";
                $pr_run = mysqli_query($con, $pr_query);
                if (mysqli_num_rows($pr_run) > 0) {
                    $checkout_items[] = ['product' => mysqli_fetch_array($pr_run), 'quantity' => $db_pro_qty];
                }
            }
        }
    }

    if (empty($checkout_items)) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
        exit();
    }
    
    // Clean up any stale pending invoices for this session to prevent duplicate deductions
    if (isset($_SESSION['confirmed_invoice'])) {
        $old_inv = mysqli_real_escape_string($con, $_SESSION['confirmed_invoice']);
        mysqli_query($con, "DELETE FROM customer_order WHERE invoice_no = '$old_inv' AND order_status = 'draft' AND customer_id = $customer_id");
    }

    mysqli_begin_transaction($con);
    try {
        $invoice = mt_rand(100000, 999999);
        $date = date("Y-m-d H:i:s");
        
        $tax_rate = 0.00;
        $tax_q = mysqli_query($con, "SELECT tax_percent FROM tax_settings WHERE is_active = 1 LIMIT 1");
        if ($tax_q && mysqli_num_rows($tax_q) > 0) {
            $tax_row = mysqli_fetch_assoc($tax_q);
            $tax_rate = floatval($tax_row['tax_percent']) / 100.00;
        }

        $base_sub_total = 0;
        foreach ($checkout_items as $item) {
            $pr_row = $item['product'];
            $qty = intval($item['quantity']);
            $db_pro_id = $pr_row['product_id'];
            
            // VERIFY STOCK AVAILABILITY ONLY - DO NOT DEDUCT YET
            // Deduction only happens on final placement to prevent false empty stock issues
            $stock_query = mysqli_query($con, "SELECT product_avail FROM furniture_product WHERE product_id = $db_pro_id");
            $stock_row = mysqli_fetch_assoc($stock_query);
            
            // Check if stock tracking is enabled (we treat > 0 as actively tracked. <= 0 means untracked/unlimited)
            $is_stock_tracked = ($stock_row['product_avail'] !== null && $stock_row['product_avail'] !== '' && intval($stock_row['product_avail']) > 0);
            
            if ($is_stock_tracked) {
                if(intval($stock_row['product_avail']) < $qty) {
                    throw new Exception("Insufficient stock for {$pr_row['product_name']}");
                }
            }
            
            $raw_db_price = strval($pr_row['product_price']);
            $parts = explode('-', $raw_db_price);
            $price = floatval(preg_replace('/[^0-9.]/', '', trim($parts[0])));
            $discount = get_active_discount($db_pro_id, $price, $con);
            if ($discount['has_discount']) {
                $price = $discount['discounted_price'];
            }
            $base_sub_total += ($price * $qty);
        }
        
        $discount_amount = 0;
        $promo_code_applied = null;
        if (isset($_SESSION['checkout_coupon'])) {
            $promo = $_SESSION['checkout_coupon'];
            if ($base_sub_total >= $promo['min_order']) {
                if ($promo['discount_type'] == 'percent') {
                    $discount_amount = $base_sub_total * (floatval($promo['discount_value']) / 100);
                } else {
                    $discount_amount = floatval($promo['discount_value']);
                }
                if ($discount_amount > $base_sub_total) $discount_amount = $base_sub_total;
                $promo_code_applied = $promo['code'];
            }
        }
        
        $is_first_row = true;
        foreach ($checkout_items as $item) {
            $pr_row = $item['product'];
            $qty = intval($item['quantity']);
            $db_pro_id = $pr_row['product_id'];

            $raw_db_price = strval($pr_row['product_price']);
            $parts = explode('-', $raw_db_price);
            $price = floatval(preg_replace('/[^0-9.]/', '', trim($parts[0])));
            $discount = get_active_discount($db_pro_id, $price, $con);
            if ($discount['has_discount']) {
                $price = $discount['discounted_price'];
            }
            
            $item_total = $price * $qty;
            $item_tax = $item_total * $tax_rate;
            $line_amount = $item_total + $item_tax;

            $promo_val = $is_first_row && $promo_code_applied ? "'$promo_code_applied'" : "NULL";
            $disc_val = $is_first_row ? $discount_amount : 0;
            $upi_val = ($payment_method === 'UPI' && $is_first_row) ? "'$upi_id'" : "NULL";
            $upi_prov_val = ($payment_method === 'UPI' && $is_first_row) ? "'$upi_provider'" : "NULL";
            $delivery_val = $delivery_id > 0 ? $delivery_id : "NULL";
            $shipping_val = $is_first_row ? $shipping_cost : 0.00;

            $sql = "INSERT INTO `customer_order`
                (`customer_id`, `customer_email`, `customer_fullname`, `customer_address`, `customer_city`, `customer_pcode`, `customer_phonenumber`,
                `product_id`, `product_amount`, `invoice_no`, `products_qty`, `order_date`, `order_status`, `payment_method`, `upi_provider`, `promo_code`, `discount_amount`, `upi_id`, `delivery_method_id`, `tax_amount`, `shipping_amount`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft', ?, {$upi_prov_val}, {$promo_val}, {$disc_val}, {$upi_val}, {$delivery_val}, {$item_tax}, {$shipping_val})";
                
            $stmt = mysqli_prepare($con, $sql);
            if (!$stmt) {
                throw new Exception("SQL Prepare Error: " . mysqli_error($con));
            }
            
            mysqli_stmt_bind_param($stmt, "isssssssidiss", 
                $customer_id, $customer_email, $fullname, $address, $city, $code, $number, 
                $db_pro_id, $line_amount, $invoice, $qty, $date, $payment_method
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to insert order data.");
            }

            // NOTE: We no longer deduct stock here in step 1. Stock is deducted ONLY in step 2.
            $is_first_row = false;
        }

        if ($shipping_cost > 0) {
            mysqli_query($con, "UPDATE `customer_order` SET `product_amount` = `product_amount` + $shipping_cost WHERE `invoice_no` = '$invoice' LIMIT 1");
        }

        mysqli_commit($con);
        
        // Save the confirmed invoice to session
        $_SESSION['confirmed_invoice'] = $invoice;
        
        echo json_encode(['success' => true, 'message' => 'Order details confirmed.']);
    } catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

} elseif ($action === 'place_order') {
    
    if (!isset($_SESSION['confirmed_invoice'])) {
        echo json_encode(['success' => false, 'message' => 'No confirmed order found.']);
        exit();
    }
    
    $invoice = $_SESSION['confirmed_invoice'];
    
    // Check if order exists and is pending
    $chk_q = mysqli_query($con, "SELECT * FROM customer_order WHERE invoice_no = '$invoice' AND customer_id = $customer_id");
    if (mysqli_num_rows($chk_q) == 0) {
        echo json_encode(['success' => false, 'message' => 'Order not found.']);
        exit();
    }
    
    $first_row = mysqli_fetch_assoc($chk_q);
    if ($first_row['order_status'] !== 'draft') {
        echo json_encode(['success' => false, 'message' => 'Order is already placed.']);
        exit();
    }

    mysqli_begin_transaction($con);
    try {
        // Step 1: Securely deduct stock right before order placement to avoid duplicate deduction bugs
        mysqli_data_seek($chk_q, 0);
        while($r = mysqli_fetch_assoc($chk_q)) {
            $pid = intval($r['product_id']);
            $pqty = intval($r['products_qty']);
            
            $stock_q = mysqli_query($con, "SELECT product_avail, product_name FROM furniture_product WHERE product_id = $pid FOR UPDATE");
            $stock_r = mysqli_fetch_assoc($stock_q);
            
            // Check if stock tracking is enabled (we treat > 0 as actively tracked. <= 0 means untracked/unlimited)
            $is_stock_tracked = ($stock_r['product_avail'] !== null && $stock_r['product_avail'] !== '' && intval($stock_r['product_avail']) > 0);
            if ($is_stock_tracked) {
                if(intval($stock_r['product_avail']) < $pqty) {
                    throw new Exception("We're sorry! '{$stock_r['product_name']}' just ran out of stock.");
                }
                // Deduct stock now!
                mysqli_query($con, "UPDATE furniture_product SET product_avail = product_avail - $pqty WHERE product_id = $pid");
            }
        }
        
        // Step 2: Update status to pending
        mysqli_query($con, "UPDATE customer_order SET order_status = 'pending' WHERE invoice_no = '$invoice'");
        
        if (isset($_SESSION['checkout_coupon'])) {
            $promo_code = $_SESSION['checkout_coupon']['code'];
            mysqli_query($con, "UPDATE promo_codes SET used_count = used_count + 1 WHERE code='$promo_code'");
        }
        
        $is_buy_now = isset($_SESSION['checkout_mode']) && $_SESSION['checkout_mode'] == 'buynow';
        if (!$is_buy_now) {
            mysqli_query($con, "DELETE FROM cart WHERE cust_id = $customer_id");
        }

        mysqli_commit($con);
        
        // ── Send Notifications Asynchronously (sort of) ──
        $totalAmount = 0;
        $orderDetails = "<ul>";
        mysqli_data_seek($chk_q, 0);
        while($r = mysqli_fetch_assoc($chk_q)) {
            $totalAmount += floatval($r['product_amount']);
            $pid = $r['product_id'];
            $pq = mysqli_query($con, "SELECT product_name FROM furniture_product WHERE product_id = $pid");
            $p_name = ($pq && mysqli_num_rows($pq)>0) ? mysqli_fetch_assoc($pq)['product_name'] : "Product ID $pid";
            $orderDetails .= "<li>{$p_name} x {$r['products_qty']}</li>";
        }
        $orderDetails .= "</ul>";
        
        $estDelivery = date('D, M j, Y', strtotime($first_row['order_date'] . ' + 5 days'));
        
        // Fire and forget (will slow down the response slightly but guarantees delivery)
        @sendOrderConfirmationEmail(
            $first_row['customer_email'], 
            $first_row['customer_fullname'], 
            $invoice, 
            $orderDetails, 
            $totalAmount, 
            $first_row['payment_method'], 
            $first_row['customer_address'] . ', ' . $first_row['customer_city'], 
            $estDelivery
        );
        
        @sendAdminOrderEmail(
            $first_row['customer_fullname'],
            $first_row['customer_email'],
            $first_row['customer_phonenumber'],
            $invoice,
            $orderDetails,
            $totalAmount,
            $first_row['payment_method']
        );
        
        @sendWhatsAppNotification(
            $first_row['customer_phonenumber'],
            $first_row['customer_fullname'],
            $invoice,
            $totalAmount,
            $estDelivery
        );
        
        // Clean session completely but retain csrf_token so immediate future checkouts don't fail!
        unset($_SESSION['checkout_mode']);
        unset($_SESSION['buy_now_product_id']);
        unset($_SESSION['buy_now_qty']);
        unset($_SESSION['checkout_coupon']);
        unset($_SESSION['checkout_address_id']);
        // unset($_SESSION['csrf_token']); <== REMOVED TO ALLOW MULTIPLE CONSECUTIVE ORDERS
        unset($_SESSION['confirmed_invoice']);
        
        echo json_encode(['success' => true, 'redirect' => "order_success.php?invoice=$invoice"]);
    } catch (Exception $e) {
        mysqli_rollback($con);
        echo json_encode(['success' => false, 'message' => 'Failed to place order: ' . $e->getMessage()]);
    }
}
?>
