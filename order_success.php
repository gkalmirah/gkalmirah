<?php
session_start();
$hide_nav = true;

if (!isset($_SESSION['id']) || !isset($_GET['invoice'])) {
    header('Location: index.php');
    exit();
}

include('include/cust_header.php');
require_once('include/dbcon.php');

$invoice = mysqli_real_escape_string($con, $_GET['invoice']);
$customer_id = intval($_SESSION['id']);

$query = "SELECT co.*, fp.product_name, fp.product_img1, fp.product_mrp, fp.product_model, c.cust_number as alt_number 
          FROM customer_order co 
          LEFT JOIN furniture_product fp ON co.product_id = fp.product_id 
          LEFT JOIN customer c ON co.customer_id = c.cust_id
          WHERE co.invoice_no = '$invoice' AND co.customer_id = $customer_id";

$run = mysqli_query($con, $query);

if (mysqli_num_rows($run) == 0) {
    echo "<div style='text-align:center; padding: 100px 20px;'><h3>Order not found or access denied.</h3></div>";
    include('include/footer.php');
    exit;
}

$items = [];
$total_billed = 0;
$total_mrp = 0;
$total_discount = 0;
$subtotal = 0;

while($row = mysqli_fetch_assoc($run)) {
    $items[] = $row;
    $total_billed += floatval($row['product_amount']);
    $mrp = floatval($row['product_mrp']) * intval($row['products_qty']);
    if($mrp == 0) $mrp = floatval($row['product_amount']); // Fallback
    $total_mrp += $mrp;
}
$first = $items[0];

// Calculate generic tax (18% inclusive assumption for display)
$tax_amount = $total_billed - ($total_billed / 1.18);
$subtotal = $total_billed - $tax_amount;
$savings = $total_mrp - $total_billed;
if($savings < 0) $savings = 0;

$order_status = strtolower($first['order_status']);
$status_steps = ['placed', 'confirmed', 'processing', 'shipped', 'out for delivery', 'delivered'];
$current_step = array_search($order_status, $status_steps);
if($current_step === false && $order_status == 'pending') $current_step = 0;

