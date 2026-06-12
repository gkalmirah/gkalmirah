<?php
ob_start(); // Buffer all output to catch accidental warnings
error_reporting(0);
session_start();

$response = ['success' => false, 'message' => 'Unknown Error'];

try {
    require_once('../include/dbcon.php');


    if (!isset($con)) {
        throw new Exception("Database Connection Missing");
    }

    if (!isset($_SESSION['id'])) {
        $response = ['success' => false, 'message' => 'Login Required'];
    } else {
        $cust_id = $_SESSION['id'];
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'add':
                $product_id = intval($_POST['product_id']);
                if ($product_id <= 0) {
                    throw new Exception("Invalid Product");
                }

                $check_q = "SELECT * FROM cart WHERE cust_id = $cust_id AND product_id = $product_id";
                $check_r = mysqli_query($con, $check_q);

                if ($check_r && mysqli_num_rows($check_r) > 0) {
                    $update_q = "UPDATE cart SET quantity = quantity + 1 WHERE cust_id = $cust_id AND product_id = $product_id";
                    mysqli_query($con, $update_q);
                } else {
                    $insert_q = "INSERT INTO cart (cust_id, product_id, quantity) VALUES ($cust_id, $product_id, 1)";
                    mysqli_query($con, $insert_q);
                }

                $response = ['success' => true];
                break;

            case 'remove':
                $product_id = intval($_POST['product_id']);
                $delete_q = "DELETE FROM cart WHERE cust_id = $cust_id AND product_id = $product_id";
                mysqli_query($con, $delete_q);
                $response = ['success' => true];
                break;

            case 'fetch_count':
                $count_q = "SELECT SUM(quantity) as total FROM cart WHERE cust_id = $cust_id";
                $count_r = mysqli_query($con, $count_q);
                $row = mysqli_fetch_assoc($count_r);
                $response = ['success' => true, 'count' => (int)($row['total'] ?? 0)];
                break;

            case 'fetch_drawer':
                $items_q = "SELECT c.*, p.product_name, p.product_price, p.product_img1 
                            FROM cart c 
                            JOIN furniture_product p ON c.product_id = p.product_id 
                            WHERE c.cust_id = $cust_id";
                $items_r = mysqli_query($con, $items_q);
                
                $html = '';
                $total = 0;
                
                if ($items_r && mysqli_num_rows($items_r) > 0) {
                    $html .= '<div class="cart-items-list mb-4">';
                    while ($row = mysqli_fetch_assoc($items_r)) {
                        $raw_price_str = strval($row['product_price']);
                        $parts = explode('-', $raw_price_str);
                        $current_price = floatval(preg_replace('/[^0-9.]/', '', trim($parts[0])));
                        
                        $discount = get_active_discount($row['product_id'], $current_price, $con);
                        if ($discount['has_discount']) {
                            $current_price = $discount['discounted_price'];
                        }
                        
                        $subtotal = $row['quantity'] * $current_price;
                        $total += $subtotal;
                        $html .= '
                        <div class="cart-drawer-item d-flex align-items-center py-3 border-bottom">
                            <div class="cart-item-img mr-3">
                                <img src="img/' . htmlspecialchars($row['product_img1']) . '" class="rounded" style="width: 70px; height: 70px; object-fit: cover; border: 1px solid #eee;">
                            </div>
                            <div class="cart-item-info flex-grow-1 min-width-0">
                                <h6 class="mb-0 text-dark font-weight-bold text-truncate">' . htmlspecialchars($row['product_name']) . '</h6>
                                <div class="d-flex align-items-center mt-1">
                                    <span class="text-muted small">' . $row['quantity'] . ' x </span>
                                    <span class="text-gold font-weight-bold ml-1">Rs. ' . number_format((float)$current_price) . '</span>
                                </div>
                            </div>
                            <button class="btn btn-link text-muted p-2" onclick="removeFromCart(' . $row['product_id'] . ')" title="Remove Item">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>';
                    }
                    $html .= '</div>';
                    $html .= '
                    <div class="cart-drawer-summary p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted font-weight-600">Subtotal</span>
                            <span class="h5 mb-0 font-weight-bold text-dark">Rs. ' . number_format((float)$total) . '</span>
                        </div>
                        <div class="cart-actions d-grid gap-2">
                            <a href="cart.php" class="btn btn-outline-dark btn-block mb-2 font-weight-bold">VIEW BAG</a>
                            <a href="checkout.php" class="btn btn-primary btn-block py-2 font-weight-bold shadow-sm" style="letter-spacing: 0.5px;">CHECKOUT NOW</a>
                        </div>
                    </div>';
                } else {
                    $html = '<div class="text-center py-5">
                                <div class="empty-cart-icon mb-3">
                                    <i class="fas fa-shopping-bag fa-4x text-light"></i>
                                </div>
                                <h5 class="text-dark">Your bag is empty!</h5>
                                <p class="text-muted small px-4">Seems like you haven\'t added anything to your cart yet.</p>
                                <a href="product.php" class="btn btn-gold btn-sm px-4 mt-2">SHOP NOW</a>
                            </div>';
                }
                
                $response = ['success' => true, 'html' => $html];
                break;

            default:
                throw new Exception("Invalid Action");
        }
    }
} catch (Throwable $t) {
    $response = ['success' => false, 'message' => $t->getMessage()];
}

header('Content-Type: application/json');
if (ob_get_length()) ob_clean(); // Discard any stray output
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit();
