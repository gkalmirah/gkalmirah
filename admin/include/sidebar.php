<?php $current_page = basename($_SERVER['PHP_SELF']); ?>

<div class="mb-3 d-md-none">
    <button class="btn btn-primary btn-block font-weight-bold shadow-sm text-left" type="button" data-toggle="collapse" data-target="#sidebarCollapse" aria-expanded="false" aria-controls="sidebarCollapse">
        <i class="fad fa-bars mr-2"></i> Navigation Menu
    </button>
</div>

<div class="collapse d-md-block" id="sidebarCollapse">
    <div class="list-group list-group-flush shadow-sm" id="sidebar">

        <li class="list-group-item list-item-group-action <?= ($current_page == 'index.php') ? 'active' : ''; ?>">
          <a href="index.php"><i class="fad fa-tachometer-alt"></i> Dashboard</a>
        </li>

        <li class="list-group-item list-item-group-action <?= ($current_page == 'pending_furniture_pro.php' || $current_page == 'delivered_furniture_pro.php') ? 'active' : ''; ?>">
          <a href="#order" data-toggle="collapse"><i class="fad fa-shopping-cart"></i> Orders <i class="fad fa-caret-down float-right mt-1"></i></a>
          <ul class="collapse <?= ($current_page == 'pending_furniture_pro.php' || $current_page == 'delivered_furniture_pro.php') ? 'show' : ''; ?>" id="order" style="list-style:none;">
            <li class="list-group-item sub-item ml-3 <?= ($current_page == 'pending_furniture_pro.php') ? 'active' : ''; ?>" style="border:none"> 
                <a href="pending_furniture_pro.php"><i class="fad fa-exclamation-circle"></i> Pending Orders</a> 
            </li>
            <li class="list-group-item sub-item ml-3 <?= ($current_page == 'delivered_furniture_pro.php') ? 'active' : ''; ?>" style="border:none"> 
                <a href="delivered_furniture_pro.php"><i class="fad fa-truck"></i> Delivered Orders</a> 
            </li>
          </ul>
        </li>

        <li class="list-group-item list-item-group-action <?= ($current_page == 'furniture_pro.php') ? 'active' : ''; ?>"> 
          <a href="furniture_pro.php"><i class="fad fa-plus"></i> Add Furniture Products</a>
        </li>

        <li class="list-group-item list-item-group-action <?= ($current_page == 'furniture_pro_view.php') ? 'active' : ''; ?>"> 
          <a href="furniture_pro_view.php"><i class="fad fa-th-list"></i> View Furniture Products</a>
        </li>

        <li class="list-group-item list-item-group-action <?= ($current_page == 'category.php') ? 'active' : ''; ?>">
          <a href="category.php"><i class="fad fa-border-all"></i> Categories</a>
        </li>

        <li class="list-group-item list-item-group-action <?= (in_array($current_page, ['discounts.php', 'discount_add.php', 'discount_edit.php', 'festival_discounts.php', 'festival_add.php', 'festival_edit.php'])) ? 'active' : ''; ?>">
          <a href="#discount-mgmt" data-toggle="collapse"><i class="fad fa-tags"></i> Discount Management <i class="fad fa-caret-down float-right mt-1"></i></a>
          <ul class="collapse <?= (in_array($current_page, ['discounts.php', 'discount_add.php', 'discount_edit.php', 'festival_discounts.php', 'festival_add.php', 'festival_edit.php'])) ? 'show' : ''; ?>" id="discount-mgmt" style="list-style:none;">
            <li class="list-group-item sub-item ml-3 <?= (in_array($current_page, ['discounts.php', 'discount_add.php', 'discount_edit.php'])) ? 'active' : ''; ?>" style="border:none"> 
                <a href="discounts.php"><i class="fad fa-tag"></i> Product Discounts</a> 
            </li>
            <li class="list-group-item sub-item ml-3 <?= (in_array($current_page, ['festival_discounts.php', 'festival_add.php', 'festival_edit.php'])) ? 'active' : ''; ?>" style="border:none"> 
                <a href="festival_discounts.php"><i class="fad fa-gift"></i> Festival Discounts</a> 
            </li>
          </ul>
        </li>

        <li class="list-group-item list-item-group-action <?= ($current_page == 'coupons.php') ? 'active' : ''; ?>">
          <a href="coupons.php"><i class="fad fa-ticket-alt"></i> Promo Coupons</a>
        </li>

        <li class="list-group-item list-item-group-action <?= (in_array($current_page, ['delivery_methods.php', 'manage_pincodes.php'])) ? 'active' : ''; ?>">
          <a href="#delivery-settings" data-toggle="collapse"><i class="fad fa-truck"></i> Delivery <i class="fad fa-caret-down float-right mt-1"></i></a>
          <ul class="collapse <?= (in_array($current_page, ['delivery_methods.php', 'manage_pincodes.php'])) ? 'show' : ''; ?>" id="delivery-settings" style="list-style:none;">
            <li class="list-group-item sub-item ml-3 <?= ($current_page == 'delivery_methods.php') ? 'active' : ''; ?>" style="border:none"> 
                <a href="delivery_methods.php"><i class="fad fa-truck-container"></i> Delivery Methods</a> 
            </li>
            <li class="list-group-item sub-item ml-3 <?= ($current_page == 'manage_pincodes.php') ? 'active' : ''; ?>" style="border:none"> 
                <a href="manage_pincodes.php"><i class="fad fa-map-pin"></i> Delivery Pincodes</a> 
            </li>
          </ul>
        </li>

        <li class="list-group-item list-item-group-action <?= (in_array($current_page, ['payment_methods.php', 'tax_settings.php'])) ? 'active' : ''; ?>">
          <a href="#checkout-settings" data-toggle="collapse"><i class="fad fa-sliders-h"></i> Checkout Settings <i class="fad fa-caret-down float-right mt-1"></i></a>
          <ul class="collapse <?= (in_array($current_page, ['payment_methods.php', 'tax_settings.php'])) ? 'show' : ''; ?>" id="checkout-settings" style="list-style:none;">
            <li class="list-group-item sub-item ml-3 <?= ($current_page == 'payment_methods.php') ? 'active' : ''; ?>" style="border:none"> 
                <a href="payment_methods.php"><i class="fad fa-credit-card"></i> Payment Methods</a> 
            </li>
            <li class="list-group-item sub-item ml-3 <?= ($current_page == 'tax_settings.php') ? 'active' : ''; ?>" style="border:none"> 
                <a href="tax_settings.php"><i class="fad fa-percent"></i> Tax Settings</a> 
            </li>
          </ul>
        </li>

        <li class="list-group-item list-item-group-action <?= ($current_page == 'manage_warranties.php') ? 'active' : ''; ?>">
          <a href="manage_warranties.php"><i class="fad fa-shield-check"></i> Warranty Activations</a>
        </li>

        <li class="list-group-item list-item-group-action <?= (strtolower($current_page) == 'customers.php') ? 'active' : ''; ?>">
          <a id="user" href="customers.php"><i class="fad fa-user"></i> Customers</a>
        </li>

        <li class="list-group-item list-item-group-action <?= ($current_page == 'distributor_enquiries.php') ? 'active' : ''; ?>">
          <a href="distributor_enquiries.php"><i class="fad fa-handshake"></i> Distributor Enquiries</a>
        </li>

        <li class="list-group-item list-item-group-action <?= ($current_page == 'profile.php') ? 'active' : ''; ?>">
          <a href="profile.php"><i class="fad fa-cog"></i> Profile Setting</a>
        </li>

        <li class="list-group-item list-item-group-action <?= ($current_page == 'api_settings.php') ? 'active' : ''; ?>">
          <a href="api_settings.php"><i class="fad fa-key"></i> API Configuration</a>
        </li>

    </div>
</div>
