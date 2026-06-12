<?php
// Set AJAX header early if it's an AJAX call to avoid header output issues
if (isset($_POST['ajax_update_status'])) {
    require_once('include/dbcon.php');
    header('Content-Type: application/json');
    $order_id = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($con, $_POST['status']);
    
    $valid_statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (in_array($new_status, $valid_statuses)) {
        $q = "UPDATE customer_order SET order_status = '$new_status' WHERE order_id = $order_id";
        if (mysqli_query($con, $q)) {
            echo json_encode(['success' => true, 'message' => 'Order status updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($con)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
    }
    exit();
}

require_once('include/header.php');

if(!isset($_SESSION['email'])){
    header('location: signin.php');
    exit();
}

// Filter configuration
$status_filter = isset($_GET['status']) ? trim(strtolower($_GET['status'])) : 'all';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build Query Parts
$where_clauses = [];

if ($status_filter !== 'all') {
    $status_safe = mysqli_real_escape_string($con, $status_filter);
    $where_clauses[] = "co.order_status = '$status_safe'";
}

if (!empty($search_query)) {
    $search_safe = mysqli_real_escape_string($con, $search_query);
    if (is_numeric($search_query)) {
        $where_clauses[] = "(co.order_id = $search_safe OR co.invoice_no = $search_safe)";
    } else {
        $where_clauses[] = "co.customer_email LIKE '%$search_safe%'";
    }
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Pagination logic
$limit = 20;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Count query
$count_query = "SELECT COUNT(*) as total FROM customer_order co $where_sql";
$count_run = mysqli_query($con, $count_query);
$total_rows = 0;
if ($count_run) {
    $count_row = mysqli_fetch_assoc($count_run);
    $total_rows = intval($count_row['total']);
}
$total_pages = ceil($total_rows / $limit);

// Fetch orders
$query = "SELECT co.*, fp.product_name, fp.product_img1, dm.name AS delivery_method_name
          FROM customer_order co
          LEFT JOIN furniture_product fp ON co.product_id = fp.product_id
          LEFT JOIN delivery_methods dm ON co.delivery_method_id = dm.id
          $where_sql
          ORDER BY co.order_date DESC, co.order_id DESC
          LIMIT $offset, $limit";
$orders_run = mysqli_query($con, $query);
?>

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-3">
            <?php include("include/sidebar.php");?>
        </div>
        
        <div class="col-md-9">
            <!-- Breadcrumbs / Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                <h4 class="font-weight-bold text-dark mb-0"><i class="fad fa-shopping-bag text-primary mr-2"></i> Orders Management</h4>
                <form class="form-inline" method="get" action="orders.php">
                    <?php if ($status_filter !== 'all'): ?>
                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
                    <?php endif; ?>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search ID or Email..." value="<?php echo htmlspecialchars($search_query); ?>">
                        <div class="input-group-append">
                            <button class="btn btn-primary btn-sm m-0 px-3" type="submit"><i class="fad fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Status Tabs -->
            <ul class="nav nav-tabs nav-justified border-0 mb-4 shadow-sm bg-white" style="border-radius: 12px; overflow: hidden; padding: 5px;">
                <?php
                $tabs = [
                    'all' => ['label' => 'All Orders', 'icon' => 'fa-list'],
                    'pending' => ['label' => 'Pending', 'icon' => 'fa-clock'],
                    'confirmed' => ['label' => 'Confirmed', 'icon' => 'fa-check-double'],
                    'processing' => ['label' => 'Processing', 'icon' => 'fa-cog'],
                    'shipped' => ['label' => 'Shipped', 'icon' => 'fa-shipping-fast'],
                    'delivered' => ['label' => 'Delivered', 'icon' => 'fa-box-check'],
                    'cancelled' => ['label' => 'Cancelled', 'icon' => 'fa-times-circle']
                ];
                foreach ($tabs as $key => $tab) {
                    $active_class = ($status_filter === $key) ? 'active font-weight-bold text-primary bg-light' : 'text-muted';
                    // Fetch counts for each tab
                    $count_clause = ($key === 'all') ? "" : "WHERE order_status = '$key'";
                    $count_res = mysqli_query($con, "SELECT COUNT(*) as cnt FROM customer_order $count_clause");
                    $count = 0;
                    if ($count_res) {
                        $count_row = mysqli_fetch_assoc($count_res);
                        $count = intval($count_row['cnt']);
                    }
                    echo "<li class='nav-item'>
                            <a class='nav-link py-3 border-0 d-flex flex-column align-items-center $active_class' href='orders.php?status=$key" . (!empty($search_query) ? "&search=" . urlencode($search_query) : "") . "'>
                                <i class='fad {$tab['icon']} mb-1' style='font-size: 1.1rem;'></i>
                                <span class='small'>{$tab['label']} <span class='badge badge-pill badge-light border ml-1'>$count</span></span>
                            </a>
                          </li>";
                }
                ?>
            </ul>

            <div id="alertContainer"></div>

            <!-- Orders Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Order Info</th>
                                    <th>Product Details</th>
                                    <th>Customer</th>
                                    <th>Total Amount</th>
                                    <th>Payment / Delivery</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($orders_run && mysqli_num_rows($orders_run) > 0) {
                                    while ($row = mysqli_fetch_assoc($orders_run)) {
                                        // Status style configuration
                                        $badge_class = 'badge-secondary';
                                        switch ($row['order_status']) {
                                            case 'pending': $badge_class = 'badge-warning'; break;
                                            case 'confirmed': $badge_class = 'badge-primary'; break;
                                            case 'processing': $badge_class = 'badge-info'; break;
                                            case 'shipped': $badge_class = 'badge-dark'; break;
                                            case 'delivered': $badge_class = 'badge-success'; break;
                                            case 'cancelled': $badge_class = 'badge-danger'; break;
                                        }
                                        
                                        $image = $row['product_img1'] ?: 'placeholder.jpg';
                                ?>
                                <tr id="order_row_<?php echo $row['order_id']; ?>">
                                    <!-- Order Info -->
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-dark">ID: #<?php echo $row['order_id']; ?></div>
                                        <div class="small text-muted">Invoice: #<?php echo htmlspecialchars($row['invoice_no']); ?></div>
                                        <div class="small text-muted" style="font-size: 0.75rem;"><?php echo date('d M Y h:i A', strtotime($row['order_date'])); ?></div>
                                    </td>
                                    
                                    <!-- Product Details -->
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <img src="../img/<?php echo htmlspecialchars($image); ?>" class="img-thumbnail rounded mr-2" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.src='../img/placeholder.jpg'">
                                            <div>
                                                <div class="font-weight-bold text-dark text-truncate" style="max-width: 150px;"><?php echo htmlspecialchars($row['product_name']); ?></div>
                                                <div class="small text-muted">Qty: <?php echo intval($row['products_qty']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Customer -->
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-dark">ID: #<?php echo $row['customer_id']; ?></div>
                                        <div class="small text-muted text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($row['customer_email']); ?>"><?php echo htmlspecialchars($row['customer_email']); ?></div>
                                    </td>
                                    
                                    <!-- Total Amount -->
                                    <td class="align-middle font-weight-bold text-success" style="font-size: 1rem;">
                                        ₹<?php echo number_format((float)$row['product_amount'], 2); ?>
                                    </td>
                                    
                                    <!-- Payment / Delivery -->
                                    <td class="align-middle small">
                                        <div><strong>Payment:</strong> <span class="text-uppercase text-muted"><?php echo htmlspecialchars($row['payment_method'] ?: 'COD'); ?></span></div>
                                        <div><strong>Delivery Method:</strong> <span class="text-muted"><?php echo htmlspecialchars($row['delivery_method_name'] ?: 'Standard'); ?></span></div>
                                    </td>
                                    
                                    <!-- Status Dropdown (AJAX) -->
                                    <td class="align-middle">
                                        <div class="d-flex flex-column">
                                            <span class="badge <?php echo $badge_class; ?> p-2 mb-2 status-badge-label"><?php echo ucfirst($row['order_status']); ?></span>
                                            <select class="form-control form-control-sm status-select" data-order-id="<?php echo $row['order_id']; ?>" style="border-radius: 4px; font-size: 0.8rem;">
                                                <option value="pending" <?php echo $row['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="confirmed" <?php echo $row['order_status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                <option value="processing" <?php echo $row['order_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="shipped" <?php echo $row['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?php echo $row['order_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?php echo $row['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </div>
                                    </td>
                                    
                                    <!-- Actions -->
                                    <td class="align-middle text-center">
                                        <a href="edit_furn_verify_pen.php?order_id=<?php echo $row['order_id']; ?>" class="btn btn-outline-primary btn-sm mb-1" title="View Full Details">
                                            <i class="fad fa-eye"></i> Details
                                        </a>
                                        <a href="invoice.php?id=<?php echo $row['order_id']; ?>" class="btn btn-outline-secondary btn-sm" title="Generate Invoice" target="_blank">
                                            <i class="fad fa-file-invoice"></i> Invoice
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center py-5 text-muted'><i class='fad fa-box-open fa-3x mb-3 d-block'></i><h5>No orders found</h5></td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="card-footer bg-white py-3">
                    <nav aria-label="Orders Page Navigation">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <!-- Previous -->
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="orders.php?status=<?php echo urlencode($status_filter); ?>&page=<?php echo $page - 1; ?><?php echo !empty($search_query) ? '&search='.urlencode($search_query) : ''; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            
                            <!-- Pages -->
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="orders.php?status=<?php echo urlencode($status_filter); ?>&page=<?php echo $i; ?><?php echo !empty($search_query) ? '&search='.urlencode($search_query) : ''; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            
                            <!-- Next -->
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="orders.php?status=<?php echo urlencode($status_filter); ?>&page=<?php echo $page + 1; ?><?php echo !empty($search_query) ? '&search='.urlencode($search_query) : ''; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // AJAX status update on selection change
    $('.status-select').on('change', function() {
        var $select = $(this);
        var orderId = $select.data('order-id');
        var newStatus = $select.val();
        var $badge = $select.siblings('.status-badge-label');
        
        $select.prop('disabled', true);
        
        $.ajax({
            url: 'orders.php',
            type: 'POST',
            data: {
                ajax_update_status: 1,
                order_id: orderId,
                status: newStatus
            },
            success: function(response) {
                $select.prop('disabled', false);
                if (response.success) {
                    // Update badge label and class
                    $badge.text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                    $badge.removeClass('badge-warning badge-primary badge-info badge-dark badge-success badge-danger badge-secondary');
                    
                    var badgeClass = 'badge-secondary';
                    switch(newStatus) {
                        case 'pending': badgeClass = 'badge-warning'; break;
                        case 'confirmed': badgeClass = 'badge-primary'; break;
                        case 'processing': badgeClass = 'badge-info'; break;
                        case 'shipped': badgeClass = 'badge-dark'; break;
                        case 'delivered': badgeClass = 'badge-success'; break;
                        case 'cancelled': badgeClass = 'badge-danger'; break;
                    }
                    $badge.addClass(badgeClass);
                    
                    // Show a quick transient alert
                    showAlert('success', 'Order #' + orderId + ' updated to ' + newStatus + '!');
                } else {
                    showAlert('danger', 'Error: ' + response.message);
                }
            },
            error: function() {
                $select.prop('disabled', false);
                showAlert('danger', 'Network or server error updating order status.');
            }
        });
    });

    function showAlert(type, message) {
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 8px;">' +
                        message +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>';
        var $alert = $(alertHtml).appendTo('#alertContainer');
        setTimeout(function() {
            $alert.alert('close');
        }, 3000);
    }
});
</script>

<?php include("include/footer.php"); ?>
