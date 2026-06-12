<?php 
require_once('include/header.php');

if(!isset($_SESSION['email'])){
    header('location: signin.php');
}

// Add New Slide
if(isset($_POST['add_slide'])){
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $subtitle = mysqli_real_escape_string($con, $_POST['subtitle']);
    
    $image = $_FILES['slider_img']['name'];
    $tmp_name = $_FILES['slider_img']['tmp_name'];
    
    if($image != ""){
        $target = "../img/" . basename($image);
        if(move_uploaded_file($tmp_name, $target)){
            $db_path = "img/" . $image; // Path stored in DB
            $query = "INSERT INTO slider_images (image_path, title, subtitle) VALUES ('$db_path', '$title', '$subtitle')";
            if(mysqli_query($con, $query)){
                $msg = "Slide Added Successfully";
            } else {
                $error = "Database Error: " . mysqli_error($con);
            }
        } else {
            $error = "Failed to upload image.";
        }
    } else {
        $error = "Please select an image.";
    }
}

// Delete Slide
if(isset($_GET['delete'])){
    $del_id = $_GET['delete'];
    $q = "DELETE FROM slider_images WHERE id='$del_id'";
    if(mysqli_query($con, $q)){
        echo "<script>alert('Slide Deleted'); window.location='manage_slider.php';</script>";
    }
}
?>

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-3 col-lg-3">
            <?php require_once('include/sidebar.php'); ?>
        </div>
        
        <div class="col-md-9 col-lg-9">
            <div class="card shadow-sm border-0 mb-4 mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-images text-success mr-2"></i> Manage Homepage Slider</h5>
                </div>
                <div class="card-body">
                    <?php 
                    if(isset($msg)) { echo "<div class='alert alert-success'>$msg</div>"; } 
                    if(isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } 
                    ?>
                    
                    <!-- Add Slide Form -->
                    <form method="post" enctype="multipart/form-data" class="mb-4 border p-4 bg-light rounded">
                        <h6 class="font-weight-bold text-muted mb-3">Add New Slide</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Image (1920x800 recommended)</label>
                                <input type="file" name="slider_img" class="form-control-file" required>
                            </div>
                            <div class="col-md-3">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Exclusive Wardrobe">
                            </div>
                            <div class="col-md-3">
                                <label>Subtitle</label>
                                <input type="text" name="subtitle" class="form-control" placeholder="e.g. Premium Finish">
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" name="add_slide" class="btn btn-success btn-block"><i class="fad fa-plus"></i> Add</button>
                            </div>
                        </div>
                    </form>
                    
                    <hr>
                    <h6 class="font-weight-bold text-muted mb-3 mt-4">Existing Slides</h6>
                    <div class="table-responsive">
                        <table class="table table-hover border text-center">
                            <thead class="thead-light">
                                <tr>
                                    <th class="font-weight-bold">Image</th>
                                    <th class="font-weight-bold">Title</th>
                                    <th class="font-weight-bold">Subtitle</th>
                                    <th class="font-weight-bold">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $q = "SELECT * FROM slider_images ORDER BY ordering ASC";
                                $r = mysqli_query($con, $q);
                                if(mysqli_num_rows($r) > 0){
                                    while($row = mysqli_fetch_array($r)){
                                ?>
                                <tr>
                                    <td class="align-middle"><img src="../<?php echo $row['image_path']; ?>" class="img-thumbnail rounded shadow-sm" width="150" alt="Slide" onerror="this.src='img/placeholder.jpg'"></td>
                                    <td class="align-middle font-weight-bold text-dark"><?php echo $row['title']; ?></td>
                                    <td class="align-middle text-muted"><?php echo $row['subtitle']; ?></td>
                                    <td class="align-middle">
                                        <a href="manage_slider.php?delete=<?php echo $row['id']; ?>" class="btn btn-outline-danger btn-sm rounded-circle shadow-sm" onclick="return confirm('Are you sure you want to delete this slide?')" title="Delete Slide"><i class="fad fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No slides found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('include/footer.php'); ?>
