<?php
session_start();

// 2. Track Recently Viewed
if (isset($_GET['product_id'])) {
    $pid = intval($_GET['product_id']);
    if (!isset($_SESSION['recently_viewed'])) {
        $_SESSION['recently_viewed'] = [];
    }
    // Remove if already exists to move to front
    if (($key = array_search($pid, $_SESSION['recently_viewed'])) !== false) {
        unset($_SESSION['recently_viewed'][$key]);
    }
    // Add to front
    array_unshift($_SESSION['recently_viewed'], $pid);
    // Limit to 10
    $_SESSION['recently_viewed'] = array_slice($_SESSION['recently_viewed'], 0, 10);
}

if (!isset($_GET['product_id'])) {
    header("location: product.php");
    exit();
}

include('include/header.php');

if (!isset($con)) {
    include('include/dbcon.php');
}

$pid = mysqli_real_escape_string($con, $_GET['product_id']);
$query = "SELECT * FROM furniture_product WHERE product_id = $pid";
$run = mysqli_query($con, $query);

if (mysqli_num_rows($run) == 0) {
    echo "<div class='container section-padding text-center'><h3>Product Not Found</h3><a href='product.php' class='btn btn-primary'>Back to Products</a></div>";
    include('include/footer.php');
    exit();
}

$row = mysqli_fetch_assoc($run);
$title = $row['product_name'];
$price = floatval($row['product_price']);
$discount = get_active_discount($pid, $price, $con);
$desc = $row['product_desc'];
$size = $row['product_size'];
$mat = $row['product_mat'];
$warranty = $row['product_warranty'];
$img1 = $row['product_img1'];
$img2 = $row['product_img2'];
$img3 = $row['product_img3'];
$img4 = isset($row['product_img4']) ? $row['product_img4'] : '';
$img5 = isset($row['product_img5']) ? $row['product_img5'] : '';
$img6 = isset($row['product_img6']) ? $row['product_img6'] : '';

// Handle Pincode Check
$pincode_res = '';
if (isset($_POST['check_pin'])) {
    $pin = mysqli_real_escape_string($con, $_POST['pincode']);
    $p_check = mysqli_query($con, "SELECT * FROM serviceable_pincodes WHERE pincode = '$pin' AND is_active = 1");
    if (mysqli_num_rows($p_check) > 0) {
        $p_data = mysqli_fetch_assoc($p_check);
        $pincode_res = "<p class='text-success mt-2'><i class='fas fa-check-circle'></i> Deliverable to $pin in {$p_data['delivery_days']} days!</p>";
    } else {
        $pincode_res = "<p class='text-danger mt-2'><i class='fas fa-times-circle'></i> Sorry, we don't deliver to $pin yet.</p>";
    }
}
?>

<!-- Add Swiper CSS for mobile slider -->
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
<link rel="stylesheet" href="css/room-preview.css">

