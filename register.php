<?php
ob_start();
session_start();
include('include/header.php');
// Database connection is handled in header.php
if(!isset($con)) {
    include('include/dbcon.php');
}

// Check if the connection was successful
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>

<style>
    /* Reset margin and padding for all elements */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa; /* Light background for the body */
        margin: 0;
        padding: 10 px;
    }

    .contain {
        margin-top: 50px;
        margin-bottom: 50px;
        padding: 40px;
        background-color: #e3dac9;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .hero-image {
        width: 100%;
        max-width: 400px;
        height: auto;
        border-radius: 8px;
        margin-bottom: 20px;
        transition: transform 0.3s;
    }

    .hero-image:hover {
        transform: scale(1.05);
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 40px;
    }

    .card-body {
        padding: 40px;
    }

    .form-control {
        border-radius: 5px;
        box-shadow: none;
        border-color: #ced4da;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        width: 100%; /* Full-width for the button */
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .alert {
        border-radius: 5px;
        margin-top: 20px;
    }

    .text-center a {
        color: #007bff;
        text-decoration: none;
        transition: text-decoration 0.3s;
    }

    .text-center a:hover {
        text-decoration: underline;
    }

    .form-group {
        margin-bottom: 20px; /* Increased margin for form groups */
    }

    h1.text-center {
        margin-bottom: 30px;
        color: #333;
    }

    .contain .row {
        display: flex;
        align-items: center;
    }
</style>

<div class="contain sign-in-up">
    <div class="row">
        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <img src="img/register1.png" alt="Furniture Store" class="hero-image">
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h1 class="text-center mt-3">Register Account</h1>
                    
                    <form method="post" class="mt-4">
                        <?php 
                        if(isset($_POST['register'])){
                            $fullname = $_POST['fullname'];
                            $email = $_POST['email'];
                            $password = $_POST['password'];
                            $conf_pass = $_POST['confirm-password'];
                            $address = $_POST['address'];
                            $city = $_POST['city'];
                            $postal_code = $_POST['code'];
                            $number = $_POST['phone_number'];

                            if(!empty($fullname) && !empty($email) && !empty($password) && !empty($conf_pass) && !empty($address) && !empty($city) && !empty($postal_code) && !empty($number)){
                                if($password === $conf_pass){
                                    $cust_query="INSERT INTO customer(`cust_name`,`cust_email`,`cust_pass`,`cust_add`,`cust_city`,`cust_postalcode`,`cust_number`) VALUES('$fullname','$email','$password','$address','$city','$postal_code','$number')";
                                    if(mysqli_query($con,$cust_query)){
                                        $message="You Are Registered Successfully!";
                                    }
                                } else {
                                    $error="Passwords do not match";
                                }
                            } else {
                                $error="All (*) Fields Required";
                            }
                        }
                        ?>

                        <?php if(isset($error)): ?>
                            <div class='alert bg-danger' role='alert'>
                                <span class='text-white text-center'><?= $error ?></span>
                            </div>
                        <?php elseif(isset($message)): ?>
                            <div class='alert bg-success' role='alert'>
                                <span class='text-white text-center'><?= $message ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <input type="text" name="fullname" placeholder="Full Name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="password" name="password" placeholder="Password" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="password" name="confirm-password" placeholder="Confirm Password" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="text" name="address" placeholder="Address" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="city" placeholder="City" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="code" placeholder="Postal Code" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="text" name="phone_number" placeholder="Phone Number" class="form-control" required>
                        </div>

                        <div class="form-group text-center mt-4">
                            <input type="submit" name="register" class="btn btn-primary" value="Register">
                        </div>

                        <div class="text-center mt-4">
                            Already a Member? <a href="sign-in.php">Sign in</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('include/footer.php'); ?>
