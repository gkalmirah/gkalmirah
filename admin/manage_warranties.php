<?php 
require_once('include/header.php');

if(!isset($_SESSION['email'])){
    header('location: signin.php');
}

// Initialize session state for mock functionality
if (!isset($_SESSION['warranty_status'])) {
    $_SESSION['warranty_status'] = [];
}
if (!isset($_SESSION['warranty_email_status'])) {
    $_SESSION['warranty_email_status'] = [];
}

// Handle Actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    $q = mysqli_query($con, "SELECT * FROM warranty_activations WHERE id = $id");
    $row = mysqli_fetch_assoc($q);
    
    if ($row) {
        if ($action === 'approve') {
            $_SESSION['warranty_status'][$id] = 'approved';
            
            // Email Sending
            $to = $row['email'];
            $subject = "GK Almirah Warranty Activated";
            $message = "Dear {$row['name']},\n\nYour GK Almirah warranty has been successfully activated.\n\nSerial Number: {$row['serial_number']}\nWarranty ID: {$row['id']}\nProduct: {$row['product_model']}\nActivation Date: " . date('Y-m-d') . "\n\nThank you for choosing GK Almirah.";
            $headers = "From: noreply@gkalmirah.com\r\n";
            $headers .= "Reply-To: support@gkalmirah.com\r\n";
            
            // Attempt to send email and set status
            if (@mail($to, $subject, $message, $headers)) {
                $_SESSION['warranty_email_status'][$id] = 'sent';
            } else {
                $_SESSION['warranty_email_status'][$id] = 'failed';
            }
            
            // WhatsApp Link Generation
            $whatsapp_text = "Your GK Almirah warranty is successfully activated.\n\nSerial Number: {$row['serial_number']}\nWarranty ID: {$row['id']}";
            $contact = !empty($row['contact_number']) ? '91' . ltrim(preg_replace('/[^0-9]/', '', $row['contact_number']), '0') : '';
            $_SESSION['warranty_whatsapp_link'][$id] = "https://wa.me/$contact?text=" . urlencode($whatsapp_text);

        } elseif ($action === 'reject') {
            $_SESSION['warranty_status'][$id] = 'rejected';
            $_SESSION['warranty_email_status'][$id] = 'sent'; // Might send a rejection email
        }
    }
    
    // Redirect to remove query params
    header('location: manage_warranties.php');
    exit();
}

// Handle Export if requested
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="warranties_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Customer Name', 'Product Model', 'Serial Number', 'Email', 'Mobile', 'City', 'Dealer', 'Activation Date']);
    
    $query = "SELECT * FROM warranty_activations ORDER BY activation_date DESC";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['id'], $row['name'], $row['product_model'], $row['serial_number'], 
            $row['email'], $row['contact_number'], $row['city'], $row['dealer_name'], $row['activation_date']
        ]);
    }
    fclose($output);
    exit();
}

// Search and Filter Logic
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';
$city_filter = isset($_GET['city']) ? mysqli_real_escape_string($con, $_GET['city']) : '';
$dealer_filter = isset($_GET['dealer']) ? mysqli_real_escape_string($con, $_GET['dealer']) : '';

