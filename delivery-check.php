<?php
session_start();
include('include/header.php');

$result_msg = '';
if (isset($_POST['check_delivery'])) {
    $pincode = mysqli_real_escape_string($con, $_POST['pincode']);
    
    $query = "SELECT * FROM serviceable_pincodes WHERE pincode = '$pincode' AND is_active = 1";
    $run = mysqli_query($con, $query);
    
    if (mysqli_num_rows($run) > 0) {
        $data = mysqli_fetch_assoc($run);
        $days = $data['delivery_days'];
        $charge = $data['shipping_charge'];
        $charge_text = ($charge == 0) ? "FREE" : "Rs. $charge";
        
        $result_msg = "
            <div class='alert alert-success mt-4' data-aos='zoom-in'>
                <div class='d-flex align-items-center'>
                    <i class='fas fa-check-circle fa-2x mr-3'></i>
                    <div>
                        <h5 class='mb-1'>Great News! We deliver to $pincode.</h5>
                        <p class='mb-0'>Estimated Delivery: <strong>$days Days</strong> | Shipping: <strong>$charge_text</strong></p>
                    </div>
                </div>
            </div>";
    } else {
        $result_msg = "
            <div class='alert alert-warning mt-4' data-aos='zoom-in'>
                <div class='d-flex align-items-center'>
                    <i class='fas fa-exclamation-triangle fa-2x mr-3'></i>
                    <div>
                        <h5 class='mb-1'>Aww! We don't deliver to $pincode yet.</h5>
                        <p class='mb-0'>We are expanding rapidly. Please check back later or contact us for special requests.</p>
                    </div>
                </div>
            </div>";
    }
}
?>

<div class="jumbotron jumbotron-custom text-white">
    <div class="container text-center">
        <h1 class="display-4" data-aos="fade-down">Delivery Availability</h1>
        <p class="lead" data-aos="fade-up">Check if we can deliver our premium Almirahs to your doorstep</p>
    </div>
</div>

<div class="container section-padding">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="glass-card p-5" data-aos="fade-up">
                <h3 class="text-center mb-4">Enter Your Pincode</h3>
                <form method="post" class="delivery-form">
                    <div class="input-group input-group-lg">
                        <input type="text" name="pincode" class="form-control" placeholder="6-Digit Pincode (e.g. 147001)" maxlength="6" pattern="\d{6}" required>
                        <div class="input-group-append">
                            <button class="btn btn-primary px-5" type="submit" name="check_delivery">Check Now</button>
                        </div>
                    </div>
                </form>
                
                <?php echo $result_msg; ?>

                <div class="mt-5 text-center">
                    <h5>Why GK Almirah's Delivery?</h5>
                    <div class="row mt-4">
                        <div class="col-md-4 mb-3">
                            <i class="fas fa-truck-loading fa-2x text-gold mb-2"></i>
                            <p>Professional Handling</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <i class="fas fa-shield-alt fa-2x text-gold mb-2"></i>
                            <p>Secure Packaging</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <i class="fas fa-clock fa-2x text-gold mb-2"></i>
                            <p>Timely Updates</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('include/footer.php'); ?>

<style>
.delivery-form input {
    border-radius: var(--radius-lg) 0 0 var(--radius-lg) !important;
    border: 1px solid #e2e8f0;
}
.delivery-form button {
    border-radius: 0 var(--radius-lg) var(--radius-lg) 0 !important;
}
</style>
