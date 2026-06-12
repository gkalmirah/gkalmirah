<?php
// Disable mysqli exceptions so failed connections return false instead of throwing
mysqli_report(MYSQLI_REPORT_OFF);

$host     = '127.0.0.1'; // Use IP to avoid named-pipe on Windows; ensures TCP connection
$username = 'root';
$password = '';
$database = 'furnitureshopmain';

// ── Port discovery with caching ──────────────────────────────────────────────
// On first ever request we probe both ports, save the winner to a tiny cache
// file so every subsequent request skips probing entirely.
$_port_cache_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'fshop_db_port.txt';

if (!function_exists('_dbcon_probe_port')) {
    function _dbcon_probe_port($host, $username, $password, $ports) {
        foreach ($ports as $port) {
            $test = @mysqli_connect($host, $username, $password, '', $port);
            if ($test) {
                mysqli_close($test);
                return $port;
            }
        }
        return null;
    }
}

// Read cached port (populated after first successful connect)
$_db_port = null;
if (file_exists($_port_cache_file)) {
    $cached = (int) trim(file_get_contents($_port_cache_file));
    if ($cached === 3306 || $cached === 3307) {
        $_db_port = $cached;
    }
}

// No valid cache – probe both ports (3306 first, XAMPP default)
if (!$_db_port) {
    $_db_port = _dbcon_probe_port($host, $username, $password, [3307, 3306]);
    if ($_db_port) {
        @file_put_contents($_port_cache_file, $_db_port);
    }
}

// ── Connection with retry (handles MySQL still warming up on boot) ────────────
$con        = false;
$_max_tries = 3;
for ($attempt = 1; $attempt <= $_max_tries; $attempt++) {
    if ($_db_port) {
        $con = @mysqli_connect($host, $username, $password, $database, $_db_port);
    }
    if ($con) break;

    // Try the other port in case the cached one stopped working
    $_alt_port = ($_db_port === 3306) ? 3307 : 3306;
    $con = @mysqli_connect($host, $username, $password, $database, $_alt_port);
    if ($con) {
        // Update cached port
        $_db_port = $_alt_port;
        @file_put_contents($_port_cache_file, $_db_port);
        break;
    }

    if ($attempt < $_max_tries) {
        sleep(1); // Wait 1 s before retrying (MySQL may still be starting)
    }
}

// ── Fatal error if all attempts failed ───────────────────────────────────────
if (!$con) {
    // Invalidate cached port so next request re-probes
    @unlink($_port_cache_file);

    $is_ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
               (isset($_SERVER['CONTENT_TYPE'])          && $_SERVER['CONTENT_TYPE'] === 'application/json') ||
               (isset($_SERVER['HTTP_ACCEPT'])            && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

    if ($is_ajax) {
        header('Content-Type: application/json');
        die(json_encode(['success' => false, 'message' => 'Database connection failed after retries: ' . mysqli_connect_error()]));
    }
    die("Database connection failed after retries: " . mysqli_connect_error());
}

// Auto-migration for Delivery Feature
$table_check = @mysqli_query($con, "SHOW TABLES LIKE 'serviceable_pincodes'");
if ($table_check && mysqli_num_rows($table_check) == 0) {
    $create_table = "CREATE TABLE `serviceable_pincodes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `pincode` varchar(10) NOT NULL UNIQUE,
        `delivery_days` int(11) DEFAULT 7,
        `shipping_charge` int(11) DEFAULT 0,
        `is_active` tinyint(1) DEFAULT 1,
        PRIMARY KEY (`id`)
    )";
    @mysqli_query($con, $create_table);

    // Insert default pincodes
    @mysqli_query($con, "INSERT IGNORE INTO serviceable_pincodes (pincode, delivery_days, shipping_charge) VALUES ('147001', 3, 0), ('147003', 4, 0), ('247001', 5, 200), ('209728', 7, 500)");
}