<div class="container section-padding pt-5">
    <div class="row">
        <!-- Visual Column (Scrollable) -->
        <div class="col-lg-7 product-visuals" data-aos="fade-up">
            <div class="product-gallery-v3">
                <div class="gallery-thumbs-stack">
                    <div class="gallery-thumb-v3 active" onclick="switchMainImageV3(this, 'img/<?php echo $img1; ?>')">
                        <img src="img/<?php echo $img1; ?>" alt="<?php echo $title; ?>">
                    </div>
                    <?php if ($img2): ?>
                        <div class="gallery-thumb-v3" onclick="switchMainImageV3(this, 'img/<?php echo $img2; ?>')">
                            <img src="img/<?php echo $img2; ?>" alt="<?php echo $title; ?>">
                        </div>
                    <?php endif; ?>
                    <?php if ($img3): ?>
                        <div class="gallery-thumb-v3" onclick="switchMainImageV3(this, 'img/<?php echo $img3; ?>')">
                            <img src="img/<?php echo $img3; ?>" alt="<?php echo $title; ?>">
                        </div>
                    <?php endif; ?>
                    <?php if ($img4): ?>
                        <div class="gallery-thumb-v3" onclick="switchMainImageV3(this, 'img/<?php echo $img4; ?>')">
                            <img src="img/<?php echo $img4; ?>" alt="<?php echo $title; ?>">
                        </div>
                    <?php endif; ?>
                    <?php if ($img5): ?>
                        <div class="gallery-thumb-v3" onclick="switchMainImageV3(this, 'img/<?php echo $img5; ?>')">
                            <img src="img/<?php echo $img5; ?>" alt="<?php echo $title; ?>">
                        </div>
                    <?php endif; ?>
                    <?php if ($img6): ?>
                        <div class="gallery-thumb-v3" onclick="switchMainImageV3(this, 'img/<?php echo $img6; ?>')">
                            <img src="img/<?php echo $img6; ?>" alt="<?php echo $title; ?>">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="main-img-container-v3" style="cursor: zoom-in;" onclick="openLightboxV3($('#mainProductImageV3').attr('src'))">
                    <img src="img/<?php echo $img1; ?>" id="mainProductImageV3" alt="<?php echo $title; ?>">
                </div>
            </div>


            <!-- Dynamic Specifications Block -->
            <div class="mt-5 pt-4 bg-light p-4 rounded-xl">
                <h4 class="font-weight-bold mb-4">Technical DNA</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Core Material</small>
                        <span class="font-weight-bold"><?php echo $mat ?: 'Industrial Grade Steel'; ?></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Dimensions (H x W x D)</small>
                        <span class="font-weight-bold"><?php echo $size ?: 'Standard'; ?> mm</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Paint Technology</small>
                        <span
                            class="font-weight-bold"><?php echo $row['product_paint'] ?: '100% Powder Coated'; ?></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Architecture</small>
                        <span class="font-weight-bold"><?php echo $row['product_door']; ?> Doors |
                            <?php echo $row['product_drawer']; ?> Drawers</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Warranty</small>
                        <span class="font-weight-bold"><?php echo $row['product_warranty'] ?: '10 Years'; ?></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <small class="text-muted d-block">Special Features</small>
                        <span class="font-weight-bold"><?php echo $row['product_feature'] ?: 'Advanced Locks'; ?></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Purchase Column (Sticky) -->
        <div class="col-lg-5 product-purchase-info" data-aos="fade-left">
            <span class="brand-name-pill"><?php echo $row['product_subtitle'] ?: 'GK Almirah Exclusive'; ?></span>
            <h1 class="product-title-v3"><?php echo $title; ?></h1>
            <p class="text-muted mb-4"><?php echo $row['product_short_desc']; ?></p>



            <?php
            // Calculate Average Rating
            $avg_q = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE product_id = $pid";
            $avg_r = mysqli_query($con, $avg_q);
            $avg_data = mysqli_fetch_assoc($avg_r);
            $avg_rating = round($avg_data['avg_rating'], 1);
            $total_reviews = $avg_data['total_reviews'];
            ?>
            <div class="rating-summary-v3 d-flex align-items-center mb-3 mt-1">
                <div class="stars-v3 mr-2">
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $avg_rating)
                            echo '<i class="fas fa-star text-warning"></i>';
                        elseif ($i - 0.5 <= $avg_rating)
                            echo '<i class="fas fa-star-half-alt text-warning"></i>';
                        else
                            echo '<i class="far fa-star text-muted"></i>';
                    }
                    ?>
                </div>
                <span class="text-muted small font-weight-bold"><?php echo $avg_rating ?: '0.0'; ?>
                    (<?php echo $total_reviews; ?> Reviews)</span>
                <span class="mx-2 text-muted">|</span>
                <a href="#customerReviews" class="small font-weight-bold text-gold scroll-to">Write a Review</a>
            </div>

            <div class="price-container-v3">
                <div class="d-flex align-items-baseline gap-3">
                    <?php if($discount['has_discount']) { ?>
                        <span class="current-price-v3">Rs. <?php echo number_format($discount['discounted_price']); ?></span>
                        <span class="text-muted small"><s>Rs. <?php echo number_format($price); ?></s></span>
                        <span class="badge badge-danger"><?php echo $discount['badge_text']; ?></span>
                    <?php } else { ?>
                        <span class="current-price-v3">Rs. <?php echo $price; ?></span>
                        <?php if ($row['product_mrp'] > 0): ?>
                            <span class="text-muted small"><s>Rs. <?php echo number_format($row['product_mrp']); ?></s></span>
                        <?php endif; ?>
                    <?php } ?>
                </div>
                <p class="text-muted small mb-0 mt-1"><?php echo $row['product_tax_inc'] ?: 'All taxes included'; ?></p>
            </div>



            <!-- Deliver Check -->
            <div class="card border-0 bg-white shadow-sm p-3 mb-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-truck-moving text-muted mr-2"></i>
                    <span class="font-weight-bold small">DELIVERY OPTIONS</span>
                </div>
                <form method="post" class="input-group input-group-sm">
                    <input type="text" name="pincode" class="form-control border-right-0" placeholder="Pincode"
                        maxlength="6" value="<?php echo isset($_SESSION['pincode']) ? $_SESSION['pincode'] : ''; ?>">
                    <div class="input-group-append">
                        <button type="submit" name="check_pin"
                            class="btn btn-outline-primary font-weight-bold px-3">CHECK</button>
                    </div>
                </form>
                <?php if ($pincode_res)
                    echo "<div class='small mt-2'>$pincode_res</div>"; ?>
            </div>

            <!-- Core Benefits Grid -->
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="benefit-text"><?php echo $warranty ?: 'Long Lasting'; ?><br>Warranty</div>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fas fa-medal"></i></div>
                    <div class="benefit-text">TATA<br>Steel Body</div>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fas fa-lock"></i></div>
                    <div class="benefit-text">High Security<br>Locks</div>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fas fa-leaf"></i></div>
                    <div class="benefit-text">Rust Free<br>Technology</div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex flex-column mb-3" style="gap: 15px;">
                <!-- Actions Container -->
                <div class="d-flex flex-wrap align-items-center desktop-buy-section d-none-mobile" style="gap: 10px;">
                    <!-- BUY NOW -->
                    <a href="javascript:void(0)"
                        class="btn flex-grow-1 d-flex align-items-center justify-content-center py-3 px-3 shadow-sm"
                        style="background-color: #ffd814; border-color: #fcd200; color: #0f1111; border-radius: 8px; min-width: 140px;"
                        onclick="buyNow(<?php echo $pid; ?>)">
                        <i class="fas fa-bolt mr-2 text-dark"></i>
                        <span class="font-weight-bold">BUY NOW</span>
                    </a>

                    <!-- ADD TO BAG -->
                    <a href="javascript:void(0)"
                        class="btn btn-outline-dark flex-grow-1 d-flex align-items-center justify-content-center py-3 px-3"
                        style="border-radius: 8px; min-width: 140px;" onclick="addToCart(<?php echo $pid; ?>)">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        <span class="font-weight-bold">ADD TO BAG</span>
                    </a>

                    <?php
                    $is_wishlisted = false;
                    if (isset($_SESSION['id'])) {
                        $wid = $_SESSION['id'];
                        $wish_check = mysqli_query($con, "SELECT 1 FROM wishlist WHERE cust_id = $wid AND product_id = $pid");
                        if ($wish_check && mysqli_num_rows($wish_check) > 0)
                            $is_wishlisted = true;
                    }
                    ?>
                    <!-- WISHLIST -->
                    <a href="javascript:void(0)"
                        class="btn btn-outline-danger d-flex align-items-center justify-content-center <?php echo $is_wishlisted ? 'active' : ''; ?>"
                        onclick="toggleWishlist(<?php echo $pid; ?>, this)"
                        style="width: 58px; height: 58px; border-radius: 8px; flex-shrink: 0;">
                        <i class="<?php echo $is_wishlisted ? 'fas' : 'far'; ?> fa-heart fa-lg"></i>
                    </a>

                    <?php
                    $wa_msg = urlencode("Hello G.K Almirah Team,\n\nI am interested in the following product from your website:\n\nProduct Name: " . $title . "\nPrice: Rs. " . ($discount['has_discount'] ? $discount['discounted_price'] : $price) . "\n\nCould you please share more details regarding availability and delivery?\n\nThank you.");
                    ?>
                    <!-- WHATSAPP -->
                    <a href="https://wa.me/9682021084?text=<?php echo $wa_msg; ?>"
                        class="btn-whatsapp-v3 d-flex align-items-center justify-content-center"
                        style="width: 58px; height: 58px; border-radius: 8px; flex-shrink: 0;">
                        <i class="fab fa-whatsapp fa-lg"></i>
                    </a>
                </div>
            </div>




            <button class="btn btn-outline-dark btn-block mt-3 py-3 font-weight-bold"
                onclick="window.location.href='contact-us.php'">
                ENQUIRE NOW
            </button>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════
     PRODUCT DETAILS — Tab Section (Feature Enhancement)
     Inserted below main product area, above Recently Viewed
     ═══════════════════════════════════════════════════ -->
<style>
/* ── Product Details Tabs ── */
.pd-tabs-section {
    background: #fff;
    border-top: 1px solid #e8ecf0;
    border-bottom: 1px solid #e8ecf0;
}

