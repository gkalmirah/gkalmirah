<?php 
require_once('include/header.php');

if(!isset($_SESSION['email'])){
    header('location: signin.php');
    exit();
}

// Handle Add/Edit
if(isset($_POST['save_tax'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = mysqli_real_escape_string($con, trim($_POST['tax_name']));
    $percent = floatval($_POST['tax_percent']);
    $status = isset($_POST['is_active']) ? 1 : 0;
    
    if($id > 0) {
        mysqli_query($con, "UPDATE tax_settings SET tax_name='$name', tax_percent=$percent, is_active=$status WHERE id=$id");
        $_SESSION['message'] = "Tax setting updated successfully.";
    } else {
        mysqli_query($con, "INSERT INTO tax_settings (tax_name, tax_percent, is_active) VALUES ('$name', $percent, $status)");
        $_SESSION['message'] = "Tax setting created successfully.";
    }
    header('location: tax_settings.php');
    exit();
}

// Handle Toggle Status
if(isset($_GET['toggle'])) {
    $toggle_id = intval($_GET['toggle']);
    mysqli_query($con, "UPDATE tax_settings SET is_active = 1 - is_active WHERE id = $toggle_id");
    $_SESSION['message'] = "Tax status updated.";
    header('location: tax_settings.php');
    exit();
}

// Handle Delete
if(isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    mysqli_query($con, "DELETE FROM tax_settings WHERE id = $del_id");
    $_SESSION['message'] = "Tax setting deleted successfully.";
    header('location: tax_settings.php');
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
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-percent text-primary mr-2"></i> Tax & GST Settings</h5>
                    <button class="btn btn-primary btn-sm btn-add" data-toggle="modal" data-target="#taxModal"><i class="fad fa-plus mr-1"></i> Add Tax Rate</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tax Label</th>
                                    <th>Percentage (%)</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM tax_settings ORDER BY id ASC";
                                $run = mysqli_query($con, $query);
                                if($run && mysqli_num_rows($run) > 0) {
                                    while($row = mysqli_fetch_array($run)) {
                                        $status_badge = $row['is_active'] == 1 
                                            ? "<a href='tax_settings.php?toggle={$row['id']}' class='badge badge-success' title='Click to Disable'>Active</a>" 
                                            : "<a href='tax_settings.php?toggle={$row['id']}' class='badge badge-secondary' title='Click to Enable'>Inactive</a>";
                                ?>
                                <tr>
                                    <td class="align-middle font-weight-bold text-dark"><?php echo htmlspecialchars($row['tax_name']); ?></td>
                                    <td class="align-middle font-weight-bold text-primary"><?php echo floatval($row['tax_percent']); ?>%</td>
                                    <td class="align-middle"><?php echo $status_badge; ?></td>
                                    <td class="align-middle text-muted small"><?php echo htmlspecialchars($row['updated_at']); ?></td>
                                    <td class="align-middle text-center">
                                        <button class="btn btn-sm btn-info edit-btn mr-1" 
                                                data-id="<?php echo $row['id']; ?>" 
                                                data-name="<?php echo htmlspecialchars($row['tax_name']); ?>" 
                                                data-percent="<?php echo floatval($row['tax_percent']); ?>" 
                                                data-active="<?php echo intval($row['is_active']); ?>"
                                                title="Edit">
                                            <i class="fad fa-edit"></i>
                                        </button>
                                        <a href="tax_settings.php?del=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this tax configuration?');" title="Delete">
                                            <i class="fad fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center py-4 text-muted'>No tax configurations found.</td></tr>";
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
<div class="modal fade" id="taxModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="post">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Add/Edit Tax Rate</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="tax_id">
                <div class="form-group">
                    <label>Tax Name / Label</label>
                    <input type="text" name="tax_name" class="form-control" placeholder="e.g. GST" required>
                </div>
                <div class="form-group">
                    <label>Percentage (%)</label>
                    <input type="number" name="tax_percent" class="form-control" min="0" max="100" step="0.01" placeholder="e.g. 18.00" required>
                </div>
                <div class="form-group custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="isActiveTax" name="is_active" checked>
                    <label class="custom-control-label" for="isActiveTax">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="save_tax" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.edit-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var percent = $(this).data('percent');
        var active = $(this).data('active');
        
        $('#tax_id').val(id);
        $('input[name="tax_name"]').val(name);
        $('input[name="tax_percent"]').val(percent);
        $('#isActiveTax').prop('checked', active == 1);
        
        $('#taxModal .modal-title').text('Edit Tax Rate');
        $('#taxModal').modal('show');
    });
    
    $('.btn-add').on('click', function() {
        $('#tax_id').val('');
        $('input[name="tax_name"]').val('');
        $('input[name="tax_percent"]').val('');
        $('#isActiveTax').prop('checked', true);
        $('#taxModal .modal-title').text('Add Tax Rate');
    });
});
</script>

<?php include("include/footer.php"); ?>
