<?php 
require_once('include/header.php');

if(!isset($_SESSION['email'])){
    header('location: signin.php');
    exit();
}

// Handle Add/Edit
if(isset($_POST['save_method'])) {
    $name = mysqli_real_escape_string($con, trim($_POST['name']));
    $charge = floatval($_POST['charge']);
    $estimated_days = mysqli_real_escape_string($con, trim($_POST['estimated_days']));
    $status = mysqli_real_escape_string($con, trim($_POST['status']));
    
    if(isset($_POST['id']) && !empty($_POST['id'])) {
        $id = intval($_POST['id']);
        $query = "UPDATE delivery_methods SET name='$name', charge=$charge, estimated_days='$estimated_days', status='$status' WHERE id=$id";
        if (mysqli_query($con, $query)) {
            $_SESSION['message'] = "Delivery method updated successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to update delivery method: " . mysqli_error($con);
        }
    } else {
        $query = "INSERT INTO delivery_methods (name, charge, estimated_days, status) VALUES ('$name', $charge, '$estimated_days', '$status')";
        if (mysqli_query($con, $query)) {
            $_SESSION['message'] = "Delivery method created successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to create delivery method: " . mysqli_error($con);
        }
    }
    header('location: delivery_methods.php');
    exit();
}

// Handle Toggle Status
if(isset($_GET['toggle'])) {
    $toggle_id = intval($_GET['toggle']);
    $q = mysqli_query($con, "SELECT status FROM delivery_methods WHERE id = $toggle_id");
    if ($q && mysqli_num_rows($q) > 0) {
        $curr = mysqli_fetch_assoc($q)['status'];
        $new_status = ($curr == 'Active') ? 'Inactive' : 'Active';
        if (mysqli_query($con, "UPDATE delivery_methods SET status = '$new_status' WHERE id = $toggle_id")) {
            $_SESSION['message'] = "Delivery method status updated to $new_status.";
        } else {
            $_SESSION['error_message'] = "Failed to update status: " . mysqli_error($con);
        }
    }
    header('location: delivery_methods.php');
    exit();
}

