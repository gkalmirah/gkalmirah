<?php
include 'include/dbcon.php';

$query = "SELECT product_id, product_name, product_price FROM furniture_product";
$res = mysqli_query($con, $query);

if (!$res) {
    die("Query failed: " . mysqli_error($con));
}

$num_rows = mysqli_num_rows($res);

echo "<html><head><title>Product List Debug</title>";
echo "<style>
    body { font-family: sans-serif; padding: 20px; background: #f4f4f4; }
    table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
    th { background: #333; color: #fff; }
    tr:hover { background: #f1f1f1; }
    h1 { color: #333; }
    .count { font-size: 1.2em; margin-bottom: 20px; font-weight: bold; color: #d9534f; }
</style></head><body>";

echo "<h1>G.K. Almirah Product List</h1>";
echo "<p class='count'>Total Products: " . $num_rows . "</p>";

if ($num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Price</th></tr>";
    while ($row = mysqli_fetch_assoc($res)) {
        echo "<tr>";
        echo "<td>" . $row['product_id'] . "</td>";
        echo "<td>" . $row['product_name'] . "</td>";
        echo "<td>" . $row['product_price'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No products found.";
}

echo "</body></html>";
?>
