<?php
session_start();
include('../include/dbcon.php');

if (isset($_POST['query']) && !empty($_POST['query'])) {
    $search = mysqli_real_escape_string($con, $_POST['query']);
    
    $query = "SELECT product_id, product_name, product_price, product_img1 
              FROM furniture_product 
              WHERE product_name LIKE '%$search%' 
              LIMIT 6";
              
    $result = mysqli_query($con, $query);
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $pid = $row['product_id'];
            $name = $row['product_name'];
            $price = $row['product_price'];
            $img = $row['product_img1'];
            
            echo "
            <a href='product-detail.php?product_id=$pid' class='suggestion-item'>
                <img src='img/$img' class='suggestion-img' alt='$name'>
                <div class='suggestion-info'>
                    <span class='suggestion-name'>$name</span>
                    <span class='suggestion-price'>Rs. $price</span>
                </div>
            </a>";
        }
        
        echo "<a href='search-results.php?query=".urlencode($search)."' class='suggestion-item text-center justify-content-center bg-light font-weight-bold'>
                View all results
              </a>";
    } else {
        echo "<div class='p-3 text-center text-muted'>No products found matching '$search'</div>";
    }
}
?>
