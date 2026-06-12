<?php 
session_start();

if(!isset($_SESSION['email'])){
    header('location: sign-in.php');
    exit();
}

include('include/header.php'); 
// Database connection is handled in header.php
if(!isset($con)) {
    include('include/dbcon.php');
}

// Check if the connection was successful
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch User's Total Orders
$customer_id = $_SESSION['id'];
$order_count_query = "SELECT COUNT(*) AS total_orders FROM customer_order WHERE customer_id=$customer_id";
$order_count_run = mysqli_query($con, $order_count_query);
$order_count_data = mysqli_fetch_assoc($order_count_run);
$total_orders = $order_count_data['total_orders'] ? $order_count_data['total_orders'] : 0;

$user_first_name = isset($_SESSION['name']) ? explode(' ', trim($_SESSION['name']))[0] : 'Customer';
$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : 'your email';

?>


     
<div class="dashboard-container">
    <div class="container">
        <div class="mb-4">
            <h3 style="font-weight: 700; color: #1e293b;">Account Details</h3>
            <p class="text-secondary mb-0" style="font-size: 0.95rem;">Welcome back, <?php echo htmlspecialchars($user_first_name); ?>! Manage your profile, your <strong><?php echo $total_orders; ?></strong> orders, and security settings.</p>
            <hr>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="dash-content-area">
                    
                    <!-- Orders Card -->
                    <a href="orders.php" class="dash-card">
                        <div class="dash-icon-wrapper">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <h5>Your Orders</h5>
                        <p>Track, return, or buy things again</p>
                        <i class="fas fa-arrow-right dash-arrow"></i>
                    </a>

                    <!-- Login & Security -->
                    <a href="access-detail.php" class="dash-card">
                        <div class="dash-icon-wrapper">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h5>Login & security</h5>
                        <p>Edit login, name, and mobile number</p>
                        <i class="fas fa-arrow-right dash-arrow"></i>
                    </a>

                    <!-- Personal Details -->
                    <a href="personal-detail.php" class="dash-card">
                        <div class="dash-icon-wrapper">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h5>Your Addresses</h5>
                        <p>Edit addresses for orders and gifts</p>
                        <i class="fas fa-arrow-right dash-arrow"></i>
                    </a>

                    <!-- Track Order -->
                    <a href="track-order.php" class="dash-card">
                        <div class="dash-icon-wrapper">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h5>Track Order</h5>
                        <p>Check the live status of your shipment</p>
                        <i class="fas fa-arrow-right dash-arrow"></i>
                    </a>

                    <!-- Delivery Check -->
                    <a href="delivery-check.php" class="dash-card">
                        <div class="dash-icon-wrapper">
                            <i class="fas fa-map-pin"></i>
                        </div>
                        <h5>Delivery Check</h5>
                        <p>Check delivery availability by pincode</p>
                        <i class="fas fa-arrow-right dash-arrow"></i>
                    </a>

                    <!-- Sign Out -->
                    <a href="sign-out.php" class="dash-card">
                        <div class="dash-icon-wrapper">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <h5>Sign Out</h5>
                        <p>Securely log out of your account</p>
                        <i class="fas fa-arrow-right dash-arrow"></i>
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include('include/footer.php') ?>