.pd-tabs-nav {
    display: flex;
    border-bottom: 2px solid #e8ecf0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}
.pd-tabs-nav::-webkit-scrollbar { display: none; }

.pd-tab-btn {
    flex-shrink: 0;
    padding: 16px 28px;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 600;
    font-size: 0.88rem;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #64748b;
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    cursor: pointer;
    transition: color 0.22s ease, border-color 0.22s ease;
    white-space: nowrap;
}
.pd-tab-btn:hover { color: #0f172a; }
.pd-tab-btn.active {
    color: #0f172a;
    border-bottom-color: #bfa15f;
}

.pd-tab-pane { display: none; padding: 40px 0; }
.pd-tab-pane.active { display: block; }

/* ── Spec Grid ── */
.pd-spec-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 0;
    border: 1px solid #e8ecf0;
    border-radius: 10px;
    overflow: hidden;
}
.pd-spec-row {
    display: flex;
    align-items: stretch;
    border-bottom: 1px solid #e8ecf0;
}
.pd-spec-row:last-child { border-bottom: none; }
.pd-spec-label {
    width: 46%;
    padding: 14px 18px;
    background: #f8fafc;
    font-size: 0.85rem;
    font-weight: 600;
    color: #475569;
    border-right: 1px solid #e8ecf0;
    display: flex;
    align-items: center;
}
.pd-spec-value {
    width: 54%;
    padding: 14px 18px;
    font-size: 0.9rem;
    color: #0f172a;
    font-weight: 500;
    display: flex;
    align-items: center;
}

/* ── Info Two-Column Table ── */
.pd-info-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}
.pd-info-table tr {
    border-bottom: 1px solid #e8ecf0;
    transition: background 0.15s;
}
.pd-info-table tr:last-child { border-bottom: none; }
.pd-info-table tr:hover { background: #f8fafc; }
.pd-info-table th {
    width: 36%;
    padding: 14px 20px;
    font-weight: 600;
    color: #475569;
    text-align: left;
    font-size: 0.83rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: #f8fafc;
    border-right: 1px solid #e8ecf0;
}
.pd-info-table td {
    padding: 14px 20px;
    color: #0f172a;
    font-weight: 500;
}

/* ── Materials Badges ── */
.pd-material-card {
    border: 1px solid #e8ecf0;
    border-radius: 10px;
    padding: 24px;
    transition: box-shadow 0.22s ease, transform 0.22s ease;
}
.pd-material-card:hover {
    box-shadow: 0 8px 24px rgba(15,23,42,0.08);
    transform: translateY(-2px);
}
.pd-material-icon {
    width: 46px;
    height: 46px;
    background: linear-gradient(135deg, #bfa15f22, #bfa15f11);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
    color: #bfa15f;
    font-size: 1.2rem;
}
.pd-material-label {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: #94a3b8;
    margin-bottom: 6px;
}
.pd-material-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.5;
}

/* ── Care Steps ── */
.pd-care-steps { list-style: none; padding: 0; margin: 0; }
.pd-care-step {
    display: flex;
    gap: 16px;
    align-items: flex-start;
    padding: 18px 0;
    border-bottom: 1px solid #f1f5f9;
}
.pd-care-step:last-child { border-bottom: none; }
.pd-care-num {
    flex-shrink: 0;
    width: 34px; height: 34px;
    border-radius: 50%;
    background: #0f172a;
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem;
    font-weight: 700;
}
.pd-care-text strong {
    display: block;
    font-size: 0.92rem;
    color: #0f172a;
    margin-bottom: 3px;
}
.pd-care-text span {
    font-size: 0.85rem;
    color: #64748b;
    line-height: 1.6;
}

/* ── Section heading style ── */
.pd-section-heading {
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 6px;
}
.pd-section-sub {
    font-size: 0.88rem;
    color: #94a3b8;
    margin-bottom: 28px;
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .pd-tab-btn { padding: 14px 18px; font-size: 0.8rem; }
    .pd-tab-pane { padding: 28px 0; }
    .pd-spec-grid { grid-template-columns: 1fr; }
    .pd-spec-label, .pd-spec-value { padding: 11px 14px; }
    .pd-info-table th { width: 42%; padding: 11px 14px; }
    .pd-info-table td { padding: 11px 14px; }
}
@media (max-width: 480px) {
    .pd-spec-row { flex-direction: column; }
    .pd-spec-label { width: 100%; border-right: none; border-bottom: 1px solid #e8ecf0; }
    .pd-spec-value { width: 100%; }
    .pd-info-table th { font-size: 0.75rem; }
}
</style>

<section class="pd-tabs-section mt-0" data-aos="fade-up">
    <div class="container">
        <!-- Tab Navigation -->
        <nav class="pd-tabs-nav" role="tablist" aria-label="Product Details Tabs">
            <button class="pd-tab-btn active" id="pdTab-specs"    role="tab" aria-controls="pdPane-specs"    aria-selected="true"  onclick="switchPDTab('specs',    this)">Specifications</button>
            <button class="pd-tab-btn"        id="pdTab-addinfo"  role="tab" aria-controls="pdPane-addinfo"  aria-selected="false" onclick="switchPDTab('addinfo',  this)">Additional Info</button>
            <button class="pd-tab-btn"        id="pdTab-material" role="tab" aria-controls="pdPane-material" aria-selected="false" onclick="switchPDTab('material', this)">Materials</button>
            <button class="pd-tab-btn"        id="pdTab-care"     role="tab" aria-controls="pdPane-care"     aria-selected="false" onclick="switchPDTab('care',     this)">Care Instructions</button>
        </nav>

        <!-- ── TAB 1 : Specifications ── -->
        <div class="pd-tab-pane active" id="pdPane-specs" role="tabpanel" aria-labelledby="pdTab-specs">
            <h2 class="pd-section-heading">Product Specifications</h2>
            <p class="pd-section-sub">Detailed technical specifications for this almirah.</p>

            <?php
            // Pull real data with sensible defaults
            $spec_size       = $row['product_size']    ?: '1800 x 900 x 450';
            $spec_mat        = $row['product_mat']      ?: 'Industrial Grade Steel';
            $spec_warranty   = $row['product_warranty'] ?: '10 Years';
            $spec_doors      = $row['product_door']     ?? '2';
            $spec_drawers    = $row['product_drawer']   ?? '1';
            $spec_paint      = $row['product_paint']    ?: '100% Epoxy Powder Coated';
            $spec_feature    = $row['product_feature']  ?: 'Multi-point locking';
            $spec_weight     = $row['product_weight']   ?? 'N/A';

            // Category
            $cat_q   = "SELECT cat.category FROM product_categories pc
                         INNER JOIN categories cat ON pc.category_id = cat.id
                         WHERE pc.product_id = $pid LIMIT 1";
            $cat_r   = mysqli_query($con, $cat_q);
            $spec_cat = ($cat_r && $cr = mysqli_fetch_assoc($cat_r)) ? $cr['category'] : 'Almirah';
            ?>

            <div class="pd-spec-grid">
                <div class="pd-spec-row">
                    <div class="pd-spec-label">Dimensions (H × W × D)</div>
                    <div class="pd-spec-value"><?php echo htmlspecialchars($spec_size); ?> mm</div>
                </div>
                <div class="pd-spec-row">
                    <div class="pd-spec-label">Weight</div>
                    <div class="pd-spec-value"><?php echo htmlspecialchars($spec_weight); ?></div>
                </div>
                <div class="pd-spec-row">
                    <div class="pd-spec-label">Number of Doors</div>
                    <div class="pd-spec-value"><?php echo htmlspecialchars($spec_doors); ?></div>
                </div>
                <div class="pd-spec-row">
                    <div class="pd-spec-label">Number of Drawers</div>
                    <div class="pd-spec-value"><?php echo htmlspecialchars($spec_drawers); ?></div>
                </div>
                <div class="pd-spec-row">
                    <div class="pd-spec-label">Material</div>
                    <div class="pd-spec-value"><?php echo htmlspecialchars($spec_mat); ?></div>
                </div>
                <div class="pd-spec-row">
                    <div class="pd-spec-label">Finish / Paint Type</div>
                    <div class="pd-spec-value"><?php echo htmlspecialchars($spec_paint); ?></div>
                </div>
                <div class="pd-spec-row">
                    <div class="pd-spec-label">Lock Type</div>
                    <div class="pd-spec-value"><?php echo htmlspecialchars($spec_feature); ?></div>
                </div>
                <div class="pd-spec-row">
                    <div class="pd-spec-label">Product Category</div>
                    <div class="pd-spec-value"><?php echo htmlspecialchars($spec_cat); ?></div>
                </div>
                <div class="pd-spec-row">
                    <div class="pd-spec-label">Warranty</div>
                    <div class="pd-spec-value"><?php echo htmlspecialchars($spec_warranty); ?></div>
                </div>
                <div class="pd-spec-row">
                    <div class="pd-spec-label">Mirror Availability</div>
                    <div class="pd-spec-value">Available on select models</div>
                </div>
                <div class="pd-spec-row">
                    <div class="pd-spec-label">Handle Type</div>
                    <div class="pd-spec-value">Ergonomic Chrome-Finish Handle</div>
                </div>
                <div class="pd-spec-row">
                    <div class="pd-spec-label">Color</div>
                    <div class="pd-spec-value">Classic Grey / Royal Blue (as shown)</div>
                </div>
            </div>
        </div>

        <!-- ── TAB 2 : Additional Information ── -->
        <div class="pd-tab-pane" id="pdPane-addinfo" role="tabpanel" aria-labelledby="pdTab-addinfo">
            <h2 class="pd-section-heading">Additional Information</h2>
            <p class="pd-section-sub">Compliance, origin, packaging and brand details.</p>

            <div class="table-responsive">
                <table class="pd-info-table">
                    <tbody>
                        <tr>
                            <th>Brand Name</th>
                            <td>G.K Almirah</td>
                        </tr>
                        <tr>
                            <th>Country of Origin</th>
                            <td>India</td>
                        </tr>
                        <tr>
                            <th>Product Category</th>
                            <td><?php echo htmlspecialchars($spec_cat); ?></td>
                        </tr>
                        <tr>
                            <th>Net Quantity</th>
                            <td>1 Unit</td>
                        </tr>
                        <tr>
                            <th>Sales Package Contents</th>
                            <td>1 Almirah + Assembly Tools + Instruction Manual</td>
                        </tr>
                        <tr>
                            <th>Mounting Type</th>
                            <td>Floor Standing (Free Standing)</td>
                        </tr>
                        <tr>
                            <th>Warranty</th>
                            <td><?php echo htmlspecialchars($spec_warranty); ?> (Manufacturing Defects)</td>
                        </tr>
                        <tr>
                            <th>Manufacturer Address</th>
                            <td>G.K Almirah Industries, India</td>
                        </tr>
                        <tr>
                            <th>Model Number</th>
                            <td>GKA-<?php echo str_pad($pid, 4, '0', STR_PAD_LEFT); ?></td>
                        </tr>
                        <tr>
                            <th>Finish</th>
                            <td><?php echo htmlspecialchars($spec_paint); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── TAB 3 : Materials ── -->
        <div class="pd-tab-pane" id="pdPane-material" role="tabpanel" aria-labelledby="pdTab-material">
            <h2 class="pd-section-heading">Materials & Build Quality</h2>
            <p class="pd-section-sub">Premium-grade raw materials engineered for durability and longevity.</p>

            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="pd-material-card h-100">
                        <div class="pd-material-icon"><i class="fas fa-industry"></i></div>
                        <div class="pd-material-label">Steel Quality</div>
                        <div class="pd-material-value"><?php echo htmlspecialchars($spec_mat); ?><br><small style="font-weight:400;color:#64748b;font-size:0.82rem;">High-tensile, heavy-gauge cold-rolled steel for structural rigidity.</small></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="pd-material-card h-100">
                        <div class="pd-material-icon"><i class="fas fa-spray-can"></i></div>
                        <div class="pd-material-label">Powder Coating</div>
                        <div class="pd-material-value"><?php echo htmlspecialchars($spec_paint); ?><br><small style="font-weight:400;color:#64748b;font-size:0.82rem;">Electrostatically applied, oven-cured at 200°C for superior adhesion.</small></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="pd-material-card h-100">
                        <div class="pd-material-icon"><i class="fas fa-shield-alt"></i></div>
                        <div class="pd-material-label">Rust Resistance</div>
                        <div class="pd-material-value">Phosphate Pre-Treatment<br><small style="font-weight:400;color:#64748b;font-size:0.82rem;">Anti-rust phosphating process applied before painting for lasting protection.</small></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="pd-material-card h-100">
                        <div class="pd-material-icon"><i class="fas fa-star"></i></div>
                        <div class="pd-material-label">Surface Finish</div>
                        <div class="pd-material-value">Smooth Matte / Gloss<br><small style="font-weight:400;color:#64748b;font-size:0.82rem;">Scratch-resistant, easy-to-clean surface that retains its sheen for years.</small></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="pd-material-card h-100">
                        <div class="pd-material-icon"><i class="fas fa-lock"></i></div>
                        <div class="pd-material-label">Lock Hardware</div>
                        <div class="pd-material-value">Heavy-Duty Locking System<br><small style="font-weight:400;color:#64748b;font-size:0.82rem;">Precision-engineered locks with multi-point engagement for superior security.</small></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="pd-material-card h-100">
                        <div class="pd-material-icon"><i class="fas fa-medal"></i></div>
                        <div class="pd-material-label">Durability</div>
                        <div class="pd-material-value"><?php echo htmlspecialchars($spec_warranty); ?> Warranty<br><small style="font-weight:400;color:#64748b;font-size:0.82rem;">Manufactured under ISO-aligned quality standards for long-lasting performance.</small></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── TAB 4 : Care Instructions ── -->
        <div class="pd-tab-pane" id="pdPane-care" role="tabpanel" aria-labelledby="pdTab-care">
            <h2 class="pd-section-heading">Care & Maintenance</h2>
            <p class="pd-section-sub">Follow these guidelines to keep your almirah looking pristine for years.</p>

            <div class="row">
                <div class="col-lg-7">
                    <ol class="pd-care-steps">
                        <li class="pd-care-step">
                            <div class="pd-care-num">1</div>
                            <div class="pd-care-text">
                                <strong>Regular Dusting</strong>
                                <span>Use a soft, dry microfibre cloth to wipe the exterior surfaces. Avoid abrasive scrubbers that can damage the powder-coated finish.</span>
                            </div>
                        </li>
                        <li class="pd-care-step">
                            <div class="pd-care-num">2</div>
                            <div class="pd-care-text">
                                <strong>Cleaning Stains & Smudges</strong>
                                <span>For stubborn marks, use a slightly damp cloth with a mild, non-abrasive liquid detergent. Wipe dry immediately — do not allow moisture to sit on the surface.</span>
                            </div>
                        </li>
                        <li class="pd-care-step">
                            <div class="pd-care-num">3</div>
                            <div class="pd-care-text">
                                <strong>Water Exposure Precautions</strong>
                                <span>Keep the almirah away from direct water exposure, wet walls, or leaking areas. Although the steel is phosphate-treated, prolonged moisture contact can degrade the finish over time.</span>
                            </div>
                        </li>
                        <li class="pd-care-step">
                            <div class="pd-care-num">4</div>
                            <div class="pd-care-text">
                                <strong>Lock & Handle Maintenance</strong>
                                <span>Apply a few drops of lock lubricant (light machine oil) to the lock mechanism once a year to ensure smooth operation. Wipe off excess oil.</span>
                            </div>
                        </li>
                        <li class="pd-care-step">
                            <div class="pd-care-num">5</div>
                            <div class="pd-care-text">
                                <strong>Avoid Harsh Chemicals</strong>
                                <span>Do not use bleach, ammonia-based cleaners, paint thinner, or acidic solutions — these can strip the powder-coat finish and cause corrosion.</span>
                            </div>
                        </li>
                        <li class="pd-care-step">
                            <div class="pd-care-num">6</div>
                            <div class="pd-care-text">
                                <strong>Placement & Usage Tips</strong>
                                <span>Place the almirah on a flat, stable surface. Do not overload shelves beyond their rated capacity. Ensure adequate ventilation around the unit to prevent condensation.</span>
                            </div>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-5">
                    <div class="pd-material-card mt-4 mt-lg-0" style="background:#f8fafc;">
                        <h5 style="font-family:'Playfair Display',serif;font-size:1.05rem;margin-bottom:16px;color:#0f172a;">Quick Reference</h5>
                        <div style="font-size:0.87rem;color:#475569;line-height:1.9;">
                            <p><i class="fas fa-check-circle text-success mr-2"></i>Soft dry / damp cloth — <strong>✓ Safe</strong></p>
                            <p><i class="fas fa-check-circle text-success mr-2"></i>Mild soapy water — <strong>✓ Safe</strong></p>
                            <p><i class="fas fa-check-circle text-success mr-2"></i>Annual lock lubrication — <strong>✓ Recommended</strong></p>
                            <p><i class="fas fa-times-circle text-danger mr-2"></i>Bleach / Ammonia — <strong>✗ Avoid</strong></p>
                            <p><i class="fas fa-times-circle text-danger mr-2"></i>Abrasive pads or powders — <strong>✗ Avoid</strong></p>
                            <p><i class="fas fa-times-circle text-danger mr-2"></i>Direct water jets — <strong>✗ Avoid</strong></p>
                            <p><i class="fas fa-times-circle text-danger mr-2"></i>Chemical solvents — <strong>✗ Avoid</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function switchPDTab(tabId, clickedBtn) {
    // Deactivate all tabs and panes
    document.querySelectorAll('.pd-tab-btn').forEach(function(btn) {
        btn.classList.remove('active');
        btn.setAttribute('aria-selected', 'false');
    });
    document.querySelectorAll('.pd-tab-pane').forEach(function(pane) {
        pane.classList.remove('active');
    });
    // Activate clicked tab and corresponding pane
    clickedBtn.classList.add('active');
    clickedBtn.setAttribute('aria-selected', 'true');
    document.getElementById('pdPane-' + tabId).classList.add('active');
}
</script>
<!-- ═══════════════════════════════════════════════════ -->

<!-- Recently Viewed Section -->
<?php
if (isset($_SESSION['recently_viewed']) && count($_SESSION['recently_viewed']) > 1) {
    ?>
    <div class="container mb-5">
        <div class="recently-viewed-section mt-5 pt-4 border-top" data-aos="fade-up">
            <h4 class="font-weight-bold mb-4">Recently Viewed</h4>
            <div class="swiper-container recent-swiper pb-5">
                <div class="swiper-wrapper">
                    <?php
                    foreach ($_SESSION['recently_viewed'] as $recent_id) {
                        if ($recent_id == $pid)
                            continue; // Skip current product
                
                        $r_res = mysqli_query($con, "SELECT * FROM furniture_product WHERE product_id = $recent_id");
                        if ($r_row = mysqli_fetch_assoc($r_res)) {
                            $r_img = $r_row['product_img1'];
                            $r_name = $r_row['product_name'];
                            $r_price = floatval($r_row['product_price']);
                            $r_discount = get_active_discount($recent_id, $r_price, $con);
                            ?>
                            <div class="swiper-slide">
                                <div class="card h-100 border-0 shadow-sm text-center p-3"
                                    style="border-radius: 12px; transition: transform 0.3s ease;">
                                    <a href="product-detail.php?product_id=<?php echo $recent_id; ?>"
                                        class="text-decoration-none text-dark">
                                        <img src="img/<?php echo $r_img; ?>" class="img-fluid mb-3"
                                            style="height: 140px; object-fit: contain;">
                                        <div class="small font-weight-bold text-truncate px-2 mb-1"><?php echo $r_name; ?></div>
                                        <div class="text-gold font-weight-bold">
                                            <?php if($r_discount['has_discount']) { ?>
                                                <del class="text-muted small">Rs. <?php echo number_format($r_price); ?></del>
                                                <span class="text-danger ml-1">Rs. <?php echo number_format($r_discount['discounted_price']); ?></span>
                                            <?php } else { ?>
                                                Rs. <?php echo number_format((float) $r_price); ?>
                                            <?php } ?>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
    <style>
        .recent-swiper .swiper-slide {
            width: 220px !important;
            height: auto;
        }

        .recent-swiper .card:hover {
            transform: translateY(-5px);
        }

        .text-gold {
            color: #bc987e;
        }

        .swiper-pagination-bullet-active {
            background: #bc987e !important;
        }
    </style>
<?php } ?>

<!-- Customer Reviews System -->
<div class="reviews-section mt-5" id="customerReviews" data-aos="fade-up">
    <div class="container pb-5">
        <h3 class="font-weight-bold mb-4">Customer Reviews</h3>

        <div class="row">
            <!-- Rating Breakdown -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm p-4 text-center" style="border-radius: 15px; background: #fdfdfd;">
                    <h1 class="display-3 font-weight-bold text-dark mb-0"><?php echo $avg_rating ?: '0.0'; ?></h1>
                    <div class="stars-v3 mb-2">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $avg_rating)
                                echo '<i class="fas fa-star text-warning fa-lg"></i>';
                            elseif ($i - 0.5 <= $avg_rating)
                                echo '<i class="fas fa-star-half-alt text-warning fa-lg"></i>';
                            else
                                echo '<i class="far fa-star text-muted fa-lg"></i>';
                        }
                        ?>
                    </div>
                    <p class="text-muted"><?php echo $total_reviews; ?> reviews</p>

                    <div class="rating-bars mt-3">
                        <?php
                        for ($star = 5; $star >= 1; $star--) {
                            $count_star_q = "SELECT COUNT(*) as count FROM reviews WHERE product_id = $pid AND rating = $star";
                            $count_star_r = mysqli_query($con, $count_star_q);
                            $count_star = mysqli_fetch_assoc($count_star_r)['count'];
                            $pct = $total_reviews > 0 ? ($count_star / $total_reviews) * 100 : 0;
                            ?>
                            <div class="d-flex align-items-center mb-2">
                                <span class="small font-weight-bold mr-2" style="width: 20px;"><?php echo $star; ?>★</span>
                                <div class="progress flex-grow-1" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar bg-warning" style="width: <?php echo $pct; ?>%;"></div>
                                </div>
                                <span class="small text-muted ml-2" style="width: 30px;"><?php echo $count_star; ?></span>
                            </div>
                        <?php } ?>
                    </div>

                    <button class="btn btn-gold btn-block mt-4 rounded-pill font-weight-bold"
                        onclick="$('#reviewForm').slideToggle()">
                        Write a Review
                    </button>
                </div>
            </div>

            <!-- Reviews List & Sorting -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 font-weight-bold">Reviews</h5>
                    <select class="form-control form-control-sm border-0 bg-light rounded-pill px-3"
                        style="width: 150px; height: 35px;" id="sortReviews">
                        <option value="recent">Most Recent</option>
                        <option value="high">Highest Rated</option>
                        <option value="low">Lowest Rated</option>
                    </select>
                </div>

                <!-- Review Submission Form (Hidden by default) -->
                <div id="reviewForm" class="card border-0 shadow-sm p-4 mb-4"
                    style="display:none; border-radius: 15px; border-left: 5px solid #bc987e !important;">
                    <h5 class="font-weight-bold mb-3">Share Your Experience</h5>
                    <form id="submitReview">
                        <input type="hidden" name="product_id" value="<?php echo $pid; ?>">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">Rating</label>
                            <div class="rating-input d-flex gap-2 text-gold">
                                <i class="far fa-star fa-2x star-input" data-value="1"></i>
                                <i class="far fa-star fa-2x star-input" data-value="2"></i>
                                <i class="far fa-star fa-2x star-input" data-value="3"></i>
                                <i class="far fa-star fa-2x star-input" data-value="4"></i>
                                <i class="far fa-star fa-2x star-input" data-value="5"></i>
                                <input type="hidden" name="rating" id="ratingValue" value="5">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold">Your Name</label>
                            <input type="text" name="name" class="form-control border-0 bg-light rounded-pill" required
                                value="<?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ''; ?>">
                        </div>
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold">Review Description</label>
                            <textarea name="comment" class="form-control border-0 bg-light" rows="3"
                                style="border-radius: 15px;" placeholder="Tell others what you think..."
                                required></textarea>
                        </div>
                        <button type="submit" class="btn btn-dark px-5 rounded-pill font-weight-bold">Submit
                            Review</button>
                    </form>
                </div>

                <div id="reviewsContainer">
                    <!-- Reviews will be loaded here via AJAX -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-gold" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Sticky Footer Overlay -->
<div class="product-actions-mobile d-md-none" style="display: none;">
    <a href="javascript:void(0)" class="btn text-dark py-3 m-0 font-weight-bold" style="background-color: #ffd814; border-color: #fcd200; border-radius: 8px;" onclick="buyNow(<?php echo $pid; ?>)">
        BUY NOW
    </a>
    <a href="javascript:void(0)" class="btn btn-outline-dark py-3 m-0 font-weight-bold" style="border-radius: 8px;" onclick="addToCart(<?php echo $pid; ?>)">
        ADD TO BAG
    </a>
</div>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
<script>
    $(document).ready(function () {
        // Initialize Recent Swiper
        if ($('.recent-swiper').length) {
            new Swiper('.recent-swiper', {
                slidesPerView: 'auto',
                spaceBetween: 20,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    640: { slidesPerView: 'auto', spaceBetween: 20 },
                    768: { slidesPerView: 'auto', spaceBetween: 30 },
                }
            });
        }

        // --- Reviews Logic ---
        function loadReviews(sort = 'recent') {
            const container = $('#reviewsContainer');
            container.html('<div class="text-center py-5"><div class="spinner-border text-gold" role="status"></div></div>');

            $.ajax({
                url: 'ajax/review-action.php',
                method: 'POST',
                data: { action: 'fetch', product_id: <?php echo $pid; ?>, sort: sort },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        container.html(response.html);
                    }
                }
            });
        }

        loadReviews(); // Initial load

        $('#sortReviews').on('change', function () {
            loadReviews($(this).val());
        });

        // Star Rating Input Interaction
        $('.star-input').on('click', function () {
            const val = $(this).data('value');
            $('#ratingValue').val(val);
            $('.star-input').each(function () {
                if ($(this).data('value') <= val) {
                    $(this).removeClass('far').addClass('fas text-warning');
                } else {
                    $(this).removeClass('fas text-warning').addClass('far text-muted');
                }
            });
        });

        // Review Submission
        $('#submitReview').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serialize() + '&action=submit';

            $.ajax({
                url: 'ajax/review-action.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        swal("Success!", "Thank you for your review!", "success");
                        $('#reviewForm').slideUp();
                        $('#submitReview')[0].reset();
                        $('.star-input').removeClass('fas text-warning').addClass('far text-muted');
                        loadReviews();
                    } else {
                        swal("Error", response.message, "error");
                    }
                }
            });
        });

        // Smooth scroll
        $('.scroll-to').on('click', function (e) {
            e.preventDefault();
            const target = $(this.getAttribute('href'));
            if (target.length) {
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 800);
            }
        });
    });

    // 1. V3 Thumbnail Switching Logic
    function switchMainImageV3(element, imgSrc) {
        // Update active thumbnail
        $('.gallery-thumb-v3').removeClass('active');
        $(element).addClass('active');

        // Change main image with professional fade
        $('#mainProductImageV3').fadeOut(200, function () {
            $(this).attr('src', imgSrc).fadeIn(200);
            
            // Sync background for zoom window
            const zoomResult = document.getElementById("zoomWindowResult");
            if (zoomResult) zoomResult.style.backgroundImage = "url('" + imgSrc + "')";
        });
    }

    // Advanced Image Magnifier (Amazon-style panel applied to Lightbox)
    function initImageZoom() {
        // Target the Lightbox elements now
        const container = document.getElementById('lightboxZoomContainer');
        const img = document.getElementById('lightboxImgV3');
        const lens = document.getElementById('zoomLens');
        const result = document.getElementById('zoomWindowResult');
        
        if (!container || !img || !lens || !result) return;
        
        // Disable on mobile where pinch-to-zoom is preferred
        if (window.innerWidth < 992) return;

        function getRenderedDimentions(img) {
            const ratio = Math.min(img.clientWidth / img.naturalWidth, img.clientHeight / img.naturalHeight);
            return {
                width: img.naturalWidth * ratio,
                height: img.naturalHeight * ratio,
                left: (img.clientWidth - (img.naturalWidth * ratio)) / 2,
                top: (img.clientHeight - (img.naturalHeight * ratio)) / 2
            };
        }

        const ZOOM_SCALE = 2.5;

        container.addEventListener('mouseenter', function() {
            if (!img.naturalWidth) return; // Wait for load
            
            const rendered = getRenderedDimentions(img);
            
            // Dynamically size the lens to perfectly match the Aspect Ratio of the Target Window
            const resultWidth = result.offsetWidth;
            const resultHeight = result.offsetHeight;
            
            // The lens is just the target window scaled down by ZOOM_SCALE
            const lensWidth = resultWidth / ZOOM_SCALE;
            const lensHeight = resultHeight / ZOOM_SCALE;
            
            lens.style.width = lensWidth + "px";
            lens.style.height = lensHeight + "px";
            
            // The background image size is the rendered image scaled up by ZOOM_SCALE
            const bgW = rendered.width * ZOOM_SCALE;
            const bgH = rendered.height * ZOOM_SCALE;
            
            result.style.backgroundImage = "url('" + img.src + "')";
            result.style.backgroundSize = bgW + "px " + bgH + "px";
            
            lens.classList.add('active');
            result.classList.add('active');
        });

        container.addEventListener('mouseleave', function() {
            lens.classList.remove('active');
            result.classList.remove('active');
        });

        container.addEventListener('mousemove', moveLens);
        
        function moveLens(e) {
            e.preventDefault();
            if (!img.naturalWidth) return;

            const rendered = getRenderedDimentions(img);
            
            // Get dynamically calculated lens dimensions
            const lensRect = lens.getBoundingClientRect();
            const lensW = lensRect.width;
            const lensH = lensRect.height;
            
            const pos = getCursorPos(e);
            
            // Adjust cursor position to be relative to the *rendered* image pixels, not the container
            let x = pos.x - rendered.left;
            let y = pos.y - rendered.top;

            // Calculate lens top-left corner centering on cursor
            x = x - (lensW / 2);
            y = y - (lensH / 2);
            
            // Prevent lens from being positioned outside the purely visible image area
            if (x > rendered.width - lensW) { x = rendered.width - lensW; }
            if (x < 0) { x = 0; }
            if (y > rendered.height - lensH) { y = rendered.height - lensH; }
            if (y < 0) { y = 0; }
            
            // Set lens absolute position relative to container
            lens.style.left = (x + rendered.left) + "px";
            lens.style.top = (y + rendered.top) + "px";
            
            // Panning the background image
            result.style.backgroundPosition = "-" + (x * ZOOM_SCALE) + "px -" + (y * ZOOM_SCALE) + "px";
        }

        function getCursorPos(e) {
            let x = 0, y = 0;
            e = e || window.event;
            const a = img.getBoundingClientRect();
            // Calculate cursor relative to the img element boundary (includes object-fit padding)
            x = e.pageX - a.left - window.scrollX;
            y = e.pageY - a.top - window.scrollY;
            return {x: x, y: y};
        }
    }

    // Initialize Global Interactive Events
    $(document).ready(function () {
        
        // Init Magnifier
        initImageZoom();

        // Same-page lightbox preview on click
        $('#mainProductImageV3').click(function () {
            openLightboxV3($(this).attr('src'));
        });

        // Close lightbox on click outside image
        $('#productLightboxV3').click(function (e) {
            if (e.target.id === 'productLightboxV3') {
                closeLightboxV3();
            }
        });

        // Sticky Mobile Bar Auto-Scroll Offset
        if ($(window).width() < 768) {
            $('body').css('padding-bottom', $('.mobile-sticky-actions').outerHeight());
        }
    });

    function openLightboxV3(imgSrc) {
        $('#lightboxImgV3').attr('src', imgSrc);
        $('#productLightboxV3').fadeIn(200);
        $('body').css('overflow', 'hidden');

        // Reset zoom state on open
        $('#lightboxImgV3').css({
            'transform': 'scale(1)',
            'cursor': 'zoom-in'
        }).data('zoomed', false);
    }



    function closeLightboxV3() {
        $('#productLightboxV3').fadeOut(200);
        $('body').css('overflow', 'auto');
    }

