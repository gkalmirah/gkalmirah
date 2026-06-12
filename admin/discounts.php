<?php 
require_once('include/header.php');

if(!isset($_SESSION['email'])){
    header('location: signin.php');
    exit();
}

// Handle Delete
if(isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    mysqli_query($con, "DELETE FROM product_discounts WHERE id = $del_id");
    $_SESSION['message'] = "Discount deleted successfully.";
    header('location: discounts.php');
    exit();
}

// Handle Status Toggle
if(isset($_GET['toggle'])) {
    $toggle_id = intval($_GET['toggle']);
    mysqli_query($con, "UPDATE product_discounts SET status = NOT status WHERE id = $toggle_id");
    $_SESSION['message'] = "Discount status updated.";
    header('location: discounts.php');
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
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-tags text-primary mr-2"></i> Discount Management</h5>
                    <a href="discount_add.php" class="btn btn-primary btn-sm"><i class="fad fa-plus mr-1"></i> Add Discount</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
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
                                $query = "SELECT d.*, p.product_name 
                                          FROM product_discounts d
                                          JOIN furniture_product p ON d.product_id = p.product_id
                                          ORDER BY d.id DESC";
                                $run = mysqli_query($con, $query);
                                if($run && mysqli_num_rows($run) > 0) {
                                    while($row = mysqli_fetch_array($run)) {
                                        $status_badge = $row['status'] == 1 ? "<span class='badge badge-success'>Active</span>" : "<span class='badge badge-secondary'>Inactive</span>";
                                        $value_display = $row['discount_type'] == 'percentage' ? floatval($row['discount_value'])."%" : "₹".floatval($row['discount_value']);
                                ?>
                                <tr>
                                    <td class="align-middle">#<?php echo $row['id']; ?></td>
                                    <td class="align-middle font-weight-bold"><?php echo htmlspecialchars($row['product_name']); ?></td>
                                    <td class="align-middle text-capitalize"><?php echo $row['discount_type']; ?></td>
                                    <td class="align-middle text-danger font-weight-bold"><?php echo $value_display; ?></td>
                                    <td class="align-middle"><?php echo date('d M Y, h:i A', strtotime($row['start_date'])); ?></td>
                                    <td class="align-middle"><?php echo date('d M Y, h:i A', strtotime($row['end_date'])); ?></td>
                                    <td class="align-middle"><?php echo $status_badge; ?></td>
                                    <td class="align-middle text-center">
                                        <a href="discounts.php?toggle=<?php echo $row['id']; ?>" class="btn btn-sm <?php echo $row['status'] == 1 ? 'btn-warning' : 'btn-success'; ?> mr-1" title="Toggle Status">
                                            <i class="fad fa-power-off"></i>
                                        </a>
                                        <a href="discount_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary mr-1" title="Edit">
                                            <i class="fad fa-edit"></i>
                                        </a>
                                        <a href="discounts.php?del=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this discount?');" title="Delete">
                                            <i class="fad fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center py-4 text-muted'><i class='fad fa-box-open fa-2x mb-2 d-block'></i> No discounts found.</td></tr>";
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
