<?php
include('include/dbcon.php');

$sql = "CREATE TABLE IF NOT EXISTS `api_configurations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `service_name` varchar(100) NOT NULL UNIQUE,
    `api_key` varchar(255) NOT NULL,
    `api_secret` varchar(255) DEFAULT NULL,
    `is_active` tinyint(1) DEFAULT 1,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
)";

if (mysqli_query($con, $sql)) {
    echo "api_configurations table created successfully.\n";
} else {
    echo "Error creating table: " . mysqli_error($con) . "\n";
}
?>
