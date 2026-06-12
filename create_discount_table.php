<?php
include('include/dbcon.php');
$sql = "CREATE TABLE IF NOT EXISTS `product_discounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if(mysqli_query($con, $sql)) {
    echo "Table created successfully.";
} else {
    echo "Error creating table: " . mysqli_error($con);
}
?>
