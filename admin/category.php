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
      
        <?php
        if(isset($_GET['del'])){
            $del   = $_GET['del'];
            $query = "DELETE FROM categories WHERE id = $del";
            $run   = mysqli_query($con,$query);
        }
        ?>
        <div class="col-md-9 col-lg-9">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-th-list text-primary mr-2"></i> Manage Categories</h5>
                </div>
                <div class="card-body">
                    <form action="" method="post" class="mb-4">
                        <?php
                            if(isset($_POST['submit'])){
                               $category = $_POST['category'];
                               $fontawesome = $_POST['fonts'];
                               $query = "INSERT INTO `categories`(`category`, `fontawesome_icon`) VALUES ('$category',' $fontawesome')";
                               $run = mysqli_query($con,$query);
                            } 
                        ?>
                        <div class="row bg-light p-3 rounded border align-items-center">
                            <div class="col-lg-5">
                                <label class="font-weight-bold small text-muted">Category Name</label>
                                <input type="text" name="category" class="form-control" placeholder="e.g. Almirahs" required>
                            </div>
                            <div class="col-lg-5">
                                <label class="font-weight-bold small text-muted">Icon Class</label>
                                <input type="text" name="fonts" class="form-control" placeholder="e.g. fa-box" required>
                            </div>
                            <div class="col-lg-2 text-right mt-4">
                                <input type="submit" name="submit" class="btn btn-primary btn-block shadow-sm" value="Add">
                            </div>
                        </div>
                    </form>

                    <?php
                    $r_data = 6;
                    $pquery = "SELECT * FROM categories";
                    $prun   = mysqli_query($con,$pquery);
                    $prow   = mysqli_num_rows($prun);
                    $page   = ceil($prow / $r_data);
                    
                    if(isset($_GET['tdata_id'])){
                         $t_id =$_GET['tdata_id'];
                    } else {
                        $t_id =1;
                    }
                    $pro_start = ($t_id - 1) * $r_data;  
                    $query=" SELECT * FROM categories ORDER BY id ASC LIMIT $pro_start,$r_data";
                    $run = mysqli_query($con,$query);
                    
                    if(mysqli_num_rows($run) > 0){
                    ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                               <tr>
                                <th class="font-weight-bold">ID</th>
                                <th class="font-weight-bold">Icon</th>
                                <th class="font-weight-bold">Category Name</th>
                                <th class="font-weight-bold text-center">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                             <?php
                                while($row = mysqli_fetch_array($run)){
                                    $id = $row['id'];
                                    $font_awesome = $row['fontawesome_icon'];
                                    $category = ucfirst($row['category']);
                                  ?>
                                 <tr>                     
                                    <td class="align-middle"><strong>#<?php echo $id;?></strong></td>
                                    <td class="align-middle"><i class="text-primary fa-2x <?php echo 'fad '.$font_awesome;?>"></i></td>
                                    <td class="align-middle font-weight-bold text-dark"><?php echo $category;?></td>
                                    <td class="align-middle text-center">
                                        <a href="editcat.php?edit=<?php echo $id; ?>" class="btn btn-primary btn-sm shadow-sm mr-1" style="border-radius: 6px;" title="Edit Category">
                                            <i class="fad fa-edit mr-1"></i> Edit
                                        </a>
                                        <a href="category.php?del=<?php echo $id;?>" class="btn btn-danger btn-sm shadow-sm" style="border-radius: 6px;" title="Delete Category" onclick="return confirm('Delete this category?');">
                                            <i class="fad fa-trash-alt mr-1"></i> Delete
                                        </a>
                                    </td>
                                 </tr>
                                  <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                           <?php 
                           for($i=1; $i<= $page; $i++) {
                                echo "<li class='page-item ".($t_id == $i ? 'active' : ''). "'><a class='page-link' href='category.php?tdata_id=".$i."'>$i</a></li>";
                           }
                           ?>
                        </ul>
                    </nav>
                  <?php } else { ?>
                      <div class="text-center py-5 text-muted">
                          <i class="fad fa-tags fa-3x mb-3 d-block"></i>
                          <h5>No categories found</h5>
                      </div>
                  <?php } ?>    
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('include/footer.php'); ?>
