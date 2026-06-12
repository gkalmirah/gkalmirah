<div class="dash-sidebar" style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;">
    <div class="text-center mb-4">
        <img src="img/logogk1.png" alt="GK Almirah" style="max-width: 120px; height: auto;">
    </div>
    <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
    <a href="cust.php" class="list-group-item <?php echo ($current_page == 'cust.php') ? 'active' : ''; ?>" style="border:none; margin-bottom: 5px; border-radius: 8px;">
        <i class="fas fa-home mr-2 text-primary"></i> Account Dashboard
    </a>
    <a href="orders.php" class="list-group-item <?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>" style="border:none; margin-bottom: 5px; border-radius: 8px;">
        <i class="fas fa-box-open mr-2 text-info"></i> My Orders
    </a>
    <a href="track-order.php" class="list-group-item <?php echo ($current_page == 'track-order.php') ? 'active' : ''; ?>" style="border:none; margin-bottom: 5px; border-radius: 8px;">
        <i class="fas fa-truck mr-2 text-success"></i> Track My Order
    </a>
    <a href="personal-detail.php" class="list-group-item <?php echo ($current_page == 'personal-detail.php') ? 'active' : ''; ?>" style="border:none; margin-bottom: 5px; border-radius: 8px;">
        <i class="fas fa-map-marked-alt mr-2 text-secondary"></i> Address Details
    </a>
    <a href="access-detail.php" class="list-group-item <?php echo ($current_page == 'access-detail.php') ? 'active' : ''; ?>" style="border:none; margin-bottom: 5px; border-radius: 8px;">
        <i class="fas fa-shield-alt mr-2 text-dark"></i> Access Details
    </a>
    <a href="sign-out.php" class="list-group-item mt-4 text-danger" style="border:none; border-radius: 8px; background: #fff5f5;">
        <i class="fas fa-sign-out-alt mr-2 text-danger"></i> Sign Out
    </a>
</div>