</script>

<style>
    /* Existing Styles */
    /* Refined styles for V3 Premium Layout */
    .badge-soft-success {
        background-color: #dcfce7;
        color: #166534;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .text-gold {
        color: var(--accent-gold);
    }

    .rounded-xl {
        border-radius: 16px;
    }

    .gap-3 {
        gap: 1rem;
    }


    /* Lightbox V3 Styles */
    .lightbox-v3 {
        display: flex;
        align-items: center;
        justify-content: center;
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.95);
    }

    .lightbox-content-wrapper {
        position: relative;
        width: 60%;
        height: 90%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }

    .lightbox-content {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
        cursor: default;
    }

    .close-lightbox {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 50px;
        line-height: 1;
        z-index: 2001;
        cursor: pointer;
        text-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }

    /* Hide zoom elements */
    .zoom-lens,
    .zoom-result {
        display: none !important;
    }

    /* Desktop Only Adjustments */
    @media (min-width: 992px) {
        .product-visuals {
            border-right: 1px solid #f1f5f9;
            padding-right: 40px;
        }
    }

    @media (max-width: 1200px) {
        .zoom-result {
            width: 400px;
            height: 400px;
        }
    }
</style>

<!-- Full Screen Zoom Lightbox -->
<div id="productLightboxV3" class="lightbox-v3">
    <span class="close-lightbox" onclick="closeLightboxV3()">&times;</span>
    <div class="lightbox-content-wrapper" id="lightboxZoomContainer">
        <img id="lightboxImgV3" class="lightbox-content" src="">
        <!-- Amazon Style Zoom Lens & Window (Now inside Lightbox) -->
        <div id="zoomLens" class="zoom-lens d-none d-md-block" style="border-color: white; box-shadow: 0 0 0 9999px rgba(0,0,0,0.5);"></div>
        <div id="zoomWindowResult" class="zoom-window-result d-none d-md-block" style="left: 100%; margin-left: 20px; background-color: #111; border-color: #333;"></div>
    </div>
