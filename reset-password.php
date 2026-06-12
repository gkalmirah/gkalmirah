<?php
ob_start();
session_start();
if(!isset($_SESSION['reset_id']) || !isset($_SESSION['otp_verified'])){
    header('location: forgot-password.php');
    exit();
}
include('include/header.php');
?>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0" style="border-radius: 16px;">
                <div class="card-body p-5">
                    <h3 style="font-weight: 700; color: #1e293b;" class="text-center mb-4">Create New Password</h3>
                    
                    <?php
                    if(isset($_POST['reset_pass'])){
                        $new_pass = $_POST['new_pass'];
                        $conf_pass = $_POST['conf_pass'];
                        
                        if($new_pass === $conf_pass){
                            $reset_id = $_SESSION['reset_id'];
                            $query = "UPDATE customer SET cust_pass='$new_pass' WHERE cust_id=$reset_id";
                            if(mysqli_query($con, $query)){
                                // Clean up recovery session variables
                                unset($_SESSION['reset_id']);
                                unset($_SESSION['otp_verified']);
                                
                                // Set success message and redirect directly to sign-in page
                                $_SESSION['success_msg'] = "Your password has been successfully reset! Please sign in with your new credentials.";
                                header('location: sign-in.php');
                                exit();
                            } else {
                                echo "<div class='alert alert-danger'><strong><i class='fas fa-times-circle'></i> Error!</strong> Could not reset password.</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'><strong><i class='fas fa-exclamation-triangle'></i> Error!</strong> Passwords do not match. Please try again.</div>";
                        }
                    }
                    ?>

                    <?php if(!isset($success)){ ?>
                    <p class="text-secondary text-center mb-4">Identity verified! Please enter your new secure password below.</p>
                    <form method="post">
                        <div class="form-group mb-4">
                            <label style="font-weight: 500; color: #475569;">New Password</label>
                            <input type="password" name="new_pass" placeholder="Enter new password" class="form-control form-control-lg" style="border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0;" required>
                        </div>
                        <div class="form-group mb-4">
                            <label style="font-weight: 500; color: #475569;">Confirm Password</label>
                            <input type="password" name="conf_pass" placeholder="Confirm new password" class="form-control form-control-lg" style="border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0;" required>
                        </div>
                        <div class="form-group mt-4">
                            <button type="submit" name="reset_pass" class="btn btn-primary btn-lg w-100" style="border-radius: 30px; font-weight: 600; background: var(--accent-gold, #d4af37); border: none; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);">Reset Password</button>
                        </div>
                    </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('include/footer.php'); ?>
