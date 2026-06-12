<?php
ob_start();
session_start();
$hide_nav = true;

if (!isset($_SESSION['id'])) {
    header('Location: sign-in.php');
    exit();
}

include('include/cust_header.php');
if (!isset($con)) {
    include('include/dbcon.php');
}
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}
require_once('include/discount_logic.php');

$tax_name = 'GST';
$tax_percent = 18.00;
$tax_q = mysqli_query($con, "SELECT tax_name, tax_percent FROM tax_settings WHERE is_active = 1 LIMIT 1");
if ($tax_q && mysqli_num_rows($tax_q) > 0) {
    $tax_row = mysqli_fetch_assoc($tax_q);
    $tax_name = $tax_row['tax_name'];
    $tax_percent = floatval($tax_row['tax_percent']);
}

// Fetch active payment methods
$pm_q = mysqli_query($con, "SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY sort_order ASC");
$active_methods = [];
while($pm = mysqli_fetch_assoc($pm_q)) {
    $active_methods[] = $pm;
}

$customer_id = $_SESSION['id'];
$customer_email = $_SESSION['email'];
$customer_name = $_SESSION['name'];

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
    header('Location: cart.php');
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error_msg = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Secure Premium Checkout - GK Almirah</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    :root {
        --primary: #D4AF37;
        --primary-dark: #b89626;
        --secondary: #f8fafc;
        --text-dark: #0f172a;
        --text-light: #64748b;
        --border: #e2e8f0;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
    }
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f1f5f9;
        color: var(--text-dark);
        margin: 0;
        padding: 0;
    }
    a { color: var(--primary); text-decoration: none; }
    a:hover { color: var(--primary-dark); }

    /* Progress Tracker */
    .progress-tracker {
        max-width: 1000px;
        margin: 110px auto 30px auto;
        display: flex;
        justify-content: space-between;
        position: relative;
        padding: 0 20px;
    }
    .progress-tracker::before {
        content: ''; position: absolute; top: 15px; left: 40px; right: 40px; height: 2px; background: var(--border); z-index: 1;
    }
    .step-indicator {
        position: relative; z-index: 2; text-align: center; width: 80px;
    }
    .step-icon {
        width: 32px; height: 32px; border-radius: 50%; background: #fff; border: 2px solid var(--border);
        display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: 600; color: var(--text-light);
        transition: 0.3s;
    }
    .step-indicator.active .step-icon { border-color: var(--primary); background: var(--primary); color: #fff; }
    .step-indicator.completed .step-icon { border-color: var(--success); background: var(--success); color: #fff; }
    .step-label { font-size: 12px; font-weight: 500; color: var(--text-light); }
    .step-indicator.active .step-label { color: var(--primary); }

    /* Layout */
    .chk-wrapper {
        max-width: 1200px;
        margin: 0 auto 40px;
        padding: 0 15px;
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        align-items: start;
    }

    .chk-main { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
    .chk-sidebar { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); position: sticky; top: 20px;}

    /* Sections */
    .step-section { margin-bottom: 30px; border-bottom: 1px solid var(--border); padding-bottom: 30px; transition: 0.3s; }
    .step-section:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0;}
    .step-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; cursor: pointer;}
    .step-header h3 { margin: 0; font-size: 18px; color: var(--text-dark); display: flex; align-items: center; gap: 12px;}
    .step-header h3 .num { background: var(--secondary); color: var(--text-dark); border-radius: 50%; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; border: 1px solid var(--border);}
    .step-body { display: none; padding-left: 40px; }
    .step-body.active { display: block; }
    
    .status-check { color: var(--success); font-size: 18px; display: none; }

    /* Cards */
    .address-card, .delivery-card { 
        border: 1px solid var(--border); padding: 20px; border-radius: 8px; margin-bottom: 15px; cursor: pointer; transition: 0.2s; background: #fff; position: relative;
    }
    .address-card:hover, .delivery-card:hover { border-color: #cbd5e1; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
    .address-card.selected, .delivery-card.selected { border: 2px solid var(--primary); background: #fdfaf0; }
    .card-badge { position: absolute; top: 15px; right: 15px; background: var(--primary); color: var(--text-dark); font-size: 11px; padding: 3px 8px; border-radius: 10px; font-weight: 600; }

    /* Payment Tabs */
    .payment-tabs { display: flex; border-bottom: 1px solid var(--border); margin-bottom: 20px; gap: 10px; overflow-x: auto;}
    .payment-tab { padding: 12px 20px; font-weight: 500; color: var(--text-light); cursor: pointer; border-bottom: 3px solid transparent; transition: 0.2s; white-space: nowrap;}
    .payment-tab:hover { background: #f1f5f9; }
    .payment-tab.active { background: #fff; border-bottom: 2px solid var(--primary); font-weight: 600; color: var(--primary); }
    .payment-pane { display: none; padding: 20px; }
    .payment-pane.active { display: block; }
    
    .upi-card { border: 1px solid var(--border); border-radius: 8px; padding: 15px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: all 0.3s; background: #fff; }
    .upi-card:hover { border-color: var(--primary); background: #fafafa; }
    .upi-card.active { border-color: var(--primary); background: #fefce8; box-shadow: 0 0 0 2px rgba(212,175,55,0.4); } /* Matching GK gold */
    .upi-card .check-icon { color: var(--primary); opacity: 0; font-size: 18px; transform: scale(0.5); transition: all 0.3s; }
    .upi-card.active .check-icon { opacity: 1; transform: scale(1); }

    /* Forms */
    .form-group { margin-bottom: 15px; }
    .form-control { width: 100%; padding: 12px 15px; border: 1px solid var(--border); border-radius: 6px; box-sizing: border-box; font-size: 14px; transition: 0.2s; font-family: 'Inter', sans-serif;}
    .form-control:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2); }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px;}

    /* Buttons */
    .btn-primary { background: var(--primary); color: var(--text-dark); border: none; padding: 14px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 15px; width: 100%; transition: 0.2s;}
    .btn-primary:hover { background: var(--primary-dark); }
    .btn-secondary { background: var(--secondary); color: var(--text-dark); border: 1px solid var(--border); padding: 14px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 15px; transition: 0.2s;}
    .btn-secondary:hover { background: #e2e8f0; }

    /* Summary */
    .summary-title { font-size: 18px; font-weight: 600; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border); color: var(--text-dark);}
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; color: var(--text-light);}
    .summary-row span:last-child { font-weight: 500; color: var(--text-dark); }
    .summary-row.discount span { color: var(--success); }
    .summary-total { display: flex; justify-content: space-between; font-size: 20px; font-weight: 700; color: var(--text-dark); border-top: 1px solid var(--border); padding-top: 20px; margin-top: 10px;}
    .summary-total span:last-child { color: var(--primary); }
    
    .trust-badges { margin-top: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .trust-badge { display: flex; align-items: center; gap: 10px; font-size: 12px; color: var(--text-dark); font-weight: 500; }
    .trust-badge i { font-size: 20px; color: var(--primary); }

    /* Animations */
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

    /* Mobile */
    @media (max-width: 768px) {
        .chk-wrapper { grid-template-columns: 1fr; }
        .progress-tracker { display: none; }
        .step-body { padding-left: 0; }
        .grid-2 { grid-template-columns: 1fr; }
        .chk-sidebar { order: -1; margin-bottom: 20px; position: static;}
    }
</style>
</head>
<body>



<div class="progress-tracker">
    <div class="step-indicator active" id="pt-1">
        <div class="step-icon"><i class="fas fa-map-marker-alt"></i></div>
        <div class="step-label">Address</div>
    </div>
    <div class="step-indicator" id="pt-2">
        <div class="step-icon"><i class="fas fa-truck"></i></div>
        <div class="step-label">Delivery</div>
    </div>
    <div class="step-indicator" id="pt-3">
        <div class="step-icon"><i class="fas fa-credit-card"></i></div>
        <div class="step-label">Payment</div>
    </div>
    <div class="step-indicator" id="pt-4">
        <div class="step-icon"><i class="fas fa-check-circle"></i></div>
        <div class="step-label">Review</div>
    </div>
</div>

<div class="chk-wrapper">
    <div class="chk-main">
        <?php if($error_msg): ?>
            <div style="background: #fef2f2; border: 1px solid #fca5a5; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #dc2626; font-weight:500;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <!-- STEP 1: ADDRESS -->
        <div class="step-section">
            <div class="step-header" onclick="openStep(1)">
                <h3><span class="num">1</span> Delivery Address</h3>
                <span class="status-check" id="step1-status"><i class="fas fa-check-circle"></i></span>
            </div>
            <div class="step-body active" id="step1">
                <div id="addressList">
                    <?php
                    $add_q = mysqli_query($con, "SELECT * FROM customer_addresses WHERE customer_id = $customer_id ORDER BY is_default DESC, id DESC");
                    if(mysqli_num_rows($add_q) > 0) {
                        while($a = mysqli_fetch_assoc($add_q)) {
                            $sel = $a['is_default'] ? 'selected' : '';
                            echo "<div class='address-card $sel' onclick='selectAddress({$a['id']}, this)'>";
                            if($a['is_default']) echo "<div class='card-badge'>Default</div>";
                            echo "<strong style='font-size:16px;'>{$a['full_name']}</strong> &bull; {$a['mobile_number']}<br>";
                            echo "<span style='color:var(--text-light); font-size:14px; display:block; margin-top:5px;'>{$a['house_no']}, {$a['street_address']}, {$a['city']}, {$a['state']} - {$a['pincode']}</span>";
                            echo "</div>";
                        }
                    }
                    ?>
                </div>
                <button class="btn-secondary mt-2" onclick="$('#newAddressForm').slideToggle();" style="width: auto;"><i class="fas fa-plus"></i> Add New Address</button>
                
                <div id="newAddressForm" style="display:none; margin-top:25px; border:1px solid var(--border); padding:25px; border-radius:8px; background:var(--secondary);">
                    <h4 style="margin-top:0; font-size:16px; margin-bottom:20px;">Enter New Delivery Address</h4>
                    <form id="addrForm" onsubmit="saveAddress(event)">
                        <div class="grid-2">
                            <div class="form-group"><input type="text" name="fullname" class="form-control" placeholder="Full Name" required></div>
                            <div class="form-group"><input type="text" name="mobile" class="form-control" placeholder="Mobile Number (10 digits)" pattern="[0-9]{10}" required></div>
                        </div>
                        <div class="grid-2">
                            <div class="form-group"><input type="text" name="pincode" id="pincode" class="form-control" placeholder="6-digit Pincode" pattern="[0-9]{6}" onkeyup="checkPincode()" required></div>
                            <div class="form-group"><input type="text" name="alt_mobile" class="form-control" placeholder="Alternate Mobile (Optional)"></div>
                        </div>
                        <div class="form-group"><input type="text" name="house_no" class="form-control" placeholder="House/Flat No, Building Name" required></div>
                        <div class="form-group"><input type="text" name="street" class="form-control" placeholder="Street Address, Area, Sector" required></div>
                        <div class="form-group"><input type="text" name="landmark" class="form-control" placeholder="Landmark (Optional)"></div>
                        <div class="grid-2">
                            <div class="form-group"><input type="text" name="city" id="city" class="form-control" placeholder="Town/City" required></div>
                            <div class="form-group"><input type="text" name="state" id="state" class="form-control" placeholder="State" required></div>
                        </div>
                        <button type="submit" class="btn-primary">Save Address & Continue</button>
                    </form>
                </div>
                
                <div class="mt-4" id="step1-continue" style="<?php echo (mysqli_num_rows($add_q) > 0) ? 'display:block;' : 'display:none;'; ?>">
                    <button class="btn-primary" onclick="completeStep(1)">Deliver to this address</button>
                </div>
            </div>
        </div>

        <!-- STEP 2: DELIVERY METHOD -->
        <div class="step-section">
            <div class="step-header" onclick="openStep(2)">
                <h3><span class="num">2</span> Delivery Method</h3>
                <span class="status-check" id="step2-status"><i class="fas fa-check-circle"></i></span>
            </div>
            <div class="step-body" id="step2">
                <div id="deliveryAvailability" style="margin-bottom:15px; color:var(--success); font-weight:500; font-size:14px; display:none;">
                    <i class="fas fa-check-circle"></i> Delivery Available to selected pincode.
                </div>
                <div id="deliveryList">
                    <?php
                    $d_q = mysqli_query($con, "SELECT * FROM delivery_methods WHERE status = 'Active' ORDER BY charge ASC");
                    $first_d = true;
                    while($d = mysqli_fetch_assoc($d_q)) {
                        $sel = $first_d ? 'selected' : '';
                        $charge_txt = $d['charge'] > 0 ? "₹" . number_format($d['charge'], 2) : "FREE";
                        $est_days = htmlspecialchars($d['estimated_days']);
                        
                        echo "<div class='delivery-card $sel' onclick='selectDelivery({$d['id']}, this)'>";
                        echo "<div style='display:flex; justify-content:space-between; align-items:center;'>";
                        echo "<strong style='font-size:16px;'>{$d['name']}</strong>";
                        echo "<strong style='color:var(--success);'>$charge_txt</strong>";
                        echo "</div>";
                        echo "<span style='color:var(--text-light); font-size:14px; display:block; margin-top:5px;'>Estimated Delivery: <b style='color:var(--text-dark);'>$est_days</b></span>";
                        echo "</div>";
                        $first_d = false;
                    }
                    ?>
                </div>
                <button class="btn-primary mt-3" onclick="completeStep(2)">Continue to Payment</button>
            </div>
        </div>

        <!-- STEP 3: PAYMENT METHOD -->
        <div class="step-section">
            <div class="step-header" onclick="openStep(3)">
                <h3><span class="num">3</span> Payment Method</h3>
                <span class="status-check" id="step3-status"><i class="fas fa-check-circle"></i></span>
            </div>
            <div class="step-body" id="step3">
                <div class="payment-tabs">
                    <?php
                    $first_pm = true;
                    foreach($active_methods as $pm) {
                        $pm_active_class = $first_pm ? 'active' : '';
                        $pm_icon = $pm['icon'] ?: 'fa-credit-card';
                        echo "<div class='payment-tab $pm_active_class' onclick=\"switchTab('{$pm['method_key']}')\"><i class='fas {$pm_icon}'></i> {$pm['method_name']}</div>";
                        $first_pm = false;
                    }
                    ?>
                </div>

                <?php 
                $active_keys = array_column($active_methods, 'method_key');
                $default_key = isset($active_keys[0]) ? $active_keys[0] : '';
                ?>

                <!-- UPI Pane -->
                <?php if (in_array('upi', $active_keys)): ?>
                <div id="pane-upi" class="payment-pane <?php echo ($default_key === 'upi') ? 'active' : ''; ?>">
                    <p style="font-size:14px; color:var(--text-light); margin-bottom:15px;">Select your preferred UPI provider.</p>
                    <div class="upi-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:20px;">
                        <div class="upi-card" onclick="selectUpi('Google Pay')" id="upi-Google Pay">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/f/f2/Google_Pay_Logo.svg" height="24" alt="GPay">
                            <i class="fas fa-check-circle check-icon"></i>
                        </div>
                        <div class="upi-card" onclick="selectUpi('PhonePe')" id="upi-PhonePe">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/7/71/PhonePe_Logo.svg" height="24" alt="PhonePe">
                            <i class="fas fa-check-circle check-icon"></i>
                        </div>
                        <div class="upi-card" onclick="selectUpi('Paytm')" id="upi-Paytm">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/2/24/Paytm_Logo_%28standalone%29.svg" height="24" alt="Paytm">
                            <i class="fas fa-check-circle check-icon"></i>
                        </div>
                        <div class="upi-card" onclick="selectUpi('BHIM UPI')" id="upi-BHIM UPI">
                            <span style="font-weight:600; font-size:16px; color:#333;">BHIM UPI</span>
                            <i class="fas fa-check-circle check-icon"></i>
                        </div>
                    </div>
                    <div class="form-group" id="upi_id_container" style="display:none;">
                        <label style="font-size:13px; font-weight:500; margin-bottom:5px; display:block;">Enter UPI ID for <span id="selected_upi_name"></span></label>
                        <input type="text" id="upi_id_field" class="form-control" placeholder="e.g. 9876543210@ybl">
                    </div>
                </div>
                <?php endif; ?>

                <!-- Card Pane -->
                <?php if (in_array('card', $active_keys)): ?>
                <div id="pane-card" class="payment-pane <?php echo ($default_key === 'card') ? 'active' : ''; ?>">
                    <p style="font-size:14px; color:var(--text-light); margin-bottom:15px;">We support Visa, Mastercard, RuPay, and Amex.</p>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Card Number" maxlength="19">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Name on Card">
                    </div>
                    <div class="grid-2">
                        <div class="form-group"><input type="text" class="form-control" placeholder="MM/YY" maxlength="5"></div>
                        <div class="form-group"><input type="password" class="form-control" placeholder="CVV" maxlength="4"></div>
                    </div>
                    <small style="color:var(--text-light);"><i class="fas fa-lock"></i> Your card details are securely encrypted.</small>
                </div>
                <?php endif; ?>

                <!-- Net Banking Pane -->
                <?php if (in_array('netbanking', $active_keys)): ?>
                <div id="pane-netbanking" class="payment-pane <?php echo ($default_key === 'netbanking') ? 'active' : ''; ?>">
                    <p style="font-size:14px; color:var(--text-light); margin-bottom:15px;">Select your bank to proceed.</p>
                    <select class="form-control">
                        <option>State Bank of India</option>
                        <option>HDFC Bank</option>
                        <option>ICICI Bank</option>
                        <option>Axis Bank</option>
                        <option>Kotak Mahindra Bank</option>
                    </select>
                </div>
                <?php endif; ?>

                <!-- Wallet Pane -->
                <?php if (in_array('wallet', $active_keys)): ?>
                <div id="pane-wallet" class="payment-pane <?php echo ($default_key === 'wallet') ? 'active' : ''; ?>">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <div style="border:1px solid var(--border); padding:15px; border-radius:6px; text-align:center; cursor:pointer;"><img src="https://upload.wikimedia.org/wikipedia/commons/2/24/Paytm_Logo_%28standalone%29.svg" height="20"></div>
                        <div style="border:1px solid var(--border); padding:15px; border-radius:6px; text-align:center; cursor:pointer;"><img src="https://upload.wikimedia.org/wikipedia/commons/1/12/Amazon_Pay_logo.svg" height="20"></div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- COD Pane -->
                <?php if (in_array('cod', $active_keys)): ?>
                <div id="pane-cod" class="payment-pane <?php echo ($default_key === 'cod') ? 'active' : ''; ?>">
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; padding:15px; border-radius:6px; color:var(--success);">
                        <i class="fas fa-info-circle"></i> Pay with cash when your order is delivered.
                    </div>
                </div>
                <?php endif; ?>

                <button class="btn-primary mt-4" onclick="completeStep(3)">Review Your Order</button>
            </div>
        </div>
        
        <!-- STEP 4: REVIEW -->
        <div class="step-section">
            <div class="step-header" onclick="openStep(4)">
                <h3><span class="num">4</span> Review Order</h3>
            </div>
            <div class="step-body" id="step4">
                <div style="background:var(--secondary); border:1px solid var(--border); border-radius:8px; padding:20px; margin-bottom:20px;">
                    <h4 style="margin-top:0; font-size:15px; color:var(--text-dark); margin-bottom:15px; border-bottom:1px solid var(--border); padding-bottom:10px;">Products</h4>
                    <?php 
                    foreach($checkout_items as $item) {
                        $raw_p = floatval(preg_replace('/[^0-9.]/', '', explode('-', $item['product']['product_price'])[0]));
                        $disc = get_active_discount($item['product']['product_id'], $raw_p, $con);
                        $final_p = $disc['has_discount'] ? $disc['discounted_price'] : $raw_p;
                        
                        echo "<div style='display:flex; gap:15px; margin-bottom:15px;'>";
                        $img_file = isset($item['product']['product_img1']) && !empty($item['product']['product_img1']) ? htmlspecialchars($item['product']['product_img1']) : 'hero-fallback.jpg';
                        
                        echo "<img src='img/{$img_file}' style='width:70px; height:70px; object-fit:cover; border-radius:6px; border:1px solid var(--border);' onerror=\"this.src='img/hero-fallback.jpg'\">";
                        echo "<div style='flex:1;'>";
                        echo "<strong style='font-size:15px;'>".htmlspecialchars($item['product']['product_name'])."</strong><br>";
                        echo "<span style='color:var(--text-light); font-size:13px;'>Qty: {$item['quantity']}</span>";
                        if($disc['has_discount']) {
                            echo "<span style='display:block; font-size:12px; color:var(--success); margin-top:4px;'><i class='fas fa-tags'></i> {$disc['badge_text']} applied</span>";
                        }
                        echo "</div>";
                        echo "<div style='font-weight:600;'>₹".number_format($final_p * $item['quantity'], 2)."</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
                
                <div id="review_payment_info" style="background:#fff; border:1px solid var(--border); border-radius:8px; padding:15px; margin-bottom:20px; font-size:14px; line-height:1.5;">
                    <!-- Payment info will be populated here via JS -->
                </div>
                
                <form id="finalOrderForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="selected_address_id" id="final_address_id">
                    <input type="hidden" name="selected_delivery_id" id="final_delivery_id">
                    <input type="hidden" name="selected_payment_method" id="final_payment_method" value="<?php echo isset($active_keys[0]) ? strtoupper($active_keys[0]) : ''; ?>">
                    <input type="hidden" name="upi_provider" id="final_upi_provider">
                    <input type="hidden" name="upi_id" id="final_upi_id">
                    <div id="confirm_msg" style="margin-bottom:15px; font-weight:600; display:none; color: var(--success);"></div>
                    <button type="button" id="btn_confirm_order" class="btn-primary" style="font-size:18px; padding:16px;" onclick="confirmOrderDetails()">Confirm</button>
                </form>
            </div>
        </div>

    </div>
    
    <!-- ORDER SUMMARY SIDEBAR -->
    <div class="chk-sidebar">
        <h3 class="summary-title">Order Summary</h3>
        
        <div class="summary-row"><span>Subtotal:</span> <span id="sum-subtotal">₹0.00</span></div>
        <div class="summary-row discount"><span>Discounts:</span> <span id="sum-discount">-₹0.00</span></div>
        <div class="summary-row discount" id="coupon-row" style="display:none;"><span>Coupon Applied:</span> <span id="sum-coupon">-₹0.00</span></div>
        <div class="summary-row"><span>Shipping:</span> <span id="sum-shipping">₹0.00</span></div>
        <div class="summary-row"><span><?php echo htmlspecialchars($tax_name); ?> (<?php echo $tax_percent; ?>%):</span> <span id="sum-tax">₹0.00</span></div>
        
        <div class="summary-total"><span>Total Payable:</span> <span id="sum-total">₹0.00</span></div>
        
        <div style="border-top:1px solid var(--border); padding-top:20px; margin-top:20px;">
            <p style="font-size:13px; font-weight:600; margin-bottom:10px;">Have a Promo Code?</p>
            <div style="display:flex; gap:10px;">
                <input type="text" id="coupon_code" class="form-control" style="margin-bottom:0;" placeholder="Enter code">
                <button type="button" class="btn-primary" style="width:auto; padding:0 20px;" onclick="applyCoupon()">Apply</button>
            </div>
            <div id="coupon-msg" style="font-size:13px; margin-top:8px; font-weight:500;"></div>
        </div>

        <button class="btn-primary" id="btn_place_order" style="margin-top:25px; display:none;" onclick="placeFinalOrder()">Place Order</button>
        <div style="font-size:12px; color:var(--text-light); text-align:center; margin-top:15px;">By placing your order, you agree to GK Almirah's Privacy Policy and Conditions of Use.</div>
        
        <div class="trust-badges">
            <div class="trust-badge"><i class="fas fa-shield-alt"></i> Safe & Secure Payments</div>
            <div class="trust-badge"><i class="fas fa-undo-alt"></i> 7 Days Easy Returns</div>
            <div class="trust-badge"><i class="fas fa-truck-loading"></i> Reliable Delivery</div>
            <div class="trust-badge"><i class="fas fa-headset"></i> 24/7 Support</div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let activeAddressId = <?php 
        $def_q = mysqli_query($con, "SELECT id FROM customer_addresses WHERE customer_id = $customer_id ORDER BY is_default DESC LIMIT 1");
        echo ($def_q && mysqli_num_rows($def_q)>0) ? mysqli_fetch_assoc($def_q)['id'] : 0; 
    ?>;
    let activeDeliveryId = <?php 
        $del_q = mysqli_query($con, "SELECT id FROM delivery_methods WHERE status='Active' ORDER BY charge ASC LIMIT 1");
        echo ($del_q && mysqli_num_rows($del_q)>0) ? mysqli_fetch_assoc($del_q)['id'] : 0; 
    ?>;
    let activePayment = '<?php echo isset($active_keys[0]) ? strtoupper($active_keys[0]) : ''; ?>';
    let activeUpi = '';

    function selectUpi(provider) {
        document.querySelectorAll('.upi-card').forEach(el => el.classList.remove('active'));
        document.getElementById('upi-' + provider).classList.add('active');
        activeUpi = provider;
        document.getElementById('final_upi_provider').value = provider;
        
        document.getElementById('selected_upi_name').innerText = provider;
        document.getElementById('upi_id_container').style.display = 'block';
    }

    $(document).ready(function() {
        updateSummary();
    });

    function updateProgress(step) {
        $('.step-indicator').removeClass('active completed');
        for(let i=1; i<=4; i++) {
            if(i < step) $('#pt-'+i).addClass('completed');
            if(i === step) $('#pt-'+i).addClass('active');
        }
    }

    function openStep(num) {
        $('.step-body').slideUp();
        $('#step'+num).slideDown();
        updateProgress(num);
    }

    function completeStep(num) {
        $('#step'+num+'-status').fadeIn();
        if(num === 1) {
            if(activeAddressId === 0) { alert('Please select or add an address.'); return; }
            $('#final_address_id').val(activeAddressId);
            $('#deliveryAvailability').fadeIn();
        }
        if(num === 2) {
            $('#final_delivery_id').val(activeDeliveryId);
            updateSummary();
        }
        if(num === 3) {
            $('#final_payment_method').val(activePayment);
            
            let currentPayHtml = '';
            
            if(activePayment === 'UPI') {
                const upiProvider = $('#final_upi_provider').val().trim();
                const upiId = $('#upi_id_field').val().trim();
                if (!upiProvider) {
                    alert('Please select a UPI provider (Google Pay, PhonePe, Paytm, or BHIM UPI).');
                    return;
                }
                if (!upiId || !/^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/.test(upiId)) {
                    alert('Please enter a valid UPI ID (e.g., yourname@okaxis).');
                    return;
                }
                $('#final_upi_id').val(upiId);
                currentPayHtml = `Payment Method:<br><strong>UPI</strong><br><span style='color:var(--text-light); font-size:13px;'>Provider: ${upiProvider}<br>UPI ID: ${upiId}</span>`;
            } else {
                $('#final_upi_id').val('');
                $('#final_upi_provider').val('');
                if (activePayment === 'COD') {
                    currentPayHtml = `Payment Method:<br><strong>Cash on Delivery</strong>`;
                } else {
                    currentPayHtml = `Payment Method:<br><strong>${activePayment}</strong>`;
                }
            }
            $('#review_payment_info').html(currentPayHtml);
        }
        openStep(num+1);
    }

    function selectAddress(id, el) {
        $('.address-card').removeClass('selected');
        $(el).addClass('selected');
        activeAddressId = id;
    }

    function selectDelivery(id, el) {
        $('.delivery-card').removeClass('selected');
        $(el).addClass('selected');
        activeDeliveryId = id;
        updateSummary();
    }

    function switchTab(method) {
        $('.payment-tab').removeClass('active');
        $(event.currentTarget).addClass('active');
        $('.payment-pane').removeClass('active');
        $('#pane-'+method).addClass('active');
        
        let map = {'upi':'UPI', 'card':'CARD', 'netbanking':'NETBANKING', 'wallet':'WALLET', 'cod':'COD'};
        activePayment = map[method];
    }

    function checkPincode() {
        let pin = $('#pincode').val();
        if(pin.length === 6) {
            $.get('https://api.postalpincode.in/pincode/'+pin, function(data) {
                if(data[0].Status === "Success") {
                    let po = data[0].PostOffice[0];
                    $('#city').val(po.District);
                    $('#state').val(po.State);
                }
            });
        }
    }

    function saveAddress(e) {
        e.preventDefault();
        let btn = $('#addrForm button[type="submit"]');
        btn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
        
        $.ajax({
            url: 'ajax/checkout-actions.php',
            method: 'POST',
            data: $('#addrForm').serialize() + '&action=save_address',
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    location.reload(); 
                } else {
                    alert(res.message);
                    btn.html('Save Address & Continue').prop('disabled', false);
                }
            }
        });
    }

    function updateSummary() {
        $.ajax({
            url: 'ajax/checkout-actions.php',
            method: 'POST',
            data: { action: 'get_summary', delivery_method_id: activeDeliveryId },
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    $('#sum-subtotal').text('₹' + res.sub_total.toFixed(2));
                    $('#sum-shipping').text(res.shipping > 0 ? '₹' + res.shipping.toFixed(2) : 'FREE');
                    $('#sum-discount').text('-₹' + res.total_discount.toFixed(2));
                    $('#sum-tax').text('₹' + res.tax.toFixed(2));
                    $('#sum-total').text('₹' + res.grand_total.toFixed(2));
                    
                    if(res.coupon_active && res.coupon_discount > 0) {
                        $('#coupon-row').show();
                        $('#sum-coupon').text('-₹' + res.coupon_discount.toFixed(2));
                    } else {
                        $('#coupon-row').hide();
                    }
                }
            }
        });
    }

    function applyCoupon() {
        let code = $('#coupon_code').val().trim();
        if(!code) return;
        
        let total = parseFloat($('#sum-subtotal').text().replace('₹','')) - parseFloat($('#sum-discount').text().replace('-₹',''));
        
        $.ajax({
            url: 'ajax/checkout-actions.php',
            method: 'POST',
            data: { action: 'apply_coupon', code: code, cart_total: total },
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    $('#coupon-msg').text(res.message).css('color', 'var(--success)');
                    updateSummary();
                } else {
                    $('#coupon-msg').text(res.message).css('color', 'var(--danger)');
                }
            }
        });
    }

    function confirmOrderDetails() {
        if(!activeAddressId) {
            alert('Please select a delivery address');
            return;
        }
        
        $('#final_address_id').val(activeAddressId);
        
        // Ensure UPI fields are set if UPI
        if(activePayment === 'UPI') {
            $('#final_upi_id').val($('#upi_id_field').val());
            $('#final_upi_provider').val(activeUpi);
        }
        
        let btn = $('#btn_confirm_order');
        btn.text('Confirming...').prop('disabled', true);
        
        $.ajax({
            url: 'checkout_action.php?action=confirm',
            method: 'POST',
            data: $('#finalOrderForm').serialize(),
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    $('#confirm_msg').text('✓ Order Details Confirmed. Ready For Final Order Placement.').show();
                    btn.hide();
                    $('#btn_place_order').show(); // Reveal final button
                    
                    // Lock inputs
                    $('.address-card, input[type=radio], input[type=text]').css('opacity', 0.6).css('pointer-events', 'none');
                } else {
                    alert('Error: ' + res.message);
                    btn.text('Confirm').prop('disabled', false);
                }
            },
            error: function() {
                alert('A network error occurred. Please try again.');
                btn.text('Confirm').prop('disabled', false);
            }
        });
    }
    
    function placeFinalOrder() {
        let btn = $('#btn_place_order');
        btn.text('Placing Order...').prop('disabled', true);
        
        $.ajax({
            url: 'checkout_action.php?action=place_order',
            method: 'POST',
            dataType: 'json',
            success: function(res) {
                if(res.success) {
                    window.location.href = res.redirect;
                } else {
                    alert('Error: ' + res.message);
                    btn.text('Place Order').prop('disabled', false);
                }
            },
            error: function() {
                alert('A network error occurred. Please try again.');
                btn.text('Place Order').prop('disabled', false);
            }
        });
    }
</script>
</body>
</html>
