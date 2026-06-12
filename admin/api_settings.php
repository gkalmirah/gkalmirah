<?php
ob_start();
session_start();
if (!isset($_SESSION['email'])) {
    header('location: signin.php');
    exit();
}

include('include/header.php');
require_once('include/dbcon.php');

$msg = "";
$msg_class = "alert-success";

// Handle Actions (Add, Edit, Delete, Toggle, Razorpay)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'add' || $action == 'edit' || $action == 'save_razorpay') {
        if ($action == 'save_razorpay') {
            $service_name = 'Razorpay';
            $api_key = mysqli_real_escape_string($con, trim($_POST['api_key']));
            $api_secret = mysqli_real_escape_string($con, trim($_POST['api_secret']));
            $webhook_secret = mysqli_real_escape_string($con, trim($_POST['webhook_secret'] ?? ''));
            $mode = mysqli_real_escape_string($con, trim($_POST['mode'] ?? 'sandbox'));
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            $check = mysqli_query($con, "SELECT id FROM api_configurations WHERE service_name = 'Razorpay'");
            if (mysqli_num_rows($check) > 0) {
                $row = mysqli_fetch_assoc($check);
                $id = $row['id'];
                $q = "UPDATE api_configurations SET api_key = '$api_key', api_secret = '$api_secret', webhook_secret = '$webhook_secret', mode = '$mode', is_active = $is_active WHERE id = $id";
            } else {
                $q = "INSERT INTO api_configurations (service_name, api_key, api_secret, webhook_secret, mode, is_active) VALUES ('Razorpay', '$api_key', '$api_secret', '$webhook_secret', '$mode', $is_active)";
            }
            if (mysqli_query($con, $q)) {
                $msg = "Razorpay configuration saved successfully!";
            } else {
                $msg = "Error: " . mysqli_error($con);
                $msg_class = "alert-danger";
            }
        } else {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $service_name = mysqli_real_escape_string($con, trim($_POST['service_name']));
            $api_key = mysqli_real_escape_string($con, trim($_POST['api_key']));
            $api_secret = mysqli_real_escape_string($con, trim($_POST['api_secret'] ?? ''));
            $webhook_secret = mysqli_real_escape_string($con, trim($_POST['webhook_secret'] ?? ''));
            $mode = mysqli_real_escape_string($con, trim($_POST['mode'] ?? 'sandbox'));
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            if (empty($service_name) || empty($api_key)) {
                $msg = "Service Name and API Key are required!";
                $msg_class = "alert-danger";
            } else {
                if ($action == 'add') {
                    $check = mysqli_query($con, "SELECT * FROM api_configurations WHERE service_name = '$service_name'");
                    if (mysqli_num_rows($check) > 0) {
                        $msg = "An API configuration for this service already exists!";
                        $msg_class = "alert-danger";
                    } else {
                        $q = "INSERT INTO api_configurations (service_name, api_key, api_secret, webhook_secret, mode, is_active) VALUES ('$service_name', '$api_key', '$api_secret', '$webhook_secret', '$mode', $is_active)";
                        if (mysqli_query($con, $q)) {
                            $msg = "API Key added successfully!";
                        } else {
                            $msg = "Error: " . mysqli_error($con);
                            $msg_class = "alert-danger";
                        }
                    }
                } else if ($action == 'edit') {
                    $q = "UPDATE api_configurations SET service_name = '$service_name', api_key = '$api_key', api_secret = '$api_secret', webhook_secret = '$webhook_secret', mode = '$mode', is_active = $is_active WHERE id = $id";
                    if (mysqli_query($con, $q)) {
                        $msg = "API Key updated successfully!";
                    } else {
                        $msg = "Error: " . mysqli_error($con);
                        $msg_class = "alert-danger";
                    }
                }
            }
        }
    } else if ($action == 'delete') {
        $id = intval($_POST['id']);
        if (mysqli_query($con, "DELETE FROM api_configurations WHERE id = $id")) {
            $msg = "API Key deleted successfully!";
        } else {
            $msg = "Error: " . mysqli_error($con);
            $msg_class = "alert-danger";
        }
    } else if ($action == 'toggle') {
        $id = intval($_POST['id']);
        $current = intval($_POST['current_status']);
        $new_status = $current ? 0 : 1;
        if (mysqli_query($con, "UPDATE api_configurations SET is_active = $new_status WHERE id = $id")) {
            $msg = "Status toggled successfully!";
        }
    }
}

