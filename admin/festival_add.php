<?php
require_once('include/header.php');

if (!isset($_SESSION['email'])) {
    header('location: signin.php');
    exit();
}

$error = '';

if (isset($_POST['add_festival'])) {
    $festival_name = mysqli_real_escape_string($con, $_POST['festival_name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $discount_type = mysqli_real_escape_string($con, $_POST['discount_type']);
    $discount_value = floatval($_POST['discount_value']);
    $start_date = mysqli_real_escape_string($con, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($con, $_POST['end_date']);
    $status = isset($_POST['status']) ? 1 : 0;

    // Image upload logic
    $banner_image = '';
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_ext = strtolower(pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed_ext)) {
            $banner_image = 'festival_' . time() . '.' . $file_ext;
            $upload_path = '../img/' . $banner_image;
            move_uploaded_file($_FILES['banner_image']['tmp_name'], $upload_path);
        } else {
            $error = "Invalid image format. Only JPG, PNG, GIF, WEBP allowed.";
        }
    } else {
        $error = "Banner image is required.";
    }

    if (empty($error)) {
        if ($end_date <= $start_date) {
            $error = "End date must be after the start date.";
        } elseif ($discount_value <= 0) {
            $error = "Discount value must be greater than zero.";
        } else {
            $query = "INSERT INTO festival_campaigns (festival_name, banner_image, description, discount_type, discount_value, start_date, end_date, status) 
                      VALUES ('$festival_name', '$banner_image', '$description', '$discount_type', $discount_value, '$start_date', '$end_date', $status)";
            
            if (mysqli_query($con, $query)) {
                $_SESSION['message'] = "Festival Campaign added successfully.";
                header('location: festival_discounts.php');
                exit();
            } else {
                $error = "Failed to add festival: " . mysqli_error($con);
            }
        }
    }
}
?>

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-3">
            <?php include("include/sidebar.php"); ?>
        </div>
        
        <div class="col-md-9">
            <div class="card shadow-sm border-0 mb-4 mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-plus text-primary mr-2"></i> Create Festival Campaign</h5>
                </div>
                <div class="card-body">
                    <?php if(!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    
                    <form method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Festival Name <span class="text-danger">*</span></label>
                                <input type="text" name="festival_name" class="form-control" placeholder="e.g., Diwali Mega Sale" required>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Banner Image <span class="text-danger">*</span></label>
                                <input type="file" name="banner_image" class="form-control-file" required accept="image/*">
                                <small class="text-muted">Will be displayed on the homepage. Suggested size: 1920x400</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Festival Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="e.g., Celebrate the festival of lights with flat 20% off site-wide!"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Discount Type <span class="text-danger">*</span></label>
                                <select name="discount_type" class="form-control" required>
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="fixed">Fixed Amount (₹)</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Discount Value <span class="text-danger">*</span></label>
                                <input type="number" name="discount_value" class="form-control" min="1" step="0.01" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Start Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">End Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="form-group custom-control custom-switch mt-3">
                            <input type="checkbox" class="custom-control-input" id="statusSwitch" name="status" checked>
                            <label class="custom-control-label font-weight-bold" for="statusSwitch">Activate Campaign immediately</label>
                        </div>
                        
                        <hr>
                        <button type="submit" name="add_festival" class="btn btn-primary"><i class="fad fa-save mr-1"></i> Save Festival Campaign</button>
                        <a href="festival_discounts.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("include/footer.php"); ?>