</div>

<!-- Room Preview Modal -->
<div id="roomPreviewModal" class="room-preview-modal">
    <div class="room-preview-content">
        <div class="room-preview-header">
            <h4><i class="fas fa-couch mr-2"></i> Preview in Your Room</h4>
            <span class="room-preview-close">&times;</span>
        </div>
        <div class="room-preview-body">
            <!-- Upload Section -->
            <div id="uploadSection" class="upload-area">
                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                <h5>Upload a photo of your room</h5>
                <p class="text-muted small mb-4">Take a photo of where you want to place the almirah</p>
                <input type="file" id="roomImageUpload" accept="image/*" style="display: none;">
                <button class="btn btn-preview btn-preview-primary"
                    onclick="document.getElementById('roomImageUpload').click()">
                    Select Room Image
                </button>
            </div>

            <!-- Preview Area -->
            <div id="previewArea" style="display: none;">
                <p class="preview-instructions">
                    <i class="fas fa-info-circle mr-1"></i> Drag to move, use corners to resize or rotate the almirah.
                </p>
                <div class="preview-container">
                    <canvas id="roomCanvas"></canvas>
                </div>
            </div>

            <!-- Controls -->
            <div id="controlsSection" class="preview-controls"
                style="display: none; flex-direction: column; align-items: center;">
                <div class="sensitivity-control"
                    style="width: 100%; max-width: 400px; text-align: center; margin-bottom: 20px;">
                    <label for="bgSensitivity"
                        style="display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 8px;">
                        <i class="fas fa-magic mr-1"></i> Background Removal Sensitivity
                    </label>
                    <input type="range" id="bgSensitivity" min="0" max="100" value="25"
                        style="width: 100%; cursor: pointer; height: 6px; border-radius: 5px; background: #e2e8f0; outline: none; transition: 0.2s; -webkit-appearance: none;">
                </div>
                <div class="d-flex gap-3 justify-content-center w-100" style="gap: 15px;">
                    <button id="downloadPreview" class="btn btn-preview btn-preview-primary">
                        <i class="fas fa-download mr-1"></i> Download
                    </button>
                    <button id="uploadNewImage" class="btn btn-preview btn-preview-secondary">
                        <i class="fas fa-redo mr-1"></i> New Photo
                    </button>
                    <button id="closePreview" class="btn btn-preview btn-preview-secondary">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fabric.js and Room Preview Logic -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
