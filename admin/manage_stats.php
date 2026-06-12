<?php 
require_once('include/header.php');

if(!isset($_SESSION['email'])){
    header('location: signin.php');
}

// Update Stats
if(isset($_POST['update_stats'])){
    $id = $_POST['stat_id'];
    $value = mysqli_real_escape_string($con, $_POST['value']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    
    $query = "UPDATE stats SET value='$value', title='$title' WHERE id='$id'";
    if(mysqli_query($con, $query)){
        $msg = "Stat Updated Successfully";
    }
}
?>

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-3 col-lg-3">
            <?php require_once('include/sidebar.php'); ?>
        </div>
        
        <div class="col-md-9 col-lg-9">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h3 class="card-title"><i class="fad fa-chart-bar"></i> Manage Stats (Why Choose Us)</h3>
                </div>
                <div class="card-body">
                    <?php if(isset($msg)) { echo "<div class='alert alert-success'>$msg</div>"; } ?>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Title</th>
                                    <th>Value</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $q = "SELECT * FROM stats ORDER BY ordering ASC";
                                $r = mysqli_query($con, $q);
                                while($row = mysqli_fetch_array($r)){
                                ?>
                                form method="post" action="">
                                <tr>
                                    <form method="post">
                                        <td>
                                            <input type="text" name="title" class="form-control" value="<?php echo $row['title']; ?>">
                                        </td>
                                        <td>
                                            <input type="text" name="value" class="form-control" value="<?php echo $row['value']; ?>">
                                        </td>
                                        <td>
                                            <input type="hidden" name="stat_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="update_stats" class="btn btn-primary btn-sm"><i class="fad fa-save"></i> Update</button>
                                        </td>
                                    </form>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('include/footer.php'); ?>