// Auto-migration for Additional Product Images and Details
$col_check = @mysqli_query($con, "SHOW COLUMNS FROM `furniture_product` LIKE 'product_img4'");
if ($col_check && mysqli_num_rows($col_check) == 0) {
    @mysqli_query($con, "ALTER TABLE furniture_product 
        ADD COLUMN `product_img4` VARCHAR(100) NOT NULL DEFAULT '' AFTER `product_img3`,
        ADD COLUMN `product_img5` VARCHAR(100) NOT NULL DEFAULT '' AFTER `product_img4`,
        ADD COLUMN `product_img6` VARCHAR(100) NOT NULL DEFAULT '' AFTER `product_img5` ");
}

// Add missing columns for product detail page
$cols_to_add = [
    'product_subtitle' => "VARCHAR(255) NOT NULL DEFAULT '' AFTER `product_name` ",
    'product_short_desc' => "TEXT AFTER `product_desc` ",
    'product_mrp' => "DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `product_price` ",
    'product_tax_inc' => "VARCHAR(100) NOT NULL DEFAULT 'All taxes included' AFTER `product_mrp` "
];

foreach ($cols_to_add as $col => $definition) {
    $check = @mysqli_query($con, "SHOW COLUMNS FROM `furniture_product` LIKE '$col'");
    if ($check && mysqli_num_rows($check) == 0) {
        @mysqli_query($con, "ALTER TABLE furniture_product ADD COLUMN `$col` $definition");
    }
}

// Auto-migration for Cart Table
$cart_check = @mysqli_query($con, "SHOW TABLES LIKE 'cart'");
if ($cart_check && mysqli_num_rows($cart_check) == 0) {
    $create_cart = "CREATE TABLE `cart` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `cust_id` int(11) NOT NULL,
        `product_id` int(11) NOT NULL,
        `quantity` int(11) NOT NULL DEFAULT 1,
        PRIMARY KEY (`id`)
    )";
    @mysqli_query($con, $create_cart);
}

