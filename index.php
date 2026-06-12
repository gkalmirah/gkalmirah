<?php
session_start();
include('include/header.php');
// if(!isset($_SESSION['email'])){
//     header('location: sign-in.php');
// } 

// Database connection is handled in header.php

if (isset($_SESSION['email'])) {
    // Cart logic is now handled via AJAX in ajax/cart-action.php
// JavaScript helper addToCart() is available globally from header.php
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/premium.css" rel="stylesheet"> <!-- Premium Styles -->

    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>G.K Almirah</title>
</head>

<body>

    <div class="hero-section">
        <video autoplay muted loop playsinline class="hero-video-bg hero-parallax" poster="video/hero-fallback.jpg">
            <source src="video/IMG_1786.mov" type="video/quicktime">
            <source src="video/IMG_1786.mov" type="video/mp4">
            <img src="video/hero-fallback.jpg" alt="Premium Steel Almirah" class="hero-parallax">
        </video>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title" data-aos="fade-up" data-aos-duration="1000">Premium Steel Almirahs<br>for Modern
                Living</h1>
            <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="200">Experience the perfect blend of security,
                durability, and elegance.</p>
            <div class="hero-actions" data-aos="fade-up" data-aos-delay="400">
                <a href="#featured-products" class="btn-premium">
                    Explore Collection <i class="fas fa-arrow-right"></i>
                </a>
                <a href="contact-us.php" class="btn-premium-outline">
                    Contact Us
                </a>
            </div>
        </div>
    </div>

    <?php
    $fest_q = "SELECT * FROM festival_campaigns 
               WHERE status = 1 
               AND NOW() BETWEEN start_date AND end_date 
               ORDER BY id DESC LIMIT 1";
    $fest_run = mysqli_query($con, $fest_q);
    if($fest_run && mysqli_num_rows($fest_run) > 0) {
        $festival = mysqli_fetch_assoc($fest_run);
    ?>
    <section class="festival-banner-section mt-4 mb-2">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center" data-aos="fade-up">
                    <a href="#featured-products">
                        <img src="img/<?php echo htmlspecialchars($festival['banner_image']); ?>" class="img-fluid rounded shadow w-100" style="max-height: 400px; object-fit: cover;" alt="<?php echo htmlspecialchars($festival['festival_name']); ?>">
                    </a>
                    <?php if(!empty($festival['description'])) { ?>
                        <div class="mt-3 p-3 bg-white rounded shadow-sm border-left border-danger" style="border-width: 4px !important;">
                            <h4 class="text-danger font-weight-bold mb-1"><i class="fad fa-gift mr-2"></i> <?php echo htmlspecialchars($festival['festival_name']); ?></h4>
                            <p class="text-muted mb-0 font-weight-bold"><?php echo htmlspecialchars($festival['description']); ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
    <?php } ?>

    <!-- Trust & Authority Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-4 h-100">
                        <i class="fas fa-shield-alt fa-3x text-gold mb-3"></i>
                        <h5 class="font-weight-bold">Durable Steel</h5>
                        <p class="text-muted small">Crafted from high-grade industrial steel for maximum longevity.</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-4 h-100">
                        <i class="fas fa-lock fa-3x text-gold mb-3"></i>
                        <h5 class="font-weight-bold">Secure Locking</h5>
                        <p class="text-muted small">Advanced locking mechanisms to keep your valuables safe.</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="p-4 h-100">
                        <i class="fas fa-paint-brush fa-3x text-gold mb-3"></i>
                        <h5 class="font-weight-bold">Modern Design</h5>
                        <p class="text-muted small">Sleek aesthetics that complement contemporary interiors.</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="p-4 h-100">
                        <i class="fas fa-certificate fa-3x text-gold mb-3"></i>
                        <h5 class="font-weight-bold">10-Year Warranty</h5>
                        <p class="text-muted small">Guaranteed quality and service support for a decade.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var carouselItems = document.querySelectorAll('.carousel-item');

            // Define the animation classes
            var animations = ['fade-in', 'slide-in-left', 'slide-in-right', 'zoom-in'];

            function setAnimation(item, animation) {
                item.classList.remove(...animations); // Remove any existing animations
                item.classList.add(animation);
            }

            // Initially apply animations to the active item
            var activeItem = document.querySelector('.carousel-item.active');
            setAnimation(activeItem, 'fade-in');

            // Add event listener for when the carousel slides
            $('#slider').on('slide.bs.carousel', function (e) {
                var nextIndex = $(e.relatedTarget).index();
                var nextItem = carouselItems[nextIndex];

                // Randomly select an animation for each slide
                var animation = animations[nextIndex % animations.length];
                setAnimation(nextItem, animation);
            });
        });
    </script>

    <!-- Shop By Category Section (Triveni Style) -->
    <section class="section-padding bg-light" data-aos="fade-up">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="font-weight-bold display-4">Shop By <span class="text-gradient">Category</span></h2>
                <p class="text-muted">Explore our wide range of premium steel almirahs designed for every need.</p>
            </div>
            <div class="row">
                <?php
                // Fetch Categories Dynamically
                $cat_q = "SELECT * FROM categories ORDER BY id ASC LIMIT 8";
                $cat_r = mysqli_query($con, $cat_q);

                $delay = 100;
                if (mysqli_num_rows($cat_r) > 0) {
                    while ($row = mysqli_fetch_assoc($cat_r)) {
                        $cid = $row['id'];
                        $cname = $row['category'];
                        
                        // Dynamically fetch a real product image from the database for this category
                        $img_query = "SELECT p.product_img1 FROM furniture_product p JOIN product_categories pc ON p.product_id = pc.product_id WHERE pc.category_id = $cid AND p.product_img1 IS NOT NULL AND p.product_img1 != '' LIMIT 1";
                        $img_run = mysqli_query($con, $img_query);
                        $cat_img = "img/hero-fallback.jpg"; // Default fallback
                        if ($img_run && mysqli_num_rows($img_run) > 0) {
                            $img_row = mysqli_fetch_assoc($img_run);
                            $cat_img = "img/" . $img_row['product_img1'];
                        }

                        ?>
                        <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                            <a href="product.php?cat_id=<?php echo $cid; ?>" class="category-card-premium">
                                <img src="<?php echo $cat_img; ?>" alt="<?php echo $cname; ?>"
                                    onerror="this.src='img/hero-fallback.jpg'">
                                <div class="category-overlay">
                                    <h3 class="cat-title-premium"><?php echo $cname; ?></h3>
                                    <span class="btn-cat-explore">Explore Collection <i
                                            class="fas fa-arrow-right ml-1"></i></span>
                                </div>
                            </a>
                        </div>
                        <?php
                        $delay += 100;
                    }
                }
                ?>
            </div>
        </div>
    </section>

    <section class="section-padding" data-aos="fade-up">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="font-weight-bold display-4">Latest <span class="text-gradient">Arrivals</span></h2>
                <p class="text-muted">Discover our newest additions to the premium collection.</p>
            </div>
            <div class="row">
                <?php
                $p_query = "SELECT * FROM furniture_product ORDER BY product_id DESC LIMIT 4";
                $p_run = mysqli_query($con, $p_query);

                $p_delay = 100;
                if ($p_run && mysqli_num_rows($p_run) > 0) {
                    while ($p_row = mysqli_fetch_array($p_run)) {
                        $pid = $p_row['product_id'];
                        $ptitle = $p_row['product_name'];
                        $p_price = floatval($p_row['product_price']);
                        $discount = get_active_discount($pid, $p_price, $con);
                        $img1 = $p_row['product_img1'];
                        $whatsapp_number = '9682021084';

                        // Wishlist check
                        $is_wishlisted = false;
                        if (isset($_SESSION['id'])) {
                            $wid = $_SESSION['id'];
                            $wish_check = mysqli_query($con, "SELECT 1 FROM wishlist WHERE cust_id = $wid AND product_id = $pid");
                            if ($wish_check && mysqli_num_rows($wish_check) > 0)
                                $is_wishlisted = true;
                        }
                        ?>
                        <div class="col-lg-3 col-md-6 mb-5" data-aos="fade-up" data-aos-delay="<?php echo $p_delay; ?>">
                            <div class="product-card-premium h-100">
                                <div class="product-img-wrapper-premium">
                                    <?php if($discount['has_discount']) { ?>
                                        <div class="badge-new bg-danger" style="z-index: 10;"><?php echo $discount['badge_text']; ?></div>
                                    <?php } else { ?>
                                        <div class="badge-new">NEW</div>
                                    <?php } ?>
                                    <a href="product-detail.php?product_id=<?php echo $pid; ?>">
                                        <img src="img/<?php echo $img1; ?>" class="product-img-premium"
                                            alt="<?php echo $ptitle; ?>" onerror="this.src='img/hero-fallback.jpg'">
                                    </a>
                                    <div class="quick-actions-overlay">
                                        <a href="javascript:void(0)" class="btn-quick-action" title="Add to Bag"
                                            onclick="addToCart(<?php echo $pid; ?>)">
                                            <i class="fas fa-shopping-bag"></i>
                                        </a>
                                        <a href="product-detail.php?product_id=<?php echo $pid; ?>" class="btn-quick-action"
                                            title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="javascript:void(0)"
                                            class="btn-quick-action <?php echo $is_wishlisted ? 'text-danger' : ''; ?>"
                                            title="Wishlist" onclick="toggleWishlist(<?php echo $pid; ?>, this)">
                                            <i class="<?php echo $is_wishlisted ? 'fas' : 'far'; ?> fa-heart"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="product-info-premium">
                                    <h5 class="product-title-premium" title="<?php echo $ptitle; ?>"><?php echo $ptitle; ?></h5>
                                    <div class="product-price-premium">
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
                        $p_delay += 100;
                    }
                } else {
                    echo "<div class='col-12 text-center'><h3>No Products Available Yet</h3></div>";
                }
                ?>
            </div>
        </div>
    </section>

    <section class="section-padding bg-light" data-aos="fade-up">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="font-weight-bold display-4">Best <span class="text-gradient">Sellers</span></h2>
                <p class="text-muted">Our most loved almirahs, chosen by you.</p>
            </div>
            <div class="row">
                <?php
                $p_query = "
                SELECT DISTINCT p.product_id, p.product_name, p.product_desc, p.product_price, p.product_size, p.product_img1
                FROM furniture_product p
                JOIN product_categories pc ON p.product_id = pc.product_id
                WHERE pc.category_id = 5
                GROUP BY p.product_id
                ORDER BY p.product_id DESC
                LIMIT 4
            ";
                $p_run = mysqli_query($con, $p_query);

                $bs_delay = 100;
                if ($p_run && mysqli_num_rows($p_run) > 0) {
                    while ($p_row = mysqli_fetch_array($p_run)) {
                        $pid = $p_row['product_id'];
                        $ptitle = $p_row['product_name'];
                        $p_price = floatval($p_row['product_price']);
                        $discount = get_active_discount($pid, $p_price, $con);
                        $img1 = $p_row['product_img1'];
                        ?>
                        <div class="col-lg-3 col-md-6 mb-5" data-aos="fade-up" data-aos-delay="<?php echo $bs_delay; ?>">
                            <div class="product-card-premium h-100">
                                <div class="product-img-wrapper-premium">
                                    <?php if($discount['has_discount']) { ?>
                                        <div class="badge-new bg-danger" style="z-index: 10;"><?php echo $discount['badge_text']; ?></div>
                                    <?php } else { ?>
                                        <div class="badge-new" style="background: var(--accent-gold);">BEST SELLER</div>
                                    <?php } ?>
                                    <?php
                                    $is_wishlisted = false;
                                    if (isset($_SESSION['id'])) {
                                        $wid = $_SESSION['id'];
                                        $wish_check = mysqli_query($con, "SELECT 1 FROM wishlist WHERE cust_id = $wid AND product_id = $pid");
                                        if ($wish_check && mysqli_num_rows($wish_check) > 0)
                                            $is_wishlisted = true;
                                    }
                                    ?>
                                    <a href="product-detail.php?product_id=<?php echo $pid; ?>">
                                        <img src="img/<?php echo $img1; ?>" class="product-img-premium"
                                            alt="<?php echo $ptitle; ?>" onerror="this.src='img/hero-fallback.jpg'">
                                    </a>
                                    <div class="quick-actions-overlay">
                                        <a href="javascript:void(0)" class="btn-quick-action" title="Add to Bag"
                                            onclick="addToCart(<?php echo $pid; ?>)">
                                            <i class="fas fa-shopping-bag"></i>
                                        </a>
                                        <a href="product-detail.php?product_id=<?php echo $pid; ?>" class="btn-quick-action"
                                            title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="javascript:void(0)"
                                            class="btn-quick-action <?php echo $is_wishlisted ? 'text-danger' : ''; ?>"
                                            title="Wishlist" onclick="toggleWishlist(<?php echo $pid; ?>, this)">
                                            <i class="<?php echo $is_wishlisted ? 'fas' : 'far'; ?> fa-heart"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="product-info-premium">
                                    <h5 class="product-title-premium" title="<?php echo $ptitle; ?>"><?php echo $ptitle; ?></h5>
                                    <div class="product-price-premium">
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
                        $bs_delay += 100;
                    }
                } else {
                    echo "<h3 class='text-center'> No Product Available Yet </h3>";
                }
                ?>
            </div>
        </div>
    </section>


    <section class="cust-seller-section" data-aos="fade-up">
        <div class="gallery-contain">
            <div class="text-bann"><strong>Get Your Products customized</strong><br>Your Product Your Choice</div>

            <?php
            $p_query = "
            SELECT DISTINCT p.product_id, p.product_name, p.product_desc, p.product_price, p.product_size, p.product_img1, p.product_img2, p.product_img3 
            FROM furniture_product p
            JOIN product_categories pc ON p.product_id = pc.product_id
            WHERE pc.category_id = 4
            GROUP BY p.product_id
            ORDER BY p.product_id DESC
            LIMIT 3
        ";
            $p_run = mysqli_query($con, $p_query);

            if ($p_run && mysqli_num_rows($p_run) > 0) {
                while ($p_row = mysqli_fetch_array($p_run)) {
                    $pid = $p_row['product_id'];
                    $ptitle = $p_row['product_name'];
                    $img1 = $p_row['product_img1'];
                    $img2 = $p_row['product_img2'];
                    ?>

                    <div class="gallery-items">
                        <a href="product-detail.php?product_id=<?php echo $pid; ?>">
                            <div class="img-container">
                                <img src="img/<?php echo $img1; ?>" alt="<?php echo $ptitle; ?>" class="img-left">

                            </div>
                        </a>
                        <div class="category-titles text-truncate"><?php echo $ptitle; ?></div>
                    </div>
                <?php
                }
            } else {
                echo "<h3 class='text-center'> No Product Available Yet </h3>";
            }
            ?>
        </div>
    </section>

    <style>
        .cust-seller-section {
            width: 100%;
            background-color: #bc987e;
            padding: 20px 0;
            color: #fff;
        }

        .gallery-contain {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-wrap: wrap;
            padding: 10px;
            gap: 10px;
        }

        .text-bann {
            flex: 1;
            text-align: center;
            font-size: 3rem;
            color: #fff;
            margin-right: 5px;
            max-width: 70%;
        }

        .gallery-items {
            flex: 1;
            background: #fff;
            border: 1px solid #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, .1);
            transition: transform .2s ease-in-out;
            padding: 10px;
            text-align: center;
            max-width: 250px;
            margin-left: 5px;
        }

        .img-container {
            display: flex;
            /* justify-content: space-between; */
            /* gap: 10px; */
            max-width: 280px;
            margin: 0 auto;
        }

        .img-container img {
            max-width: 100%;
            height: 50%;
            display: block;
            object-fit: contain;
        }

        .category-titles {
            width: 100%;
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: .25em;
            font-size: 1rem;
            margin-top: 10px;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .text-bann {
                font-size: 2.5rem;
            }

            .gallery-contain {
                flex-direction: column;
                /* Stack the text and image vertically on small screens */
                align-items: center;
            }

            .text-bann {
                text-align: center;
                margin-right: 0;
                margin-bottom: 20px;
            }

            .gallery-items {
                width: 100%;
                /* Full width on mobile */
            }
        }

        @media (max-width: 576px) {
            .text-bann {
                font-size: 2rem;
            }

            .img-container {
                flex-direction: column;
                /* Stack images on top of each other on small screens */
            }
        }
    </style>
    <style>
        .best-seller-section {
            width: 100%;
            background-color: #000;
            padding: 20px 0;
            color: #fff;
        }


        .gallery-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .text-banner {
            flex-basis: 100%;
            text-align: center;
            font-size: 2.5rem;
            color: #fff;
            margin-bottom: 40px;
        }

        .gallery-item {
            flex-basis: calc(33.333% - 20px);
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform .3s ease, box-shadow .3s ease;
            cursor: pointer;
            text-align: center;
        }

        .gallery-item:hover {
            transform: translateY(-10px);
            box-shadow: 0px 16px 32px rgba(0, 0, 0, 0.2);
        }

        .img-wrapper {
            position: relative;
            width: 100%;
            height: 70%;
            /* padding-top: 60%;  */
            /* padding-left: 30%;  */
            background-color: #f8f8f8;
            /* Fallback background color */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .img-wrapper img {
            position: relative;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            height: 50%;
            /* margin: auto; */
            object-fit: contain;
            transition: transform .3s ease;
        }

        .gallery-item:hover .img-wrapper img {
            transform: scale(1.05);
        }

        .category-title {
            width: 100%;
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: 10px;
            font-size: 1rem;
            letter-spacing: 1px;
            transition: background-color .3s ease;
        }

        .gallery-link {
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
        }

        .gallery-item:hover .category-title {
            background-color: #444;
        }
    </style>

    <section class="back-peach pt-4" data-aos="fade-up">
        <div class="container">
            <div class="section-heading">
                <h1>Features of Our <span class="highlight">Products</span></h1>
            </div>

            <div class="row mt-4">

                <div class="col-md-4 d-flex justify-content-center mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card feature-card hover-effect h-100">
                        <img src="img/powder-coated.jpg" class="card-img-tops" alt="Powder Coated">
                        <div class="card-body">
                            <h4 class="text-center">Powder Coated</h4>
                            <p class="text-center">100% powder coated steel wardrobes which is fully rust-resistant</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-flex justify-content-center mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card feature-card hover-effect h-100">
                        <img src="img/machine.jpeg" class="card-img-tops" alt="CNC Machines">
                        <div class="card-body">
                            <h4 class="text-center">CNC Machines</h4>
                            <p class="text-center">Product is ready by CNC machines (bending and shearing machines,
                                etc).</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-flex justify-content-center mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card feature-card hover-effect h-100">
                        <img src="img/steel.jpg" class="card-img-tops" alt="Rustproof and Stainless Steel">
                        <div class="card-body">
                            <h4 class="text-center">Rustproof and Stainless Steel</h4>
                            <p class="text-center">Stainless CR sheet of top brand like TATA STEEL,BHUSHAN STEEL is used
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-flex justify-content-center mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card feature-card hover-effect h-100">
                        <img src="img/warranty.jpg" class="card-img-tops" alt="Warranty up to 10 Years">
                        <div class="card-body">
                            <h4 class="text-center">Warranty up to 10 Years</h4>
                            <p class="text-center">Paint & Lockers have a warranty of up to 10 years.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-flex justify-content-center mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card feature-card hover-effect h-100">
                        <img src="img/satisfaction.jpg" class="card-img-tops" alt="Client Satisfaction">
                        <div class="card-body">
                            <h4 class="text-center">Client Satisfaction</h4>
                            <p class="text-center">High quality at an affordable price.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 d-flex justify-content-center mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card feature-card hover-effect h-100">
                        <img src="img/time.jpg" class="card-img-tops" alt="Given Time">
                        <div class="card-body">
                            <h4 class="text-center">Given Time</h4>
                            <p class="text-center">We provide our service within the given time.</p>
                        </div>
                    </div>
                </div>







            </div>
        </div>
    </section>



    <section class="back-gray pt-4 pb-4" data-aos="fade-up">
        <div class="container">
            <div class="section-heading text-center mb-4" data-aos="fade-up">
                <h2>How It <span class="highlight">Works</span></h2>
            </div>
            <div class="row">
                <!-- Card 1 -->
                <div class="col-sm-6 col-md-3 p-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="card hover-effect equal-card h-100">
                        <div class="card-body mt-3 text-center">
                            <i class="fas fa-shopping-bag fa-3x card-icon"></i>
                            <div class="heading mt-2">
                                <h4>Product</h4>
                                <h6 class="text-secondary">Choose your own product</h6>
                            </div>
                            <p class="mt-2">Add product to cart. Enter your shipping address, then checkout.</p>
                        </div>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="col-sm-6 col-md-3 p-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="card hover-effect equal-card h-100">
                        <div class="card-body mt-3 text-center">
                            <i class="fas fa-thumbs-up fa-3x card-icon"></i>
                            <div class="heading mt-2">
                                <h4>Receive</h4>
                                <h6 class="text-secondary">Receive Your Product</h6>
                            </div>
                            <p class="mt-2">Your product will be delivered to you within 7 working days.</p>
                        </div>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="col-sm-6 col-md-3 p-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="card hover-effect equal-card h-100">
                        <div class="card-body mt-3 text-center">
                            <i class="fas fa-cogs fa-3x card-icon"></i>
                            <div class="heading mt-2">
                                <h4>Product Customization</h4>
                                <h6 class="text-secondary">Customize Your Product</h6>
                            </div>
                            <p class="mt-2">Get your product customized according to your preference.</p>
                        </div>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="col-sm-6 col-md-3 p-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="card hover-effect equal-card h-100">
                        <div class="card-body mt-3 text-center">
                            <i class="fas fa-wallet fa-3x card-icon"></i>
                            <div class="heading mt-2">
                                <h4>Cash</h4>
                                <h6 class="text-secondary">Cash on Delivery</h6>
                            </div>
                            <p class="mt-2">On delivery of your product, pay cash at the moment of receipt.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Testimonials Section (Centered E-Commerce Style) -->
    <section class="py-4 bg-light" data-aos="fade-up">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="font-weight-bold display-4">Client <span class="text-gradient">Stories</span></h2>
                <p class="text-muted">Trusted by businesses and homeowners across the nation.</p>
            </div>

            <div id="testimonialSlider" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row">
                            <!-- Card 1 -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="testimonial-card-amazon">
                                    <p class="testimonial-text-amazon">"The quality of the Almirah is excellent. I'm
                                        very satisfied with the response and the prompt delivery. Highly recommended for
                                        home use."</p>
                                    <div class="text-warning small mb-3"><i class="fas fa-star"></i><i
                                            class="fas fa-star"></i><i class="fas fa-star"></i><i
                                            class="fas fa-star"></i><i class="fas fa-star"></i></div>
                                    <div class="client-details">
                                        <h5 class="mb-1">Rakakant</h5>
                                        <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified
                                            Purchase</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2 -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="testimonial-card-amazon">
                                    <p class="testimonial-text-amazon">"Very good quality Almirah & Wardrobe. The
                                        material used is premium and the finish is very smooth. Will definitely buy
                                        again."</p>
                                    <div class="text-warning small mb-3"><i class="fas fa-star"></i><i
                                            class="fas fa-star"></i><i class="fas fa-star"></i><i
                                            class="fas fa-star"></i><i class="fas fa-star"></i></div>
                                    <div class="client-details">
                                        <h5 class="mb-1">Bhagya</h5>
                                        <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified
                                            Purchase</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3 -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="testimonial-card-amazon">
                                    <p class="testimonial-text-amazon">"Bought an Iron Almirah recently. The build
                                        quality is strong and the locker system is very secure. Best prices in the
                                        area."</p>
                                    <div class="text-warning small mb-3"><i class="fas fa-star"></i><i
                                            class="fas fa-star"></i><i class="fas fa-star"></i><i
                                            class="fas fa-star"></i><i class="fas fa-star"></i></div>
                                    <div class="client-details">
                                        <h5 class="mb-1">Abhishek Gupta</h5>
                                        <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified
                                            Purchase</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2 (Duplicate for Demo) -->
                    <div class="carousel-item">
                        <div class="row">
                            <!-- Card 4 -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="testimonial-card-amazon">
                                    <p class="testimonial-text-amazon">"Sturdy and stylish. Fits perfectly in my
                                        bedroom. The delivery team was also very professional."</p>
                                    <div class="text-warning small mb-3"><i class="fas fa-star"></i><i
                                            class="fas fa-star"></i><i class="fas fa-star"></i><i
                                            class="fas fa-star"></i><i class="fas fa-star"></i></div>
                                    <div class="client-details">
                                        <h5 class="mb-1">Meera R.</h5>
                                        <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified
                                            Purchase</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 5 -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="testimonial-card-amazon">
                                    <p class="testimonial-text-amazon">"Excellent value for money. The locker mechanism
                                        is smooth and feels very secure. Highly recommended."</p>
                                    <div class="text-warning small mb-3"><i class="fas fa-star"></i><i
                                            class="fas fa-star"></i><i class="fas fa-star"></i><i
                                            class="fas fa-star"></i><i class="fas fa-star"></i></div>
                                    <div class="client-details">
                                        <h5 class="mb-1">Rajesh Kumar</h5>
                                        <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified
                                            Purchase</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 6 -->
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="testimonial-card-amazon">
                                    <p class="testimonial-text-amazon">"Beautiful finish. It looks exactly like the
                                        website pictures. Very happy with my purchase from GK Almirah."</p>
                                    <div class="text-warning small mb-3"><i class="fas fa-star"></i><i
                                            class="fas fa-star"></i><i class="fas fa-star"></i><i
                                            class="fas fa-star"></i><i class="fas fa-star"></i></div>
                                    <div class="client-details">
                                        <h5 class="mb-1">Sita Verma</h5>
                                        <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified
                                            Purchase</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <a class="carousel-control-prev" href="#testimonialSlider" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
                </a>
                <a class="carousel-control-next" href="#testimonialSlider" role="button" data-slide="next">
                    <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
                </a>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section section-padding" data-aos="fade-up">
        <div class="container text-center">
            <h2 class="mb-4">Get Business Updates</h2>
            <p class="mb-5">Subscribe to our newsletter for exclusive offers, new product launches, and expert interior
                tips.</p>
            <form class="d-flex justify-content-center flex-wrap gap-3">
                <input type="email" class="newsletter-input mr-3 mb-3" placeholder="Enter your business email">
                <button type="submit" class="btn btn-custom mb-3">Subscribe Now</button>
            </form>
        </div>
    </section>

    <!-- Floating Actions -->
    <?php
    $wa_global_msg = urlencode("Hello G.K Almirah Team,\n\nI am interested in exploring your collection of premium steel almirahs. Could you please assist me?\n\nThank you.");
    ?>
    <a href="https://wa.me/9682021084?text=<?php echo $wa_global_msg; ?>" class="floating-whatsapp floating"
        target="_blank" title="Chat with us">
        <i class="fab fa-whatsapp"></i>
    </a>

    <a href="#" class="back-to-top" id="backToTop" title="Back to Top">
        <i class="fas fa-chevron-up"></i>
    </a>


    <?php
    include('include/footer.php');
    ?>

    <!-- Include jQuery, Popper.js, and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Back to top button visibility
        window.addEventListener('scroll', function () {
            var backToTop = document.getElementById('backToTop');
            if (window.pageYOffset > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        });

        // Smooth scroll for back to top
        document.getElementById('backToTop').addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>

</body>

</html>
