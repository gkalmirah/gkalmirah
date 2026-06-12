<?php 
require_once('include/header.php');

if(!isset($_SESSION['email'])){
    header('location: signin.php');
    exit();
}

if(isset($_POST['submit'])){
    $product_id = intval($_POST['product_id']);
    $discount_type = mysqli_real_escape_string($con, $_POST['discount_type']);
    $discount_value = floatval($_POST['discount_value']);
    $start_date = mysqli_real_escape_string($con, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($con, $_POST['end_date']);
    $status = intval($_POST['status']);

    $query = "INSERT INTO product_discounts (product_id, discount_type, discount_value, start_date, end_date, status)
              VALUES ($product_id, '$discount_type', $discount_value, '$start_date', '$end_date', $status)";
    
    if(mysqli_query($con, $query)){
        $_SESSION['message'] = "Discount added successfully.";
        header('location: discounts.php');
        exit();
    } else {
        $_SESSION['error'] = "Failed to add discount: " . mysqli_error($con);
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
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-plus-circle text-primary mr-2"></i> Add Discount</h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label class="font-weight-bold">Select Product</label>
                            <select name="product_id" class="form-control" required>
                                <option value="">-- Choose Product --</option>
                                <?php
                                $p_query = "SELECT product_id, product_name, product_price FROM furniture_product ORDER BY product_id DESC";
                                $p_run = mysqli_query($con, $p_query);
                                while($p_row = mysqli_fetch_array($p_run)){
                                    echo "<option value='{$p_row['product_id']}'>{$p_row['product_name']} (Base: ₹{$p_row['product_price']})</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Discount Type</label>
                                <select name="discount_type" class="form-control" required>
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="fixed">Fixed Amount (₹)</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Discount Value</label>
                                <input type="number" step="0.01" min="0" name="discount_value" class="form-control" required placeholder="e.g. 10 or 1500">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Start Date & Time</label>
                                <input type="datetime-local" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">End Date & Time</label>
                                <input type="datetime-local" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group mt-4">
                            <button type="submit" name="submit" class="btn btn-primary"><i class="fad fa-save mr-1"></i> Save Discount</button>
                            <a href="discounts.php" class="btn btn-secondary ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("include/footer.php"); ?>
