<?php
ob_start();
session_start();

// Ensure the user actually requested an OTP
if(!isset($_SESSION['reset_id']) || !isset($_SESSION['otp'])){
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
                    <h3 style="font-weight: 700; color: #1e293b;" class="text-center mb-4">Enter OTP</h3>
                    <p class="text-secondary text-center mb-4">We've securely sent a 6-digit verification code to your email. Please enter it below.</p>
                    
                    <?php
                    if(isset($_POST['verify_otp'])){
                        $entered_otp = $_POST['otp_code'];
                        
                        if($entered_otp == $_SESSION['otp']){
                            $_SESSION['otp_verified'] = true; // Mark as verified
                            
                            // Clean up OTP so it can't be reused
                            unset($_SESSION['otp']);
                            
                            // Proceed to password reset screen
                            header('location: reset-password.php');
                            exit();
                        } else {
                            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                    <strong><i class='fas fa-times-circle'></i> Error!</strong> Incorrect OTP entered. Please try again.
                                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                        <span aria-hidden='true'>&times;</span>
                                    </button>
                                  </div>";
                        }
                    }
                    ?>

                    <form method="post">
                        <div class="form-group mb-4">
                            <label style="font-weight: 500; color: #475569;">6-Digit OTP Code</label>
                            <input type="text" name="otp_code" placeholder="Enter 6-digit code" class="form-control form-control-lg text-center" style="border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; font-size: 1.5rem; letter-spacing: 5px; font-weight: 600;" required autocomplete="off" maxlength="6">
                        </div>
                        <div class="form-group mt-4">
                            <button type="submit" name="verify_otp" class="btn btn-primary btn-lg w-100" style="border-radius: 30px; font-weight: 600; background: var(--accent-gold, #d4af37); border: none; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);">Verify Code</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="forgot-password.php" style="color: #64748b; font-weight: 500;">Didn't receive it? Request again</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('include/footer.php'); ?>
