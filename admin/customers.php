<?php  
require_once('include/header.php');
if(!isset($_SESSION['email'])){
  header('location: signin.php');
  exit();
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
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-users text-primary mr-2"></i> View All Customers</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="font-weight-bold">ID</th>
                                    <th class="font-weight-bold">Name</th>
                                    <th class="font-weight-bold">Email</th>
                                    <th class="font-weight-bold">Password</th>
                                    <th class="font-weight-bold">Address</th>
                                    <th class="font-weight-bold">City</th>
                                    <th class="font-weight-bold">Postal Code</th>
                                    <th class="font-weight-bold">Contact Number</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php     
                                $query = "SELECT * FROM customer";
                                $run   = mysqli_query($con,$query);
                                            
                                if(mysqli_num_rows($run) > 0){
                                  while($row = mysqli_fetch_array($run)){
                                    $cust_id         = $row['cust_id'];
                                    $cust_name       = $row['cust_name'];
                                    $cust_email      = $row['cust_email'];
                                    $cust_pass       = $row['cust_pass'];
                                    $cust_add        = $row['cust_add'];    
                                    $cust_city       = $row['cust_city'];
                                    $cust_postalcode = $row['cust_postalcode'];
                                    $cust_number     = $row['cust_number'];
                                ?> 
                                 <tr>
                                     <td class="align-middle text-muted"><strong>#<?php echo $cust_id;?></strong></td>
                                     <td class="align-middle font-weight-bold text-dark"><?php echo $cust_name;?></td>
                                     <td class="align-middle"><a href="mailto:<?php echo $cust_email;?>" class="text-primary"><?php echo $cust_email;?></a></td>
                                     <td class="align-middle">
                                        <input type="password" class="form-control form-control-sm border-0 bg-light text-muted" value="<?php echo $cust_pass;?>" disabled style="max-width: 100px;">
                                     </td>
                                     <td class="align-middle text-muted small" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($cust_add); ?>"><?php echo $cust_add;?></td>
                                     <td class="align-middle"><span class="badge badge-light p-2 border"><?php echo $cust_city ?></span></td>
                                     <td class="align-middle"><?php echo $cust_postalcode;?></td>
                                     <td class="align-middle font-weight-bold"><?php echo $cust_number;?></td>
                                 </tr>   
                               <?php 
                                   }
                                } else {
                                    echo "<tr><td colspan='8' class='text-center text-muted py-5'><i class='fad fa-users fa-3x mb-3 d-block'></i><h5>No customers registered yet</h5></td></tr>";
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

<?php require_once('include/footer.php'); ?>
