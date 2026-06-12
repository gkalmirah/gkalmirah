<?php 
require_once('include/header.php');

if(!isset($_SESSION['email'])){
    header('location: signin.php');
    exit();
}

// Handle Add/Edit
if(isset($_POST['save_coupon'])) {
    $code = strtoupper(mysqli_real_escape_string($con, trim($_POST['code'])));
    $type = mysqli_real_escape_string($con, $_POST['discount_type']);
    $value = floatval($_POST['discount_value']);
    $min_order = floatval($_POST['min_order']);
    $expiry = mysqli_real_escape_string($con, $_POST['expiry_date']);
    $limit = !empty($_POST['usage_limit']) ? intval($_POST['usage_limit']) : 'NULL';
    $status = isset($_POST['is_active']) ? 1 : 0;
    
    if(isset($_POST['id']) && !empty($_POST['id'])) {
        $id = intval($_POST['id']);
        mysqli_query($con, "UPDATE promo_codes SET code='$code', discount_type='$type', discount_value=$value, min_order=$min_order, expiry_date='$expiry', usage_limit=$limit, is_active=$status WHERE id=$id");
        $_SESSION['message'] = "Coupon updated successfully.";
    } else {
        mysqli_query($con, "INSERT IGNORE INTO promo_codes (code, discount_type, discount_value, min_order, expiry_date, usage_limit, is_active) VALUES ('$code', '$type', $value, $min_order, '$expiry', $limit, $status)");
        $_SESSION['message'] = "Coupon created successfully.";
    }
    header('location: coupons.php');
    exit();
}

// Handle Toggle Status
if(isset($_GET['toggle'])) {
    $toggle_id = intval($_GET['toggle']);
    mysqli_query($con, "UPDATE promo_codes SET is_active = 1 - is_active WHERE id = $toggle_id");
    $_SESSION['message'] = "Coupon status updated.";
    header('location: coupons.php');
    exit();
}

