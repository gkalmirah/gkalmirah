<?php 
require_once('include/header.php');

if(!isset($_SESSION['email'])){
    header('location: signin.php');
    exit();
}

// Handle Delete
if(isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    // Fetch image path to delete
    $img_res = mysqli_query($con, "SELECT banner_image FROM festival_campaigns WHERE id = $del_id");
    if($img_res && mysqli_num_rows($img_res) > 0) {
        $img_row = mysqli_fetch_assoc($img_res);
        $banner_path = "../img/" . $img_row['banner_image'];
        if(file_exists($banner_path) && !empty($img_row['banner_image'])) {
            unlink($banner_path);
        }
    }
    mysqli_query($con, "DELETE FROM festival_campaigns WHERE id = $del_id");
    $_SESSION['message'] = "Festival Campaign deleted successfully.";
    header('location: festival_discounts.php');
    exit();
}

// Handle Status Toggle
if(isset($_GET['toggle'])) {
    $toggle_id = intval($_GET['toggle']);
    mysqli_query($con, "UPDATE festival_campaigns SET status = NOT status WHERE id = $toggle_id");
    $_SESSION['message'] = "Festival status updated.";
    header('location: festival_discounts.php');
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
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-gift text-primary mr-2"></i> Festival Discounts</h5>
                    <a href="festival_add.php" class="btn btn-primary btn-sm"><i class="fad fa-plus mr-1"></i> Add Festival</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Banner</th>
                                    <th>Festival Name</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM festival_campaigns ORDER BY id DESC";
                                $run = mysqli_query($con, $query);
                                if($run && mysqli_num_rows($run) > 0) {
                                    while($row = mysqli_fetch_array($run)) {
                                        $status_badge = $row['status'] == 1 ? "<span class='badge badge-success'>Active</span>" : "<span class='badge badge-secondary'>Inactive</span>";
                                        $value_display = $row['discount_type'] == 'percentage' ? floatval($row['discount_value'])."%" : "₹".floatval($row['discount_value']);
                                ?>
                                <tr>
                                    <td class="align-middle">
                                        <img src="../img/<?php echo htmlspecialchars($row['banner_image']); ?>" style="width: 80px; height: 40px; object-fit: cover; border-radius: 4px;" alt="Banner">
                                    </td>
                                    <td class="align-middle font-weight-bold"><?php echo htmlspecialchars($row['festival_name']); ?></td>
                                    <td class="align-middle text-capitalize"><?php echo $row['discount_type']; ?></td>
                                    <td class="align-middle text-danger font-weight-bold"><?php echo $value_display; ?></td>
                                    <td class="align-middle"><?php echo date('d M Y, h:i A', strtotime($row['start_date'])); ?></td>
                                    <td class="align-middle"><?php echo date('d M Y, h:i A', strtotime($row['end_date'])); ?></td>
                                    <td class="align-middle"><?php echo $status_badge; ?></td>
                                    <td class="align-middle text-center">
                                        <a href="festival_discounts.php?toggle=<?php echo $row['id']; ?>" class="btn btn-sm <?php echo $row['status'] == 1 ? 'btn-warning' : 'btn-success'; ?> mr-1" title="Toggle Status">
                                            <i class="fad fa-power-off"></i>
                                        </a>
                                        <a href="festival_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary mr-1" title="Edit">
                                            <i class="fad fa-edit"></i>
                                        </a>
                                        <a href="festival_discounts.php?del=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this festival campaign?');" title="Delete">
                                            <i class="fad fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center py-4 text-muted'><i class='fad fa-box-open fa-2x mb-2 d-block'></i> No festival campaigns found.</td></tr>";
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

<?php include("include/footer.php"); ?>
