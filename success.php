<?php 
ob_start();
session_start();
include('include/cust_header.php');

// Redirect if hit directly without an order
if(!isset($_SESSION['order_invoice'])) {
    header('Location: index.php');
    exit();
}

$invoice = $_SESSION['order_invoice'];
$est_date = date('l, M j', strtotime('+4 days')); // Simulate 4 day delivery

// Clear the invoice after reading it so refresh doesn't trigger it again
unset($_SESSION['order_invoice']);
?>

<style>
    .success-wrapper {
        background-color: #f8f9fa;
        min-height: 70vh;
        display: flex;
        align-items: center;
        padding: 60px 0;
    }
    .success-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid #e9ecef;
        text-align: center;
        padding: 50px 30px;
        max-width: 600px;
        margin: 0 auto;
    }
    .check-icon {
        color: #fff;
        background-color: #28a745;
        border-radius: 50%;
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        margin: 0 auto 25px auto;
        box-shadow: 0 4px 10px rgba(40,167,69,0.3);
    }
    .success-title {
        color: #111;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .order-details {
        background-color: #fcfcfc;
        border: 1px solid #f0f0f0;
        border-radius: 8px;
        padding: 20px;
        margin: 25px 0;
        text-align: left;
    }
    .order-details strong {
        color: #333;
    }
    .btn-amazon {
        background-color: #ffd814;
        border-color: #fcd200;
        color: #0f1111;
        font-weight: bold;
        border-radius: 8px;
        padding: 12px 30px;
        box-shadow: 0 2px 5px rgba(213,217,217,.5);
        transition: all 0.2s;
    }
    .btn-amazon:hover {
        background-color: #f7ca00;
        border-color: #f2c200;
        text-decoration: none;
        color: #0f1111;
    }
</style>

<div class="success-wrapper">
    <div class="container">
        <div class="success-card pb-5">
            <div class="check-icon">
                <i class="fas fa-check"></i>
            </div>
            <h2 class="success-title">Thank you for your order!</h2>
            <p class="text-muted text-lg">We have received your order and are getting it ready to be shipped.</p>
            
            <div class="order-details">
                <div class="row align-items-center">
                    <div class="col-sm-6 mb-2 mb-sm-0">
                        <span class="text-muted d-block small">Order Number:</span>
                        <strong class="text-primary" style="font-size: 1.1rem;">#<?php echo htmlspecialchars($invoice); ?></strong>
                    </div>
                    <div class="col-sm-6 text-sm-right border-sm-left pl-sm-4">
                        <span class="text-muted d-block small">Estimated Delivery:</span>
                        <strong><?php echo htmlspecialchars($est_date); ?></strong>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 gap-3 d-flex flex-column flex-sm-row justify-content-center">
                <a href="orders.php" class="btn btn-outline-secondary font-weight-bold px-4 py-2 border-2 align-self-center my-2 my-sm-0 mr-sm-3" style="border-radius: 8px;">
                    Track Order
                </a>
                <a href="product.php" class="btn btn-amazon align-self-center">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
include('include/footer.php');
ob_end_flush();
?>