$where_clauses = [];
if (!empty($search)) {
    $where_clauses[] = "(name LIKE '%$search%' OR serial_number LIKE '%$search%' OR email LIKE '%$search%')";
}
if (!empty($city_filter)) {
    $where_clauses[] = "city = '$city_filter'";
}
if (!empty($dealer_filter)) {
    $where_clauses[] = "dealer_name = '$dealer_filter'";
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";
?>

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-3">
            <?php require_once('include/sidebar.php'); ?>
        </div>
        
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <h2 class="mb-0 text-primary font-weight-bold"><i class="fad fa-shield-alt mr-2"></i>Warranty Activations</h2>
                <a href="?export=true" class="btn btn-success shadow-sm font-weight-bold"><i class="fad fa-file-export mr-1"></i> Export to CSV</a>
            </div>

            <!-- Filters Section -->
            <div class="card shadow-sm border-0 mb-4 rounded-lg filters-card">
                <div class="card-body p-0">
                    <form method="GET" class="form-row align-items-center m-0 p-3">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, email, or serial..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <select name="city" class="form-control">
                                <option value="">All Cities</option>
                                <?php
                                $c_q = mysqli_query($con, "SELECT DISTINCT city FROM warranty_activations WHERE city IS NOT NULL AND city != '' ORDER BY city");
                                while($c_row = mysqli_fetch_assoc($c_q)){
                                    $sel = ($city_filter == $c_row['city']) ? 'selected' : '';
                                    echo "<option value='".htmlspecialchars($c_row['city'])."' $sel>".htmlspecialchars($c_row['city'])."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <select name="dealer" class="form-control">
                                <option value="">All Dealers</option>
                                <?php
                                $d_q = mysqli_query($con, "SELECT DISTINCT dealer_name FROM warranty_activations WHERE dealer_name IS NOT NULL AND dealer_name != '' ORDER BY dealer_name");
                                while($d_row = mysqli_fetch_assoc($d_q)){
                                    $sel = ($dealer_filter == $d_row['dealer_name']) ? 'selected' : '';
                                    echo "<option value='".htmlspecialchars($d_row['dealer_name'])."' $sel>".htmlspecialchars($d_row['dealer_name'])."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block font-weight-bold"><i class="fad fa-search"></i> Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0 text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th class="text-left">Customer</th>
                            <th>Product Model</th>
                            <th>Serial No.</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM warranty_activations $where_sql ORDER BY id DESC";
                        $run = mysqli_query($con, $query);
                        if (mysqli_num_rows($run) > 0) {
                            while ($row = mysqli_fetch_array($run)) {
                                $invoice_link = $row['gst_invoice_file'] ? "../admin/img/invoices/{$row['gst_invoice_file']}" : "#";
                                $model = !empty($row['product_model']) ? $row['product_model'] : 'N/A';
                                $id = $row['id'];
                                
                                // Get status from session
                                $status = isset($_SESSION['warranty_status'][$id]) ? $_SESSION['warranty_status'][$id] : 'pending';
                                $email_status = isset($_SESSION['warranty_email_status'][$id]) ? $_SESSION['warranty_email_status'][$id] : 'pending';
                                
                                // Status Badges
                                $status_badge = "";
                                if ($status == 'approved') {
                                    $status_badge = "<span class='badge badge-approved'><i class='fad fa-check-circle'></i> Approved</span>";
                                } elseif ($status == 'rejected') {
                                    $status_badge = "<span class='badge badge-rejected'><i class='fad fa-times-circle'></i> Rejected</span>";
                                } else {
                                    $status_badge = "<span class='badge badge-pending'><i class='fad fa-clock'></i> Pending</span>";
                                }
                                
                                $email_badge = "";
                                if ($email_status == 'sent') {
                                    $email_badge = "<span class='badge badge-email-sent mt-1 d-block'><i class='fad fa-envelope'></i> Email Sent</span>";
                                } elseif ($email_status == 'failed') {
                                    $email_badge = "<span class='badge badge-email-failed mt-1 d-block'><i class='fad fa-exclamation-triangle'></i> Email Failed</span>";
                                }

                                $row_json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                
                                echo "<tr class='align-middle'>
                                        <td class='align-middle font-weight-bold text-muted'>#{$id}</td>
                                        <td class='text-left align-middle'>
                                            <h6 class='mb-0 font-weight-bold text-dark'>{$row['name']}</h6>
                                            <small class='text-muted'><i class='fad fa-map-marker-alt mr-1'></i>{$row['city']}</small>
                                        </td>
                                        <td class='align-middle'>{$model}</td>
                                        <td class='align-middle'><code class='bg-light px-2 py-1 rounded text-dark font-weight-bold border'>{$row['serial_number']}</code></td>
                                        <td class='align-middle'>
                                            $status_badge $email_badge
                                        </td>
                                        <td class='align-middle'>
                                            <span class='d-block'>" . date('d M', strtotime($row['activation_date'])) . "</span>
                                            <small class='text-muted'>" . date('Y', strtotime($row['activation_date'])) . "</small>
                                        </td>
                                <td class='align-middle'>
                                            <div class='btn-group shadow-sm'>
                                                <button class='btn btn-sm btn-light border btn-view-details' data-details='$row_json' data-toggle='modal' data-target='#detailsModal' title='View Details'><i class='fad fa-eye text-info'></i></button>
                                                <a href='$invoice_link' target='_blank' class='btn btn-sm btn-light border' title='View Invoice'><i class='fad fa-file-invoice text-primary'></i></a>";
                                
                                if ($status == 'pending') {
                                    echo "      <a href='?action=approve&id={$id}' class='btn btn-sm btn-success border-success' title='Approve'><i class='fad fa-check'></i></a>
                                                <a href='?action=reject&id={$id}' class='btn btn-sm btn-danger border-danger' title='Reject'><i class='fad fa-times'></i></a>";
                                } elseif ($status == 'approved' && isset($_SESSION['warranty_whatsapp_link'][$id])) {
                                    $wa_link = $_SESSION['warranty_whatsapp_link'][$id];
                                    echo "      <a href='$wa_link' target='_blank' class='btn btn-sm btn-success border-success' title='Send WhatsApp Confirmation'><i class='fab fa-whatsapp'></i></a>";
                                } else {
                                    echo "      <button class='btn btn-sm btn-secondary border-secondary disabled' title='Actioned' disabled><i class='fad fa-check'></i></button>";
                                }

                                echo "      </div>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center p-5 text-muted'><h5>No warranty activations found matching your criteria.</h5></td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="detailsModalLabel"><i class="fad fa-shield-check mr-2"></i>Warranty Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <div class="row">
             <div class="col-md-6 mb-3">
                 <small class="text-muted text-uppercase font-weight-bold">Customer Name</small>
                 <div class="h5" id="modalName"></div>
             </div>
             <div class="col-md-6 mb-3">
                 <small class="text-muted text-uppercase font-weight-bold">Email Address</small>
                 <div class="h5" id="modalEmail"></div>
             </div>
             <div class="col-md-6 mb-3">
                 <small class="text-muted text-uppercase font-weight-bold">Contact Number</small>
                 <div class="h5" id="modalContact"></div>
             </div>
             <div class="col-md-6 mb-3">
                 <small class="text-muted text-uppercase font-weight-bold">City</small>
                 <div class="h5" id="modalCity"></div>
             </div>
             <div class="col-md-6 mb-3">
                 <small class="text-muted text-uppercase font-weight-bold">State</small>
                 <div class="h5" id="modalState"></div>
             </div>
             <div class="col-md-6 mb-3">
                 <small class="text-muted text-uppercase font-weight-bold">Pincode</small>
                 <div class="h5" id="modalPincode"></div>
             </div>
         </div>
         <hr>
         <div class="row">
             <div class="col-md-6 mb-3">
                 <small class="text-muted text-uppercase font-weight-bold">Product Model</small>
                 <div class="h5" id="modalModel"></div>
             </div>
             <div class="col-md-6 mb-3">
                 <small class="text-muted text-uppercase font-weight-bold">Serial Number</small>
                 <div class="h5"><code class="bg-light p-1 rounded border" id="modalSerial"></code></div>
             </div>
             <div class="col-md-6 mb-3">
                 <small class="text-muted text-uppercase font-weight-bold">Dealer Name</small>
                 <div class="h5" id="modalDealer"></div>
             </div>
             <div class="col-md-6 mb-3">
                 <small class="text-muted text-uppercase font-weight-bold">Purchase Date</small>
                 <div class="h5" id="modalPurchaseDate"></div>
             </div>
         </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
    $('.btn-view-details').on('click', function() {
        var data = $(this).data('details');
        
        $('#modalName').text(data.name || 'N/A');
        $('#modalEmail').text(data.email || 'N/A');
        $('#modalContact').text(data.contact_number || 'N/A');
        $('#modalCity').text(data.city || 'N/A');
        $('#modalState').text(data.state || 'N/A');
        $('#modalPincode').text(data.pincode || 'N/A');
        
        $('#modalModel').text(data.product_model || 'N/A');
        $('#modalSerial').text(data.serial_number || 'N/A');
        $('#modalDealer').text(data.dealer_name || 'N/A');
        $('#modalPurchaseDate').text(data.purchase_date || 'N/A');
    });
});
</script>

<?php require_once('include/footer.php'); ?>
