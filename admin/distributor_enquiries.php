<?php 
require_once('include/header.php');

if(!isset($_SESSION['email'])){
    header('location: signin.php');
    exit();
}

// Handle Status Update
if (isset($_GET['status_update']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $status = mysqli_real_escape_string($con, trim($_GET['status_update']));
    
    $valid_statuses = ['New', 'Contacted', 'Approved', 'Rejected'];
    if (in_array($status, $valid_statuses)) {
        if (mysqli_query($con, "UPDATE distributor_inquiries SET status = '$status' WHERE id = $id")) {
            $_SESSION['message'] = "Inquiry status updated to '$status' successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to update status: " . mysqli_error($con);
        }
    }
    header('location: distributor_enquiries.php');
    exit();
}

// Handle Delete Inquiry
if (isset($_GET['del'])) {
    $del_id = intval($_GET['del']);
    if (mysqli_query($con, "DELETE FROM distributor_inquiries WHERE id = $del_id")) {
        $_SESSION['message'] = "Distributor inquiry deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete inquiry: " . mysqli_error($con);
    }
    header('location: distributor_enquiries.php');
    exit();
}

// Active Tab configuration
$active_tab = isset($_GET['tab']) ? mysqli_real_escape_string($con, trim($_GET['tab'])) : 'All';
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($con, trim($_GET['search'])) : '';

// Build Query
$where_clauses = [];
if ($active_tab !== 'All') {
    $where_clauses[] = "status = '$active_tab'";
}
if (!empty($search_query)) {
    $where_clauses[] = "(full_name LIKE '%$search_query%' OR company_name LIKE '%$search_query%' OR email LIKE '%$search_query%' OR phone LIKE '%$search_query%')";
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

$query = "SELECT * FROM distributor_inquiries $where_sql ORDER BY created_at DESC";
$run = mysqli_query($con, $query);
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

            <!-- Header & Search -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                <h4 class="font-weight-bold text-dark mb-0"><i class="fad fa-handshake text-primary mr-2"></i> Distributor Enquiries</h4>
                <form class="form-inline" method="get">
                    <?php if ($active_tab !== 'All'): ?>
                        <input type="hidden" name="tab" value="<?php echo htmlspecialchars($active_tab); ?>">
                    <?php endif; ?>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name, company..." value="<?php echo htmlspecialchars($search_query); ?>">
                        <div class="input-group-append">
                            <button class="btn btn-primary btn-sm m-0 px-3" type="submit"><i class="fad fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tab Filters -->
            <ul class="nav nav-tabs nav-justified border-0 mb-4 shadow-sm bg-white" style="border-radius: 12px; overflow: hidden; padding: 5px;">
                <?php
                $tabs = [
                    'All' => ['label' => 'All Enquiries', 'icon' => 'fa-list'],
                    'New' => ['label' => 'New', 'icon' => 'fa-bell'],
                    'Contacted' => ['label' => 'Contacted', 'icon' => 'fa-phone-laptop'],
                    'Approved' => ['label' => 'Approved', 'icon' => 'fa-check-circle'],
                    'Rejected' => ['label' => 'Rejected', 'icon' => 'fa-times-circle']
                ];
                foreach ($tabs as $key => $tab) {
                    $active_class = ($active_tab === $key) ? 'active font-weight-bold text-primary bg-light' : 'text-muted';
                    
                    // Count entries
                    $count_clause = ($key === 'All') ? "" : "WHERE status = '$key'";
                    $count_res = mysqli_query($con, "SELECT COUNT(*) as cnt FROM distributor_inquiries $count_clause");
                    $count = 0;
                    if ($count_res) {
                        $count = intval(mysqli_fetch_assoc($count_res)['cnt']);
                    }
                    
                    echo "<li class='nav-item'>
                            <a class='nav-link py-3 border-0 d-flex flex-column align-items-center $active_class' href='distributor_enquiries.php?tab=$key" . (!empty($search_query) ? "&search=" . urlencode($search_query) : "") . "'>
                                <i class='fad {$tab['icon']} mb-1' style='font-size: 1.1rem;'></i>
                                <span class='small'>{$tab['label']} <span class='badge badge-pill badge-light border ml-1'>$count</span></span>
                            </a>
                          </li>";
                }
                ?>
            </ul>

            <!-- Enquiries Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Applicant Details</th>
                                    <th>Company Name</th>
                                    <th>Location</th>
                                    <th>Business / Investment</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="enquiriesTableBody">
                                <?php
                                if ($run && mysqli_num_rows($run) > 0) {
                                    while ($row = mysqli_fetch_assoc($run)) {
                                        // Status badge style
                                        $badge_class = 'badge-secondary';
                                        switch ($row['status']) {
                                            case 'New': $badge_class = 'badge-warning'; break;
                                            case 'Contacted': $badge_class = 'badge-info'; break;
                                            case 'Approved': $badge_class = 'badge-success'; break;
                                            case 'Rejected': $badge_class = 'badge-danger'; break;
                                        }
                                ?>
                                <tr id="inquiry_row_<?php echo $row['id']; ?>">
                                    <!-- Date -->
                                    <td class="align-middle text-muted small">
                                        <?php echo date('d M Y', strtotime($row['created_at'])); ?><br>
                                        <span style="font-size: 0.75rem;"><?php echo date('h:i A', strtotime($row['created_at'])); ?></span>
                                    </td>
                                    
                                    <!-- Applicant details -->
                                    <td class="align-middle font-weight-bold text-dark applicant-name-cell">
                                        <?php echo htmlspecialchars($row['full_name']); ?><br>
                                        <span class="text-muted small font-weight-normal"><?php echo htmlspecialchars($row['phone']); ?></span><br>
                                        <span class="text-muted small font-weight-normal"><?php echo htmlspecialchars($row['email']); ?></span>
                                    </td>
                                    
                                    <!-- Company -->
                                    <td class="align-middle font-weight-bold text-dark company-name-cell">
                                        <?php echo htmlspecialchars($row['company_name']); ?>
                                    </td>
                                    
                                    <!-- Location -->
                                    <td class="align-middle">
                                        <span class="font-weight-bold text-dark"><?php echo htmlspecialchars($row['city']); ?></span><br>
                                        <span class="text-muted small"><?php echo htmlspecialchars($row['state']); ?></span>
                                    </td>
                                    
                                    <!-- Business / Investment -->
                                    <td class="align-middle small">
                                        <div><strong>Type:</strong> <span class="text-muted"><?php echo htmlspecialchars($row['business_type']); ?></span></div>
                                        <div><strong>Capital:</strong> <span class="text-gold font-weight-bold"><?php echo htmlspecialchars($row['investment_capacity']); ?></span></div>
                                    </td>
                                    
                                    <!-- Status selector -->
                                    <td class="align-middle">
                                        <div class="d-flex flex-column">
                                            <span class="badge <?php echo $badge_class; ?> p-2 mb-2"><?php echo $row['status']; ?></span>
                                            <select class="form-control form-control-sm status-update-select" data-id="<?php echo $row['id']; ?>" style="font-size: 0.8rem; border-radius: 4px;">
                                                <option value="New" <?php echo $row['status'] == 'New' ? 'selected' : ''; ?>>New</option>
                                                <option value="Contacted" <?php echo $row['status'] == 'Contacted' ? 'selected' : ''; ?>>Contacted</option>
                                                <option value="Approved" <?php echo $row['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                                <option value="Rejected" <?php echo $row['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                            </select>
                                        </div>
                                    </td>
                                    
                                    <!-- Actions -->
                                    <td class="align-middle text-center">
                                        <button class="btn btn-outline-primary btn-sm view-btn mb-1" 
                                                data-id="<?php echo $row['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($row['full_name']); ?>"
                                                data-company="<?php echo htmlspecialchars($row['company_name']); ?>"
                                                data-phone="<?php echo htmlspecialchars($row['phone']); ?>"
                                                data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                                data-city="<?php echo htmlspecialchars($row['city']); ?>"
                                                data-state="<?php echo htmlspecialchars($row['state']); ?>"
                                                data-type="<?php echo htmlspecialchars($row['business_type']); ?>"
                                                data-experience="<?php echo htmlspecialchars($row['experience']); ?>"
                                                data-investment="<?php echo htmlspecialchars($row['investment_capacity']); ?>"
                                                data-message="<?php echo htmlspecialchars($row['message']); ?>"
                                                data-date="<?php echo date('d M Y h:i A', strtotime($row['created_at'])); ?>"
                                                data-status="<?php echo $row['status']; ?>"
                                                title="View Details">
                                            <i class="fad fa-eye mr-1"></i> Details
                                        </button>
                                        <a href="distributor_enquiries.php?del=<?php echo $row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this distributor enquiry?');" title="Delete">
                                            <i class="fad fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center py-5 text-muted'><i class='fad fa-box-open fa-3x mb-3 d-block'></i><h5>No inquiries found</h5></td></tr>";
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

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold"><i class="fad fa-id-card text-primary mr-2"></i> Distributor Profile Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong class="small text-muted text-uppercase">Applicant Name</strong>
                        <p class="h6 font-weight-bold text-dark" id="det-name"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong class="small text-muted text-uppercase">Company Name</strong>
                        <p class="h6 font-weight-bold text-dark" id="det-company"></p>
                    </div>
                </div>
                
                <hr class="my-2">
                
                <div class="row">
                    <div class="col-md-6 mb-3 mt-2">
                        <strong class="small text-muted text-uppercase">Contact Mobile</strong>
                        <p class="h6 text-dark" id="det-phone"></p>
                    </div>
                    <div class="col-md-6 mb-3 mt-2">
                        <strong class="small text-muted text-uppercase">Email Address</strong>
                        <p class="h6 text-dark" id="det-email"></p>
                    </div>
                </div>
                
                <hr class="my-2">
                
                <div class="row">
                    <div class="col-md-6 mb-3 mt-2">
                        <strong class="small text-muted text-uppercase">State</strong>
                        <p class="h6 text-dark" id="det-state"></p>
                    </div>
                    <div class="col-md-6 mb-3 mt-2">
                        <strong class="small text-muted text-uppercase">City / District</strong>
                        <p class="h6 text-dark" id="det-city"></p>
                    </div>
                </div>
                
                <hr class="my-2">
                
                <div class="row">
                    <div class="col-md-4 mb-3 mt-2">
                        <strong class="small text-muted text-uppercase">Business Type</strong>
                        <p class="h6 text-dark" id="det-type"></p>
                    </div>
                    <div class="col-md-4 mb-3 mt-2">
                        <strong class="small text-muted text-uppercase">Experience</strong>
                        <p class="h6 text-dark" id="det-experience"></p>
                    </div>
                    <div class="col-md-4 mb-3 mt-2">
                        <strong class="small text-muted text-uppercase">Investment Capital</strong>
                        <p class="h6 text-gold font-weight-bold" id="det-investment"></p>
                    </div>
                </div>
                
                <hr class="my-2">
                
                <div class="form-group mt-3">
                    <strong class="small text-muted text-uppercase d-block mb-1">Proposal Message</strong>
                    <div class="bg-light p-3 rounded text-dark" style="min-height: 100px; white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;" id="det-message"></div>
                </div>
                
                <hr class="my-2">
                
                <div class="row">
                    <div class="col-md-6 mt-2">
                        <strong class="small text-muted text-uppercase">Date Submitted</strong>
                        <p class="small text-dark" id="det-date"></p>
                    </div>
                    <div class="col-md-6 mt-2">
                        <strong class="small text-muted text-uppercase">Current Status</strong>
                        <p class="small" id="det-status"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle change of status dropdown
    $('.status-update-select').on('change', function() {
        var id = $(this).data('id');
        var status = $(this).val();
        window.location.href = 'distributor_enquiries.php?status_update=' + status + '&id=' + id;
    });

    // Handle View Details button click
    $('.view-btn').on('click', function() {
        var name = $(this).data('name');
        var company = $(this).data('company');
        var phone = $(this).data('phone');
        var email = $(this).data('email');
        var city = $(this).data('city');
        var state = $(this).data('state');
        var type = $(this).data('type');
        var experience = $(this).data('experience');
        var investment = $(this).data('investment');
        var message = $(this).data('message');
        var date = $(this).data('date');
        var status = $(this).data('status');
        
        $('#det-name').text(name);
        $('#det-company').text(company);
        $('#det-phone').html('<a href="tel:' + phone + '">' + phone + '</a>');
        $('#det-email').html('<a href="mailto:' + email + '">' + email + '</a>');
        $('#det-city').text(city);
        $('#det-state').text(state);
        $('#det-type').text(type);
        $('#det-experience').text(experience);
        $('#det-investment').text(investment);
        $('#det-message').text(message ? message : 'No proposal message provided.');
        $('#det-date').text(date);
        
        var badge = 'New';
        var badge_class = 'badge-warning';
        switch (status) {
            case 'New': badge_class = 'badge-warning'; break;
            case 'Contacted': badge_class = 'badge-info'; break;
            case 'Approved': badge_class = 'badge-success'; break;
            case 'Rejected': badge_class = 'badge-danger'; break;
        }
        $('#det-status').html('<span class="badge ' + badge_class + ' p-2">' + status + '</span>');
        
        $('#detailsModal').modal('show');
    });
});
</script>

<?php include("include/footer.php"); ?>