// Handle Delete
if(isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    if (mysqli_query($con, "DELETE FROM delivery_methods WHERE id = $del_id")) {
        $_SESSION['message'] = "Delivery method deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete delivery method: " . mysqli_error($con);
    }
    header('location: delivery_methods.php');
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
                echo "<div class='alert alert-success alert-dismissible fade show mt-4' role='alert'>
                        <i class='fad fa-check-circle mr-2'></i>{$_SESSION['message']}
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                      </div>";
                unset($_SESSION['message']);
            }
            if(isset($_SESSION['error_message'])){
                echo "<div class='alert alert-danger alert-dismissible fade show mt-4' role='alert'>
                        <i class='fad fa-exclamation-circle mr-2'></i>{$_SESSION['error_message']}
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                      </div>";
                unset($_SESSION['error_message']);
            }
            ?>
            <div class="card shadow-sm border-0 mb-4 mt-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-truck text-primary mr-2"></i> Delivery Methods</h5>
                    <button class="btn btn-primary btn-sm btn-add" data-toggle="modal" data-target="#methodModal"><i class="fad fa-plus mr-1"></i> Add Method</button>
                </div>
                <div class="card-body">
                    <!-- Search Input -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0"><i class="fad fa-search text-muted"></i></span>
                                </div>
                                <input type="text" id="methodSearch" class="form-control border-left-0" placeholder="Search delivery methods by name...">
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Delivery Method Name</th>
                                    <th>Delivery Charge</th>
                                    <th>Estimated Delivery Days</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="methodsTableBody">
                                <?php
                                $query = "SELECT * FROM delivery_methods ORDER BY charge ASC";
                                $run = mysqli_query($con, $query);
                                if($run && mysqli_num_rows($run) > 0) {
                                    while($row = mysqli_fetch_array($run)) {
                                ?>
                                <tr>
                                    <td class="align-middle font-weight-bold text-dark method-name-cell"><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td class="align-middle font-weight-bold text-success">
                                        <?php echo $row['charge'] > 0 ? "₹" . number_format($row['charge'], 2) : "FREE"; ?>
                                    </td>
                                    <td class="align-middle text-muted"><?php echo htmlspecialchars($row['estimated_days']); ?></td>
                                    <td class="align-middle">
                                        <!-- Dynamic Switch toggle -->
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input status-toggle-chk" 
                                                   id="statusSwitch_<?php echo $row['id']; ?>" 
                                                   data-id="<?php echo $row['id']; ?>" 
                                                   <?php echo $row['status'] == 'Active' ? 'checked' : ''; ?>>
                                            <label class="custom-control-label" for="statusSwitch_<?php echo $row['id']; ?>">
                                                <span class="badge <?php echo $row['status'] == 'Active' ? 'badge-success' : 'badge-secondary'; ?>">
                                                    <?php echo htmlspecialchars($row['status']); ?>
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <button class="btn btn-sm btn-info edit-btn mr-1" 
                                                data-id="<?php echo $row['id']; ?>" 
                                                data-name="<?php echo htmlspecialchars($row['name']); ?>" 
                                                data-charge="<?php echo floatval($row['charge']); ?>" 
                                                data-days="<?php echo htmlspecialchars($row['estimated_days']); ?>" 
                                                data-status="<?php echo htmlspecialchars($row['status']); ?>"
                                                title="Edit">
                                            <i class="fad fa-edit mr-1"></i> Edit
                                        </button>
                                        <a href="delivery_methods.php?del=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this delivery method?');" title="Delete">
                                            <i class="fad fa-trash-alt mr-1"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center py-4 text-muted'>No delivery methods found.</td></tr>";
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
<div class="modal fade" id="methodModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" method="post">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Add/Edit Delivery Method</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="mid">
                
                <div class="form-group">
                    <label class="font-weight-bold">Delivery Method Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Express Delivery" required>
                </div>
                
                <div class="form-group">
                    <label class="font-weight-bold">Delivery Charge (₹)</label>
                    <input type="number" name="charge" class="form-control" min="0" step="0.01" value="0.00" required>
                </div>
                
                <div class="form-group">
                    <label class="font-weight-bold">Estimated Delivery Days</label>
                    <input type="text" name="estimated_days" class="form-control" placeholder="e.g. 2-3 Days or Same Day" required>
                </div>
                
                <div class="form-group">
                    <label class="font-weight-bold">Status</label>
                    <select name="status" id="methodStatus" class="form-control">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" name="save_method" class="btn btn-primary">Save Method</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Dynamic on-the-fly Search filtering
    $('#methodSearch').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#methodsTableBody tr').filter(function() {
            var rowText = $(this).find('.method-name-cell').text().toLowerCase();
            $(this).toggle(rowText.indexOf(value) > -1);
        });
        
        // Show "No results" row if no rows are visible
        var visibleRows = $('#methodsTableBody tr:visible').not('#noMatchRow').length;
        if (visibleRows === 0) {
            if ($('#noMatchRow').length === 0) {
                $('#methodsTableBody').append('<tr id="noMatchRow"><td colspan="5" class="text-center py-4 text-muted"><i class="fad fa-search-minus fa-2x mb-2 d-block"></i> No matching delivery methods found.</td></tr>');
            }
        } else {
            $('#noMatchRow').remove();
        }
    });

    // Handle Edit button click
    $('.edit-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var charge = $(this).data('charge');
        var days = $(this).data('days');
        var status = $(this).data('status');
        
        $('#mid').val(id);
        $('input[name="name"]').val(name);
        $('input[name="charge"]').val(charge);
        $('input[name="estimated_days"]').val(days);
        $('#methodStatus').val(status);
        
        $('#methodModal .modal-title').text('Edit Delivery Method');
        $('#methodModal').modal('show');
    });
    
    // Handle Add button click
    $('.btn-add').on('click', function() {
        $('#mid').val('');
        $('input[name="name"]').val('');
        $('input[name="charge"]').val('0.00');
        $('input[name="estimated_days"]').val('');
        $('#methodStatus').val('Active');
        $('#methodModal .modal-title').text('Add Delivery Method');
    });

    // Handle toggle switch state change
    $('.status-toggle-chk').on('change', function() {
        var id = $(this).data('id');
        window.location.href = 'delivery_methods.php?toggle=' + id;
    });
});
</script>

<?php include("include/footer.php"); ?>
