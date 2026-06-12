<?php
require_once('include/dbcon.php');

$query = "CREATE TABLE IF NOT EXISTS festival_campaigns (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    festival_name VARCHAR(255) NOT NULL,
    banner_image VARCHAR(255) NOT NULL,
    description TEXT,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(10, 2) NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($con, $query)) {
    echo "Table 'festival_campaigns' created successfully.";
} else {
    echo "Error creating table: " . mysqli_error($con);
}
?>
