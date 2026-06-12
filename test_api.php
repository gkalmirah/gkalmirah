<?php
include('include/dbcon.php');
include('include/api_config.php');

echo "<h2>API Key Test Script</h2>";

// Let's try to fetch credentials for 'Razorpay'
$service = 'Razorpay';
$credentials = get_api_credentials($service, $con);

if ($credentials) {
    echo "<p style='color: green; font-weight: bold;'>Successfully retrieved credentials for <strong>{$service}</strong>!</p>";
    echo "<ul>";
    echo "<li><strong>API Key:</strong> " . htmlspecialchars($credentials['api_key']) . "</li>";
    echo "<li><strong>API Secret:</strong> " . (empty($credentials['api_secret']) ? '<em>None provided</em>' : htmlspecialchars($credentials['api_secret'])) . "</li>";
    echo "</ul>";
} else {
    echo "<p style='color: red; font-weight: bold;'>No active credentials found for <strong>{$service}</strong>. (Ensure it is added in the Admin Panel and set to Active!)</p>";
}
?>
