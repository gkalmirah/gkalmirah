<?php 
require_once('include/header.php');

if(!isset($_SESSION['email'])){
    header('location: signin.php');
    exit();
}

// Handle Edit/Save
if(isset($_POST['save_method'])) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($con, trim($_POST['method_name']));
    $desc = mysqli_real_escape_string($con, trim($_POST['description']));
    $icon = mysqli_real_escape_string($con, trim($_POST['icon']));
    $sort = intval($_POST['sort_order']);
    $status = isset($_POST['is_active']) ? 1 : 0;
    
    if($id > 0) {
        mysqli_query($con, "UPDATE payment_methods SET method_name='$name', description='$desc', icon='$icon', sort_order=$sort, is_active=$status WHERE id=$id");
        $_SESSION['message'] = "Payment method updated successfully.";
    } else {
        $key = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
        mysqli_query($con, "INSERT INTO payment_methods (method_key, method_name, description, icon, sort_order, is_active) VALUES ('$key', '$name', '$desc', '$icon', $sort, $status)");
        $_SESSION['message'] = "Payment method added successfully.";
    }
    header('location: payment_methods.php');
    exit();
}

// Handle Toggle Status
if(isset($_GET['toggle'])) {
    $toggle_id = intval($_GET['toggle']);
    mysqli_query($con, "UPDATE payment_methods SET is_active = 1 - is_active WHERE id = $toggle_id");
    $_SESSION['message'] = "Payment method status updated.";
    header('location: payment_methods.php');
    exit();
}

// Handle Delete (optional helper for custom ones)
if(isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    mysqli_query($con, "DELETE FROM payment_methods WHERE id = $del_id");
    $_SESSION['message'] = "Payment method deleted successfully.";
    header('location: payment_methods.php');
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
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-credit-card text-primary mr-2"></i> Payment Methods</h5>
                    <button class="btn btn-primary btn-sm btn-add" data-toggle="modal" data-target="#paymentModal"><i class="fad fa-plus mr-1"></i> Add Custom Method</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="80">Icon</th>
                                    <th>Method Name</th>
                                    <th>Description</th>
                                    <th width="120">Sort Order</th>
                                    <th width="120">Status</th>
                                    <th class="text-center" width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM payment_methods ORDER BY sort_order ASC, method_name ASC";
                                $run = mysqli_query($con, $query);
                                if($run && mysqli_num_rows($run) > 0) {
                                    while($row = mysqli_fetch_array($run)) {
                                        $status_badge = $row['is_active'] == 1 
                                            ? "<a href='payment_methods.php?toggle={$row['id']}' class='badge badge-success' title='Click to Disable'>Active</a>" 
                                            : "<a href='payment_methods.php?toggle={$row['id']}' class='badge badge-secondary' title='Click to Enable'>Inactive</a>";
                                ?>
                                <tr>
                                    <td class="align-middle text-center text-primary font-weight-bold" style="font-size: 1.2rem;">
                                        <i class="fad <?php echo htmlspecialchars($row['icon'] ?: 'fa-money-bill'); ?>"></i>
                                    </td>
                                    <td class="align-middle font-weight-bold text-dark"><?php echo htmlspecialchars($row['method_name']); ?></td>
                                    <td class="align-middle text-muted small"><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td class="align-middle text-center"><?php echo intval($row['sort_order']); ?></td>
                                    <td class="align-middle"><?php echo $status_badge; ?></td>
                                    <td class="align-middle text-center">
                                        <button class="btn btn-sm btn-info edit-btn mr-1" 
                                                data-id="<?php echo $row['id']; ?>" 
                                                data-name="<?php echo htmlspecialchars($row['method_name']); ?>" 
                                                data-desc="<?php echo htmlspecialchars($row['description']); ?>" 
                                                data-icon="<?php echo htmlspecialchars($row['icon']); ?>" 
                                                data-sort="<?php echo intval($row['sort_order']); ?>" 
                                                data-active="<?php echo intval($row['is_active']); ?>"
                                                title="Edit">
                                            <i class="fad fa-edit"></i>
                                        </button>
                                        <a href="payment_methods.php?del=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this payment method?');" title="Delete">
                                            <i class="fad fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center py-4 text-muted'>No payment methods found.</td></tr>";
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
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="post">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Add/Edit Payment Method</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="pm_id">
                <div class="form-group">
                    <label>Method Name</label>
                    <input type="text" name="method_name" class="form-control" placeholder="e.g. UPI" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" class="form-control" placeholder="e.g. Google Pay, PhonePe, Paytm">
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Icon Class (FontAwesome)</label>
                        <input type="text" name="icon" class="form-control" placeholder="e.g. fa-qrcode" value="fa-credit-card" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" min="0" value="0" required>
                    </div>
                </div>
                <div class="form-group custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="isActivePayment" name="is_active" checked>
                    <label class="custom-control-label" for="isActivePayment">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="save_method" class="btn btn-primary">Save Method</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.edit-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var desc = $(this).data('desc');
        var icon = $(this).data('icon');
        var sort = $(this).data('sort');
        var active = $(this).data('active');
        
        $('#pm_id').val(id);
        $('input[name="method_name"]').val(name);
        $('input[name="description"]').val(desc);
        $('input[name="icon"]').val(icon);
        $('input[name="sort_order"]').val(sort);
        $('#isActivePayment').prop('checked', active == 1);
        
        $('#paymentModal .modal-title').text('Edit Payment Method');
        $('#paymentModal').modal('show');
    });
    
    $('.btn-add').on('click', function() {
        $('#pm_id').val('');
        $('input[name="method_name"]').val('');
        $('input[name="description"]').val('');
        $('input[name="icon"]').val('fa-credit-card');
        $('input[name="sort_order"]').val('0');
        $('#isActivePayment').prop('checked', true);
        $('#paymentModal .modal-title').text('Add Payment Method');
    });
});
</script>

<?php include("include/footer.php"); ?>
