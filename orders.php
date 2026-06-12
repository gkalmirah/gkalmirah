<?php 
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['email'])){
  header('location: sign-in.php');
  exit();
}

include('include/cust_header.php');
?>

   <div class="jumbotron jumbotron-custom text-white">
    <div class="container text-center">
        <h2 class="display-4">My Orders</h2>
        <p class="lead">Track your orders</p>
    </div>
</div>
<style>
   .jumbotron-custom {
        position: relative;
        /* background-image: url('path-to-your-background-image.jpg'); Replace with your background image path */
        background-size: cover;
        background-position: center;
        color: #fff;
        padding: 100px 25px;
        margin-bottom: 0;
    }

    .jumbotron-custom::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); /* Black overlay with opacity */
        z-index: 1;
    }

    .jumbotron-custom .container {
        position: relative;
        z-index: 2;
    }

    .jumbotron-custom h2 {
        font-size: 3.5rem;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .jumbotron-custom p.lead {
        font-size: 1.5rem;
        margin-bottom: 0;
    }

</style>
     
     <div class="container mt-5 mb-5">
      
      <div class="row">

        <div class="col-md-10 mx-auto">
          <!-- Back to Account Link -->
          <div class="mb-4">
              <a href="cust.php" style="color: #007185; text-decoration: none; font-weight: 500;">
                  <i class="fas fa-chevron-left mr-1"></i> Back to Your Account
              </a>
          </div>
          <!-- <h3>My Orders:</h3><hr> -->
          <?php 
          if (isset($_SESSION['id'])) {
              $customer_id = $_SESSION['id'];

              $order_query = "SELECT * FROM customer_order WHERE customer_id=$customer_id";
              $run = mysqli_query($con, $order_query);

              if ($run && mysqli_num_rows($run) > 0) {


                  if(isset($_SESSION['message'])){
                    echo $_SESSION['message'];
                  }
          ?>      
                   <table class="table table-responsive table-hover ">
                      <thead class="thead-light">
                          <tr>
                              <th>#Invoice</th>
                              <th width="120px">Product image</th>
                              <th>Product name</th>
                              <th>Product quantity</th>
                              <th>Total Price (MRP)</th>
                              <th>Date</th>
                              <th width="120px">Status</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                     
                      <tbody>
                          <?php
                              
                                    while($order_row = mysqli_fetch_array($run)){
                                      $order_invoice = $order_row['invoice_no'];
                                      $order_pro_id  = $order_row['product_id'];
                                      $order_qty     = $order_row['products_qty'];
                                      $order_amount  = $order_row['product_amount'];
                                      $order_date    = $order_row['order_date'];
                                      $order_status  = $order_row['order_status'];

                                      $pro_query  = "SELECT * FROM furniture_product WHERE product_id=$order_pro_id";
                                      $pro_run    = mysqli_query($con,$pro_query);
                                                      
                                       if(mysqli_num_rows($pro_run) > 0){
                                        while($pr_row = mysqli_fetch_array($pro_run)){
                                             
                                             $title = $pr_row['product_name'];
                                             $img1 = $pr_row['product_img1'];
                                           
                                  
                                    
                            ?> 
                             <tr>
                                <td>#<?php echo $order_invoice;?></td>
                                 <td>
                                     <img src="../img/<?php echo $img1;?>" width="100%">
                                 </td>
                                 <td>
                                    <h6><?php echo $title;?></h6>
                                  
                                 </td>
                                 <td>
                                    x <?php echo $order_qty;?>
                                 </td>
                                 <td><?php echo $order_amount;?> </td>
                                 <td><?php echo $order_date;?></td>
                                 <td>
                                   <?php 
                                     $status_lower = strtolower($order_status);
                                     if($status_lower == 'pending' || $status_lower == 'processing'){
                                       echo "<i class='far fa-clock text-warning'></i> $order_status";
                                     } else if($status_lower == 'verified' || $status_lower == 'confirmed' || $status_lower == 'placed'){
                                       echo "<i class='far fa-check-circle text-success'></i> $order_status";
                                     } else if($status_lower == 'shipped' || $status_lower == 'out for delivery' || $status_lower == 'delivered'){
                                       echo "<i class='far fa-truck text-primary'></i> $order_status";
                                     } else {
                                       echo $order_status;
                                     }
                                   ?> 
                                 </td>
                                 <td>
                                     <a href="order_success.php?invoice=<?php echo $order_invoice; ?>" class="btn btn-sm btn-outline-info" style="border-color: #D4AF37; color: #0F172A;">
                                         <i class="fas fa-file-invoice"></i> Receipt
                                     </a>
                                 </td>
                             </tr>   
                            <?php
                              }
                            } 
                          } 
                              ?>
                          
                      </tbody>
                   </table>
              <?php                     
                            
                         } else {
?>
                             <div class="text-center py-5 mt-4" style="background: #fff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
                                 <div class="mb-4">
                                     <i class="fas fa-box-open" style="font-size: 5rem; color: #cbd5e1;"></i>
                                 </div>
                                 <h3 class="mb-3" style="color: #1e293b; font-weight: 700;">No Orders Yet</h3>
                                 <p class="text-muted mb-4" style="font-size: 1.1rem; max-width: 400px; margin: 0 auto;">It looks like you haven't placed any orders. Start exploring our premium collection today!</p>
                                 <a href="product.php" class="btn btn-primary px-5 py-2" style="border-radius: 30px; font-weight: 600; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4); background: var(--accent-gold, #d4af37); border: none; color: #fff;">Start Shopping</a>
                             </div>
<?php
                         }
          } else {
              echo "<h2 class='text-center text-secondary mt-5 mb-5'>Please log in to view orders</h2>";
          }
          ?>

          
         </div>
       </div>

     </div>
      
   

     <?php include('include/footer.php');?>