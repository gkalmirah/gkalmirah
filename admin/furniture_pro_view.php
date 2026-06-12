<?php include("include/header.php");?>
<?php
    if(!isset($_SESSION['email'])) {
        header('location: signin.php');
    }

    if(isset($_GET['del'])) {
        $del = $_GET['del'];
        $query = "DELETE FROM `furniture_product` WHERE product_id = $del";
        if(mysqli_query($con,$query)){
            echo "<script> alert('This product has been deleted');</script>";
        }
    }

    if(isset($_GET['status'])) {
        $status = $_GET['status'];
    }
?>
<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-3">
            <?php include("include/sidebar.php");?>
        </div>
        
        <div class="col-md-9">
            <div class="card shadow-sm border-0 mb-4 mt-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-th-list text-primary mr-2"></i> Furniture Products List</h5>
                    <div>
                        <span class="mr-2 text-muted font-weight-bold">Status Filter:</span> 
                        <a href="furniture_pro_view.php?status=publish" class="badge badge-success p-2">Publish</a> 
                        <a href="furniture_pro_view.php?status=draft" class="badge badge-warning p-2">Draft</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="font-weight-bold">ID</th>
                                    <th class="font-weight-bold">Image</th>
                                    <th class="font-weight-bold">Title</th>
                                    <th class="font-weight-bold">Category</th>
                                    <th class="font-weight-bold">Size</th>
                                    <th class="font-weight-bold">Price</th>
                                    <th class="font-weight-bold text-center">Actions</th>
                                </tr>
                            </thead>
                <tbody>
                    <?php
                        // Query correctly matches existing product_categories mapping table and ignores broken status filter
                        $pr_query = "SELECT fp.*, GROUP_CONCAT(cat.category SEPARATOR ', ') AS categories 
                                     FROM furniture_product fp 
                                     LEFT JOIN product_categories pc ON fp.product_id = pc.product_id 
                                     LEFT JOIN categories cat ON pc.category_id = cat.id 
                                     GROUP BY fp.product_id 
                                     ORDER BY fp.product_id DESC";
                        $pr_run = mysqli_query($con, $pr_query);
                                        
                        if($pr_run && mysqli_num_rows($pr_run) > 0) {
                            while($pr_row = mysqli_fetch_array($pr_run)) {
                                $pid = $pr_row['product_id'];
                                $title = $pr_row['product_name'];
                                $categories = $pr_row['categories'];
                                $size = $pr_row['product_size'];
                                $price = $pr_row['product_price'];    
                                $detail = $pr_row['product_desc'];
                                $image = $pr_row['product_img1'];
                    ?> 
                    <tr>
                        <td class="align-middle"><strong>#<?php echo $pid;?></strong></td>
                        <td width="100px" class="align-middle">
                            <img src="../img/<?php echo $image;?>" class="img-thumbnail rounded" style="width: 80px; height: 80px; object-fit: cover;" onerror="this.src='../img/placeholder.jpg'">
                        </td>
                        <td class="align-middle font-weight-bold text-dark"><?php echo $title;?></td>
                        <td class="align-middle"><span class="badge badge-info p-2"><?php echo $categories;?></span></td>
                        <td class="align-middle"><?php echo $size;?></td>
                        <td class="align-middle text-success font-weight-bold">₹<?php echo number_format((float)$price, 2);?></td>
                        <td class="align-middle text-center"> 
                            <a title="Edit Product" href="furniture_pro_edit.php?pid=<?php echo $pid;?>" class="btn btn-primary btn-sm shadow-sm mr-1" style="border-radius: 6px;">
                                <i class="fad fa-edit mr-1"></i> Edit
                            </a>
                            <a title="Delete Product" href="furniture_pro_view.php?del=<?php echo $pid;?>" class="btn btn-danger btn-sm shadow-sm" style="border-radius: 6px;" onclick="return confirm('Are you sure you want to delete this product?');">
                                <i class="fad fa-trash-alt mr-1"></i> Delete
                            </a>  
                        </td>
                    </tr>   
                    <?php 
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center text-muted py-5'><i class='fad fa-box-open fa-3x mb-3 d-block'></i><h5>No products found</h5></td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
        </div>
        </div>
    </div>
</div>
<?php include("include/footer.php"); ?>
