<?php 

include('include/header.php');

if(!isset($_SESSION['email'])){
    header('location:signin.php');
}
if(isset($_SESSION['email'])){
     $email = $_SESSION['email'];
    }

?>
<div class="container-fluid mt-2">
    <div class="row">
        <!---sidenavbar Column-->
        <div class="col-md-3 col-lg-3">
            <?php require_once('include/sidebar.php'); ?>
        </div>
        <!---Main Column -->
        <div class="col-md-9 col-lg-9">
            <!-- Premium Stats Cards-->
            <div class="row mb-4">
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card stat-revenue h-100">
                        <div class="card-body-icon">
                            <i class="fad fa-sack text-warning"></i>
                        </div>
                        <a href="#" class="text-decoration-none">
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h5>Total Revenue</h5>
                                    <?php  
                                       $query = "SELECT SUM(product_amount) as 'earn' FROM customer_order";
                                       $run   = mysqli_query($con,$query);
                                       $row=mysqli_fetch_array($run);
                                       $earning = $row['earn'] ? $row['earn'] : 0;
                                     ?>
                                    <h2>PKR <?php echo number_format($earning); ?></h2>
                                </div>
                                <div class="stat-icon-wrapper">
                                    <i class="fad fa-sack"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card stat-orders h-100">
                        <div class="card-body-icon">
                            <i class="fad fa-users text-primary"></i>
                        </div>
                        <a href="customers.php" class="text-decoration-none">
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h5>Active Customers</h5>
                                    <?php  
                                       $query = "SELECT * FROM customer";
                                       $run   = mysqli_query($con,$query);
                                       $num_customer = mysqli_num_rows($run);
                                     ?>
                                    <h2><?php echo $num_customer;?></h2>
                                </div>
                                <div class="stat-icon-wrapper">
                                    <i class="fad fa-users"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card stat-pending h-100">
                        <div class="card-body-icon">
                            <i class="fad fa-shopping-cart text-warning"></i>
                        </div>
                        <a href="pending_furniture_pro.php" class="text-decoration-none">
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h5>Pending Orders</h5>
                                    <?php  
                                       $query = "SELECT * FROM customer_order WHERE order_status='pending'";
                                       $run   = mysqli_query($con,$query);
                                       $num_new_orders = mysqli_num_rows($run);
                                     ?>
                                    <h2><?php echo $num_new_orders;?></h2>
                                </div>
                                <div class="stat-icon-wrapper">
                                    <i class="fad fa-shopping-cart"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card stat-delivered h-100">
                        <div class="card-body-icon">
                            <i class="fad fa-truck text-success"></i>
                        </div>
                        <a href="delivered_furniture_pro.php" class="text-decoration-none">
                            <div class="stat-card">
                                <div class="stat-info">
                                    <h5>Delivered Orders</h5>
                                    <?php  
                                       $query = "SELECT * FROM customer_order WHERE order_status='delivered'";
                                       $run   = mysqli_query($con,$query);
                                       $num_delivered_orders = mysqli_num_rows($run);
                                     ?> 
                                    <h2><?php echo $num_delivered_orders;?></h2>
                                </div>
                                <div class="stat-icon-wrapper">
                                    <i class="fad fa-truck"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Charts & Overview Section -->
            <div class="row mb-5">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h5 class="text-muted font-weight-bold mb-4 text-uppercase">Revenue & Orders Overview</h5>
                            <canvas id="revenueChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h5 class="text-muted font-weight-bold mb-4 text-uppercase">Quick Actions</h5>
                            <div class="list-group list-group-flush">
                                <a href="furniture_pro.php" class="list-group-item list-group-item-action d-flex align-items-center">
                                    <i class="fad fa-plus-circle text-success fa-lg mr-3"></i> 
                                    <div>
                                        <h6 class="mb-0 text-dark">Add Product</h6>
                                        <small class="text-muted">Create a new listing</small>
                                    </div>
                                </a>
                                <a href="manage_warranties.php" class="list-group-item list-group-item-action d-flex align-items-center">
                                    <i class="fad fa-shield-check text-warning fa-lg mr-3"></i> 
                                    <div>
                                        <h6 class="mb-0 text-dark">Warranties</h6>
                                        <small class="text-muted">Approve pending claims</small>
                                    </div>
                                </a>
                                <a href="pending_furniture_pro.php" class="list-group-item list-group-item-action d-flex align-items-center">
                                    <i class="fad fa-box-open text-primary fa-lg mr-3"></i> 
                                    <div>
                                        <h6 class="mb-0 text-dark">Pending Orders</h6>
                                        <small class="text-muted">Verify new customer orders</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
                <h3 class="mb-0">New Orders</h3>
                <a href="pending_furniture_pro.php" class="btn btn-primary btn-sm">View All</a>
            </div>
            <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>#Invoice No.</th>
                        <th>Order ID</th>
                        <th>Product Image</th>
                        <th>Product Category</th>
                        <th>Customer Email</th>
                        <th>Price (Pkr)</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                        $order_query = "SELECT * FROM customer_order WHERE order_status='pending' ORDER BY order_id LIMIT 5";
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

                                // Updated query to join with the new product_categories table
                                $pr_query = "SELECT fp.product_id, fp.product_img1, cat.category 
                                             FROM furniture_product fp 
                                             INNER JOIN product_categories pc ON fp.product_id = pc.product_id 
                                             INNER JOIN categories cat ON pc.category_id = cat.id 
                                             WHERE fp.product_id = $order_pro_id";
                                $pr_run   = mysqli_query($con,$pr_query);
                                
                                if(mysqli_num_rows($pr_run) > 0){
                                    while($pr_row = mysqli_fetch_array($pr_run)){
                                        $pid   = $pr_row['product_id'];
                                        $image = $pr_row['product_img1'];
                                        $category = $pr_row['category'];
                    ?> 
                        <tr>
                            <td><strong><?php echo $order_invoice;?></strong></td>
                            <td><?php echo $order_id;?></td>
                            <td>
                                <img src="../img/<?php echo $image ? $image : 'placeholder.jpg';?>" class="table-product-img" onerror="this.src='../img/placeholder.jpg'">
                            </td>
                            <td><?php echo $category;?></td>
                            <td><?php echo $cust_email;?></td>
                            <td><strong><?php echo number_format($order_amount);?></strong></td>
                            <td><?php echo $order_qty;?></td>
                            <td><span class="badge badge-pending"><i class="fad fa-clock"></i> Pending</span></td>
                            <td><?php echo date('d M, Y', strtotime($order_date));?></td>
                            <td><a href="pending_furniture_pro.php" class="btn btn-primary btn-sm">Verify</a></td>
                        </tr>   
                    <?php 
                                  }
                                }
                              }

                            }else {
                              echo "<tr><td colspan='10'><h4 class='text-center text-muted my-4'>No pending orders</h4></td></tr>";
                            }
                        
                     
                    
                    ?>
                </tbody>
            </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
                <h3 class="mb-0">Customers Account</h3>
                <a href="customers.php" class="btn btn-primary btn-sm">View All</a>
            </div>
            <div class="table-responsive mb-5">
            <table class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>#Cust ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>City</th>
                        <th>Postal code</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php     
                        $query = "SELECT * FROM customer ORDER BY cust_id DESC LIMIT 5";
                        $run   = mysqli_query($con,$query);
                                    
                        if(mysqli_num_rows($run) > 0){
                            while($row = mysqli_fetch_array($run)){
                                $cust_id         = $row['cust_id'];
                                $cust_name       = $row['cust_name'];
                                $cust_email      = $row['cust_email'];    
                                $cust_city       = $row['cust_city'];
                                $cust_postalcode = $row['cust_postalcode'];
                    ?> 
                        <tr>
                            <td><strong><?php echo $cust_id;?></strong></td>
                            <td><div class="font-weight-bold text-dark"><?php echo $cust_name;?></div></td>
                            <td><?php echo $cust_email;?></td>
                            <td><?php echo $cust_city; ?></td>
                            <td><?php echo $cust_postalcode;?></td>
                            <td><a href="customers.php" class="btn btn-light btn-sm"><i class="fad fa-arrow-right"></i></a></td>
                        </tr>   
                    <?php 
                           }

                        }else {
                          echo "<tr><td colspan='6'><h4 class='text-center text-muted my-4'>No Registered Customer Yet</h4></td></tr>";
                        }
                    ?>
                </tbody> 
            </table>   
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById('revenueChart').getContext('2d');
    var gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(15, 23, 42, 0.2)');   
    gradient.addColorStop(1, 'rgba(15, 23, 42, 0)');

    var revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: 'Revenue (PKR)',
                data: [12000, 19000, 15000, 22000, 18000, 25000, <?php echo $earning ? $earning : 30000; ?>],
                backgroundColor: gradient,
                borderColor: '#0f172a',
                borderWidth: 2,
                pointBackgroundColor: '#d4af37',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [5, 5] }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>

<?php include('include/footer.php');?>
