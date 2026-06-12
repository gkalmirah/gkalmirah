<?php 
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('include/dbcon.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Premium Furniture Admin Dashboard">
  <meta name="author" content="">

  <title>GK Almirah Premium | Admin Dashboard</title>

  <link href="css/mdb.min.css" rel="stylesheet">
  <!-- Custom styles for this template-->
  <link href="css/bootstrap.css" rel="stylesheet">
  <link href="css/all.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  <!-- Chart.js for Dashboard Charts -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
</head>

<body id="page-top">
<nav class="navbar navbar-expand-md bg-white">
  <a class="navbar-brand" href="index.php"><i class="fad fa-gem"></i> GK Almirah</a>
  
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <i class="fad fa-bars text-dark"></i>
  </button>
  
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <!-- Search Bar -->
    <form class="form-inline ml-auto d-none d-md-flex search-bar mr-4">
      <i class="fad fa-search"></i>
      <input type="text" placeholder="Search orders, products..." aria-label="Search">
    </form>
    
    <ul class="navbar-nav align-items-center">
      <!-- Notifications -->
      <li class="nav-item mr-3">
        <a class="nav-link nav-icon-btn" href="#">
          <i class="fad fa-bell"></i>
          <span class="badge-notify">3</span>
        </a>
      </li>
      
       <?php 
         $image = 'default.png';
         if(isset($_SESSION['email']) )
         {
             $session_email = $_SESSION['email'];
             $query ="SELECT image from admin WHERE email='$session_email'";
             $run = mysqli_query($con,$query);
             if ($run) {
                 $row = mysqli_fetch_array($run);
                 if ($row && isset($row['image']) && !empty($row['image'])) {
                     $image = $row['image'];
                 }
             }
         }
         ?>
         
      <!-- Profile Dropdown -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <img src="img/<?php echo htmlspecialchars($image);?>" alt="user" class="rounded-circle mr-2" width="37px" height="37px" style="object-fit:cover; border: 2px solid var(--accent-gold);">
          <span class="d-none d-md-inline font-weight-bold" style="color:var(--primary-navy);">Admin</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right shadow-sm border-0" aria-labelledby="navbarDropdown" style="border-radius:12px;">
          <a class="dropdown-item py-2" href="profile.php"><i class="fad fa-user-circle mr-2 text-muted"></i> My Profile</a>
          <a class="dropdown-item py-2" href="../index.php" target="_blank"><i class="fad fa-store mr-2 text-muted"></i> View Store</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item py-2 text-danger font-weight-bold" href="logout.php"><i class="fad fa-sign-out-alt mr-2"></i> Logout</a>
        </div>
      </li>
    </ul>
  </div>  
</nav>
