<?php
// Core Discount Engine Utility
// Ensures discounts are calculated correctly and automatically applied.

if (!function_exists('get_active_discount')) {
    function get_active_discount($product_id, $base_price, $con) {
        $result = [
            'has_discount' => false,
            'original_price' => $base_price,
            'discounted_price' => $base_price,
            'badge_text' => ''
        ];

        // 1. Fetch active Festival Discount
        $festival_discount = null;
        $fest_q = "SELECT * FROM festival_campaigns 
                   WHERE status = 1 
                   AND NOW() BETWEEN start_date AND end_date 
                   ORDER BY id DESC LIMIT 1";
        $fest_run = mysqli_query($con, $fest_q);
        if($fest_run && mysqli_num_rows($fest_run) > 0) {
            $festival_discount = mysqli_fetch_assoc($fest_run);
        }

        // 2. Fetch active Product Discount
        $product_discount = null;
        $query = "SELECT * FROM product_discounts 
                  WHERE product_id = " . intval($product_id) . " 
                  AND status = 1 
                  AND NOW() BETWEEN start_date AND end_date 
                  ORDER BY id DESC LIMIT 1";
        $run = mysqli_query($con, $query);
        if($run && mysqli_num_rows($run) > 0) {
            $product_discount = mysqli_fetch_assoc($run);
        }

        // 3. Determine the best deal
        $best_price = $base_price;
        $best_badge = '';
        $has_deal = false;

        // Calculate product discount price
        $p_price = $base_price;
        if($product_discount) {
            if($product_discount['discount_type'] == 'percentage') {
                $p_price = $base_price - (($base_price * floatval($product_discount['discount_value'])) / 100);
            } else {
                $p_price = $base_price - floatval($product_discount['discount_value']);
            }
        }

        // Calculate festival discount price
        $f_price = $base_price;
        if($festival_discount) {
            if($festival_discount['discount_type'] == 'percentage') {
                $f_price = $base_price - (($base_price * floatval($festival_discount['discount_value'])) / 100);
            } else {
                $f_price = $base_price - floatval($festival_discount['discount_value']);
            }
        }

        // Apply the best deal
        if($product_discount && $festival_discount) {
            if($p_price <= $f_price) {
                $best_price = $p_price;
                $best_badge = ($product_discount['discount_type'] == 'percentage') ? round($product_discount['discount_value']) . "% OFF" : "₹" . round($product_discount['discount_value']) . " OFF";
            } else {
                $best_price = $f_price;
                $val = ($festival_discount['discount_type'] == 'percentage') ? round($festival_discount['discount_value']) . "% OFF" : "₹" . round($festival_discount['discount_value']) . " OFF";
                $best_badge = "🎉 " . $festival_discount['festival_name'] . "<br>" . $val;
            }
            $has_deal = true;
        } else if ($product_discount) {
            $best_price = $p_price;
            $best_badge = ($product_discount['discount_type'] == 'percentage') ? round($product_discount['discount_value']) . "% OFF" : "₹" . round($product_discount['discount_value']) . " OFF";
            $has_deal = true;
        } else if ($festival_discount) {
            $best_price = $f_price;
            $val = ($festival_discount['discount_type'] == 'percentage') ? round($festival_discount['discount_value']) . "% OFF" : "₹" . round($festival_discount['discount_value']) . " OFF";
            $best_badge = "🎉 " . $festival_discount['festival_name'] . "<br>" . $val;
            $has_deal = true;
        }

        if($has_deal) {
            $result['has_discount'] = true;
            $result['discounted_price'] = max(0, $best_price);
            $result['badge_text'] = $best_badge;
        }
        
        return $result;
    }
}
?>
