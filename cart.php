<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('include/cust_header.php');
require_once('include/dbcon.php');

if (isset($_SESSION['id'])) {
    $customer_id = $_SESSION['id'];
    
    // Clear any dangling "Buy Now" session states when viewing the regular cart
    unset($_SESSION['checkout_mode']);
    unset($_SESSION['buy_now_product_id']);
    unset($_SESSION['buy_now_qty']);
    // Handle deletion of product from cart
    if (isset($_GET['product_id'])) {
    $proid = intval($_GET['product_id']);
    $del_query = "DELETE FROM cart WHERE product_id = $proid AND cust_id = $customer_id";
    if (mysqli_query($con, $del_query)) {
        header("Location: cart.php");
        exit();
    } else {
        echo "Error deleting product: " . mysqli_error($con);
    }
}

    // Fetch cart items
    $cart_query = "SELECT * FROM cart WHERE cust_id='$customer_id'";
    $run = mysqli_query($con, $cart_query);
    if (!$run) {
        die("Error in cart query: " . mysqli_error($con));
    }

    $sub_total = 0;
    $shipping_cost = 0;
    $total = 0;

    $num_rows = mysqli_num_rows($run);

    if ($num_rows > 0) {
?>

<div class="jumbotron jumbotron-custom text-white">
    <div class="container text-center">
        <h2 class="display-4">Your Cart</h2>
        <p class="lead">Add your favourite products to cart to buy them anytime</p>
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
    <div class="container my-5">
        <div class="row">
            <!-- Shopping Cart -->
            <div class="col-md-9 p-3">
                <!-- <h5>Shopping Cart</h5><hr> -->
                <table class="table table-responsive table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th colspan="2">Product Detail</th>
                            <th>Quantity</th>
                            <th>Price (MRP)</th>
                            <th>Total</th>
                            <th colspan="4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($cart_row = mysqli_fetch_array($run)) {
                            $db_pro_id = $cart_row['product_id'];
                            $db_pro_qty = $cart_row['quantity'];

                            $pr_query = "SELECT * FROM furniture_product WHERE product_id=$db_pro_id";
                            $pr_run = mysqli_query($con, $pr_query);

                            if (!$pr_run) {
                                die("Error in product query: " . mysqli_error($con));
                            }

                            if (mysqli_num_rows($pr_run) > 0) {
                                while ($pr_row = mysqli_fetch_array($pr_run)) {
                                    $pid = $pr_row['product_id'] ?? 'N/A';
                                    $title = $pr_row['product_name'] ?? 'Unknown Product';
                                    $raw_price_str = strval($pr_row['product_price'] ?? 0);
                                    $parts = explode('-', $raw_price_str);
                                    $price = floatval(preg_replace('/[^0-9.]/', '', trim($parts[0])));

                                    $discount = get_active_discount($pid, $price, $con);
                                    if ($discount['has_discount']) {
                                        $price = $discount['discounted_price'];
                                    }                                    $size = $pr_row['product_size'] ?? 'N/A';
                                    $img1 = $pr_row['product_img1'] ?? 'default.jpg';

                                    $single_pro_total_price = $db_pro_qty * $price;
                                    $sub_total += $single_pro_total_price;
                                    $total = $sub_total + $shipping_cost;
                        ?> 
                        <tr>
                            <td width="150px">
                                <img src="img/<?php echo htmlspecialchars($img1); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($title); ?>">
                            </td>
                            <td>
                                <h5><?php echo htmlspecialchars($title); ?></h5>
                                <p>Dimension: <?php echo htmlspecialchars($size); ?></p>
                            </td>
                            <td>x <?php echo htmlspecialchars($db_pro_qty); ?></td>
                            <td>
                                <?php if($discount['has_discount']) { ?>
                                    <del class="text-muted small">Rs. <?php echo htmlspecialchars($discount['original_price']); ?></del><br>
                                    <span class="text-danger">Rs. <?php echo htmlspecialchars($price); ?></span>
                                    <br><span class="badge badge-danger"><?php echo $discount['badge_text']; ?></span>
                                <?php } else { ?>
                                    Rs. <?php echo htmlspecialchars($price); ?>
                                <?php } ?>
                            </td>
                            <td class="font-weight-bold">Rs. <?php echo htmlspecialchars($single_pro_total_price); ?> </td>
                            <td colspan="4" class="text-center">
                                <a title="Edit Product" href="edit_cart.php?cart_id=<?php echo $pid; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a title="Delete Product" href="cart.php?product_id=<?php echo $pid; ?>" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Delete
                                </a>  
                            </td>
                        </tr>   
                        <?php 
                                }
                            } else {
                                echo "<tr><td colspan='6'>Error: Product not found.</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- End Cart -->

            <!-- Order Detail -->
            <div class="col-md-3 p-3 border rounded">
                <h5>Order Details</h5><hr>
                <div class="row">
                    <div class="col-6">
                        <h6>Subtotal</h6>
                        <h6>Shipping</h6>
                        <h5 class="font-weight-bold">Total</h5>
                    </div>
                    <div class="col-6 text-right">
                        <h6>Rs. <?php echo htmlspecialchars($sub_total); ?></h6>
                        <h6>Rs. <?php echo htmlspecialchars($shipping_cost); ?></h6>
                        <h5 class="font-weight-bold">Rs. <?php echo htmlspecialchars($total); ?></h5>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="checkout.php" class="btn btn-success btn-block">Proceed to Checkout</a>
                </div>
            </div>
            <!-- End Order -->
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <a href="product.php" class="btn btn-primary btn-block">Continue Shopping</a>
            </div>
        </div>
    </div>

<?php
    } else {
        echo "<h2 class='text-center text-secondary mt-5' style='height:57vh; font-size:48px;'><i class='fad fa-shopping-cart text-primary'></i> Your Cart is Empty</h2>";
    }
} else {
    echo "<h2 class='text-center text-secondary mt-5' style='height:57vh; font-size:48px;'><i class='fad fa-shopping-cart text-primary'></i> Your Cart is Empty</h2>";
}
?>

<?php include('include/footer.php'); ?>