<script src="js/room-preview.js"></script>
<script>
    function openRoomPreview() {
        if (!window.roomPreview) {
            initRoomPreview(<?php echo $pid; ?>, 'img/<?php echo $img1; ?>');
        }
        window.roomPreview.open();
    }

    function openARViewer() {
        const modal = document.getElementById('arViewerModal');
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeARViewer() {
        const modal = document.getElementById('arViewerModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
</script>

<!-- WebAR Viewer Modal -->
<div id="arViewerModal" class="room-preview-modal">
    <div class="room-preview-content" style="max-width: 800px;">
        <div class="room-preview-header">
            <h4><i class="fas fa-vr-cardboard mr-2"></i> 3D & AR Viewer</h4>
            <span class="room-preview-close" onclick="closeARViewer()">&times;</span>
        </div>
        <div class="room-preview-body" style="padding: 0;">
            <div id="arContainer" style="width: 100%; height: 500px; background: #f8fafc;">
                <?php
                // Fixed ID-based GLB pathing
                $glb_path = "models/$pid.glb";
                $model_exists = file_exists($glb_path);
                ?>

                <?php if ($model_exists): ?>
                    <model-viewer src="<?php echo $glb_path; ?>" ar ar-modes="webxr scene-viewer quick-look" camera-controls
                        auto-rotate shadow-intensity="1" alt="A 3D model of <?php echo $title; ?>"
                        style="width: 100%; height: 100%;">
                        <button slot="ar-button" class="btn btn-preview btn-preview-primary"
                            style="position: absolute; bottom: 20px; right: 20px; z-index: 100;">
                            <i class="fas fa-expand mr-2"></i> START AR
                        </button>
                        <div id="ar-prompt"
                            style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.7); color: white; padding: 15px; border-radius: 10px;">
                            Move your phone to detect floor
                        </div>
                    </model-viewer>
                <?php else: ?>
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 p-5 text-center">
                        <i class="fas fa-cube fa-4x text-muted mb-3"></i>
                        <h5>3D Model Not Available</h5>
                        <p class="text-muted small">We are working on adding 3D models for all our products. Please check
                            back later!</p>
                        <div class="mt-3 p-3 bg-light border rounded text-left">
                            <code class="small text-muted">Expected file: <?php echo $glb_path; ?></code>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="preview-controls">
                <?php if ($model_exists): ?>
                    <p class="text-muted small w-100 text-center mb-3">
                        <i class="fas fa-mobile-alt mr-1"></i> Scan the floor with your camera to place the almirah.
                    </p>
                <?php endif; ?>
                <button onclick="closeARViewer()" class="btn btn-preview btn-preview-secondary">Close Viewer</button>
            </div>
        </div>
    </div>
</div>

<script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>

<?php include('include/footer.php'); ?>