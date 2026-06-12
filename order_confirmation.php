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
$customer_id = $_SESSION['id'];

$query = "SELECT co.*, fp.product_name, fp.product_img1 
          FROM customer_order co 
          JOIN furniture_product fp ON co.product_id = fp.product_id 
          WHERE co.invoice_no = '$invoice' AND co.customer_id = $customer_id";

$run = mysqli_query($con, $query);

if (mysqli_num_rows($run) == 0) {
    echo "<h3>Order not found or access denied.</h3>";
    include('include/footer.php');
    exit;
}

$items = [];
$total_billed = 0;
while($row = mysqli_fetch_assoc($run)) {
    $items[] = $row;
    $total_billed += floatval($row['product_amount']);
}
$first = $items[0];

?>
<link href="https://fonts.googleapis.com/css2?family=Amazon+Ember:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    body { font-family: "Amazon Ember", Arial, sans-serif; background-color: #EAEDED; margin: 0; }
    .conf-wrapper { max-width: 800px; margin: 40px auto; padding: 0 15px; }
    .conf-card { background: #fff; border: 1px solid #D5D9D9; border-radius: 8px; padding: 40px; text-align: center; box-shadow: 0 2px 5px rgba(213,217,217,0.5); }
    .conf-icon { font-size: 60px; color: #007600; margin-bottom: 20px; }
    .conf-title { font-size: 24px; font-weight: 700; color: #111; margin-bottom: 10px; }
    .conf-sub { font-size: 16px; color: #565959; margin-bottom: 30px; }
    
    .conf-details { text-align: left; background: #f8f8f8; border: 1px solid #ddd; padding: 20px; border-radius: 4px; margin-bottom: 30px; }
    .conf-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 14px; }
    .conf-label { font-weight: 700; color: #565959; margin-bottom: 4px; display: block; }
    .conf-value { color: #111; line-height: 1.4; }

    .conf-item { display: flex; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ddd; text-align: left; }
    .conf-item:last-child { border: none; margin: 0; padding: 0; }
    .conf-item img { width: 60px; height: 60px; object-fit: cover; border: 1px solid #ddd; }
    
    .btn-cont { display: inline-block; background: linear-gradient(to bottom, #f7dfa5, #f0c14b); border: 1px solid #a88734; border-radius: 3px; padding: 12px 24px; font-size: 14px; font-weight: 500; color: #111; text-decoration: none; box-shadow: 0 1px 0 rgba(255,255,255,0.4) inset; }
    .btn-cont:hover { background: linear-gradient(to bottom, #f5d576, #eeb933); }
</style>

<div class="conf-wrapper">
    <div class="conf-card">
        <div class="conf-icon"><i class="fas fa-check-circle"></i></div>
        <div class="conf-title">Order placed, thank you!</div>
        <div class="conf-sub">Confirmation will be sent to <strong><?php echo htmlspecialchars($first['customer_email']); ?></strong>.</div>

        <div class="conf-details">
            <h3 style="margin-top:0; border-bottom: 1px solid #ddd; padding-bottom:10px;">Order Details</h3>
            <div class="conf-grid">
                <div>
                    <span class="conf-label">Order Number</span>
                    <span class="conf-value" style="color:#007185; font-weight:700;">#<?php echo htmlspecialchars($invoice); ?></span>
                </div>
                <div>
                    <span class="conf-label">Total Billed Amount</span>
                    <span class="conf-value" style="color:#B12704; font-weight:700;">₹<?php echo number_format($total_billed, 2); ?></span>
                </div>
                <div>
                    <span class="conf-label">Shipping Address</span>
                    <span class="conf-value">
                        <?php echo htmlspecialchars($first['customer_fullname']); ?><br>
                        <?php echo htmlspecialchars($first['customer_address']); ?><br>
                        <?php echo htmlspecialchars($first['customer_city']); ?>, <?php echo htmlspecialchars($first['customer_pcode']); ?>
                    </span>
                </div>
                <div>
                    <span class="conf-label">Payment Method</span>
                    <span class="conf-value">
                        <?php 
                        echo htmlspecialchars($first['payment_method']); 
                        if ($first['payment_method'] === 'UPI') {
                            if (!empty($first['upi_provider'])) {
                                echo " - " . htmlspecialchars($first['upi_provider']);
                            }
                            if (!empty($first['upi_id'])) {
                                echo " (" . htmlspecialchars($first['upi_id']) . ")";
                            }
                        }
                        ?>
                    </span>
                </div>
            </div>
            
            <h4 style="margin:25px 0 10px;">Items in this Order</h4>
            <?php foreach($items as $itm): 
                $img = !empty($itm['product_img1']) ? $itm['product_img1'] : 'hero-fallback.jpg';
            ?>
            <div class="conf-item">
                <img src="img/<?php echo htmlspecialchars($img); ?>">
                <div>
                    <div style="font-weight:700; color:#007185; margin-bottom:4px;"><?php echo htmlspecialchars($itm['product_name']); ?></div>
                    <div style="color:#565959; font-size:13px;">Qty: <?php echo intval($itm['products_qty']); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <div style="margin-top:20px; font-size:14px; color:#007600; font-weight:700;">
                <i class="fas fa-truck"></i> Guaranteed Delivery by <?php echo date('D, M j', strtotime($first['order_date'] . ' + 5 days')); ?>
            </div>
        </div>

        <a href="index.php" class="btn-cont">Continue Shopping</a>
    </div>
</div>

<?php include('include/footer.php'); ?>
