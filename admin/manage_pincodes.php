<?php
require_once('include/header.php');

if (!isset($_SESSION['email'])) {
    header('location: signin.php');
    exit();
}

// Handle Add/Edit/Delete/Toggle Actions
$msg = '';
if (isset($_POST['save_pincode'])) {
    $pin = mysqli_real_escape_string($con, trim($_POST['pincode']));
    $days = (int)$_POST['days'];
    $charge = (int)$_POST['charge'];
    $status = isset($_POST['is_active']) ? 1 : 0;
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        $query = "UPDATE serviceable_pincodes SET pincode='$pin', delivery_days=$days, shipping_charge=$charge, is_active=$status WHERE id = $id";
        if (mysqli_query($con, $query)) {
            $_SESSION['message'] = "Pincode updated successfully!";
        } else {
            $_SESSION['message'] = "Error updating pincode: " . mysqli_error($con);
        }
    } else {
        $query = "INSERT INTO serviceable_pincodes (pincode, delivery_days, shipping_charge, is_active) VALUES ('$pin', $days, $charge, $status)";
        if (mysqli_query($con, $query)) {
            $_SESSION['message'] = "Pincode added successfully!";
        } else {
            $_SESSION['message'] = "Error adding pincode: " . mysqli_error($con);
        }
    }
    header('location: manage_pincodes.php');
    exit();
}

// Handle Toggle Status
if (isset($_GET['toggle'])) {
    $toggle_id = (int)$_GET['toggle'];
    mysqli_query($con, "UPDATE serviceable_pincodes SET is_active = 1 - is_active WHERE id = $toggle_id");
    $_SESSION['message'] = "Pincode status updated.";
    header('location: manage_pincodes.php');
    exit();
}

// Handle Delete
if (isset($_GET['del_id'])) {
    $del_id = (int)$_GET['del_id'];
    mysqli_query($con, "DELETE FROM serviceable_pincodes WHERE id = $del_id");
    $_SESSION['message'] = "Pincode deleted successfully.";
    header('location: manage_pincodes.php');
    exit();
}
?>

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-3">
            <?php include('include/sidebar.php'); ?>
        </div>
        <div class="col-md-9">
            <?php
            if(isset($_SESSION['message'])){
                echo "<div class='alert alert-success mt-4'>{$_SESSION['message']}</div>";
                unset($_SESSION['message']);
            }
            ?>
            <div class="card shadow-sm border-0 mb-4 mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-map-marker-alt text-danger mr-2"></i> Manage Delivery Pincodes</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="card shadow-sm border border-light" id="pincodeFormCard">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 font-weight-bold text-secondary" id="formTitle">Add New Pincode</h6>
                                </div>
                                <div class="card-body">
                                    <form method="post" id="pincodeForm">
                                        <input type="hidden" name="id" id="pin_id">
                                        <div class="form-group">
                                            <label class="small font-weight-bold">Pincode (6-Digit)</label>
                                            <input type="text" name="pincode" id="pin_input" class="form-control" maxlength="6" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="small font-weight-bold">Estimated Days</label>
                                            <input type="number" name="days" id="days_input" class="form-control" value="7" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="small font-weight-bold">Shipping Charge (Rs.)</label>
                                            <input type="number" name="charge" id="charge_input" class="form-control" value="0" required>
                                        </div>
                                        <div class="form-group custom-control custom-switch" id="activeGroup" style="display:none;">
                                            <input type="checkbox" class="custom-control-input" id="isActivePin" name="is_active" checked>
                                            <label class="custom-control-label" for="isActivePin">Active</label>
                                        </div>
                                        <button type="submit" name="save_pincode" id="saveBtn" class="btn btn-primary btn-block shadow-sm">Add Pincode</button>
                                        <button type="button" id="cancelBtn" class="btn btn-secondary btn-block shadow-sm mt-2" style="display:none;">Cancel Edit</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-hover border mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="font-weight-bold">Pincode</th>
                                            <th class="font-weight-bold">Days</th>
                                            <th class="font-weight-bold">Charge</th>
                                            <th class="font-weight-bold">Status</th>
                                            <th class="font-weight-bold text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $res = mysqli_query($con, "SELECT * FROM serviceable_pincodes ORDER BY pincode ASC");
                                        if($res && mysqli_num_rows($res) > 0) {
                                            while ($row = mysqli_fetch_assoc($res)) {
                                                $status_badge = $row['is_active'] == 1 
                                                    ? "<a href='manage_pincodes.php?toggle={$row['id']}' class='badge badge-success' title='Click to Disable'>Active</a>" 
                                                    : "<a href='manage_pincodes.php?toggle={$row['id']}' class='badge badge-secondary' title='Click to Enable'>Inactive</a>";
                                        ?>
                                        <tr>
                                            <td class="align-middle font-weight-bold text-dark"><?php echo htmlspecialchars($row['pincode']); ?></td>
                                            <td class="align-middle"><span class="badge badge-info p-2"><?php echo intval($row['delivery_days']); ?> Days</span></td>
                                            <td class="align-middle text-success font-weight-bold">₹<?php echo floatval($row['shipping_charge']); ?></td>
                                            <td class="align-middle"><?php echo $status_badge; ?></td>
                                            <td class="align-middle text-center">
                                                <button class="btn btn-sm btn-info edit-btn mr-1 shadow-sm" style="border-radius: 6px;"
                                                        data-id="<?php echo $row['id']; ?>" 
                                                        data-pincode="<?php echo htmlspecialchars($row['pincode']); ?>" 
                                                        data-days="<?php echo intval($row['delivery_days']); ?>" 
                                                        data-charge="<?php echo floatval($row['shipping_charge']); ?>" 
                                                        data-active="<?php echo intval($row['is_active']); ?>"
                                                        title="Edit">
                                                    <i class="fad fa-edit"></i>
                                                </button>
                                                <a href="manage_pincodes.php?del_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm shadow-sm" style="border-radius: 6px;" onclick="return confirm('Delete this pincode?')" title="Delete">
                                                    <i class="fad fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='5' class='text-center text-muted py-4'>No pincodes found</td></tr>";
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
    </div>
</div>

<script>
$(document).ready(function() {
    $('.edit-btn').on('click', function() {
        var id = $(this).data('id');
        var pincode = $(this).data('pincode');
        var days = $(this).data('days');
        var charge = $(this).data('charge');
        var active = $(this).data('active');
        
        $('#pin_id').val(id);
        $('#pin_input').val(pincode);
        $('#days_input').val(days);
        $('#charge_input').val(charge);
        $('#isActivePin').prop('checked', active == 1);
        
        $('#activeGroup').show();
        $('#cancelBtn').show();
        $('#formTitle').text('Edit Pincode #' + pincode);
        $('#saveBtn').text('Save Changes');
        $('#pincodeFormCard').addClass('border-info');
        
        $('html, body').animate({
            scrollTop: $("#pincodeFormCard").offset().top - 20
        }, 300);
    });
    
    $('#cancelBtn').on('click', function() {
        $('#pin_id').val('');
        $('#pin_input').val('');
        $('#days_input').val('7');
        $('#charge_input').val('0');
        $('#isActivePin').prop('checked', true);
        
        $('#activeGroup').hide();
        $('#cancelBtn').hide();
        $('#formTitle').text('Add New Pincode');
        $('#saveBtn').text('Add Pincode');
        $('#pincodeFormCard').removeClass('border-info');
    });
});
</script>

<?php include('include/footer.php'); ?>
