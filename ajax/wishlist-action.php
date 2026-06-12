<?php
ob_start();
session_start();
error_reporting(0);

$response = ['success' => false, 'message' => 'Unknown Error'];

try {
    require_once('../include/dbcon.php');

    if (!isset($con)) {
        throw new Exception("Database Connection Missing");
    }

    if (!isset($_SESSION['id'])) {
        $response = ['success' => false, 'message' => 'Login Required', 'login_required' => true];
    } else {
        $cust_id = $_SESSION['id'];
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $action = isset($_POST['action']) ? $_POST['action'] : '';

        if ($action !== 'fetch_count' && $product_id <= 0) {
            throw new Exception("Invalid Product ID");
        }

        switch ($action) {
            case 'toggle':
                $check_q = "SELECT * FROM wishlist WHERE cust_id = $cust_id AND product_id = $product_id";
                $check_r = mysqli_query($con, $check_q);

                if (mysqli_num_rows($check_r) > 0) {
                    $delete_q = "DELETE FROM wishlist WHERE cust_id = $cust_id AND product_id = $product_id";
                    if (mysqli_query($con, $delete_q)) {
                        $response = ['success' => true, 'action' => 'removed', 'message' => 'Removed from Wishlist'];
                    }
                } else {
                    $insert_q = "INSERT INTO wishlist (cust_id, product_id) VALUES ($cust_id, $product_id)";
                    if (mysqli_query($con, $insert_q)) {
                        $response = ['success' => true, 'action' => 'added', 'message' => 'Added to Wishlist'];
                    }
                }
                break;

            case 'check':
                $check_q = "SELECT * FROM wishlist WHERE cust_id = $cust_id AND product_id = $product_id";
                $check_r = mysqli_query($con, $check_q);
                $is_wishlisted = (mysqli_num_rows($check_r) > 0);
                $response = ['success' => true, 'is_wishlisted' => $is_wishlisted];
                break;

            case 'fetch_count':
                $w_count = 0;
                $check_q = "SELECT COUNT(*) AS w_count FROM wishlist WHERE cust_id = $cust_id";
                $check_r = mysqli_query($con, $check_q);
                if ($check_r && $row = mysqli_fetch_assoc($check_r)) {
                    $w_count = $row['w_count'];
                }
                $response = ['success' => true, 'count' => $w_count];
                break;

            default:
                throw new Exception("Invalid Action");
        }
    }
} catch (Throwable $t) {
    $response = ['success' => false, 'message' => $t->getMessage()];
}

header('Content-Type: application/json');
if (ob_get_length()) ob_clean();
echo json_encode($response);
exit();
