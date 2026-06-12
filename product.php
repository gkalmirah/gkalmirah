<?php 
session_start();
include('include/header.php');

// Database connection is handled in header.php

// Determine the current page
if (isset($_GET['page'])) {
    $page_id = $_GET['page'];
} else {
    $page_id = 1;
}

// Define the number of products to display per page
$required_pro = 30;
$product_start = ($page_id - 1) * $required_pro;

if (isset($_SESSION['id'])) {
    $custid = $_SESSION['id'];

    if (isset($_GET['cart_id'])) {
        $p_id = $_GET['cart_id'];

        $sel_cart = "SELECT * FROM cart WHERE cust_id = $custid and product_id = $p_id";
        $run_cart = mysqli_query($con, $sel_cart);

        if (mysqli_num_rows($run_cart) == 0) {
            $cart_query = "INSERT INTO `cart`(`cust_id`, `product_id`, quantity) VALUES ($custid, $p_id, 1)";
            if (mysqli_query($con, $cart_query)) {
                header("location:product.php");
            }
        } else {
            while ($row = mysqli_fetch_array($run_cart)) {
                $exist_pro_id = $row['product_id'];
                if ($p_id == $exist_pro_id) {
                    $error = "<script>alert('⚠️ This product is already in your cart');</script>";
                }
            }
        }
    }
} else if (!isset($_SESSION['email'])) {
    echo "<script> function a(){alert('⚠️ Login is required to add this product into cart');}</script>";
}

// Define the phone number for WhatsApp inquiries
$whatsapp_number = '9682021084'; // Replace with the actual phone number
?>

<!-- <div class="jumbotron">
    <h2 class="text-center mt-4">Choose Products</h2>
</div> -->

<div class="jumbotron jumbotron-custom text-white">
    <div class="container text-center">
        <h2 class="display-4 my-5">Explore Our Exclusive Products</h2>
        <p class="lead">Find the best Almirahs for your home and office</p>
    </div>
</div>



<div class="container mt-5">
    <div class="row">
        <!-- Filter Sidebar -->
        <div class="col-md-3 col-12">
            <button class="btn btn-primary filter-mobile-btn mb-3 d-md-none" id="toggleFilters">
                <i class="fas fa-filter"></i> Filters
            </button>
            <div class="filter-sidebar" id="filterSidebar">
                <div class="filter-title d-md-none">
                    <span>Filters</span>
                    <button type="button" class="close" id="closeFilters"><span>&times;</span></button>
                </div>
                
                <!-- Category Filter -->
                <div class="filter-group">
                    <div class="filter-title">Categories</div>
                    <div class="filter-options">
                        <?php  
                        $cat_query = "SELECT * FROM categories ORDER BY id ASC";
                        $cat_run = mysqli_query($con, $cat_query);
                        while ($cat_row = mysqli_fetch_array($cat_run)) {
                            $cid = $cat_row['id'];
                            $cat_name = ucfirst($cat_row['category']);
                            $selected = (isset($_GET['cat_id']) && $_GET['cat_id'] == $cid) ? 'checked' : '';
                            echo "
                            <label class='custom-checkbox'>
                                $cat_name
                                <input type='checkbox' class='cat-filter' value='$cid' $selected>
                                <span class='checkmark'></span>
                            </label>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Price Filter -->
                <div class="filter-group">
                    <div class="filter-title">Price Range</div>
                    <div class="price-range-inputs">
                        <input type="number" id="minPrice" class="form-control" placeholder="Min" value="0">
                        <input type="number" id="maxPrice" class="form-control" placeholder="Max" value="150000">
                    </div>
                </div>

                <!-- Availability -->
                <div class="filter-group">
                    <div class="filter-title">Availability</div>
                    <div class="filter-options">
                        <label class="custom-checkbox">
                            In Stock
                            <input type="checkbox" id="inStockOnly" value="in_stock" checked>
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>

                <div class="mt-4">
                    <button class="btn btn-primary btn-block" id="applyFilters">Apply Filters</button>
                    <button class="btn btn-outline-secondary btn-block mt-2" id="resetFilters">Reset</button>
                </div>
            </div>
        </div>

        <!-- Product Display Area -->
        <div class="col-md-9 col-12">
            <div class="row mb-4 align-items-center">
                <div class="col-md-6">
                    <h3 class="font-weight-bold" id="productCountTitle">Products</h3>
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-end">
                    <span class="mr-2 d-none d-md-block">Sort by:</span>
                    <select class="form-control w-50" id="sortProducts">
                        <option value="newest">Newest First</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                    </select>
                </div>
            </div>

            <div id="loadingSpinner" class="text-center py-5 d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>

            <div class="row" id="productDisplay">
                <!-- Products will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function fetchProducts() {
        $('#productDisplay').css('opacity', '0.5');
        $('#loadingSpinner').removeClass('d-none');
        
        const categories = [];
        $('.cat-filter:checked').each(function() {
            categories.push($(this).val());
        });

        const formData = {
            categories: categories,
            min_price: $('#minPrice').val(),
            max_price: $('#maxPrice').val(),
            availability: $('#inStockOnly:checked').val(),
            sort: $('#sortProducts').val()
        };

        $.ajax({
            url: 'ajax/fetch-products.php',
            method: 'POST',
            data: formData,
            success: function(data) {
                $('#productDisplay').html(data).css('opacity', '1');
                $('#loadingSpinner').addClass('d-none');
                
                // Update product count
                const count = $('#productDisplay .product-card').length;
                $('#productCountTitle').text(count + ' Products Found');
            }
        });
    }

    // Initial load
    fetchProducts();

    $('#applyFilters, #sortProducts').on('click change', function() {
        fetchProducts();
        if ($(window).width() < 768) {
            $('#filterSidebar').removeClass('active');
        }
    });

    $('#resetFilters').on('click', function() {
        $('.cat-filter').prop('checked', false);
        $('#minPrice').val(0);
        $('#maxPrice').val(150000);
        $('#inStockOnly').prop('checked', true);
        $('#sortProducts').val('newest');
        fetchProducts();
        if ($(window).width() < 768) {
            $('#filterSidebar').removeClass('active');
        }
    });

    // Mobile menu toggles
    $('#toggleFilters').on('click', function() {
        $('#filterSidebar').addClass('active');
    });

    $('#closeFilters').on('click', function() {
        $('#filterSidebar').removeClass('active');
    });
});
</script>



