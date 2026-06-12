<?php 

 require_once('include/header.php');



if(!isset($_SESSION['email'])){

    header('location: signin.php');

}

if(isset($_SESSION['email'])){

    $session_id = $_SESSION['id'];

    $session_email = $_SESSION['email'];

   

}

?>

<div class="container-fluid mt-2">

     <div class="row">

<?php 
 require_once('include/header.php');

if(!isset($_SESSION['email'])){
    header('location: signin.php');
}
if(isset($_SESSION['email'])){
    $session_id = $_SESSION['id'];
    $session_email = $_SESSION['email'];
   
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
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-user-cog text-primary mr-2"></i> Profile Settings</h5>
                </div>
                <div class="card-body">
             <form method="post" enctype="multipart/form-data">
                <?php
                 $query = "SELECT * FROM admin WHERE email='$session_email'";
                 $run   = mysqli_query($con,$query);
                 $row   = mysqli_fetch_array($run);
                    
                    $db_name         = $row['name'];
                    $db_email        = $row['email'];
                    $db_password     = $row['password'];               
                    $db_image        = $row['image'];
                    
                 
                 
                 if(isset($_POST['submit']))
                 {  
                     $name         = $_POST['name'];
                     $password     = $_POST['password'];                   
                     $image        = $_FILES['upload']['name'];
                     $tmp_image    = $_FILES['upload']['tmp_name'];
                     
                     
                     $u_query ="UPDATE admin SET name='$name', password='$password', image='$image' WHERE id ='$session_id'";
                      if(mysqli_query($con,$u_query)){
                         $message = "Profile Has Been Updated";
                         if(move_uploaded_file($tmp_image,"img/".$image)){
                             header('location:profile.php');
                         }
                          
                     }
                 }
                 ?>
                 <?php if(isset($message)){
                        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                <i class='fad fa-check-circle mr-2'></i> $message
                                <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                              </div>";
                    }?>

                 <div class="row">
                   <div class="col-md-8">
                      <div class="form-group">
                        <label class="font-weight-bold small text-muted">Admin Email Address</label>
                        <input type="text" class="form-control bg-light text-muted" value="<?php echo $db_email;?>" disabled>
                      </div>

                      <div class="form-group mt-3">
                        <label class="font-weight-bold small text-muted">Full Name</label>
                        <input type="text" name="name" value="<?php echo $db_name;?>" class="form-control" required>
                      </div>

                      <div class="form-group mt-3">
                        <label class="font-weight-bold small text-muted">Password</label>
                        <input type="password" name="password" value="<?php echo $db_password;?>" class="form-control" required>
                      </div>

                      <div class="form-group mt-4 p-3 border rounded bg-light">
                          <label class="font-weight-bold small text-muted d-block mb-2">Profile Avatar</label>
                          <input type="file" name="upload" class="form-control-file">
                      </div>
                      
                      <button type="submit" name="submit" class="btn btn-primary px-4 mt-3 shadow-sm">Save Changes</button>
                    </div>
                  
                    <div class="col-md-4 text-center mt-4 mt-md-0">
                      <div class="p-3 border rounded h-100 d-flex flex-column align-items-center justify-content-center bg-light">
                          <label class="font-weight-bold small text-muted mb-3">Current Avatar</label>
                          <img src="img/<?php echo $db_image;?>" class="img-thumbnail rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover;" onerror="this.src='img/placeholder.jpg'">
                      </div>
                    </div>
                 </div>
                </form>
              </div>
            </div>
        </div>
    </div>

     <?php 
 require_once('include/footer.php');
?>
