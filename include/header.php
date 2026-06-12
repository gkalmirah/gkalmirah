<?php
// session_start();
include('include/dbcon.php');


// Initialize $count to 0 as a default value
$count = 0;
$wishlist_count = 0;

// Check if the user is logged in and if there is a session variable for the cart
if (isset($_SESSION['email'])) {
    $cust_id = $_SESSION['id']; // Assuming user ID is stored in the session

    // Connect to the database
    // include('include/dbcon.php'); // Moved to top

    // Query to count the number of items in the cart for the logged-in user
    $cart_query = "SELECT COUNT(*) AS cart_count FROM cart WHERE cust_id = $cust_id";
    $result = mysqli_query($con, $cart_query);

    // Fetch the result and assign it to $count
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $count = $row['cart_count'];
    } else {
        echo "Error: " . mysqli_error($con);
    }

    $wish_query = "SELECT COUNT(*) AS w_count FROM wishlist WHERE cust_id = $cust_id";
    $w_result = mysqli_query($con, $wish_query);
    if ($w_result) {
        $w_row = mysqli_fetch_assoc($w_result);
        $wishlist_count = $w_row['w_count'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>G.K Almirah</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <!-- Google Fonts: Playfair Display (Serif) & Plus Jakarta Sans (Sans-Serif) -->
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="css/premium.css" rel="stylesheet"> <!-- Premium Styles -->
    <link href="img/logogk1.png" rel="icon">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>

<body>




    <?php
    // Fetch Site Settings
    $settings_q = "SELECT * FROM site_settings";
    $settings_r = mysqli_query($con, $settings_q);
    $site_config = [];
    while ($row = mysqli_fetch_assoc($settings_r)) {
        $site_config[$row['setting_key']] = $row['setting_value'];
    }
    ?>

    <nav class="navbar navbar-expand-md navbar-custom sticky-top">
        <div class="container-fluid">
            <a href="index.php" class="navbar-brand">
                <div class="logo-container">
                    <img src="img/logogk1.png" class="navbar-logo" alt="GK Almirah Logo">
                </div>
                <span class="brand-text">G.K <span class="highlight">Almirah</span></span>
            </a>

            <!-- Navbar Delivery Feature (Beside Logo) -->
            <div class="nav-delivery-widget ml-md-3 d-none d-sm-flex align-items-center">
                <div class="delivery-text ml-2" data-toggle="modal" data-target="#locationModal"
                    style="cursor: pointer;">
                    <small class="d-block text-muted">Deliver to</small>
                    <i class="fas fa-map-marker-alt text-gold mr-1"></i>
                    <strong>
                        <span
                            id="displayLocation"><?php echo isset($_SESSION['pincode']) ? $_SESSION['pincode'] : (isset($_SESSION['city_state']) ? $_SESSION['city_state'] : 'Detecting...'); ?></span>
                    </strong>
                </div>
            </div>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapisblenav"
                aria-controls="collapisblenav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
            </button>

            <!-- Mobile Search Toggle -->
            <button class="btn d-md-none text-dark search-mobile-toggle" id="mobileSearchToggle">
                <i class="fas fa-search"></i>
            </button>

            <!-- Search Bar -->
            <div class="search-container mx-auto d-none d-md-block">
                <form action="search-results.php" method="GET" class="search-form">
                    <div class="search-input-group">
                        <input type="text" name="query" id="headerSearchInput" class="form-control search-input"
                            placeholder="Search products..." autocomplete="off">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div id="searchSuggestions" class="search-suggestions-container"></div>
                </form>
            </div>
            <div class="collapse navbar-collapse" id="collapisblenav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="product.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="about-us.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact-us.php">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="activate-warranty.php">Activate Warranty</a></li>
                    <li class="nav-item"><a class="nav-link" href="distribution.php">Become Distributor</a></li>

                    <?php if (!isset($_SESSION['email'])) { ?>
                        <li class="nav-item"><a class="nav-link" href="sign-in.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php"><button type="button"
                                    class="btn btn-primary btn-sm">Register</button></a></li>
                    <?php } else { ?>
                        <li class="nav-item"><a class="nav-link" href="cust.php"><i class="far fa-user top-icon"></i>
                                Account</a></li>
                    <?php } ?>
                    <li class="nav-item wishlist-nav-item">
                        <a class="nav-link" href="wishlist.php" id="wishlistHeaderToggle">
                            <i class="far fa-heart top-icon text-danger"></i>
                            <span class="badge badge-danger wishlist-badge"><?php echo $wishlist_count; ?></span>
                        </a>
                    </li>
                    <li class="nav-item cart-nav-item">
                        <a class="nav-link" href="javascript:void(0)" id="cartDrawerToggle">
                            <i class="fas fa-shopping-cart top-icon"></i>
                            <span class="badge badge-primary cart-badge"><?php echo $count; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Location Selection Modal -->
    <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content glass-modal">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-weight-bold" id="locationModalLabel">Choose your delivery location
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <button id="detectLocationBtn"
                        class="btn btn-outline-primary btn-block mb-4 d-flex align-items-center justify-content-center">
                        <i class="fas fa-crosshairs mr-2"></i> Detect My Location
                    </button>

                    <div class="pincode-entry mt-3">
                        <p class="mb-2 text-muted small">Enter your Pincode</p>
                        <div class="input-group">
                            <input type="text" id="manualPincode" class="form-control"
                                placeholder="Enter 6 digit pincode" maxlength="6" pattern="\d{6}">
                            <div class="input-group-append">
                                <button id="applyPincodeBtn" class="btn btn-primary px-4">Apply</button>
                            </div>
                        </div>
                        <div id="locationMsg" class="mt-2 small"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </header>
    <!-- Header Script for Mobile Menu and Location -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.navbar-toggler').click(function () {
                $('#collapisblenav').toggleClass('open');
            });

            $(document).click(function (e) {
                var target = $(e.target);
                if (!target.closest('.navbar-collapse').length && !target.closest('.navbar-toggler').length) {
                    $('#collapisblenav').removeClass('open');
                }
            });

            // Location Logic
            const detectBtn = $('#detectLocationBtn');
            const applyBtn = $('#applyPincodeBtn');
            const pincodeInput = $('#manualPincode');
            const locationMsg = $('#locationMsg');
            const displayLocation = $('#displayLocation');

            function updateLocationUI(cityState, pincode = '') {
                displayLocation.text(pincode || cityState);
                localStorage.setItem('userCityState', cityState);
                if (pincode) localStorage.setItem('selectedPincode', pincode);

                $.post('ajax/set-location.php', { pincode: pincode, city_state: cityState }, function () { });
            }

            function fetchCityState(lat, lon, silent = true) {
                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}&addressdetails=1`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.address) {
                            let city = data.address.city || data.address.town || data.address.village || data.address.county || 'Unknown Area';
                            let state = data.address.state || '';
                            let pincode = data.address.postcode || '';

                            if (pincode) formattedLocation = pincode;
                            else if (state) formattedLocation = city + `, ${state}`;
                            else formattedLocation = city;

                            updateLocationUI(formattedLocation, pincode);

                            if (!silent) {
                                $('#locationModal').modal('hide');
                                swal("Location Updated!", `We will show delivery estimation for ${pincode || formattedLocation}.`, "success");
                                detectBtn.html('<i class="fas fa-crosshairs mr-2"></i> Detect My Location');
                            }
                        } else if (silent) {
                            fetchIPLocation();
                        }
                    })
                    .catch(err => {
                        console.error("Reverse Geocoding Error:", err);
                        if (silent) {
                            fetchIPLocation();
                        } else {
                            swal("Error", "Failed to fetch exact location details.", "error");
                            detectBtn.html('<i class="fas fa-crosshairs mr-2"></i> Detect My Location');
                        }
                    });
            }

            function fetchIPLocation() {
                // Fallback to IP-based location silently
                fetch('https://ipapi.co/json/')
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.city) {
                            let city = data.city;
                            let region = data.region || '';
                            let pincode = data.postal || '';

                            let formattedLocation = pincode || city;

                            updateLocationUI(formattedLocation, pincode);
                        } else {
                            displayLocation.text('Select your location');
                        }
                    })
                    .catch(err => {
                        console.error("IP Geolocation Error:", err);
                        displayLocation.text('Select your location');
                    });
            }

            function initBackgroundLocation() {
                let storedLocation = localStorage.getItem('userCityState');

                if (storedLocation) {
                    displayLocation.text(storedLocation);
                } else if (navigator.permissions && navigator.geolocation) {
                    navigator.permissions.query({ name: 'geolocation' }).then(function (result) {
                        if (result.state === 'granted' || result.state === 'prompt') {
                            navigator.geolocation.getCurrentPosition(
                                function (position) {
                                    fetchCityState(position.coords.latitude, position.coords.longitude, true);
                                },
                                function (error) {
                                    console.warn("Background Geolocation skipped or denied:", error.message);
                                    // Fallback to IP if geolocation is explicitly blocked or errors out
                                    fetchIPLocation();
                                },
                                // Set timeout so it doesn't hang if user ignores prompt
                                { timeout: 10000, maximumAge: 60000 }
                            );
                        } else {
                            // Blocked entirely via browser settings, fallback early
                            fetchIPLocation();
                        }
                    });
                } else {
                    // Geolocation not supported, fallback
                    fetchIPLocation();
                }
            }

            // Initialize silent detection
            initBackgroundLocation();

            detectBtn.click(function () {
                if (!navigator.geolocation) {
                    swal("Oops!", "Geolocation is not supported by your browser.", "error");
                    return;
                }

                detectBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Detecting...');

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        fetchCityState(position.coords.latitude, position.coords.longitude, false);
                    },
                    function (error) {
                        console.error(error);
                        swal("Permission Denied", "Please enable location services or enter pincode manually.", "warning");
                        detectBtn.html('<i class="fas fa-crosshairs mr-2"></i> Detect My Location');
                    }
                );
            });

            applyBtn.click(function () {
                const pin = pincodeInput.val();
                if (/^\d{6}$/.test(pin)) {
                    applyBtn.html('<i class="fas fa-spinner fa-spin"></i>');

                    // First try to resolve the City from the inserted pincode (India focused as it's 6 digits)
                    fetch(`https://api.postalpincode.in/pincode/${pin}`)
                        .then(res => res.json())
                        .then(data => {
                            let cityStateString = pin; // Fallback
                            if (data && data[0] && data[0].Status === 'Success' && data[0].PostOffice && data[0].PostOffice.length > 0) {
                                let city = data[0].PostOffice[0].District || data[0].PostOffice[0].Block || data[0].PostOffice[0].Name;
                                let state = data[0].PostOffice[0].State;
                                cityStateString = pin;
                            }

                            $.post('ajax/set-location.php', { pincode: pin, city_state: cityStateString }, function (response) {
                                if (response.success) {
                                    updateLocationUI(cityStateString, pin);
                                    $('#locationModal').modal('hide');
                                    swal("Location Updated!", "We will now show delivery estimation for " + pin, "success");
                                } else {
                                    locationMsg.html('<span class="text-danger">Failed to update location.</span>');
                                }
                                applyBtn.html('Apply');
                            }, 'json');
                        })
                        .catch(err => {
                            console.error("Pincode Lookup Error:", err);
                            // Fallback if API fails: Just use the Pincode directly
                            $.post('ajax/set-location.php', { pincode: pin, city_state: pin }, function (response) {
                                if (response.success) {
                                    updateLocationUI(pin, pin);
                                    $('#locationModal').modal('hide');
                                    swal("Location Updated!", "We will now show delivery estimation for " + pin, "success");
                                }
                                applyBtn.html('Apply');
                            }, 'json');
                        });
                } else {
                    locationMsg.html('<span class="text-danger">Please enter a valid 6-digit pincode.</span>');
                }
            });

            // Search Suggestions Logic
            const searchInput = $('#headerSearchInput');
            const suggestionsContainer = $('#searchSuggestions');

            searchInput.on('input', function () {
                const query = $(this).val().trim();

                if (query.length > 2) {
                    $.ajax({
                        url: 'ajax/search-suggestions.php',
                        method: 'POST',
                        data: { query: query },
                        success: function (data) {
                            suggestionsContainer.html(data).fadeIn();
                        }
                    });
                } else {
                    suggestionsContainer.fadeOut();
                }
            });

            // Hide suggestions when clicking outside
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.search-container').length) {
                    suggestionsContainer.fadeOut();
                }
            });

            searchInput.on('focus', function () {
                if ($(this).val().trim().length > 2) {
                    suggestionsContainer.fadeIn();
                }
            });

            // Mobile Search Toggle
            $('#mobileSearchToggle').on('click', function () {
                $('.search-container').toggleClass('mobile-active').toggle();
                if ($('.search-container').hasClass('mobile-active')) {
                    $('#headerSearchInput').focus();
                }
            });

            // Hide Navbar on Scroll Logic
            let lastScrollTop = 0;
            const navbar = $('.navbar-custom');
            const scrollThreshold = 100;

            $(window).scroll(function () {
                let st = $(this).scrollTop();

                if (Math.abs(lastScrollTop - st) <= 5) return;

                if (st > lastScrollTop && st > scrollThreshold) {
                    // Scroll Down - Hide Navbar
                    navbar.css('transform', 'translateY(-100%)');
                } else {
                    // Scroll Up - Show Navbar
                    navbar.css('transform', 'translateY(0)');
                }
                lastScrollTop = st;
            });
        });
    </script>
    <style>
        .navbar-custom {
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
            will-change: transform;
        }

        .wishlist-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            background: rgba(255, 255, 255, 0.9);
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            color: #666;
            text-decoration: none !important;
        }

        .wishlist-btn:hover {
            transform: scale(1.1);
            color: #e74c3c;
        }

        .wishlist-btn.active {
            color: #e74c3c;
        }

        @keyframes pulse-red {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        .pulse-animation {
            animation: pulse-red 0.4s ease-out;
        }

        .wishlist-badge {
            position: absolute;
            top: 0;
            right: -5px;
            font-size: 0.65em;
            padding: 0.35em 0.5em;
            border-radius: 50%;
            background-color: #e74c3c;
            color: white;
        }

        .wishlist-nav-item .nav-link {
            position: relative;
            display: inline-block;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Premium Cart Side Drawer -->
    <div class="cart-drawer-overlay" id="cartOverlay"></div>
    <div class="cart-drawer" id="cartDrawer">
        <div class="cart-drawer-header d-flex justify-content-between align-items-center p-3 border-bottom">
            <h5 class="mb-0 font-weight-bold">Shopping Bag</h5>
            <button type="button" class="close" id="closeCartDrawer">&times;</button>
        </div>
        <div class="cart-drawer-body p-3" id="cartDrawerContent">
            <!-- Content loaded via AJAX -->
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-gold"></i>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            const cartDrawer = $('#cartDrawer');
            const cartOverlay = $('#cartOverlay');
            const cartBadge = $('.cart-badge');
            const drawerContent = $('#cartDrawerContent');

            function openCartDrawer() {
                cartDrawer.addClass('open');
                cartOverlay.addClass('show');
                $('body').css('overflow', 'hidden');
                fetchCartDrawer();
            }

            function closeCartDrawer() {
                cartDrawer.removeClass('open');
                cartOverlay.removeClass('show');
                $('body').css('overflow', 'auto');
            }

            $('#cartDrawerToggle, #closeCartDrawer, #cartOverlay').on('click', function (e) {
                if (e.target.id === 'cartDrawerToggle' || $(e.target).closest('#cartDrawerToggle').length) {
                    openCartDrawer();
                } else {
                    closeCartDrawer();
                }
            });

            window.fetchCartDrawer = function () {
                const drawerContent = $('#cartDrawerContent');
                drawerContent.html('<div class="text-center py-5"><div class="spinner-border text-gold" role="status"></div></div>');

                $.ajax({
                    url: 'ajax/cart-action.php',
                    method: 'POST',
                    data: { action: 'fetch_drawer' },
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (response) {
                        if (response.success) {
                            drawerContent.html(response.html);
                        } else if (response.message === 'Login Required') {
                            drawerContent.html(`
                                    <div class="text-center py-5">
                                        <i class="fas fa-user-lock fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Please login to view your cart</p>
                                        <a href="sign-in.php" class="btn btn-outline-primary btn-sm mt-2">Sign In</a>
                                    </div>
                                `);
                        } else {
                            drawerContent.html(`<div class="alert alert-danger mx-3">${response.message || 'Error loading cart'}</div>`);
                        }
                    },
                    error: function (xhr, status, error) {
                        let msg = 'Server Error: ' + status;
                        console.error("AJAX Error:", status, error);
                        console.log("Raw Response:", xhr.responseText);
                        if (xhr.responseText) {
                            // Escape HTML to show the raw response safely
                            let safeResponse = $('<div>').text(xhr.responseText).html();
                            msg += '<br><div class="small text-muted border p-1 mt-2" style="max-height:100px; overflow:auto; white-space:pre-wrap;">' + safeResponse + '</div>';
                        }
                        drawerContent.html('<div class="alert alert-danger mx-3">' + msg + '</div>');
                    }
                });
                updateCartCount();
            };

            window.updateCartCount = function () {
                $.ajax({
                    url: 'ajax/cart-action.php',
                    method: 'POST',
                    data: { action: 'fetch_count' },
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (response) {
                        if (response.success) {
                            cartBadge.text(response.count);
                        }
                    }
                });
            };

            window.updateWishlistCount = function () {
                $.ajax({
                    url: 'ajax/wishlist-action.php',
                    method: 'POST',
                    data: { action: 'fetch_count' },
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (response) {
                        if (response.success) {
                            $('.wishlist-badge').text(response.count);
                        }
                    }
                });
            };

            window.addToCart = function (productId) {
                <?php if (!isset($_SESSION['email'])) { ?>
                    swal("Login Required", "Please sign in to add items to your cart.", "info")
                        .then(() => window.location.href = 'sign-in.php');
                    return;
                <?php } ?>

                $.ajax({
                    url: 'ajax/cart-action.php',
                    method: 'POST',
                    data: { action: 'add', product_id: productId },
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (response) {
                        if (response.success) {
                            openCartDrawer(); // Show drawer automatically for premium feedback
                            updateCartCount();
                        } else {
                            swal("Error", response.message || "Could not add product.", "error");
                        }
                    }
                });
            };

            window.buyNow = function (productId) {
                swal({
                    title: "Processing...",
                    text: "Preparing your secure checkout",
                    buttons: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false
                });

                $.ajax({
                    url: 'ajax/buy-now-action.php',
                    method: 'POST',
                    data: { action: 'buynow', product_id: productId, qty: 1 },
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (response) {
                        if (response.status === 'success') {
                            window.location.href = response.redirect;
                        } else {
                            swal("Error", response.message || "Failed to process Buy Now.", "error");
                        }
                    },
                    error: function() {
                        swal("Error", "Network error occurred.", "error");
                    }
                });
            };

            window.removeFromCart = function (productId) {
                $.ajax({
                    url: 'ajax/cart-action.php',
                    method: 'POST',
                    data: { action: 'remove', product_id: productId },
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (response) {
                        if (response.success) {
                            fetchCartDrawer();
                        }
                    }
                });
            };

            window.toggleWishlist = function (productId, element) {
                <?php if (!isset($_SESSION['email'])) { ?>
                    swal("Login Required", "Please sign in to add items to your wishlist.", "info")
                        .then(() => window.location.href = 'sign-in.php');
                    return;
                <?php } ?>

                const icon = $(element).find('i');

                $.ajax({
                    url: 'ajax/wishlist-action.php',
                    method: 'POST',
                    data: { action: 'toggle', product_id: productId },
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (response) {
                        if (response.success) {
                            if (response.action === 'added') {
                                icon.removeClass('far').addClass('fas text-danger pulse-animation');
                                swal({
                                    title: "Saved!",
                                    text: "Product added to your wishlist.",
                                    icon: "success",
                                    timer: 1500,
                                    buttons: false
                                });
                            } else {
                                icon.removeClass('fas text-danger pulse-animation').addClass('far');
                                swal({
                                    title: "Removed!",
                                    text: "Product removed from your wishlist.",
                                    icon: "info",
                                    timer: 1500,
                                    buttons: false
                                });
                            }
                            updateWishlistCount();
                        } else {
                            swal("Error", response.message || "Failed to update wishlist.", "error");
                        }
                    }
                });
            };

            // Initialize animations and geolocation from previous logic
            // (Previous logic remains intact via multi-replace)
        });
    </script>