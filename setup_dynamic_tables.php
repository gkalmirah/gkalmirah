<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
$dbconPath = __DIR__ . '/include/dbcon.php';
if (!file_exists($dbconPath)) {
    die("Error: dbcon.php not found at $dbconPath");
}
require_once($dbconPath);

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

echo "Connected to database successfully.<br>";

// SQL queries to create tables
$queries = [
    "slider_images" => "CREATE TABLE IF NOT EXISTS `slider_images` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `image_path` varchar(255) NOT NULL,
        `title` varchar(255),
        `subtitle` varchar(255),
        `link` varchar(255),
        `ordering` int(11) DEFAULT 0,
        PRIMARY KEY (`id`)
    )",
    "site_settings" => "CREATE TABLE IF NOT EXISTS `site_settings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `setting_key` varchar(50) NOT NULL UNIQUE,
        `setting_value` text,
        PRIMARY KEY (`id`)
    )",
    "stats" => "CREATE TABLE IF NOT EXISTS `stats` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(100) NOT NULL,
        `value` varchar(50) NOT NULL,
        `icon` varchar(50),
        `ordering` int(11) DEFAULT 0,
        PRIMARY KEY (`id`)
    )"
];

// Execute table creation queries
foreach ($queries as $tableName => $query) {
    if (mysqli_query($con, $query)) {
        echo "Table '$tableName' check/creation successful.<br>";
    } else {
        echo "Error creating table '$tableName': " . mysqli_error($con) . "<br>";
    }
}

// Helper function to check if table is empty
function isTableEmpty($con, $table) {
    $result = mysqli_query($con, "SELECT COUNT(*) as count FROM `$table`");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['count'] == 0;
    }
    return false;
}

// Insert default data for Site Settings
if (isTableEmpty($con, 'site_settings')) {
    $default_settings = [
        "usp_brand" => "India's No.1 Almirah Brand",
        "usp_guarantee" => "10 Years Guarantee",
        "usp_delivery" => "Free Home Delivery",
        "contact_phone" => "+91 96820 21084",
        "contact_email" => "contact@gkalmirah.com"
    ];
    
    foreach ($default_settings as $key => $value) {
        $keyEscaped = mysqli_real_escape_string($con, $key);
        $valueEscaped = mysqli_real_escape_string($con, $value);
        if(mysqli_query($con, "INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES ('$keyEscaped', '$valueEscaped')")) {
             echo "Inserted setting: $key<br>";
        } else {
             echo "Error inserting setting $key: " . mysqli_error($con) . "<br>";
        }
    }
} else {
    echo "Site settings table not empty, skipping default insertion.<br>";
}

// Insert default data for Stats
if (isTableEmpty($con, 'stats')) {
    $default_stats = [
        ['Cities Covered', '1000+', 'fas fa-city'],
        ['Dealer Network', '3500+', 'fas fa-users'],
        ['Years Guarantee', '10+', 'fas fa-shield-alt'],
        ['Almirah Brand', 'No.1', 'fas fa-trophy']
    ];
    
    foreach ($default_stats as $stat) {
        $title = mysqli_real_escape_string($con, $stat[0]);
        $value = mysqli_real_escape_string($con, $stat[1]);
        $icon = mysqli_real_escape_string($con, $stat[2]);
        mysqli_query($con, "INSERT INTO `stats` (`title`, `value`, `icon`) VALUES ('$title', '$value', '$icon')");
    }
    echo "Default stats inserted.<br>";
} else {
    echo "Stats table not empty, skipping default insertion.<br>";
}

// Insert default Slider Images
if (isTableEmpty($con, 'slider_images')) {
    $default_slider = [
        ['img/WS11.jpg', 'Exclusive Wardrobe', 'Premium Finish', 1],
        ['img/slider2.jpg', 'Modern Design', 'Elegant Looks', 2],
        ['img/slider3.jpg', 'Spacious Storage', 'For Your Needs', 3]
    ];
    
    foreach ($default_slider as $slide) {
        $img = mysqli_real_escape_string($con, $slide[0]);
        $title = mysqli_real_escape_string($con, $slide[1]);
        $sub = mysqli_real_escape_string($con, $slide[2]);
        $order = (int)$slide[3];
        mysqli_query($con, "INSERT INTO `slider_images` (`image_path`, `title`, `subtitle`, `ordering`) VALUES ('$img', '$title', '$sub', $order)");
    }
    echo "Default slider images inserted.<br>";
} else {
    echo "Slider images table not empty, skipping default insertion.<br>";
}

echo "Database setup script completed.";
?>
