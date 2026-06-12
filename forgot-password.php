<?php
ob_start();
session_start();
include('include/header.php');
?>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0" style="border-radius: 16px;">
                <div class="card-body p-5">
                    <h3 style="font-weight: 700; color: #1e293b;" class="text-center mb-4">Reset Password</h3>
                    <p class="text-secondary text-center mb-4">Please enter your registered email address and mobile number to verify your identity securely.</p>
                    
                    <?php
                    if(isset($_POST['verify'])){
                        $email = mysqli_real_escape_string($con, $_POST['email']);
                        $phone = mysqli_real_escape_string($con, $_POST['phone']);
                        
                        $query = "SELECT * FROM customer WHERE cust_email='$email' AND cust_number='$phone'";
                        $run = mysqli_query($con, $query);
                        
                        if(mysqli_num_rows($run) > 0){
                            $row = mysqli_fetch_array($run);
                            
                            // Generate 6-digit OTP
                            $otp = rand(100000, 999999);
                            $_SESSION['otp'] = $otp;
                            $_SESSION['reset_id'] = $row['cust_id'];
                            
                            // Include Mail Service
                            if(!function_exists('sendHtmlEmail')){
                                require_once('include/mail_service.php');
                            }
                            
                            // Prepare and send OTP Email
                            $to = $email;
                            $subject = "Your Password Reset OTP - GK Almirah";
                            $body = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                                        <h2 style='color: #1e293b;'>Password Reset Request</h2>
                                        <p>Dear " . htmlspecialchars($row['cust_name']) . ",</p>
                                        <p>We received a request to reset your password. Your secure One-Time Password (OTP) is:</p>
                                        <div style='background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; display: inline-block; margin: 15px 0;'>
                                            <h1 style='margin: 0; color: #d4af37; letter-spacing: 5px;'>" . $otp . "</h1>
                                        </div>
                                        <p style='color: #64748b;'>Please enter this code on the verification screen to proceed.</p>
                                        <p style='color: #64748b; font-size: 0.9em; margin-top: 30px;'>If you did not request a password reset, please ignore this email.</p>
                                     </div>";
                                     
                            sendHtmlEmail($to, $subject, $body);
                            
                            // Redirect to OTP verification screen
                            header('location: otp-verification.php');
                            exit();
                        } else {
                            echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                    <strong><i class='fas fa-times-circle'></i> Error!</strong> Invalid Email or Mobile Number.
                                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                        <span aria-hidden='true'>&times;</span>
                                    </button>
                                  </div>";
                        }
                    }
                    ?>

                    <form method="post">
                        <div class="form-group mb-4">
                            <label style="font-weight: 500; color: #475569;">Email Address</label>
                            <input type="email" name="email" placeholder="Enter your registered email" class="form-control form-control-lg" style="border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0;" required>
                        </div>
                        <div class="form-group mb-4">
                            <label style="font-weight: 500; color: #475569;">Mobile Number</label>
                            <input type="text" name="phone" placeholder="Enter your registered mobile number" class="form-control form-control-lg" style="border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0;" required>
                        </div>
                        <div class="form-group mt-4">
                            <button type="submit" name="verify" class="btn btn-primary btn-lg w-100" style="border-radius: 30px; font-weight: 600; background: var(--accent-gold, #d4af37); border: none; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);">Verify Identity</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="sign-in.php" style="color: #64748b; font-weight: 500;"><i class="fas fa-arrow-left mr-1"></i> Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('include/footer.php'); ?>
