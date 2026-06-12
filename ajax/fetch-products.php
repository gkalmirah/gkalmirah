<?php
session_start();
include('../include/dbcon.php');


$query = "SELECT DISTINCT p.* FROM furniture_product p";
$where_clauses = [];

// Category Filter
if (isset($_POST['categories']) && !empty($_POST['categories'])) {
    $categories = array_map('intval', $_POST['categories']);
    $cat_list = implode(',', $categories);
    $query .= " JOIN product_categories pc ON p.product_id = pc.product_id";
    $where_clauses[] = "pc.category_id IN ($cat_list)";
}

// Search Filter
if (isset($_POST['search']) && !empty($_POST['search'])) {
    $search = mysqli_real_escape_string($con, $_POST['search']);
    $where_clauses[] = "(p.product_name LIKE '%$search%' OR p.product_desc LIKE '%$search%')";
}

// Price Filter
if (isset($_POST['min_price']) && isset($_POST['max_price'])) {
    $min = (int) $_POST['min_price'];
    $max = (int) $_POST['max_price'];
    $where_clauses[] = "p.product_price BETWEEN $min AND $max";
}

// Availability Filter
if (isset($_POST['availability']) && !empty($_POST['availability'])) {
    // Assuming quantity column exists or just filter by price > 0
    // In this basic schema, we'll assume products with price > 0 are in stock
    if ($_POST['availability'] == 'in_stock') {
        $where_clauses[] = "p.product_price > 0";
    }
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(' AND ', $where_clauses);
}

// Grouping and Sorting
$query .= " GROUP BY p.product_id";

if (isset($_POST['sort'])) {
    switch ($_POST['sort']) {
        case 'price_low':
            $query .= " ORDER BY p.product_price ASC";
            break;
        case 'price_high':
            $query .= " ORDER BY p.product_price DESC";
            break;
        case 'newest':
            $query .= " ORDER BY p.product_id DESC";
            break;
        default:
            $query .= " ORDER BY p.product_id DESC";
            break;
    }
} else {
    $query .= " ORDER BY p.product_id DESC";
}

$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) > 0) {
    while ($p_row = mysqli_fetch_array($result)) {
        $pid = $p_row['product_id'];
        $ptitle = $p_row['product_name'];
        $p_price = floatval($p_row['product_price']);
        $discount = get_active_discount($pid, $p_price, $con);
        $img1 = $p_row['product_img1'];
        $whatsapp_number = '9682021084';
        ?>
        <div class="col-lg-4 col-md-6 col-6 mb-4">
            <div class="card product-card h-100">
                <div class="product-img-wrapper">
                    <?php if($discount['has_discount']) { ?>
                        <div class="badge-new bg-danger" style="z-index: 10;"><?php echo $discount['badge_text']; ?></div>
                    <?php } ?>
                    <?php
                    // Check if wishlisted
                    $is_wishlisted = false;
                    if (isset($_SESSION['id'])) {
                        $wid = $_SESSION['id'];
                        $wish_check = mysqli_query($con, "SELECT 1 FROM wishlist WHERE cust_id = $wid AND product_id = $pid");
                        if ($wish_check && mysqli_num_rows($wish_check) > 0)
                            $is_wishlisted = true;
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
                        <a href="javascript:void(0)" class="action-btn" title="Add to Bag"
                            onclick="addToCart(<?php echo $pid; ?>)">
                            <i class="fas fa-shopping-bag"></i>
                        </a>
                        <a href="product-detail.php?product_id=<?php echo $pid; ?>" class="action-btn" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php
                        $wa_msg = urlencode("Hello G.K Almirah Team,\n\nI am interested in the following product from your website:\n\nProduct Name: " . $ptitle . "\nPrice Range: Rs. " . $p_price . "\n\nCould you please share more details regarding availability and delivery?\n\nThank you.");
                        ?>
                        <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=<?php echo $wa_msg; ?>" target="_blank"
                            class="action-btn btn-whatsapp-card">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="text-truncate" title="<?php echo $ptitle; ?>">
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
    echo "<div class='col-12 text-center py-5'><h3>No products match your filters</h3><p class='text-muted'>Try adjusting your selection</p></div>";
}
?>
