<?php
session_start();
include('include/header.php');

$track_msg = '';
if (isset($_POST['track_order'])) {
    $invoice = mysqli_real_escape_string($con, $_POST['invoice_no']);
    
    $query = "SELECT order_date, order_status, customer_fullname FROM customer_order WHERE invoice_no = '$invoice' LIMIT 1";
    $run = mysqli_query($con, $query);
    
    if (mysqli_num_rows($run) > 0) {
        $order = mysqli_fetch_assoc($run);
        $status = ucfirst($order['order_status']);
        $date = $order['order_date'];
        $name = $order['customer_fullname'];
        
        $status_color = 'text-warning';
        $icon = 'fa-clock';
        
        if ($order['order_status'] == 'verified') {
            $status_color = 'text-info';
            $icon = 'fa-check-double';
        } else if ($order['order_status'] == 'delivered') {
            $status_color = 'text-success';
            $icon = 'fa-truck-check';
        }
        
        $track_msg = "
            <div class='glass-card p-4 mt-5' data-aos='zoom-in'>
                <div class='row align-items-center'>
                    <div class='col-md-2 text-center'>
                        <i class='fas $icon fa-4x $status_color'></i>
                    </div>
                    <div class='col-md-10'>
                        <h4>Order Details for #$invoice</h4>
                        <hr>
                        <div class='row'>
                            <div class='col-sm-4'><strong>Customer:</strong> <br> $name</div>
                            <div class='col-sm-4'><strong>Date Ordered:</strong> <br> $date</div>
                            <div class='col-sm-4'><strong>Current Status:</strong> <br> <span class='$status_color font-weight-bold'>$status</span></div>
                        </div>
                    </div>
                </div>
            </div>";
    } else {
        $track_msg = "
            <div class='alert alert-danger mt-5' data-aos='shake'>
                <i class='fas fa-times-circle mr-2'></i> 
                Invalid Invoice Number. Please check your order confirmation email.
            </div>";
    }
}
?>

<div class="jumbotron jumbotron-custom text-white">
    <div class="container text-center">
        <h1 class="display-4" data-aos="fade-down">Track Your Order</h1>
        <p class="lead" data-aos="fade-up">Enter your invoice number to get real-time delivery updates</p>
    </div>
</div>

<div class="container section-padding">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="glass-card p-5" data-aos="fade-up">
                <form method="post" class="track-form">
                    <div class="form-group mb-0">
                        <label class="h5 mb-3">Invoice Number</label>
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-right-0">#</span>
                            </div>
                            <input type="text" name="invoice_no" class="form-control border-left-0" placeholder="e.g. 1767434801" required>
                            <div class="input-group-append">
                                <button class="btn btn-primary px-5" type="submit" name="track_order">Track Status</button>
                            </div>
                        </div>
                    </div>
                </form>
                
                <?php echo $track_msg; ?>
            </div>
        </div>
    </div>
</div>

<?php include('include/footer.php'); ?>

<style>
.track-form .input-group-text {
    border-radius: var(--radius-lg) 0 0 var(--radius-lg);
    border: 1px solid #e2e8f0;
}
.track-form input {
    border: 1px solid #e2e8f0;
}
.track-form button {
    border-radius: 0 var(--radius-lg) var(--radius-lg) 0 !important;
}
</style>
