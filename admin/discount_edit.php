<?php 
require_once('include/header.php');

if(!isset($_SESSION['email'])){
    header('location: signin.php');
    exit();
}

if(!isset($_GET['id'])) {
    header('location: discounts.php');
    exit();
}
$id = intval($_GET['id']);

$query = "SELECT * FROM product_discounts WHERE id = $id";
$run = mysqli_query($con, $query);
if(mysqli_num_rows($run) == 0) {
    header('location: discounts.php');
    exit();
}
$discount = mysqli_fetch_assoc($run);

if(isset($_POST['submit'])){
    $product_id = intval($_POST['product_id']);
    $discount_type = mysqli_real_escape_string($con, $_POST['discount_type']);
    $discount_value = floatval($_POST['discount_value']);
    $start_date = mysqli_real_escape_string($con, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($con, $_POST['end_date']);
    $status = intval($_POST['status']);

    $update_query = "UPDATE product_discounts SET 
                     product_id = $product_id, 
                     discount_type = '$discount_type', 
                     discount_value = $discount_value, 
                     start_date = '$start_date', 
                     end_date = '$end_date', 
                     status = $status 
                     WHERE id = $id";
    
    if(mysqli_query($con, $update_query)){
        $_SESSION['message'] = "Discount updated successfully.";
        header('location: discounts.php');
        exit();
    } else {
        $_SESSION['error'] = "Failed to update discount: " . mysqli_error($con);
    }
}
?>

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-3">
            <?php include("include/sidebar.php");?>
        </div>
        <div class="col-md-9">
            <?php
            if(isset($_SESSION['error'])){
                echo "<div class='alert alert-danger mt-4'>{$_SESSION['error']}</div>";
                unset($_SESSION['error']);
            }
            ?>
            <div class="card shadow-sm border-0 mb-4 mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-edit text-primary mr-2"></i> Edit Discount</h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label class="font-weight-bold">Select Product</label>
                            <select name="product_id" class="form-control" required>
                                <?php
                                $p_query = "SELECT product_id, product_name, product_price FROM furniture_product ORDER BY product_id DESC";
                                $p_run = mysqli_query($con, $p_query);
                                while($p_row = mysqli_fetch_array($p_run)){
                                    $selected = $p_row['product_id'] == $discount['product_id'] ? 'selected' : '';
                                    echo "<option value='{$p_row['product_id']}' $selected>{$p_row['product_name']} (Base: ₹{$p_row['product_price']})</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Discount Type</label>
                                <select name="discount_type" class="form-control" required>
                                    <option value="percentage" <?php echo $discount['discount_type'] == 'percentage' ? 'selected' : ''; ?>>Percentage (%)</option>
                                    <option value="fixed" <?php echo $discount['discount_type'] == 'fixed' ? 'selected' : ''; ?>>Fixed Amount (₹)</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Discount Value</label>
                                <input type="number" step="0.01" min="0" name="discount_value" class="form-control" value="<?php echo floatval($discount['discount_value']); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Start Date & Time</label>
                                <input type="datetime-local" name="start_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($discount['start_date'])); ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">End Date & Time</label>
                                <input type="datetime-local" name="end_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($discount['end_date'])); ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="1" <?php echo $discount['status'] == 1 ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?php echo $discount['status'] == 0 ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="form-group mt-4">
                            <button type="submit" name="submit" class="btn btn-primary"><i class="fad fa-save mr-1"></i> Update Discount</button>
                            <a href="discounts.php" class="btn btn-secondary ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("include/footer.php"); ?>
