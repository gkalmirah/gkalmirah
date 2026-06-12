<?php 
require_once('include/header.php');

if(!isset($_SESSION['email'])){
    header('location: signin.php');
}

if(isset($_POST['update_settings'])){
    $settings = [
        'usp_brand' => $_POST['usp_brand'],
        'usp_guarantee' => $_POST['usp_guarantee'],
        'usp_delivery' => $_POST['usp_delivery'],
        'contact_phone' => $_POST['contact_phone'],
        'contact_email' => $_POST['contact_email']
    ];

    foreach($settings as $key => $value){
        $value = mysqli_real_escape_string($con, $value);
        $query = "UPDATE site_settings SET setting_value = '$value' WHERE setting_key = '$key'";
        mysqli_query($con, $query);
    }
    $msg = "Settings Updated Successfully";
}
?>

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-3 col-lg-3">
            <?php require_once('include/sidebar.php'); ?>
        </div>
        
        <div class="col-md-9 col-lg-9">
            <div class="card shadow-sm border-0 mb-4 mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-cogs text-primary mr-2"></i> Site Settings</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($msg)) { echo "<div class='alert alert-success'>$msg</div>"; } ?>
                    
                    <form method="post">
                        <!-- USP Bar Settings -->
                        <h5 class="text-primary mt-3">USP Top Bar Content</h5>
                        <hr>
                        <?php 
                        $q = "SELECT * FROM site_settings";
                        $r = mysqli_query($con, $q);
                        $data = [];
                        while($row = mysqli_fetch_assoc($r)){
                            $data[$row['setting_key']] = $row['setting_value'];
                        }
                        ?>
                        
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Brand USP (Icon: Trophy):</label>
                            <div class="col-sm-9">
                                <input type="text" name="usp_brand" class="form-control" value="<?php echo isset($data['usp_brand']) ? $data['usp_brand'] : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Guarantee Text (Icon: Shield):</label>
                            <div class="col-sm-9">
                                <input type="text" name="usp_guarantee" class="form-control" value="<?php echo isset($data['usp_guarantee']) ? $data['usp_guarantee'] : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Delivery Text (Icon: Truck):</label>
                            <div class="col-sm-9">
                                <input type="text" name="usp_delivery" class="form-control" value="<?php echo isset($data['usp_delivery']) ? $data['usp_delivery'] : ''; ?>">
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <h5 class="text-primary mt-4">Contact Information</h5>
                        <hr>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Phone Number:</label>
                            <div class="col-sm-9">
                                <input type="text" name="contact_phone" class="form-control" value="<?php echo isset($data['contact_phone']) ? $data['contact_phone'] : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Email Address:</label>
                            <div class="col-sm-9">
                                <input type="text" name="contact_email" class="form-control" value="<?php echo isset($data['contact_email']) ? $data['contact_email'] : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group row mt-4">
                            <div class="col-sm-12 text-center mt-3">
                                <button type="submit" name="update_settings" class="btn btn-primary px-5 shadow-sm"><i class="fad fa-save mr-2"></i> Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('include/footer.php'); ?>