<?php include('include/footer.php'); ?>

<!-- Additional CSS for styling -->
<style>
    /* Primary Color Scheme */
    :root {
        --primary-color: #28a745; /* Example primary color */
        --secondary-color: #f8f9fa; /* Light background */
        --text-color: #333;
        --button-hover-color: #218838;
    }

    /* General Styling */
    body {
        font-family: 'Roboto', sans-serif;
        color: var(--text-color);
        background-color: var(--secondary-color);
    }

    /* Product Card */
    .product-card {
        border: 1px solid #ddd;
        border-radius: 5px;
        overflow: hidden;
        transition: transform 0.3s ease;
        margin-bottom: 20px;
    }

    .product-card:hover {
        transform: scale(1.05);
    }

    /* Buttons */
    .btn-primary {
        background-color: var(--primary-color);
        border: none;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: var(--button-hover-color);
    }

    .btn-default {
        background-color: transparent;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
    }

    .btn-default:hover {
        background-color: var(--primary-color);
        color: #fff;
    }

    /* Product Image */
    .product-img {
        height: 190px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-img:hover {
        transform: scale(1.1);
    }

    /* Pagination */
    .pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    /* Tooltip */
    .tooltip {
        font-size: 14px;
        background-color: var(--primary-color);
        color: #fff;
    }

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

    .list-group-custom {
        border: none;
        padding: 0;
    }

    .list-group-item-custom {
        border: none;
        padding: 15px 20px;
        border-radius: 5px;
        margin-bottom: 10px;
        background-color: var(--secondary-color);
        transition: background-color 0.3s ease, color 0.3s ease;
        display: flex;
        align-items: center;
    }

    .list-group-item-custom i {
        margin-right: 10px;
        font-size: 1.2rem;
    }

    .list-group-item-custom:hover, 
    .list-group-item-custom.active {
        background-color: var(--primary-color);
        color: white;
    }

    .list-group-item-custom:hover i,
    .list-group-item-custom.active i {
        color: white;
    }

    @media (max-width: 767px) {
    .product-info {
        min-height: 100px; /* Adjust this value for smaller screens */
    }
}
</style>

<!-- Additional JS for functionalities -->
<script>
    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Tooltip initialization
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    