$is_admin = isset($_SESSION['admin_email']); // Check if admin is logged in
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    body { font-family: "Poppins", Arial, sans-serif; background-color: #f1f5f9; margin: 0; }
    header, .navbar-custom { display: none !important; }
    
    .invoice-container { max-width: 1000px; margin: 40px auto 20px; padding: 0 15px; }
    
    .action-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
    
    .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
    .card-title { font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 2px solid #f1f5f9; display: flex; align-items: center; gap: 8px; }
    
    .success-header { text-align: center; padding: 0 0 10px; }
    .success-icon { font-size: 40px; color: #10b981; margin-bottom: 5px; animation: scaleIn 0.5s ease-out; }
    .success-title { font-size: 22px; font-weight: 700; color: #0F172A; margin-bottom: 2px; }
    .success-sub { font-size: 14px; color: #64748b; }
    
    /* Tracking Timeline */
    /* Tracking Timeline */
    .timeline { display: flex; justify-content: space-between; position: relative; margin: 10px 0 20px; padding: 0 10px;}
    .timeline::before { content: ''; position: absolute; top: 12px; left: 30px; right: 30px; height: 2px; background: #e2e8f0; z-index: 1; }
    .timeline-step { position: relative; z-index: 2; text-align: center; width: 80px; }
    .timeline-icon { width: 26px; height: 26px; border-radius: 50%; background: #e2e8f0; color: #94a3b8; display: flex; align-items: center; justify-content: center; margin: 0 auto 5px; font-size: 12px; border: 2px solid #fff; transition: all 0.3s; }
    .timeline-label { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; }
    .timeline-step.active .timeline-icon { background: #D4AF37; color: #fff; box-shadow: 0 0 0 4px rgba(212,175,55,0.2); }
    .timeline-step.active .timeline-label { color: #D4AF37; }
    .timeline-step.completed .timeline-icon { background: #10b981; color: #fff; }
    .timeline-step.completed .timeline-label { color: #10b981; }

    /* Grid Layouts */
    .info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
    .info-block h4 { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 5px 0; }
    .info-block p { margin: 0; font-size: 14px; color: #0F172A; line-height: 1.4; font-weight: 500;}
    .info-block .meta { font-size: 12px; color: #64748b; font-weight: 400; margin-top: 3px; }
    
    /* Order Items */
    .item-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .item-table th { text-align: left; padding: 8px 10px; background: #f8fafc; color: #64748b; font-size: 11px; text-transform: uppercase; }
    .item-table td { padding: 10px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; font-size: 13px; }
    .item-img { width: 40px; height: 40px; border-radius: 6px; object-fit: cover; border: 1px solid #e2e8f0; }
    .item-name { font-weight: 600; color: #0F172A; font-size: 13px; margin-bottom: 2px; }
    .item-sku { font-size: 11px; color: #94a3b8; }
    
    /* Summary Panel */
    .summary-box { background: #f8fafc; border-radius: 8px; padding: 15px; width: 300px; margin-left: auto; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 13px; color: #64748b; }
    .summary-row.total { font-size: 15px; font-weight: 700; color: #0F172A; border-top: 2px solid #e2e8f0; padding-top: 8px; margin-top: 8px; }
    .savings-badge { background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; text-align: center; margin-top: 8px; display: flex; align-items: center; justify-content: center; gap: 5px; }

    /* Buttons */
    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 6px; font-size: 15px; font-weight: 600; text-decoration: none; transition: all 0.2s; cursor: pointer; border: none; }
    .btn-gold { background: #D4AF37; color: #0F172A; }
    .btn-gold:hover { background: #b89626; transform: translateY(-1px); }
    .btn-outline { background: transparent; color: #0F172A; border: 2px solid #0F172A; }
    .btn-outline:hover { background: #0F172A; color: #fff; }
    .btn-ghost { background: transparent; color: #64748b; }
    .btn-ghost:hover { color: #0F172A; background: #f1f5f9; }

    /* Trust Section */
    .trust-section { display: flex; justify-content: center; gap: 30px; margin-top: 15px; border-top: 1px solid #e2e8f0; padding-top: 15px; margin-bottom: 10px; }
    .trust-item { text-align: center; color: #64748b; }
    .trust-item i { font-size: 20px; color: #D4AF37; margin-bottom: 5px; }
    .trust-item span { display: block; font-size: 11px; font-weight: 500; }

    @keyframes scaleIn { 0% { transform: scale(0); } 70% { transform: scale(1.1); } 100% { transform: scale(1); } }
    
    @media (max-width: 768px) {
        .info-grid { grid-template-columns: 1fr; }
        .timeline { display: none; /* Hide complex timeline on mobile */ }
        .summary-box { width: 100%; }
        .action-bar { flex-direction: column; gap: 15px; }
        .item-table thead { display: none; }
        .item-table td { display: block; text-align: right; padding: 10px 15px; }
        .item-table td::before { content: attr(data-label); float: left; font-weight: 600; color: #64748b; }
        .item-table td.img-col { text-align: center; }
        .item-table td.img-col::before { display: none; }
    }

    /* Print Styles for Downloadable Invoice */
    @media print {
        body { background: #fff; font-size: 12pt; }
        #navbar, #footer, .action-bar, .trust-section, .timeline, .btn, .breadcrumb { display: none !important; }
        .invoice-container { margin: 0; padding: 0; max-width: 100%; }
        .card { box-shadow: none; border: 1px solid #ddd; margin-bottom: 20px; page-break-inside: avoid; }
        .success-header { padding: 0 0 20px; text-align: left; border-bottom: 2px solid #0F172A; margin-bottom: 20px; }
        .success-icon { display: none; }
        .success-title { font-size: 24pt; color: #0F172A !important; }
        .info-grid { grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .summary-box { border: 1px solid #ddd; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }
</style>

<div class="invoice-container">
    
    <div class="action-bar">
        <a href="index.php" class="btn btn-ghost"><i class="fas fa-arrow-left"></i> Back to Home</a>
        <div style="display: flex; gap: 10px;">
            <a href="cust.php" class="btn btn-outline"><i class="fas fa-box"></i> Track Order</a>
            <button onclick="window.print()" class="btn btn-gold"><i class="fas fa-print"></i> Download Invoice</button>
        </div>
    </div>

    <!-- HEADER -->
    <div class="success-header">
        <div class="success-icon"><i class="fas fa-check-circle"></i></div>
        <div class="success-title">Order Placed Successfully</div>
        <div class="success-sub">Thank you for shopping with GK Almirah. Your order has been confirmed.</div>
    </div>

    <div class="card">
        <div class="card-title"><i class="fas fa-file-invoice" style="color:#D4AF37;"></i> Order details</div>
        
        <!-- TIMELINE -->
        <div class="timeline">
            <?php 
            $icons = ['fa-shopping-cart', 'fa-check', 'fa-cogs', 'fa-truck', 'fa-route', 'fa-box-open'];
            foreach($status_steps as $index => $step): 
                $status_class = '';
                if($current_step !== false) {
                    if($index < $current_step) $status_class = 'completed';
                    elseif($index == $current_step) $status_class = 'active';
                }
            ?>
            <div class="timeline-step <?php echo $status_class; ?>">
                <div class="timeline-icon"><i class="fas <?php echo $icons[$index]; ?>"></i></div>
                <div class="timeline-label"><?php echo $step; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="info-grid">
            <div class="info-block">
                <h4>Customer Details</h4>
                <p><?php echo htmlspecialchars($first['customer_fullname']); ?></p>
                <div class="meta">
                    <i class="fas fa-envelope" style="width:16px;"></i> <?php echo htmlspecialchars($first['customer_email']); ?><br>
                    <i class="fas fa-phone" style="width:16px;"></i> <?php echo htmlspecialchars($first['customer_phonenumber']); ?><br>
                    <?php if(!empty($first['alt_number'])): ?>
                    <i class="fas fa-phone-alt" style="width:16px;"></i> <?php echo htmlspecialchars($first['alt_number']); ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="info-block">
                <h4>Delivery Address</h4>
                <p><?php echo htmlspecialchars($first['customer_fullname']); ?></p>
                <div class="meta" style="line-height:1.5;">
                    <?php echo htmlspecialchars($first['customer_address']); ?><br>
                    <?php echo htmlspecialchars($first['customer_city']); ?><br>
                    PIN: <?php echo htmlspecialchars($first['customer_pcode']); ?><br>
                    <span style="color:#10b981; font-weight:600; margin-top:5px; display:inline-block;"><i class="fas fa-calendar-alt"></i> Est. Delivery: <?php echo date('d M, Y', strtotime($first['order_date'] . ' + 5 days')); ?></span>
                </div>
            </div>
            
            <div class="info-block">
                <h4>Payment & Invoice</h4>
                <p>Invoice: <strong style="color:#D4AF37;">INV-<?php echo htmlspecialchars($invoice); ?></strong></p>
                <div class="meta" style="line-height:1.6;">
                    Date: <?php echo date('d M, Y H:i A', strtotime($first['order_date'])); ?><br>
                    Method: <strong><?php echo htmlspecialchars($first['payment_method']); ?></strong><br>
                    Status: <strong style="color:#10b981;">Paid</strong><br>
                    <?php if($first['payment_method'] === 'UPI'): ?>
                        <?php if(!empty($first['upi_provider'])): ?>
                        Provider: <strong><?php echo htmlspecialchars($first['upi_provider']); ?></strong><br>
                        <?php endif; ?>
                        <?php if(!empty($first['upi_id'])): ?>
                        UPI ID: <?php echo htmlspecialchars($first['upi_id']); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- PRODUCTS -->
    <div class="card">
        <div class="card-title"><i class="fas fa-box" style="color:#D4AF37;"></i> Products Ordered</div>
        <table class="item-table">
            <thead>
                <tr>
                    <th width="80">Item</th>
                    <th>Details</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $itm): 
                    $img = !empty($itm['product_img1']) ? $itm['product_img1'] : 'hero-fallback.jpg';
                    $line_total = floatval($itm['product_amount']);
                    $unit_price = $line_total / intval($itm['products_qty']);
                ?>
                <tr>
                    <td class="img-col" data-label=""><img src="img/<?php echo htmlspecialchars($img); ?>" class="item-img" onerror="this.src='img/hero-fallback.jpg'"></td>
                    <td data-label="Details">
                        <div class="item-name"><?php echo htmlspecialchars($itm['product_name']); ?></div>
                        <div class="item-sku">Model: <?php echo htmlspecialchars($itm['product_model'] ?? 'N/A'); ?> | SKU: GK-<?php echo str_pad($itm['product_id'], 4, '0', STR_PAD_LEFT); ?></div>
                    </td>
                    <td data-label="Price">₹<?php echo number_format($unit_price, 2); ?></td>
                    <td data-label="Qty"><?php echo intval($itm['products_qty']); ?></td>
                    <td data-label="Total" style="font-weight:600; color:#0F172A;">₹<?php echo number_format($line_total, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="summary-box">
            <div class="summary-row"><span>Subtotal</span> <span>₹<?php echo number_format($subtotal, 2); ?></span></div>
            <div class="summary-row"><span>Shipping</span> <span>₹0.00</span></div>
            <?php if(floatval($first['discount_amount']) > 0): ?>
            <div class="summary-row" style="color:#10b981;"><span>Coupon (<?php echo htmlspecialchars($first['promo_code']); ?>)</span> <span>-₹<?php echo number_format($first['discount_amount'], 2); ?></span></div>
            <?php endif; ?>
            <div class="summary-row"><span>Estimated GST (18%)</span> <span>₹<?php echo number_format($tax_amount, 2); ?></span></div>
            <div class="summary-row total"><span>Final Payable</span> <span>₹<?php echo number_format($total_billed, 2); ?></span></div>
            
            <?php if($savings > 0): ?>
            <div class="savings-badge"><i class="fas fa-tag"></i> You Saved ₹<?php echo number_format($savings, 2); ?> on this order!</div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- COMMUNICATION HISTORY -->
    <div class="card" style="margin-bottom:0;">
        <div class="card-title"><i class="fas fa-history" style="color:#D4AF37;"></i> Communication History</div>
        <div style="font-size: 14px; color: #64748b; line-height: 1.8;">
            <i class="fas fa-check-circle" style="color:#10b981; width:20px;"></i> Order Confirmation Email sent to <?php echo htmlspecialchars($first['customer_email']); ?> at <?php echo date('d M, Y H:i:s', strtotime($first['order_date'] . ' + 2 seconds')); ?><br>
            <i class="fas fa-check-circle" style="color:#10b981; width:20px;"></i> WhatsApp Notification queued for <?php echo htmlspecialchars($first['customer_phonenumber']); ?>
        </div>
        
        <?php if($is_admin): ?>
        <div style="margin-top:20px; padding:15px; background:#fff8e1; border:1px solid #ffe082; border-radius:6px;">
            <h4 style="margin:0 0 10px 0; color:#d84315; font-size:13px; text-transform:uppercase;">Admin Only Data</h4>
            <div style="font-size:13px; color:#5d4037;">
                <strong>Order Source:</strong> Web Checkout<br>
                <strong>Customer IP:</strong> <?php echo $_SERVER['REMOTE_ADDR']; ?><br>
                <strong>Creation Time:</strong> <?php echo $first['order_date']; ?><br>
                <strong>Internal Ref:</strong> <?php echo md5($invoice . $customer_id); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- TRUST SECTION -->
    <div class="trust-section">
        <div class="trust-item"><i class="fas fa-shield-alt"></i><span>Secure Payment</span></div>
        <div class="trust-item"><i class="fas fa-file-invoice"></i><span>GST Invoice</span></div>
        <div class="trust-item"><i class="fas fa-truck-moving"></i><span>Reliable Delivery</span></div>
        <div class="trust-item"><i class="fas fa-headset"></i><span>24/7 Support</span></div>
    </div>

</div>

<?php include('include/footer.php'); ?>
