<?php
session_start();
include('include/header.php');

if (!isset($_SESSION['id'])) {
    echo "<script>window.location.href='sign-in.php';</script>";
    exit();
}

$cust_id = $_SESSION['id'];

// Fetch Wishlist Items from DB
$wish_q = "SELECT p.* FROM furniture_product p 
           JOIN wishlist w ON p.product_id = w.product_id 
           WHERE w.cust_id = $cust_id 
           ORDER BY w.wishlist_id DESC";
$wish_r = mysqli_query($con, $wish_q);
?>

<div class="jumbotron jumbotron-custom text-white mb-0" style="padding: 60px 0; background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('img/WS11.jpg') center/cover;">
    <div class="container text-center">
        <h2 class="display-4 font-weight-bold">My Wishlist</h2>
        <p class="lead">Your favorite pieces, saved for later.</p>
    </div>
</div>

<div class="container mt-5 mb-5">
    <div class="row">
        <?php 
        if (mysqli_num_rows($wish_r) > 0) {
            while ($p_row = mysqli_fetch_array($wish_r)) {
                $pid = $p_row['product_id'];
                $ptitle = $p_row['product_name'];
                $p_price = floatval($p_row['product_price']);
                $discount = get_active_discount($pid, $p_price, $con);
                $img1 = $p_row['product_img1'];
        ?>
                <div class="col-lg-3 col-md-4 col-6 mb-4 wish-item-<?php echo $pid; ?>">
                    <div class="card product-card h-100 shadow-sm border-0">
                        <div class="product-img-wrapper position-relative">
                            <?php if($discount['has_discount']) { ?>
                                <div class="badge-new bg-danger" style="z-index: 10;"><?php echo $discount['badge_text']; ?></div>
                            <?php } ?>
                            <a href="product-detail.php?product_id=<?php echo $pid; ?>">
                                <img src="img/<?php echo $img1; ?>" class="card-img-top" alt="<?php echo $ptitle; ?>" style="height: 200px; object-fit: contain;">
                            </a>
                            <button class="btn btn-sm btn-light position-absolute" style="top: 10px; right: 10px; border-radius: 50%; width: 32px; height: 32px; padding: 0;" 
                                    onclick="toggleWishlist(<?php echo $pid; ?>, this); $(this).closest('.col-lg-3').fadeOut();">
                                <i class="fas fa-times text-muted"></i>
                            </button>
                        </div>
                        <div class="card-body text-center">
                            <h6 class="text-truncate font-weight-bold mb-2">
                                <a href="product-detail.php?product_id=<?php echo $pid; ?>" class="text-dark"><?php echo $ptitle; ?></a>
                            </h6>
                            <div class="mb-3">
                                <div class="text-gold font-weight-bold">
                                    <?php if($discount['has_discount']) { ?>
                                        <del class="text-muted small">Rs. <?php echo number_format($p_price); ?></del>
                                        <span class="text-danger ml-1">Rs. <?php echo number_format($discount['discounted_price']); ?></span>
                                    <?php } else { ?>
                                        Rs. <?php echo number_format($p_price); ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <button class="btn btn-primary btn-sm btn-block rounded-pill py-2" onclick="addToCart(<?php echo $pid; ?>)">
                                <i class="fas fa-shopping-bag mr-2"></i> Add to Bag
                            </button>
                        </div>
                    </div>
                </div>
        <?php 
            }
        } else {
            echo "
            <div class='col-12 text-center py-5'>
                <div class='mb-4'>
                    <i class='far fa-heart fa-4x text-muted animate-pulse'></i>
                </div>
                <h3>Your wishlist is empty</h3>
                <p class='text-muted'>Explore our collection and save items you love!</p>
                <a href='product.php' class='btn btn-primary px-4 py-2 mt-3 rounded-pill'>Start Shopping</a>
            </div>";
        }
        ?>
    </div>
</div>

<?php include('include/footer.php'); ?>
