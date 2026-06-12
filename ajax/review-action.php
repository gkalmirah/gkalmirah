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

    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if ($product_id <= 0) {
        throw new Exception("Invalid Product ID");
    }

    switch ($action) {
        case 'fetch':
            $sort = isset($_POST['sort']) ? $_POST['sort'] : 'recent';
            $order_by = "created_at DESC";
            if ($sort == 'high') $order_by = "rating DESC, created_at DESC";
            if ($sort == 'low') $order_by = "rating ASC, created_at DESC";

            $res = mysqli_query($con, "SELECT * FROM reviews WHERE product_id = $product_id ORDER BY $order_by");
            $html = '';
            
            if (mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    $html .= '
                    <div class="card border-0 shadow-sm p-4 mb-3" style="border-radius: 12px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="font-weight-bold mb-0">' . htmlspecialchars($row['name']) . '</h6>
                            <span class="small text-muted">' . date('M d, Y', strtotime($row['created_at'])) . '</span>
                        </div>
                        <div class="stars-v3 mb-2 small">';
                    for ($i = 1; $i <= 5; $i++) {
                        $html .= ($i <= $row['rating']) ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-muted"></i>';
                    }
                    $html .= '</div>
                        <p class="text-secondary small mb-0">' . nl2br(htmlspecialchars($row['comment'])) . '</p>
                    </div>';
                }
            } else {
                $html = '<div class="text-center py-5 text-muted"><p>No reviews yet. Be the first to share your thoughts!</p></div>';
            }
            $response = ['success' => true, 'html' => $html];
            break;

        case 'submit':
            // Although the prompt says reviewers name if available, keep it simple
            $name = isset($_POST['name']) ? mysqli_real_escape_string($con, $_POST['name']) : 'Anonymous';
            $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
            $comment = isset($_POST['comment']) ? mysqli_real_escape_string($con, $_POST['comment']) : '';

            if (empty($comment)) {
                throw new Exception("Please enter a comment");
            }

            $query = "INSERT INTO reviews (product_id, name, rating, comment, created_at) VALUES ($product_id, '$name', $rating, '$comment', NOW())";
            if (mysqli_query($con, $query)) {
                $response = ['success' => true, 'message' => 'Review submitted successfully!'];
            } else {
                throw new Exception("Failed to submit review");
            }
            break;

        default:
            throw new Exception("Invalid Action");
    }
} catch (Throwable $t) {
    $response = ['success' => false, 'message' => $t->getMessage()];
}

header('Content-Type: application/json');
if (ob_get_length()) ob_clean();
echo json_encode($response);
exit();
