<?php
session_start();
require_once('../include/dbcon.php');

header('Content-Type: application/json');

if (!isset($_POST['code']) || !isset($_POST['subtotal'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$code = mysqli_real_escape_string($con, trim($_POST['code']));
$subtotal = floatval($_POST['subtotal']);

$query = "SELECT * FROM promo_codes WHERE code='$code' AND is_active=1";
$run = mysqli_query($con, $query);

if (mysqli_num_rows($run) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Promo code is invalid or not active.']);
    unset($_SESSION['promo']);
    exit;
}

$promo = mysqli_fetch_assoc($run);

// Check expiry
if (strtotime($promo['expiry_date']) < strtotime(date('Y-m-d'))) {
    echo json_encode(['status' => 'error', 'message' => 'Promo code has expired.']);
    unset($_SESSION['promo']);
    exit;
}

// Check min order
if ($subtotal < floatval($promo['min_order'])) {
    echo json_encode(['status' => 'error', 'message' => 'Minimum order amount of ₹' . $promo['min_order'] . ' required.']);
    unset($_SESSION['promo']);
    exit;
}

// Calculate discount
$discount = 0;
if ($promo['discount_type'] == 'percent') {
    $discount = $subtotal * (floatval($promo['discount_value']) / 100);
} else {
    $discount = floatval($promo['discount_value']);
}

if ($discount > $subtotal) {
    $discount = $subtotal;
}

// Store in session
$_SESSION['promo'] = [
    'code' => $code,
    'amount' => $discount
];

echo json_encode([
    'status' => 'success',
    'message' => 'Promo code applied successfully!',
    'discount_amount' => $discount
]);
