<?php
// Festival Offer Logic System
// Safely calculate dates and dynamic discounts without touching actual product prices in DB

function get_active_festival($con) {
    // We get all active festivals
    $query = "SELECT * FROM festivals WHERE is_active = 1 ORDER BY festival_date ASC";
    $result = mysqli_query($con, $query);
    
    if($result && mysqli_num_rows($result) > 0) {
        $today = strtotime(date('Y-m-d'));
        
        while($row = mysqli_fetch_assoc($result)) {
            $festival_date = strtotime($row['festival_date']);
            $start_date = strtotime('-3 days', $festival_date);
            $end_date = strtotime('+1 day', $festival_date); // End of next day
            
            // Check if today falls in the active window
            if($today >= $start_date && $today <= $end_date) {
                // Return this festival
                $row['calculated_end_timestamp'] = $end_date;
                return $row;
            }
        }
    }
    return false; // No active festival in the current window
}

function is_valid_festival_price($price) {
    if(is_numeric($price) && $price > 0) return true;
    if(is_string($price) && strpos($price, '-') !== false) {
        $parts = explode('-', $price);
        if(count($parts) >= 1 && is_numeric(trim($parts[0])) && trim($parts[0]) > 0) return true;
    }
    return false;
}

function calculate_festival_discount($base_price, $active_festival) {
    if(!$active_festival) return $base_price;
    
    if(is_string($base_price) && strpos($base_price, '-') !== false) {
        $parts = explode('-', $base_price);
        if(count($parts) == 2) {
            $p1 = calculate_festival_discount(trim($parts[0]), $active_festival);
            $p2 = calculate_festival_discount(trim($parts[1]), $active_festival);
            return "$p1-$p2";
        }
    }
    
    if(!is_numeric($base_price)) return $base_price;
    
    $discount_val = (float)$active_festival['discount_value'];
    
    if($active_festival['discount_type'] == 'percentage') {
        $discount_amount = ($base_price * $discount_val) / 100;
        return round($base_price - $discount_amount);
    } else {
        return max(0, $base_price - $discount_val);
    }
}

function get_festival_savings($base_price, $active_festival) {
    if(!$active_festival) return false;
    
    if(is_string($base_price) && strpos($base_price, '-') !== false) {
        $parts = explode('-', $base_price);
        $base_price = trim($parts[0]);
    }
    
    if(!is_numeric($base_price) || $base_price <= 0) return false;
    
    $discount_val = (float)$active_festival['discount_value'];
    
    if($active_festival['discount_type'] == 'percentage') {
        $discount_amount = ($base_price * $discount_val) / 100;
        return [
            'amount' => round($discount_amount),
            'percentage' => round($discount_val)
        ];
    } else {
        $discount_amount = $discount_val;
        $percentage = ($discount_amount / $base_price) * 100;
        return [
            'amount' => round($discount_amount),
            'percentage' => round($percentage)
        ];
    }
}

// Global invocation so other files can check it once and avoid repeated queries
global $active_festival_data;
if(!isset($active_festival_data) && isset($con)) {
    $active_festival_data = get_active_festival($con);
}
?>
