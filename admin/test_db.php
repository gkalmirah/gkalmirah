<?php 
require('include/dbcon.php'); 
$pr_query = "SELECT fp.product_id, fp.product_name, fp.product_size, fp.product_price, fp.product_desc, fp.product_img1, GROUP_CONCAT(cat.category SEPARATOR ', ') AS categories 
                                     FROM furniture_product fp 
                                     LEFT JOIN product_categories pc ON fp.product_id = pc.product_id 
                                     LEFT JOIN categories cat ON pc.category_id = cat.id 
                                     GROUP BY fp.product_id 
                                     ORDER BY fp.product_id";
$res = mysqli_query($con, $pr_query); 
if (!$res) {
    echo mysqli_error($con);
} else {
    echo "Query OK. Rows: " . mysqli_num_rows($res);
}
?>