// Handle Delete
if(isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    mysqli_query($con, "DELETE FROM promo_codes WHERE id = $del_id");
    $_SESSION['message'] = "Coupon deleted successfully.";
    header('location: coupons.php');
    exit();
}
?>

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-3">
            <?php include("include/sidebar.php");?>
        </div>
        
        <div class="col-md-9">
            <?php
            if(isset($_SESSION['message'])){
                echo "<div class='alert alert-success mt-4'>{$_SESSION['message']}</div>";
                unset($_SESSION['message']);
            }
            ?>
            <div class="card shadow-sm border-0 mb-4 mt-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-ticket-alt text-primary mr-2"></i> Coupon Management</h5>
                    <button class="btn btn-primary btn-sm btn-add" data-toggle="modal" data-target="#couponModal"><i class="fad fa-plus mr-1"></i> Add Coupon</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Min Order</th>
                                    <th>Expiry Date</th>
                                    <th>Usage (Used/Limit)</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM promo_codes ORDER BY id DESC";
                                $run = mysqli_query($con, $query);
                                if($run && mysqli_num_rows($run) > 0) {
                                    while($row = mysqli_fetch_array($run)) {
                                        $status_badge = $row['is_active'] == 1 
                                            ? "<a href='coupons.php?toggle={$row['id']}' class='badge badge-success' title='Click to Disable'>Active</a>" 
                                            : "<a href='coupons.php?toggle={$row['id']}' class='badge badge-secondary' title='Click to Enable'>Inactive</a>";
                                        $value_display = $row['discount_type'] == 'percent' ? floatval($row['discount_value'])."%" : "₹".floatval($row['discount_value']);
                                        $usage_display = intval($row['used_count']) . ' / ' . ($row['usage_limit'] !== null ? intval($row['usage_limit']) : '∞');
                                ?>
                                <tr>
                                    <td class="align-middle font-weight-bold text-uppercase text-primary"><?php echo htmlspecialchars($row['code']); ?></td>
                                    <td class="align-middle text-capitalize"><?php echo $row['discount_type']; ?></td>
                                    <td class="align-middle font-weight-bold"><?php echo $value_display; ?></td>
                                    <td class="align-middle">₹<?php echo floatval($row['min_order']); ?></td>
                                    <td class="align-middle"><?php echo date('d M Y', strtotime($row['expiry_date'])); ?></td>
                                    <td class="align-middle font-weight-bold"><?php echo $usage_display; ?></td>
                                    <td class="align-middle"><?php echo $status_badge; ?></td>
                                    <td class="align-middle text-center">
                                        <button class="btn btn-sm btn-info edit-btn mr-1" 
                                                data-id="<?php echo $row['id']; ?>" 
                                                data-code="<?php echo htmlspecialchars($row['code']); ?>" 
                                                data-type="<?php echo htmlspecialchars($row['discount_type']); ?>" 
                                                data-value="<?php echo floatval($row['discount_value']); ?>" 
                                                data-min="<?php echo floatval($row['min_order']); ?>" 
                                                data-expiry="<?php echo htmlspecialchars($row['expiry_date']); ?>"
                                                data-limit="<?php echo $row['usage_limit'] !== null ? intval($row['usage_limit']) : ''; ?>"
                                                data-active="<?php echo intval($row['is_active']); ?>"
                                                title="Edit">
                                            <i class="fad fa-edit"></i>
                                        </button>
                                        <a href="coupons.php?del=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this coupon?');" title="Delete">
                                            <i class="fad fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center py-4 text-muted'>No coupons found.</td></tr>";
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

<!-- Modal -->
<div class="modal fade" id="couponModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="post">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Add/Edit Coupon</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="cid">
                <div class="form-group">
                    <label>Coupon Code</label>
                    <input type="text" name="code" class="form-control text-uppercase" placeholder="e.g. SAVE20" required>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Discount Type</label>
                        <select name="discount_type" class="form-control" required>
                            <option value="percent">Percentage (%)</option>
                            <option value="flat">Flat Amount (₹)</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Discount Value</label>
                        <input type="number" name="discount_value" class="form-control" min="1" step="0.01" placeholder="e.g. 10" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Minimum Order Amount (₹)</label>
                        <input type="number" name="min_order" class="form-control" min="0" value="0">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Usage Limit (leave empty for unlimited)</label>
                        <input type="number" name="usage_limit" class="form-control" min="1" placeholder="Unlimited">
                    </div>
                </div>
                <div class="form-group">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control" required>
                </div>
                <div class="form-group custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="isActive" name="is_active" checked>
                    <label class="custom-control-label" for="isActive">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="save_coupon" class="btn btn-primary">Save Coupon</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.edit-btn').on('click', function() {
        var id = $(this).data('id');
        var code = $(this).data('code');
        var type = $(this).data('type');
        var value = $(this).data('value');
        var min = $(this).data('min');
        var expiry = $(this).data('expiry');
        var limit = $(this).data('limit');
        var active = $(this).data('active');
        
        $('#cid').val(id);
        $('input[name="code"]').val(code);
        $('select[name="discount_type"]').val(type);
        $('input[name="discount_value"]').val(value);
        $('input[name="min_order"]').val(min);
        $('input[name="expiry_date"]').val(expiry);
        $('input[name="usage_limit"]').val(limit);
        $('#isActive').prop('checked', active == 1);
        
        $('#couponModal .modal-title').text('Edit Coupon');
        $('#couponModal').modal('show');
    });
    
    $('.btn-add').on('click', function() {
        $('#cid').val('');
        $('input[name="code"]').val('');
        $('select[name="discount_type"]').val('percent');
        $('input[name="discount_value"]').val('');
        $('input[name="min_order"]').val('0');
        $('input[name="expiry_date"]').val('');
        $('input[name="usage_limit"]').val('');
        $('#isActive').prop('checked', true);
        $('#couponModal .modal-title').text('Add Coupon');
    });
});
</script>

<?php include("include/footer.php"); ?>
