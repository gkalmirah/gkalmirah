<?php
require_once('include/dbcon.php');

$queries = [
    "CREATE TABLE IF NOT EXISTS customer_addresses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        mobile_number VARCHAR(20) NOT NULL,
        alt_mobile VARCHAR(20) DEFAULT NULL,
        house_no VARCHAR(100) NOT NULL,
        street_address TEXT NOT NULL,
        landmark VARCHAR(100) DEFAULT NULL,
        city VARCHAR(100) NOT NULL,
        district VARCHAR(100) DEFAULT NULL,
        state VARCHAR(100) NOT NULL,
        pincode VARCHAR(20) NOT NULL,
        is_default TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS delivery_methods (
        id INT AUTO_INCREMENT PRIMARY KEY,
        method_name VARCHAR(100) NOT NULL,
        description VARCHAR(255) DEFAULT NULL,
        cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        estimated_days INT NOT NULL DEFAULT 3,
        is_active TINYINT(1) DEFAULT 1
    )",
    
    "CREATE TABLE IF NOT EXISTS promo_codes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(50) UNIQUE NOT NULL,
        discount_type ENUM('percent','flat') NOT NULL,
        discount_value DECIMAL(10,2) NOT NULL,
        min_order DECIMAL(10,2) DEFAULT 0,
        expiry_date DATE NOT NULL,
        is_active TINYINT(1) DEFAULT 1,
        used_count INT DEFAULT 0
    )"
];

foreach ($queries as $q) {
    if(!mysqli_query($con, $q)) {
        echo "Error: " . mysqli_error($con) . "<br>";
    }
}

// Insert mock delivery methods if empty
$check = mysqli_query($con, "SELECT count(*) as c FROM delivery_methods");
$row = mysqli_fetch_assoc($check);
if($row['c'] == 0) {
    mysqli_query($con, "INSERT INTO delivery_methods (method_name, description, cost, estimated_days) VALUES 
        ('Standard Delivery', 'Delivery in 5-7 business days', 0.00, 7),
        ('Express Delivery', 'Delivery in 2-3 business days', 150.00, 3),
        ('Same-Day Delivery', 'Order before 2 PM for today delivery', 300.00, 0)");
}

echo "Tables setup complete.";
?>
