<?php
include('include/header.php');
require_once('include/warranty_utils.php');
require_once('include/mail_service.php');
require_once('include/warranty_email_template.php');

$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
$product_name = "";

if ($pid > 0) {
    $p_res = mysqli_query($con, "SELECT product_name FROM furniture_product WHERE product_id = $pid");
    if ($p_res && $row = mysqli_fetch_assoc($p_res)) {
        $product_name = $row['product_name'];
    }
}

$message = "";
if (isset($_POST['activate'])) {
    // Collect and sanitize inputs
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $mobile = mysqli_real_escape_string($con, $_POST['mobile']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $city = mysqli_real_escape_string($con, $_POST['city']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $state = mysqli_real_escape_string($con, $_POST['state']);
    $postal_code = mysqli_real_escape_string($con, $_POST['postal_code']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    
    $product_model = mysqli_real_escape_string($con, $_POST['product_model']);
    $p_date = mysqli_real_escape_string($con, $_POST['purchase_date']);
    $dealer_name = mysqli_real_escape_string($con, $_POST['dealer_name']);
    $invoice_number = mysqli_real_escape_string($con, $_POST['invoice_number']);
    $serial = mysqli_real_escape_string($con, $_POST['serial']);

    // File Upload Handling
    $upload_dir = "admin/img/invoices/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $barcode_file = '';
    if (isset($_FILES['barcode_file']) && $_FILES['barcode_file']['error'] == 0) {
        $barcode_file = time() . "_barcode_" . $_FILES['barcode_file']['name'];
        move_uploaded_file($_FILES['barcode_file']['tmp_name'], $upload_dir . $barcode_file);
    }

    $gst_invoice_file = '';
    if (isset($_FILES['gst_invoice_file']) && $_FILES['gst_invoice_file']['error'] == 0) {
        $gst_invoice_file = time() . "_gst_" . $_FILES['gst_invoice_file']['name'];
        move_uploaded_file($_FILES['gst_invoice_file']['tmp_name'], $upload_dir . $gst_invoice_file);
    }

    $warranty_card_file = '';
    if (isset($_FILES['warranty_card_file']) && $_FILES['warranty_card_file']['error'] == 0) {
        $warranty_card_file = time() . "_warranty_" . $_FILES['warranty_card_file']['name'];
        move_uploaded_file($_FILES['warranty_card_file']['tmp_name'], $upload_dir . $warranty_card_file);
    }

    // Generate ID and Format Serial
    $warranty_id = generateWarrantyId($con);
    $formatted_serial = formatSerialNumber($serial);

    // Existing invoice mapping (backward compatibility)
    $invoice_file = $gst_invoice_file; 

    $q = "INSERT INTO warranty_activations (
            name, contact_number, email, activation_date, serial_number, city, address, 
            state, postal_code, password, product_model, dealer_name, invoice_number, 
            barcode_file, gst_invoice_file, warranty_card_file, warranty_code
          ) VALUES (
            '$name', '$mobile', '$email', '$p_date', '$formatted_serial', '$city', '$address', 
            '$state', '$postal_code', '$password', '$product_model', '$dealer_name', '$invoice_number', 
            '$barcode_file', '$gst_invoice_file', '$warranty_card_file', '$warranty_id'
          )";
    
    if (mysqli_query($con, $q)) {
        // Send Confirmation Email
        $inserted_id = mysqli_insert_id($con);
        
        $email_data = [
            'customer_name' => $name,
            'product_name' => $product_name ?: $product_model,
            'serial_number' => $formatted_serial,
            'warranty_id' => $warranty_id,
            'purchase_date' => date('d M Y', strtotime($p_date)),
            'expiry_date' => date('d M Y', strtotime('+1 year', strtotime($p_date))) // Assuming 1 year warranty
        ];

        $htmlContent = getWarrantyEmailTemplate($email_data);
        $email_sent = sendHtmlEmail($email, "GK Almirah Warranty Registration Confirmation", $htmlContent);

        // Success Message with WhatsApp Button
        $whatsapp_text = "Your GK Almirah warranty is successfully activated.\n\nSerial Number: $formatted_serial\nWarranty ID: $warranty_id";
        $whatsapp_url = "https://wa.me/919682021084?text=" . urlencode($whatsapp_text);

        $email_status_msg = $email_sent ? 
            "<p class='text-success small mb-0 mt-2'><i class='fas fa-envelope-open-text mr-1'></i> A confirmation email has been sent to $email.</p>" : 
            "<p class='text-warning small mb-0 mt-2'><i class='fas fa-exclamation-triangle mr-1'></i> Warranty registered successfully, but confirmation email could not be sent. Please save your Warranty ID.</p>";

        $message = "<div class='alert alert-success mt-4 shadow-sm' data-aos='zoom-in'>
                        <h4 class='alert-heading'><i class='fas fa-check-circle mr-2'></i>Warranty Activated Successfully!</h4>
                        <p>Thank you <strong>$name</strong>. Your warranty for <strong>" . ($product_name ?: $product_model) . "</strong> has been registered in our system.</p>
                        <div class='bg-light p-3 rounded my-3 border' style='border-left: 4px solid #2563EB !important;'>
                            <strong class='text-dark'>Your Warranty ID:</strong> <span class='text-primary font-weight-bold ml-2' style='font-size: 1.1rem;'>$warranty_id</span><br>
                            <strong class='text-dark'>Serial Number:</strong> <span class='ml-2'>$formatted_serial</span>
                        </div>
                        <p>Our team will review your documents. For any queries, contact our support at help@gkalmirah.com</p>
                        <div class='mt-3'>
                            <a href='$whatsapp_url' target='_blank' class='btn btn-success font-weight-bold shadow-sm'>
                                <i class='fab fa-whatsapp mr-2'></i>Confirm on WhatsApp
                            </a>
                        </div>
                        <hr>
                        $email_status_msg
                    </div>";

    } else {
        $message = "<div class='alert alert-danger mt-4'>Error: " . mysqli_error($con) . "</div>";
    }
}
?>

<div class="jumbotron jumbotron-custom text-white" style="background-color: #1E293B; padding: 4rem 2rem;">
    <div class="container text-center">
        <h1 class="display-4 font-weight-bold" data-aos="fade-down" style="color: #FFFFFF;">Activate Your Warranty</h1>
        <p class="lead" data-aos="fade-up" style="color: #F8FAFC;">Register your GK Almirah product to enjoy seamless service, priority support, and long-term reliability.</p>
    </div>
</div>

<div class="container section-padding mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="glass-card p-5 shadow-lg" data-aos="fade-up" style="background-color: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 20px; margin-top: -50px;">
                <?php if($message) echo $message; else { ?>
                <h3 class="mb-5 text-center font-weight-bold" style="color: #1E293B;"><?php echo $product_name ? "Warranty Registration for $product_name" : "Warranty Registration Form"; ?></h3>
                
                <form method="post" enctype="multipart/form-data">
                    
                    <!-- Section: Customer Details -->
                    <div class="mb-5">
                        <h5 class="text-uppercase font-weight-bold mb-4" style="border-left: 5px solid #2563EB; padding-left: 15px; color: #111827;">🔶 Customer Details</h5>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter Full Name" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Mobile Number</label>
                                <input type="text" name="mobile" class="form-control" placeholder="Enter Mobile Number" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="Enter Email Address" required autocomplete="off">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold">City</label>
                                <input type="text" name="city" class="form-control" placeholder="City" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="font-weight-bold">State</label>
                                <input type="text" name="state" class="form-control" placeholder="State" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 form-group">
                                <label class="font-weight-bold">Full Address</label>
                                <input type="text" name="address" class="form-control" placeholder="Flat No, Street, Landmark" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold">Postal Code (PIN Code)</label>
                                <input type="text" name="postal_code" class="form-control" placeholder="6-digit PIN" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label class="font-weight-bold">Create Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter Password" required autocomplete="new-password">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Product Details -->
                    <div class="mb-5">
                        <h5 class="text-uppercase font-weight-bold mb-4" style="border-left: 5px solid #2563EB; padding-left: 15px; color: #111827;">🔶 Product Details</h5>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Product Model (Optional)</label>
                                <input type="text" name="product_model" class="form-control" value="<?php echo $product_name; ?>" placeholder="Enter Model Name">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Date of Purchase</label>
                                <input type="date" name="purchase_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Dealer / Store Name</label>
                                <input type="text" name="dealer_name" class="form-control" placeholder="Enter Dealer Name" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Invoice Number (GST Bill No.)</label>
                                <input type="text" name="invoice_number" class="form-control" placeholder="Enter Invoice Number" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Product Serial Number</label>
                            <input type="text" name="serial" class="form-control" placeholder="e.g. GKA-2024-XXXX" required>
                        </div>
                    </div>

                    <!-- Section: Upload Required Documents -->
                    <div class="mb-5">
                        <h5 class="text-uppercase font-weight-bold mb-4" style="border-left: 5px solid #2563EB; padding-left: 15px; color: #111827;">🔶 Upload Required Documents</h5>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-muted small">Upload Barcode Image</label>
                                <input type="file" name="barcode_file" class="form-control-file border p-1 rounded">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-muted small">Upload GST Invoice (Mandatory)</label>
                                <input type="file" name="gst_invoice_file" class="form-control-file border p-1 rounded" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-muted small">Upload Warranty Card (Mandatory)</label>
                                <input type="file" name="warranty_card_file" class="form-control-file border p-1 rounded" required>
                            </div>
                        </div>
                        <div class="alert alert-info py-2 mt-2">
                           <i class="fas fa-info-circle mr-1"></i> <small>Note: Warranty activation will not be processed without valid documents.</small>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" class="form-check-input" id="declareCheck" required>
                        <label class="form-check-label small" for="declareCheck">
                            I hereby declare that the information provided above is true and correct. I agree to the warranty terms and conditions.
                        </label>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <button type="submit" name="activate" class="btn btn-warranty-primary btn-lg px-5 shadow font-weight-bold">ACTIVATE WARRANTY</button>
                    </div>
                </form>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Warranty Information Sections -->
<div class="container mb-5">
    <div class="row">
        <!-- Covered -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm" style="border-radius: 15px; background-color: #FFFFFF; border: 1px solid #E5E7EB;">
                <div class="card-body">
                    <h5 class="card-title font-weight-bold text-success border-bottom pb-2 mb-3">
                        <i class="fas fa-shield-alt mr-2"></i>What is Covered Under Warranty
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Lock Malfunctioning (Manufacturing Defect Only)</li>
                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Peeling of Paint (Under Normal Usage Conditions)</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Not Covered -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm" style="border-radius: 15px; background-color: #FFFFFF; border: 1px solid #E5E7EB;">
                <div class="card-body">
                    <h5 class="card-title font-weight-bold text-danger border-bottom pb-2 mb-3">
                        <i class="fas fa-times-circle mr-2"></i>Warranty Does Not Cover
                    </h5>
                    <div class="row">
                        <div class="col-sm-6">
                            <ul class="list-unstyled small">
                                <li class="mb-2">✖ Broken Key</li>
                                <li class="mb-2">✖ External Damage to Lock/Body</li>
                                <li class="mb-2">✖ Scratches by Sharp Objects</li>
                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <ul class="list-unstyled small">
                                <li class="mb-2">✖ Loss or Theft of Stored Items</li>
                                <li class="mb-2">✖ Damage Due to Mishandling/Misuse</li>
                                <li class="mb-2">✖ Normal Wear and Tear</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terms -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm" style="border-radius: 15px; background-color: #FFFFFF; border: 1px solid #E5E7EB;">
                <div class="card-body">
                    <h5 class="card-title font-weight-bold text-dark border-bottom pb-2 mb-3">
                        <i class="fas fa-file-contract mr-2"></i>Important Terms
                    </h5>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2">• Warranty is valid only with GST Invoice and Warranty Card</li>
                        <li class="mb-2">• Unauthorized repairs will void warranty</li>
                        <li class="mb-2">• Product must be used under normal conditions</li>
                        <li class="mb-2">• GK Almirah may inspect product before approval</li>
                        <li>• Final decision lies with GK Almirah</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Commitment -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm text-white bg-dark-custom" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="card-title font-weight-bold border-bottom pb-2 mb-3" style="color: #FFFFFF !important;">
                        <i class="fas fa-handshake mr-2"></i>Our Commitment
                    </h5>
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-white rounded-circle p-2 mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; color: #60A5FA;">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold" style="color: #FFFFFF !important;">24-Hour Service Response</h6>
                            <small style="color: #F8FAFC !important;">(T&C Apply)</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-white rounded-circle p-2 mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; color: #60A5FA;">
                            <i class="fas fa-home"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold" style="color: #FFFFFF !important;">Free Home Service</h6>
                            <small style="color: #F8FAFC !important;">(Selected Locations Only)</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle p-2 mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; color: #60A5FA;">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold" style="color: #FFFFFF !important;">Dedicated Customer Support</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('include/footer.php'); ?>

<style>
    body { background-color: #F8FAFC; color: #111827; line-height: 1.6; }
    label { color: #111827 !important; font-weight: 600; }
    
    /* Card/Section Shadow Updates */
    .glass-card, .card {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
        border: 1px solid #E5E7EB;
        background-color: #FFFFFF;
    }
    
    /* Heading Refinements */
    h3, h5 { 
        color: #1E293B !important; 
        margin-bottom: 10px !important;
        letter-spacing: 0.5px !important;
    }
    
    /* Text Readability - Global */
    p, li, .form-check-label {
        line-height: 1.6 !important;
        color: #111827 !important;
    }

    /* Fix visibility on Dark Backgrounds */
    .text-white, .jumbotron p, .bg-dark-custom p, .bg-dark-custom h6, .bg-dark-custom small, .bg-dark-custom h5 {
        color: #FFFFFF !important;
    }
    .jumbotron .lead {
        color: #F8FAFC !important;
        opacity: 0.9;
    }

    /* Primary Button Styling */
    .btn-warranty-primary {
        background-color: #2563EB !important;
        color: #FFFFFF !important;
        border: none !important;
        transition: all 0.3s ease;
    }
    .btn-warranty-primary:hover {
        background-color: #1D4ED8 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
    }

    /* Commitment Card - Dark Blue Theme Fixes */
    .bg-dark-custom {
        background-color: #1E293B !important;
        border: none !important;
    }
    
    .text-secondary { color: #4B5563 !important; }
</style>

