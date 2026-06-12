<?php 
session_start();
include('include/header.php');

$query = "";
if (isset($_GET['query'])) {
    $query = mysqli_real_escape_string($con, $_GET['query']);
}

// Pagination logic
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Fetch results
$search_q = "SELECT * FROM furniture_product WHERE product_name LIKE '%$query%' OR product_desc LIKE '%$query%' LIMIT $offset, $limit";
$search_r = mysqli_query($con, $search_q);

// Count total for pagination
$count_q = "SELECT COUNT(*) as total FROM furniture_product WHERE product_name LIKE '%$query%' OR product_desc LIKE '%$query%'";
$count_r = mysqli_query($con, $count_q);
$total_rows = mysqli_fetch_assoc($count_r)['total'];
$total_pages = ceil($total_rows / $limit);
?>

<div class="jumbotron jumbotron-custom text-white mb-0" style="padding: 60px 0;">
    <div class="container text-center">
        <h2 class="display-4">Search Results</h2>
        <p class="lead">Showing results for "<?php echo htmlspecialchars($query); ?>"</p>
    </div>
</div>

<div class="container mt-5 mb-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="font-weight-bold">
                <?php echo $total_rows; ?> Products Found
            </h4>
        </div>
        <div class="col-md-6 text-md-right">
            <span>Sorted by: <strong>Relevance</strong></span>
        </div>
    </div>

    <div class="row">
        <?php 
        if (mysqli_num_rows($search_r) > 0) {
            while ($p_row = mysqli_fetch_array($search_r)) {
                $pid = $p_row['product_id'];
                $ptitle = $p_row['product_name'];
                $p_price = floatval($p_row['product_price']);
                $discount = get_active_discount($pid, $p_price, $con);
                $img1 = $p_row['product_img1'];
        ?>
                <div class="col-lg-3 col-md-4 col-6 mb-4">
                    <div class="card product-card h-100">
                        <div class="product-img-wrapper">
                            <?php if($discount['has_discount']) { ?>
                                <div class="badge-new bg-danger" style="z-index: 10;"><?php echo $discount['badge_text']; ?></div>
                            <?php } ?>
                            <?php
                            $is_wishlisted = false;
                            if (isset($_SESSION['id'])) {
                                $wid = $_SESSION['id'];
                                $wish_check = mysqli_query($con, "SELECT 1 FROM wishlist WHERE cust_id = $wid AND product_id = $pid");
                                if ($wish_check && mysqli_num_rows($wish_check) > 0) $is_wishlisted = true;
                            }
                            ?>
                            <a href="javascript:void(0)" class="wishlist-btn <?php echo $is_wishlisted ? 'active' : ''; ?>" 
                               onclick="toggleWishlist(<?php echo $pid; ?>, this)" title="Add to Wishlist">
                                <i class="<?php echo $is_wishlisted ? 'fas text-danger' : 'far'; ?> fa-heart"></i>
                            </a>
                            <a href="product-detail.php?product_id=<?php echo $pid; ?>">
                                <img src="img/<?php echo $img1; ?>" alt="<?php echo $ptitle; ?>">
                            </a>
                            <div class="product-actions">
                                <a href="javascript:void(0)" class="action-btn" title="Add to Bag" onclick="addToCart(<?php echo $pid; ?>)">
                                    <i class="fas fa-shopping-cart"></i>
                                </a>
                                <a href="product-detail.php?product_id=<?php echo $pid;?>" class="action-btn" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="text-truncate" title="<?php echo $ptitle;?>">
                                <a href="product-detail.php?product_id=<?php echo $pid; ?>"><?php echo substr($ptitle, 0, 25); ?>..</a>
                            </h5>
                            <div class="product-price">
                                <?php if($discount['has_discount']) { ?>
                                    <del class="text-muted small">Rs. <?php echo number_format($p_price); ?></del>
                                    <span class="text-danger ml-1">Rs. <?php echo number_format($discount['discounted_price']); ?></span>
                                <?php } else { ?>
                                    Rs. <?php echo number_format($p_price); ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
        <?php 
            }
        } else {
            echo "
            <div class='col-12 text-center py-5'>
                <i class='fas fa-search fa-4x text-muted mb-4'></i>
                <h3>No products found for '" . htmlspecialchars($query) . "'</h3>
                <p class='text-muted'>Try checking your spelling or using more general keywords.</p>
                <a href='product.php' class='btn btn-primary mt-3'>Back to Products</a>
            </div>";
        }
        ?>
    </div>

    <?php if ($total_pages > 1) { ?>
    <ul class="pagination justify-content-center mt-5">
        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                <a class="page-link" href="search-results.php?query=<?php echo urlencode($query); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php } ?>
    </ul>
    <?php } ?>
</div>

<?php include('include/footer.php'); ?>
