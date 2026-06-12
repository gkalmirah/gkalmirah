<?php
$str = "15000 - 20000";
$price_old = floatval($str);
$price_mine = floatval(preg_replace('/[^0-9.]/', '', strval($str)));

$parts = explode('-', $str);
$price_new = floatval(preg_replace('/[^0-9.]/', '', trim($parts[0])));

echo "Old: $price_old\nMine: $price_mine\nNew: $price_new\n";
?>
