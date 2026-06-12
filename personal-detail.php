<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect if the user is not logged in
if (!isset($_SESSION['email'])) {
    header('location: sign-in.php');
    exit();
}

include_once('include/dbcon.php');

$session_email = $_SESSION['email'];

if (isset($_POST['update_address'])) {
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $city = mysqli_real_escape_string($con, $_POST['city']);
    $code = mysqli_real_escape_string($con, $_POST['code']);

    $up_query = "UPDATE `customer` SET 
                 `cust_add`='$address',
                 `cust_city`='$city',
                 `cust_postalcode`='$code'
                 WHERE cust_email='$session_email'";

    if (mysqli_query($con, $up_query)) {
        $_SESSION['msg'] = "<div class='alert alert-success alert-dismissible fade show pt-1 pb-1 pl-3' role='alert'>
                            <strong><i class='fas fa-check-circle'></i> Success! </strong>Your shipping address has been updated.
                            <button type='button' class='close p-2' data-dismiss='alert' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                            </button>
                            </div>";

        header('location: personal-detail.php');
        exit();
    }
}

// Fetch current details reliably using the email
$query = "SELECT * FROM customer WHERE cust_email='$session_email'";
$run   = mysqli_query($con, $query);

if ($run && mysqli_num_rows($run) > 0) {
    $row = mysqli_fetch_array($run);
    $cust_add = $row['cust_add'];
    $cust_city = $row['cust_city'];
    $cust_pcode = $row['cust_postalcode'];
} else {
    $cust_add = '';
    $cust_city = '';
    $cust_pcode = '';
}

include('include/header.php');

?>








<div class="container mt-5">
    <div class="row">

     <div class="col-md-8 mx-auto">
          <!-- Back to Account Link -->
          <div class="mb-4">
              <a href="cust.php" style="color: #007185; text-decoration: none; font-weight: 500;">
                  <i class="fas fa-chevron-left mr-1"></i> Back to Your Account
              </a>
          </div>

          <div class="card shadow-sm border-0" style="border-radius: 16px;">
              <div class="card-body p-4 p-md-5">
       <h3 style="font-weight: 700; color: #1e293b;">Address Details</h3><hr>
       <h6 class="text-muted mb-3" style="font-weight: 600; letter-spacing: 0.5px;">SHIPPING & BILLING ADDRESS</h6>
        <p class="text-secondary mb-4" style="font-size: 0.95rem;">Update your delivery address to ensure seamless shipping for your future purchases.</p>
          
          <?php 
               if(isset($_SESSION['msg'])){
                 echo $_SESSION['msg'];
                 unset($_SESSION['msg']); // Ensure it disappears after refresh
                }
               ?>
            
          <form method="post" class="mt-4 mb-5 pb-5">
              <div class="form-group mb-4">
                <label style="font-weight: 500; color: #475569;">Street Address <span class="text-danger">*</span></label>
                <input type="text" name="address" placeholder="Enter your full street address" value="<?php echo htmlspecialchars($cust_add); ?>" class="form-control form-control-lg" style="border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0;" required>
            </div>
             
            <div class="row">
              <div class="col-md-6 form-group mb-4">
                <label style="font-weight: 500; color: #475569;">City <span class="text-danger">*</span></label>
                <input type="text" name="city" placeholder="City" value="<?php echo htmlspecialchars($cust_city); ?>" class="form-control form-control-lg" style="border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0;" required>
             </div>
              
              <div class="col-md-6 form-group mb-4">
                <label style="font-weight: 500; color: #475569;">Postal Code <span class="text-danger">*</span></label>
                <input type="number" name="code" placeholder="Postal code" value="<?php echo htmlspecialchars($cust_pcode); ?>" class="form-control form-control-lg" style="border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0;" required>
             </div>
            </div>

              <div class="form-group mt-3">
                <button type="submit" name="update_address" class="btn btn-primary btn-lg px-5 py-2" style="border-radius: 30px; font-weight: 600; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4); background: var(--accent-gold, #d4af37); border: none;">
                    Save Address
                </button>
              </div>
          </form> 
              </div>
          </div>
      </div>
    </div>
</div>

        
<?php include('include/footer.php');?>
<?php ob_end_flush(); ?>