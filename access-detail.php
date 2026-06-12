<?php 
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['email'])){
    header('location: sign-in.php');
    exit();
}

$customer_id = $_SESSION['id'];
include('include/cust_header.php');
?>


<div class="jumbotron jumbotron-custom text-white">
    <div class="container text-center">
        <h2 class="display-4">Account Details</h2>
        <p class="lead">View your Account Details</p>
    </div>
</div>
<style>
   .jumbotron-custom {
        position: relative;
        /* background-image: url('path-to-your-background-image.jpg'); Replace with your background image path */
        background-size: cover;
        background-position: center;
        color: #fff;
        padding: 100px 25px;
        margin-bottom: 0;
    }

    .jumbotron-custom::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); /* Black overlay with opacity */
        z-index: 1;
    }

    .jumbotron-custom .container {
        position: relative;
        z-index: 2;
    }

    .jumbotron-custom h2 {
        font-size: 3.5rem;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .jumbotron-custom p.lead {
        font-size: 1.5rem;
        margin-bottom: 0;
    }

</style>

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
                  <h3 style="font-weight: 700; color: #1e293b;">Access Details</h3>
                  <hr>
                  <?php
                  // Fetch current user details robustly using session email
                  $session_email = $_SESSION['email'];
                  $user_query = "SELECT * FROM customer WHERE cust_email='$session_email'";
                  $user_run = mysqli_query($con, $user_query);
                  
                  if($user_run && mysqli_num_rows($user_run) > 0) {
                      $user_data = mysqli_fetch_array($user_run);
                      $customer_id = $user_data['cust_id']; // Ensure ID is set for updates
                      $cust_name = $user_data['cust_name'];
                      $cust_email = $user_data['cust_email'];
                      $cust_number = $user_data['cust_number'];
                  } else {
                      $cust_name = '';
                      $cust_email = $session_email;
                      $cust_number = '';
                  }

                  if(isset($_POST['update_profile'])){
                      $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
                      $email = mysqli_real_escape_string($con, $_POST['email']);
                      $number = mysqli_real_escape_string($con, $_POST['phone_number']);
                      $password = mysqli_real_escape_string($con, $_POST['password']);
                      
                      $update_query = "UPDATE customer SET cust_name='$fullname', cust_email='$email', cust_number='$number'";
                      
                      if(!empty($password)){
                          $update_query .= ", cust_pass='$password'";
                      }
                      
                      $update_query .= " WHERE cust_id=$customer_id";

                      if(mysqli_query($con, $update_query)){
                          $_SESSION['name'] = $fullname;
                          $_SESSION['email'] = $email;
                          $_SESSION['number'] = $number;
                          $msg ="<div class='alert alert-success alert-dismissible fade show pt-1 pb-1 pl-3' role='alert'>
                                 <strong><i class='fas fa-check-circle'></i> Success! </strong> Your login details have been successfully updated.
                                 <button type='button' class='close p-2' data-dismiss='alert' aria-label='Close'>
                                  <span aria-hidden='true'>&times;</span>
                                 </button>
                                 </div>";
                          $cust_name = $fullname;
                          $cust_email = $email;
                          $cust_number = $number;
                      } else {
                          $error ="<div class='alert alert-danger alert-dismissible fade show pt-1 pb-1 pl-3' role='alert'>
                                 <strong><i class='fas fa-times-circle'></i> Error! </strong> Something went wrong during the update.
                                 <button type='button' class='close p-2' data-dismiss='alert' aria-label='Close'>
                                  <span aria-hidden='true'>&times;</span>
                                 </button>
                                 </div>";
                      }
                  }

                  if(isset($msg)){
                    echo $msg;
                  }
                  else if(isset($error)){
                    echo $error;
                  }
                  ?>
                  
                  <form method="post" class="mt-4 mb-5 pb-5">
                      
                      <!-- LOGIN DETAILS -->
                      <h6 class="text-muted mb-3" style="font-weight: 600; letter-spacing: 0.5px;">LOGIN DETAILS</h6>
                      <p class="text-secondary mb-4" style="font-size: 0.95rem;">Manage your personal information and login credentials.</p>
                      
                      <div class="row">
                          <div class="col-md-6 form-group mb-4">
                              <label style="font-weight: 500; color: #475569;">Full Name <span class="text-danger">*</span></label>
                              <input type="text" name="fullname" value="<?php echo htmlspecialchars($cust_name); ?>" class="form-control form-control-lg" style="border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0;" required>
                          </div>
                          <div class="col-md-6 form-group mb-4">
                              <label style="font-weight: 500; color: #475569;">Email Address <span class="text-danger">*</span></label>
                              <input type="email" name="email" value="<?php echo htmlspecialchars($cust_email); ?>" class="form-control form-control-lg" style="border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0;" required>
                          </div>
                      </div>
                      
                      <div class="row">
                          <div class="col-md-6 form-group mb-4">
                              <label style="font-weight: 500; color: #475569;">Mobile Number <span class="text-danger">*</span></label>
                              <input type="text" name="phone_number" value="<?php echo htmlspecialchars($cust_number); ?>" class="form-control form-control-lg" style="border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0;" required>
                          </div>
                          <div class="col-md-6 form-group mb-4">
                              <label style="font-weight: 500; color: #475569; display: flex; justify-content: space-between;">
                                  <span>Password</span>
                                  <a href="forgot-password.php" style="color: var(--accent-gold, #d4af37); font-weight: 600; text-decoration: none; font-size: 0.9rem;">Forgot Password?</a>
                              </label>
                              <input type="password" name="password" class="form-control form-control-lg" style="border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0;">
                          </div>
                      </div>

                      <div class="form-group mt-3">
                        <button type="submit" name="update_profile" class="btn btn-primary btn-lg px-5 py-2" style="border-radius: 30px; font-weight: 600; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4); background: var(--accent-gold, #d4af37); border: none;">
                            Save Details
                        </button>
                      </div>
                  </form>
              </div>
          </div>
         </div>
       </div>
   </div>
   
   <?php include('include/footer.php');?>