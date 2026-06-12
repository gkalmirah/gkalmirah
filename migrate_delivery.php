<?php
$dbconPath = __DIR__ . '/include/dbcon.php';
require_once($dbconPath);

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}
$queries = [
    "CREATE TABLE IF NOT EXISTS `serviceable_pincodes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `pincode` varchar(10) NOT NULL UNIQUE,
        `delivery_days` int(11) DEFAULT 7,
        `shipping_charge` int(11) DEFAULT 0,
        `is_active` tinyint(1) DEFAULT 1,
        PRIMARY KEY (`id`)
    )"
];

foreach ($queries as $query) {
    if (mysqli_query($con, $query)) {
        echo "Query successful.<br>";
    } else {
        echo "Error: " . mysqli_error($con) . "<br>";
    }
}

// Insert some sample data if empty
$check = mysqli_query($con, "SELECT COUNT(*) as count FROM serviceable_pincodes");
$row = mysqli_fetch_assoc($check);
if ($row['count'] == 0) {
    $samples = [
        ['147001', 3, 0],
        ['147003', 4, 0],
        ['247001', 5, 200],
        ['209728', 7, 500]
    ];
    foreach ($samples as $s) {
        mysqli_query($con, "INSERT INTO serviceable_pincodes (pincode, delivery_days, shipping_charge) VALUES ('$s[0]', $s[1], $s[2])");
    }
    echo "Sample pincodes inserted.<br>";
}

echo "Migration completed.";
?>