// Fetch Razorpay Config
$rz_key = '';
$rz_secret = '';
$rz_webhook = '';
$rz_mode = 'sandbox';
$rz_active = 1;

$rz_res = mysqli_query($con, "SELECT * FROM api_configurations WHERE service_name = 'Razorpay' LIMIT 1");
if ($rz_res && mysqli_num_rows($rz_res) > 0) {
    $rz_row = mysqli_fetch_assoc($rz_res);
    $rz_key = $rz_row['api_key'] ?? '';
    $rz_secret = $rz_row['api_secret'] ?? '';
    $rz_webhook = $rz_row['webhook_secret'] ?? '';
    $rz_mode = ($rz_row['mode'] ?? '') ?: 'sandbox';
    $rz_active = $rz_row['is_active'] ?? 1;
}
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-3">
            <?php include('include/sidebar.php'); ?>
        </div>
        
        <div class="col-md-9">
            <h2 class="mb-4"><i class="fad fa-key"></i> API Configuration Management</h2>
            
            <?php if (!empty($msg)): ?>
            <div class="alert <?php echo $msg_class; ?> alert-dismissible fade show" role="alert">
                <?php echo $msg; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <!-- Razorpay Settings Card -->
            <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(145deg, #ffffff 0%, #f8faff 100%); border-left: 5px solid #3399cc !important;">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-credit-card text-info mr-2"></i> Razorpay Payment Gateway Settings</h5>
                    <span class="badge badge-info py-2 px-3" style="border-radius: 20px;"><i class="fad fa-shield-check mr-1"></i> Secure Integration</span>
                </div>
                <div class="card-body">
                    <form method="post" action="api_settings.php">
                        <input type="hidden" name="action" value="save_razorpay">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">Razorpay Key ID</label>
                                <div class="input-group">
                                    <input type="password" name="api_key" id="rz_key_input" class="form-control" value="<?php echo htmlspecialchars($rz_key); ?>" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibility('rz_key_input', this)"><i class="fad fa-eye"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">Razorpay Secret Key</label>
                                <div class="input-group">
                                    <input type="password" name="api_secret" id="rz_secret_input" class="form-control" value="<?php echo htmlspecialchars($rz_secret); ?>" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibility('rz_secret_input', this)"><i class="fad fa-eye"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-muted small">Webhook Secret</label>
                                <div class="input-group">
                                    <input type="password" name="webhook_secret" id="rz_webhook_input" class="form-control" value="<?php echo htmlspecialchars($rz_webhook); ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibility('rz_webhook_input', this)"><i class="fad fa-eye"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold text-muted small d-block">Gateway Mode</label>
                                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                    <label class="btn btn-outline-primary btn-sm <?php echo $rz_mode == 'sandbox' ? 'active' : ''; ?>">
                                        <input type="radio" name="mode" value="sandbox" autocomplete="off" <?php echo $rz_mode == 'sandbox' ? 'checked' : ''; ?>> Sandbox
                                    </label>
                                    <label class="btn btn-outline-danger btn-sm <?php echo $rz_mode == 'live' ? 'active' : ''; ?>">
                                        <input type="radio" name="mode" value="live" autocomplete="off" <?php echo $rz_mode == 'live' ? 'checked' : ''; ?>> Live Mode
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3 form-group d-flex flex-column justify-content-end">
                                <label class="font-weight-bold text-muted small mb-2 text-center">Status</label>
                                <div class="custom-control custom-switch text-center pb-2">
                                    <input type="checkbox" class="custom-control-input" id="rzActiveSwitch" name="is_active" value="1" <?php echo $rz_active ? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="rzActiveSwitch">Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="text-right mt-3">
                            <button type="submit" class="btn btn-primary px-4"><i class="fad fa-save mr-1"></i> Save Razorpay Settings</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Add New API Configuration</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="api_settings.php">
                        <input type="hidden" name="action" value="add">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Service Name *</label>
                                    <input type="text" name="service_name" class="form-control" placeholder="e.g. Razorpay" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>API Key *</label>
                                    <input type="password" name="api_key" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>API Secret (Optional)</label>
                                    <input type="password" name="api_secret" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <label class="d-block text-center">Active</label>
                                    <div class="custom-control custom-switch text-center">
                                        <input type="checkbox" class="custom-control-input" id="addActiveSwitch" name="is_active" checked>
                                        <label class="custom-control-label" for="addActiveSwitch"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fad fa-plus"></i> Add Key</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Existing Integrations</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Service</th>
                                <th>API Key</th>
                                <th>API Secret</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = mysqli_query($con, "SELECT * FROM api_configurations WHERE service_name != 'Razorpay' ORDER BY service_name ASC");
                            $modals_html = '';
                            if (mysqli_num_rows($res) > 0) {
                                while ($row = mysqli_fetch_assoc($res)) {
                                    ?>
                                    <tr>
                                        <td class="align-middle font-weight-bold"><?php echo htmlspecialchars($row['service_name']); ?></td>
                                        <td class="align-middle">
                                            <div class="input-group input-group-sm" style="width: 200px;">
                                                <input type="password" class="form-control" value="<?php echo htmlspecialchars($row['api_key']); ?>" readonly id="key_<?php echo $row['id']; ?>">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibility('key_<?php echo $row['id']; ?>', this)"><i class="fad fa-eye"></i></button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <?php if (!empty($row['api_secret'])): ?>
                                            <div class="input-group input-group-sm" style="width: 200px;">
                                                <input type="password" class="form-control" value="<?php echo htmlspecialchars($row['api_secret']); ?>" readonly id="sec_<?php echo $row['id']; ?>">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibility('sec_<?php echo $row['id']; ?>', this)"><i class="fad fa-eye"></i></button>
                                                </div>
                                            </div>
                                            <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle">
                                            <form method="post" action="api_settings.php" class="d-inline">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="current_status" value="<?php echo $row['is_active']; ?>">
                                                <button type="submit" class="btn btn-sm <?php echo $row['is_active'] ? 'btn-success' : 'btn-secondary'; ?>" title="Click to toggle">
                                                    <?php echo $row['is_active'] ? '<i class="fad fa-check"></i> Active' : '<i class="fad fa-times"></i> Disabled'; ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="align-middle text-right">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#editModal<?php echo $row['id']; ?>"><i class="fad fa-edit"></i> Edit</button>
                                            <form method="post" action="api_settings.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this API configuration?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fad fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>

                                    <?php
                                    ob_start();
                                    ?>
                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="post" action="api_settings.php">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit <?php echo htmlspecialchars($row['service_name']); ?></h5>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="action" value="edit">
                                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                        
                                                        <div class="form-group">
                                                            <label>Service Name *</label>
                                                            <input type="text" name="service_name" class="form-control" value="<?php echo htmlspecialchars($row['service_name']); ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>API Key *</label>
                                                            <input type="text" name="api_key" class="form-control" value="<?php echo htmlspecialchars($row['api_key']); ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>API Secret</label>
                                                            <input type="text" name="api_secret" class="form-control" value="<?php echo htmlspecialchars($row['api_secret']); ?>">
                                                        </div>
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input" id="editSwitch<?php echo $row['id']; ?>" name="is_active" <?php echo $row['is_active'] ? 'checked' : ''; ?>>
                                                            <label class="custom-control-label" for="editSwitch<?php echo $row['id']; ?>">Active</label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    $modals_html .= ob_get_clean();
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-4 text-muted'>No API Configurations found. Add one above.</td></tr>";
                            }
                            ?>
                    </table>
                </div>
            </div>
            
            <!-- Render Modals Here Outside of Table -->
            <?php echo isset($modals_html) ? $modals_html : ''; ?>
            
        </div>
    </div>
</div>

<script>
function toggleVisibility(inputId, btnEl) {
    var input = document.getElementById(inputId);
    var icon = btnEl.querySelector('i');
    
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<?php include('include/footer.php'); ?>
