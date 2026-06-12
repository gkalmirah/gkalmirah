<?php
ob_start();
session_start(); // Start the session at the beginning

include('include/header.php');

// Database connection parameters
// Database connection is handled in header.php
if(!isset($con)) {
    include('include/dbcon.php');
}

// Check if the connection was successful
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle form submission
if (isset($_POST['signin'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $query = "SELECT * FROM customer WHERE cust_email='$email'";
    $run = mysqli_query($con, $query);

    if (mysqli_num_rows($run) > 0) {
        $row = mysqli_fetch_array($run);
        $db_cust_id = $row['cust_id'];
        $db_cust_name = $row['cust_name'];
        $db_cust_email = $row['cust_email'];
        $db_cust_pass = $row['cust_pass'];
        $db_cust_add = $row['cust_add'];
        $db_cust_city = $row['cust_city'];
        $db_cust_pcode = $row['cust_postalcode'];
        $db_cust_number = $row['cust_number'];

        if ($password == $db_cust_pass) {
            $_SESSION['id'] = $db_cust_id;
            $_SESSION['name'] = $db_cust_name;
            $_SESSION['email'] = $db_cust_email;
            $_SESSION['add'] = $db_cust_add;
            $_SESSION['city'] = $db_cust_city;
            $_SESSION['pcode'] = $db_cust_pcode;
            $_SESSION['number'] = $db_cust_number;

            // Instant Checkout Re-Routing Logic
            if(isset($_SESSION['checkout_mode']) && $_SESSION['checkout_mode'] == 'buynow') {
                header('Location: checkout.php');
            } else {
                header('Location: cust.php');
            }
            
            exit();
        } else {
            $error = "Invalid Email or Password";
        }
    } else {
        $error = "This account doesn't exist";
    }
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
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background-color: #f5deb3; /* Light background for the body */
    }

    .contain {
        margin: 0 auto;
        padding: 20px;
    }

    .sign-in-up {
        padding-top: 120px; 
        padding-bottom: 20px; 
    }

    .hero-image {
        width: 100%;
        max-width: 400px;
        height: auto;
        border-radius: 4px;
        transition: transform 0.3s;
    }

    .hero-image:hover {
        transform: scale(1.05);
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 0;
        margin-bottom: 0;
        background-color: white; /* White background for the card */
    }

    .card-body {
        padding: 30px;
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
            <img src="img/login.png" alt="Furniture Store" class="hero-image">
        </div>

        <div class="col-md-6">
            <div class="card w-100">
                <div class="card-body">
                    <h1 class="text-center">Sign in</h1>

                    <form method="post" class="mt-4 p-3">
                        <?php if (isset($error)): ?>
                            <div class='alert bg-danger' role='alert'>
                                <span class='text-white text-center'><?= $error ?></span>
                                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success_msg'])): ?>
                            <div class='alert bg-success' role='alert'>
                                <span class='text-white text-center'><?= $_SESSION['success_msg'] ?></span>
                                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                </button>
                            </div>
                            <?php unset($_SESSION['success_msg']); ?>
                        <?php endif; ?>

                        <div class="form-group">
                            <input type="text" name="email" placeholder="Email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Password" class="form-control" required>
                        </div>

                        <a href="forgot-password.php">Forget Password?</a>

                        <div class="form-group text-center mt-4">
                            <input type="submit" name="signin" class="btn btn-primary" value="Sign in">
                        </div>

                        <div class="text-center mt-4"> Not a Member Yet? 
                            <a href="register.php">Register </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 

include('include/footer.php'); 
ob_end_flush();

?>
