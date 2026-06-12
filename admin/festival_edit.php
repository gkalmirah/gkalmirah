<?php
require_once('include/header.php');

if (!isset($_SESSION['email'])) {
    header('location: signin.php');
    exit();
}

$error = '';

if(!isset($_GET['id'])) {
    header('location: festival_discounts.php');
    exit();
}

$id = intval($_GET['id']);
$query = "SELECT * FROM festival_campaigns WHERE id = $id";
$run = mysqli_query($con, $query);
if(!$run || mysqli_num_rows($run) == 0) {
    header('location: festival_discounts.php');
    exit();
}
$festival = mysqli_fetch_assoc($run);

if (isset($_POST['edit_festival'])) {
    $festival_name = mysqli_real_escape_string($con, $_POST['festival_name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $discount_type = mysqli_real_escape_string($con, $_POST['discount_type']);
    $discount_value = floatval($_POST['discount_value']);
    $start_date = mysqli_real_escape_string($con, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($con, $_POST['end_date']);
    $status = isset($_POST['status']) ? 1 : 0;

    $banner_image = $festival['banner_image']; // keep existing by default

    // Image upload logic
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == 0) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_ext = strtolower(pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed_ext)) {
            $new_banner = 'festival_' . time() . '.' . $file_ext;
            $upload_path = '../img/' . $new_banner;
            if(move_uploaded_file($_FILES['banner_image']['tmp_name'], $upload_path)) {
                // delete old banner
                $old_path = '../img/' . $banner_image;
                if(file_exists($old_path) && !empty($banner_image)) unlink($old_path);
                $banner_image = $new_banner;
            }
        } else {
            $error = "Invalid image format. Only JPG, PNG, GIF, WEBP allowed.";
        }
    }

    if (empty($error)) {
        if ($end_date <= $start_date) {
            $error = "End date must be after the start date.";
        } elseif ($discount_value <= 0) {
            $error = "Discount value must be greater than zero.";
        } else {
            $update_q = "UPDATE festival_campaigns SET 
                          festival_name = '$festival_name', 
                          banner_image = '$banner_image', 
                          description = '$description', 
                          discount_type = '$discount_type', 
                          discount_value = $discount_value, 
                          start_date = '$start_date', 
                          end_date = '$end_date', 
                          status = $status 
                          WHERE id = $id";
            
            if (mysqli_query($con, $update_q)) {
                $_SESSION['message'] = "Festival Campaign updated successfully.";
                header('location: festival_discounts.php');
                exit();
            } else {
                $error = "Failed to update festival: " . mysqli_error($con);
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
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-edit text-primary mr-2"></i> Edit Festival Campaign</h5>
                </div>
                <div class="card-body">
                    <?php if(!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    
                    <form method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Festival Name <span class="text-danger">*</span></label>
                                <input type="text" name="festival_name" class="form-control" value="<?php echo htmlspecialchars($festival['festival_name']); ?>" required>
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Update Banner Image</label>
                                <input type="file" name="banner_image" class="form-control-file" accept="image/*">
                                <small class="text-muted">Leave blank to keep existing banner.</small>
                                <div class="mt-2">
                                    <img src="../img/<?php echo htmlspecialchars($festival['banner_image']); ?>" style="height: 60px; border-radius: 4px;">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Festival Description</label>
                            <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($festival['description']); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Discount Type <span class="text-danger">*</span></label>
                                <select name="discount_type" class="form-control" required>
                                    <option value="percentage" <?php if($festival['discount_type'] == 'percentage') echo 'selected'; ?>>Percentage (%)</option>
                                    <option value="fixed" <?php if($festival['discount_type'] == 'fixed') echo 'selected'; ?>>Fixed Amount (₹)</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Discount Value <span class="text-danger">*</span></label>
                                <input type="number" name="discount_value" class="form-control" min="1" step="0.01" value="<?php echo floatval($festival['discount_value']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Start Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($festival['start_date'])); ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">End Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($festival['end_date'])); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group custom-control custom-switch mt-3">
                            <input type="checkbox" class="custom-control-input" id="statusSwitch" name="status" <?php if($festival['status']) echo 'checked'; ?>>
                            <label class="custom-control-label font-weight-bold" for="statusSwitch">Campaign Active</label>
                        </div>
                        
                        <hr>
                        <button type="submit" name="edit_festival" class="btn btn-primary"><i class="fad fa-save mr-1"></i> Update Campaign</button>
                        <a href="festival_discounts.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("include/footer.php"); ?>
