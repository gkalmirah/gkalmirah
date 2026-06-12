<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('dbcon.php');

// Initialize cart count
$cart_count = 0;

// Check if the user is logged in and retrieve cart count
if (isset($_SESSION['id'])) {
    $cust_id = $_SESSION['id'];
    $query = "SELECT COUNT(quantity) AS total_items FROM cart WHERE cust_id = $cust_id";
    $run = mysqli_query($con, $query);

    if ($run) {
        $result = mysqli_fetch_assoc($run);
        $cart_count = $result['total_items'] ?? 0;
    } else {
        echo "Error retrieving cart count: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>G.K Almirah</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="img/logogk1.png" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@400;600&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <style>
        .navbar-custom {
            background-color: #ffffff !important;
            padding-bottom: 10px !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            position: relative;
            display: flex;
            align-items: center;
        }

        .logo-container {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 80px;
        }

        .navbar-logo {
            width: 100%;
            height: auto;
        }

        .brand-text {
            margin-left: 90px;
            font-family: 'Poppins', sans-serif;
            /* font-family: 'Playfair Display', serif; */
            font-size: 35px;
            font-weight: 600;
            color: #333;
            position: relative;
        }

        .brand-text .highlight {
            /* font-family: 'Great Vibes', cursive; */
            font-family: 'Poppins', sans-serif;
            font-size: 1.2em;
            color: #ff0000;
        }

        .navbar-nav .nav-link {
            font-size: 20px;
            color: #000000 !important;
            margin-right: 30px;
            transition: color 0.3s ease, transform 0.3s ease;
            display: inline-block;
        }

        .navbar-nav .nav-link:hover {
            color: #ff0000 !important;
            transform: scale(1.4);
        }

        .top-icon {
            font-size: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .badge-primary {
            background-color: #007bff;
        }
    </style>
</head>

<body>
    <header>
        <!-- Header -->
        <nav class="navbar navbar-expand-md navbar-light navbar-custom fixed-top">
            <a href="index.php" class="navbar-brand">
                <div class="logo-container">
                    <img src="../img/logogk1.png" class="navbar-logo" alt="GK Almirah Logo">
                </div>
                <span class="brand-text">G.K <span class="highlight">Almirah</span></span>
            </a>
            <?php if (!isset($hide_nav) || !$hide_nav): ?>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapisblenav"
                    aria-controls="collapsiblenav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="collapisblenav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="product.php">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="about-us.php">About Us</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact-us.php">Contact Us</a></li>
                        <li class="nav-item"><a class="nav-link" href="activate-warranty.php">Activate Warranty</a></li>
                        <li class="nav-item"><a class="nav-link" href="distribution.php">Become Our Distributor</a></li>

                        <li class="nav-item ml-5"><a class="nav-link" href="../cust.php"><i
                                    class="far fa-user top-icon"></i>Account</a></li>

                        <li class="nav-item">
                            <a class="nav-link" href="../cart.php">
                                <i class="fas fa-shopping-cart top-icon"></i>
                                <span class="badge badge-primary"
                                    style="position:absolute; margin-left:-5px;"><?php echo $cart_count; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </nav>
    </header>
</body>

</html>