// Auto-migration for Festival Offers
$festival_check = @mysqli_query($con, "SHOW TABLES LIKE 'festivals'");
if ($festival_check && mysqli_num_rows($festival_check) == 0) {
    $create_festival = "CREATE TABLE `festivals` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `festival_name` varchar(255) NOT NULL,
        `festival_date` date NOT NULL,
        `discount_type` enum('percentage', 'fixed') NOT NULL DEFAULT 'percentage',
        `discount_value` decimal(10,2) NOT NULL DEFAULT '0.00',
        `banner_title` varchar(255) NOT NULL,
        `banner_subtitle` varchar(255) NOT NULL,
        `is_active` tinyint(1) DEFAULT 1,
        PRIMARY KEY (`id`)
    )";
    @mysqli_query($con, $create_festival);

    // Dynamic Seed: Insert a festival that is precisely active today for testing purposes.
    // Assuming today's date + 1 day as the festival date, putting us squarely in the 3-day active window.
    $mock_festival_date = date('Y-m-d', strtotime('+1 day'));
    @mysqli_query($con, "INSERT IGNORE INTO festivals (festival_name, festival_date, discount_type, discount_value, banner_title, banner_subtitle, is_active) 
        VALUES ('Holi Special', '$mock_festival_date', 'percentage', 25.00, '🎆 Holi Mega Sale – Flat 25% OFF', 'Celebrate vibrant savings today!', 1)");

    // Insert some future inactive ones
    $diwali_date = date('Y-m-d', strtotime('+200 days'));
    @mysqli_query($con, "INSERT IGNORE INTO festivals (festival_name, festival_date, discount_type, discount_value, banner_title, banner_subtitle, is_active) 
        VALUES ('Diwali Dhamaka', '$diwali_date', 'percentage', 35.00, '🪔 Diwali Dhamaka Sale – Up to 35% OFF', 'Sparkle up your home this Diwali.', 1)");
}


// ── Auto-migration: delivery_methods ─────────────────────────────────────────
$dm_check = @mysqli_query($con, "SHOW TABLES LIKE 'delivery_methods'");
$dm_needs_migration = true;
if ($dm_check && mysqli_num_rows($dm_check) > 0) {
    $col_check = @mysqli_query($con, "SHOW COLUMNS FROM `delivery_methods` LIKE 'name'");
    if ($col_check && mysqli_num_rows($col_check) > 0) {
        $dm_needs_migration = false;
    } else {
        @mysqli_query($con, "DROP TABLE `delivery_methods`");
    }
}

if ($dm_needs_migration) {
    @mysqli_query($con, "CREATE TABLE `delivery_methods` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `charge` decimal(10,2) NOT NULL DEFAULT '0.00',
        `estimated_days` varchar(50) NOT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'Active',
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    )");
    @mysqli_query($con, "INSERT INTO `delivery_methods` (name, charge, estimated_days, status) VALUES
        ('Standard Delivery', 0.00, '5-7 Days', 'Active'),
        ('Express Delivery', 150.00, '2-3 Days', 'Active'),
        ('Same-Day Delivery', 300.00, 'Same Day', 'Inactive')
    ");
}

// ── Auto-migration: payment_methods ──────────────────────────────────────────
$pm_check = @mysqli_query($con, "SHOW TABLES LIKE 'payment_methods'");
if ($pm_check && mysqli_num_rows($pm_check) == 0) {
    @mysqli_query($con, "CREATE TABLE `payment_methods` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `method_key` varchar(50) NOT NULL UNIQUE,
        `method_name` varchar(100) NOT NULL,
        `description` varchar(255) DEFAULT '',
        `icon` varchar(50) DEFAULT 'fa-money-bill',
        `is_active` tinyint(1) NOT NULL DEFAULT 1,
        `sort_order` int(11) NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`)
    )");
    @mysqli_query($con, "INSERT INTO `payment_methods` (method_key, method_name, description, icon, is_active, sort_order) VALUES
        ('upi',        'UPI',          'Google Pay, PhonePe, Paytm, BHIM', 'fa-qrcode',          1, 1),
        ('card',       'Card',         'Visa, Mastercard, RuPay, Amex',    'fa-credit-card',     1, 2),
        ('netbanking', 'Net Banking',  'All major Indian banks',           'fa-university',      1, 3),
        ('wallet',     'Wallets',      'Paytm, Amazon Pay',                'fa-wallet',          1, 4),
        ('cod',        'Cash on Delivery', 'Pay when delivered',          'fa-money-bill-wave', 1, 5)
    ");
}

// ── Auto-migration: tax_settings ─────────────────────────────────────────────
$tax_check = @mysqli_query($con, "SHOW TABLES LIKE 'tax_settings'");
if ($tax_check && mysqli_num_rows($tax_check) == 0) {
    @mysqli_query($con, "CREATE TABLE `tax_settings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `tax_name` varchar(100) NOT NULL DEFAULT 'GST',
        `tax_percent` decimal(5,2) NOT NULL DEFAULT '18.00',
        `is_active` tinyint(1) NOT NULL DEFAULT 1,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    )");
    @mysqli_query($con, "INSERT INTO `tax_settings` (tax_name, tax_percent, is_active) VALUES ('GST', 18.00, 1)");
}

// ── Auto-migration: promo_codes usage_limit column ───────────────────────────
$ul_check = @mysqli_query($con, "SHOW COLUMNS FROM `promo_codes` LIKE 'usage_limit'");
if ($ul_check && mysqli_num_rows($ul_check) == 0) {
    @mysqli_query($con, "ALTER TABLE `promo_codes` ADD COLUMN `usage_limit` int(11) DEFAULT NULL AFTER `min_order`");
}

// ── Auto-migration: customer_order — add payment/delivery columns ─────────────
$co_cols_to_add = [
    'payment_method' => "VARCHAR(50) DEFAULT 'COD' AFTER `order_status` ",
    'delivery_method_id' => "INT(11) DEFAULT NULL AFTER `payment_method` ",
    'tax_amount' => "DECIMAL(10,2) DEFAULT 0.00 AFTER `delivery_method_id` ",
    'shipping_amount' => "DECIMAL(10,2) DEFAULT 0.00 AFTER `tax_amount` "
];
foreach ($co_cols_to_add as $col => $definition) {
    $check = @mysqli_query($con, "SHOW COLUMNS FROM `customer_order` LIKE '$col'");
    if ($check && mysqli_num_rows($check) == 0) {
        @mysqli_query($con, "ALTER TABLE `customer_order` ADD COLUMN `$col` $definition");
    }
}

// ── Ensure order_status ENUM has all modern statuses ────────────────────────
// We use a VARCHAR approach for compatibility — change column type safely
$os_check = @mysqli_query($con, "SHOW COLUMNS FROM `customer_order` LIKE 'order_status'");
if ($os_check && $os_row = mysqli_fetch_assoc($os_check)) {
    if (strpos($os_row['Type'], 'draft') === false) {
        @mysqli_query($con, "ALTER TABLE `customer_order` MODIFY `order_status` ENUM('draft','pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'draft'");
        // Fix any blank statuses from previous checkout attempts that wrote 'Placed'
        @mysqli_query($con, "UPDATE `customer_order` SET `order_status` = 'pending' WHERE `order_status` = '' OR `order_status` IS NULL");
    }
}

// ── Auto-migration: api_configurations webhook_secret and mode ─────────────────
$api_table_check = @mysqli_query($con, "SHOW TABLES LIKE 'api_configurations'");
if ($api_table_check && mysqli_num_rows($api_table_check) == 0) {
    @mysqli_query($con, "CREATE TABLE `api_configurations` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `service_name` varchar(100) NOT NULL UNIQUE,
        `api_key` varchar(255) NOT NULL,
        `api_secret` varchar(255) DEFAULT NULL,
        `webhook_secret` varchar(255) DEFAULT NULL,
        `mode` varchar(50) DEFAULT 'sandbox',
        `is_active` tinyint(1) DEFAULT 1,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    )");
} else {
    $ws_check = @mysqli_query($con, "SHOW COLUMNS FROM `api_configurations` LIKE 'webhook_secret'");
    if ($ws_check && mysqli_num_rows($ws_check) == 0) {
        @mysqli_query($con, "ALTER TABLE `api_configurations` ADD COLUMN `webhook_secret` VARCHAR(255) DEFAULT NULL AFTER `api_secret`");
    }
    $mode_check = @mysqli_query($con, "SHOW COLUMNS FROM `api_configurations` LIKE 'mode'");
    if ($mode_check && mysqli_num_rows($mode_check) == 0) {
        @mysqli_query($con, "ALTER TABLE `api_configurations` ADD COLUMN `mode` VARCHAR(50) DEFAULT 'sandbox' AFTER `webhook_secret`");
    }
}

// ── Auto-migration: distributor_inquiries ─────────────────────────────────────
$di_check = @mysqli_query($con, "SHOW TABLES LIKE 'distributor_inquiries'");
if ($di_check && mysqli_num_rows($di_check) == 0) {
    @mysqli_query($con, "CREATE TABLE `distributor_inquiries` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `full_name` varchar(100) NOT NULL,
        `company_name` varchar(150) NOT NULL,
        `phone` varchar(20) NOT NULL,
        `email` varchar(100) NOT NULL,
        `state` varchar(100) NOT NULL,
        `city` varchar(100) NOT NULL,
        `business_type` varchar(100) NOT NULL,
        `experience` varchar(50) NOT NULL,
        `investment_capacity` varchar(50) NOT NULL,
        `message` text,
        `status` varchar(20) NOT NULL DEFAULT 'New',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}


// Require Discount Engine globally
require_once(__DIR__ . '/discount_logic.php');
