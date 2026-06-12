<?php 
 require_once('include/header.php');
 
 if(!isset($_SESSION['email'])){
  header('location: signin.php');
  exit();
}
if(isset($_SESSION['email'])){
    $session_id = $_SESSION['id'];
    $session_email = $_SESSION['email'];
    $session_name = $_SESSION['name'];
}
?>

<div class="container-fluid mt-2">
    <script src="ckeditor/ckeditor.js"></script>
      <div class="row">
        <div class="col-md-3 col-lg-3">
            <?php require_once('include/sidebar.php'); ?>
        </div>
        
        <div class="col-md-9 col-lg-9">
          <form method="post" enctype="multipart/form-data">
             <?php
                    if(isset($_POST['submit'])){ 
                        $title      = mysqli_real_escape_string($con, $_POST['title']);
                        $subtitle   = mysqli_real_escape_string($con, $_POST['subtitle']);
                        $short_desc = mysqli_real_escape_string($con, $_POST['short_desc']);
                        $detail     = mysqli_real_escape_string($con, $_POST['detail']);
                        $category   = intval($_POST['category']);
                        
                        $price      = mysqli_real_escape_string($con, $_POST['price']);
                        $mrp        = mysqli_real_escape_string($con, $_POST['mrp']);
                        $tax_inc    = mysqli_real_escape_string($con, $_POST['tax_inc']);
                        
                        $mat        = mysqli_real_escape_string($con, $_POST['mat']);
                        $paint      = mysqli_real_escape_string($con, $_POST['paint']);
                        $size       = mysqli_real_escape_string($con, $_POST['size']);
                        $door       = mysqli_real_escape_string($con, $_POST['door']);
                        $drawer     = mysqli_real_escape_string($con, $_POST['drawer']);
                        $warranty   = mysqli_real_escape_string($con, $_POST['warranty']);
                        $feature    = mysqli_real_escape_string($con, $_POST['feature']);
                        $avail      = intval($_POST['avail']);
                        $three60    = mysqli_real_escape_string($con, $_POST['three60']);

                        // Handle Images
                        $images = [];
                        for($i=1; $i<=6; $i++) {
                            $field = ($i == 1) ? 'upload' : 'upload'.$i;
                            if(isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
                                $img_name = time() . "_" . $_FILES[$field]['name'];
                                $tmp_name = $_FILES[$field]['tmp_name'];
                                $path = "img/".$img_name;
                                if(move_uploaded_file($tmp_name, "../".$path)) {
                                    $images[$i] = $img_name;
                                } else {
                                    $images[$i] = '';
                                }
                            } else {
                                $images[$i] = '';
                            }
                        }
                        
                        if(!empty($title) && !empty($price)){
                            $query = "INSERT INTO furniture_product(
                                `product_name`, `product_subtitle`, `product_short_desc`, `product_img1`, `product_img2`, 
                                `product_img3`, `product_img4`, `product_img5`, `product_img6`, `product_360`,
                                `product_price`, `product_mrp`, `product_tax_inc`, `product_size`, `product_desc`, 
                                `product_mat`, `product_warranty`, `product_door`, `product_drawer`, `product_paint`, 
                                `product_feature`, `product_avail`
                            ) VALUES (
                                '$title', '$subtitle', '$short_desc', '{$images[1]}', '{$images[2]}', 
                                '{$images[3]}', '{$images[4]}', '{$images[5]}', '{$images[6]}', '$three60',
                                '$price', '$mrp', '$tax_inc', '$size', '$detail', 
                                '$mat', '$warranty', '$door', '$drawer', '$paint', 
                                '$feature', $avail
                            )";
                            
                            if(mysqli_query($con, $query)){
                                $product_id = mysqli_insert_id($con);
                                // Map to category
                                mysqli_query($con, "INSERT INTO product_categories (product_id, category_id) VALUES ($product_id, $category)");
                                $_SESSION['message'] = "Furniture Product Has Been Published Successfully";
                            } else {
                                $_SESSION['error'] = "Failed to add product: " . mysqli_error($con);
                            }    
                        } else {
                            $_SESSION['error'] = "Title and Price are required";
                        }
                        
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit();
                    }

                if(isset($_SESSION['message'])){
                    echo "<div class='alert alert-success'><i class='fad fa-smile'></i> {$_SESSION['message']}</div>";
                    unset($_SESSION['message']);
                }
                if(isset($_SESSION['error'])){
                    echo "<div class='alert alert-danger'><i class='fad fa-frown'></i> {$_SESSION['error']}</div>";
                    unset($_SESSION['error']);
                }
            ?>
       
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fad fa-plus-circle mr-2"></i>Add New Product (Almirah)</h5>
                    <span class="badge badge-primary">Admin Panel</span>
                </div>
                <div class="card-body">
                    <h5 class="text-primary border-bottom pb-2 mb-3">1. Basic Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Product Name*</label>
                                <input type="text" class="form-control" name="title" placeholder="e.g. GK BHAGYA SHREE" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Product Subtitle (Optional)</label>
                                <input type="text" class="form-control" name="subtitle" placeholder="e.g. GK ALMIRAH EXCLUSIVE">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Category Selection*</label>
                                <select class="form-control" name="category" required>
                                    <?php
                                    $cat_query = "SELECT * FROM categories ORDER BY id ASC";
                                    $cat_run   = mysqli_query($con,$cat_query);
                                    while($cat_row = mysqli_fetch_array($cat_run)){
                                        echo "<option value='{$cat_row['id']}'>".ucfirst($cat_row['category'])."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Product Short Description</label>
                                <input type="text" class="form-control" name="short_desc" placeholder="One-line snippet for listing pages">
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4 text-primary border-bottom pb-2 mb-3">2. Pricing Information</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Offer Price / Range*</label>
                                <input type="text" class="form-control" name="price" placeholder="e.g. 17199 or 13600-15232" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">MRP Price</label>
                                <input type="number" step="0.01" class="form-control" name="mrp" placeholder="e.g. 21000">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Tax Setting</label>
                                <select class="form-control" name="tax_inc">
                                    <option value="Included">All taxes included</option>
                                    <option value="Excluded">Taxes extra</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4 text-primary border-bottom pb-2 mb-3">3. Technical DNA Section</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Core Material</label>
                                <input type="text" class="form-control form-control-sm" name="mat" placeholder="e.g. C.R.SHEET(TATA STEEL)">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Paint Technology</label>
                                <input type="text" class="form-control form-control-sm" name="paint" placeholder="e.g. 100% powder coated">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small font-weight-bold">Dimensions (H x W x D)</label>
                                <input type="text" class="form-control form-control-sm" name="size" placeholder="e.g. 1980*1060*525">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Doors</label>
                                <input type="text" class="form-control form-control-sm" name="door" placeholder="e.g. 2">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Drawers</label>
                                <input type="text" class="form-control form-control-sm" name="drawer" placeholder="e.g. 2">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Warranty Information</label>
                                <input type="text" class="form-control form-control-sm" name="warranty" value="10 years on paints and lock">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small font-weight-bold">Quantity Available</label>
                                <input type="number" class="form-control form-control-sm" name="avail" value="10">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <label class="small font-weight-bold">Additional Features</label>
                        <input type="text" class="form-control form-control-sm" name="feature" placeholder="e.g. Bangle store, Secret Locker, etc.">
                    </div>

                    <h5 class="mt-4 text-primary border-bottom pb-2 mb-3">4. Product Detailed Description</h5>
                    <div class="form-group">
                        <textarea name="detail" id="detail"></textarea>
                    </div>

                    <h5 class="mt-4 text-primary border-bottom pb-2 mb-3">5. Images & 360° Visuals</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group border p-3 rounded bg-light">
                                <label class="small font-weight-bold d-block mb-2">Main Display Image*</label>
                                <input type="file" name="upload" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group border p-3 rounded bg-light">
                                <label class="small font-weight-bold d-block mb-2">Gallery Image 2</label>
                                <input type="file" name="upload2">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group border p-3 rounded bg-light">
                                <label class="small font-weight-bold d-block mb-2">Gallery Image 3</label>
                                <input type="file" name="upload3">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group border p-3 rounded bg-light">
                                <label class="small font-weight-bold d-block mb-2">Gallery Image 4</label>
                                <input type="file" name="upload4">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group border p-3 rounded bg-light">
                                <label class="small font-weight-bold d-block mb-2">Gallery Image 5</label>
                                <input type="file" name="upload5">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group border p-3 rounded bg-light">
                                <label class="small font-weight-bold d-block mb-2">Gallery Image 6</label>
                                <input type="file" name="upload6">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label class="font-weight-bold">360° Image Frame URL / JSON (Optional)</label>
                        <input type="text" class="form-control" name="three60" placeholder="Paste 360 viewer config or frame range">
                    </div>

                    <div class="text-right mt-5">
                        <a href="furniture_pro_view.php" class="btn btn-outline-dark btn-lg px-4 mr-2">Cancel</a>
                        <button type="submit" name="submit" class="btn btn-primary btn-lg px-5 shadow">PUBLISH TO WEBSITE</button>
                    </div>
                </div>
            </div>
          </form>
        </div>
      </div>
</div>
        
<script>
 CKEDITOR.replace('detail');
</script>

<?php require_once('include/footer.php'); ?>
