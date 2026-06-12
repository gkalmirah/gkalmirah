<?php include("include/header.php");
 if(!isset($_SESSION['email'])){
    header('location: signin.php');
}                

?>

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-3">
            <?php include("include/sidebar.php");?>
        </div>
        
        <div class="col-md-9">
            <div class="card shadow-sm border-0 mb-4 mt-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fad fa-box-check text-success mr-2"></i> Verified Orders</h5>
                </div>
                <div class="card-body p-0">
                    <form method="post">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                      <th class="font-weight-bold">Invoice No.</th>
                                      <th class="font-weight-bold">Order ID</th>
                                      <th class="font-weight-bold">Product ID</th>
                                      <th class="font-weight-bold">Image</th>
                                      <th class="font-weight-bold">Category</th>
                                      <th class="font-weight-bold">Cust ID</th>
                                      <th class="font-weight-bold">Email</th>
                                      <th class="font-weight-bold">Price</th>
                                      <th class="font-weight-bold">Qty</th>
                                      <th class="font-weight-bold">Status</th>
                                      <th class="font-weight-bold">Date</th>
                                      <th class="font-weight-bold text-center">Invoice</th>
                                      <th class="font-weight-bold text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                  <?php
                                    $order_query = "SELECT * FROM customer_order WHERE order_status='verified'";
                                    $run = mysqli_query($con,$order_query);
                        
                                    if(mysqli_num_rows($run) > 0){
                                        while($order_row = mysqli_fetch_array($run)){
                                            $order_invoice = $order_row['invoice_no'];
                                            $order_id      = $order_row['order_id'];
                                            $cust_id       = $order_row['customer_id'];
                                            $cust_email    = $order_row['customer_email'];
                                            $order_pro_id  = $order_row['product_id'];
                                            $order_qty     = $order_row['products_qty'];
                                            $order_amount  = $order_row['product_amount'];
                                            $order_date    = $order_row['order_date'];
                                            $order_status  = $order_row['order_status'];

                                            // Fetch correct category using product_categories mapping table
                                            $pr_query = "SELECT fp.*, GROUP_CONCAT(cat.category SEPARATOR ', ') AS categories 
                                                         FROM furniture_product fp 
                                                         LEFT JOIN product_categories pc ON fp.product_id = pc.product_id 
                                                         LEFT JOIN categories cat ON pc.category_id = cat.id 
                                                         WHERE fp.product_id = $order_pro_id
                                                         GROUP BY fp.product_id";
                                            $pr_run   = mysqli_query($con,$pr_query);
                                                
                                            if(mysqli_num_rows($pr_run) > 0){
                                                while($pr_row = mysqli_fetch_array($pr_run)){
                                                    $pid   = $pr_row['product_id'];
                                                    $image = $pr_row['product_img1'];
                                                    $category = $pr_row['category'];
                                                    $category = $pr_row['categories'];
                                              
                                    ?> 
                                     <tr>
                                         <td class="align-middle font-weight-bold text-dark">#<?php echo $order_invoice;?></td>
                                         <td class="align-middle"><?php echo $order_id;?></td>
                                         <td class="align-middle text-muted">#<?php echo $order_pro_id;?></td>
                                         <td width="70px" class="align-middle">
                                            <img src="../img/<?php echo $image;?>" class="img-thumbnail rounded" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.src='../img/placeholder.jpg'">
                                        </td>
                                         <td class="align-middle"><span class="badge badge-info p-2"><?php echo $category;?></span></td>
                                         <td class="align-middle"><?php echo $cust_id;?></td>
                                         <td class="align-middle text-muted"><?php echo $cust_email;?></td>
                                         <td class="align-middle text-success font-weight-bold">₹<?php echo number_format((float)$order_amount, 2);?></td>
                                         <td class="align-middle font-weight-bold"><?php echo $order_qty;?></td>
                                         <td class="align-middle">
                                            <span class="badge badge-success p-2"><i class="fad fa-check-circle mr-1"></i> Verified</span>
                                         </td>
                                         <td class="align-middle text-muted"><?php echo $order_date;?></td>
                                         <td class="align-middle text-center">
                                            <a href="invoice.php?invoice=<?php echo $order_invoice; ?>" class="btn btn-info btn-sm shadow-sm text-white" style="border-radius: 6px;" title="Download Invoice">
                                                <i class="fad fa-file-invoice mr-1"></i> Invoice
                                            </a>
                                         </td>
                                         <td class="align-middle text-center">
                                            <a href="edit_furn_verify_pen.php?order_id=<?php echo $order_id; ?>" class="btn btn-primary btn-sm shadow-sm" style="border-radius: 6px;" title="Edit Order Status">
                                                <i class="fad fa-edit mr-1"></i> Edit
                                            </a>
                                         </td>
                                     </tr>   
                                   <?php 
                                          }
                                        }
                                      } 

                                    }else {
                                        echo "<tr><td colspan='13' class='text-center text-muted py-5'><i class='fad fa-box-open fa-3x mb-3 d-block'></i><h5>No verified orders found</h5></td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("include/footer.php"); ?>
