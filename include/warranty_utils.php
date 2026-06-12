<?php
function generateWarrantyId($con) {
    $is_unique = false;
    $warranty_id = '';
    
    // Safety check: ensure column exists (auto-migration)
    $check_col = mysqli_query($con, "SHOW COLUMNS FROM warranty_activations LIKE 'warranty_code'");
    if(mysqli_num_rows($check_col) == 0) {
        mysqli_query($con, "ALTER TABLE warranty_activations ADD COLUMN warranty_code VARCHAR(50) NULL UNIQUE");
    }

    // Generate unique GKWR- ID
    while (!$is_unique) {
        $random_number = mt_rand(100000, 999999);
        $warranty_id = "GKWR-" . $random_number;
        
        $query = "SELECT id FROM warranty_activations WHERE warranty_code = '$warranty_id'";
        $result = mysqli_query($con, $query);
        
        if ($result && mysqli_num_rows($result) == 0) {
            $is_unique = true;
        }
    }
    
    return $warranty_id;
}

function formatSerialNumber($serial) {
    $serial = strtoupper(trim($serial));
    // If not already formatted with our prefix, format it nicely
    if (strpos($serial, 'GK-ALM-') !== 0) {
        // Strip out non-alphanumeric chars
        $clean = preg_replace('/[^A-Z0-9]/', '', $serial);
        return "GK-ALM-" . date('Y') . "-" . $clean;
    }
    return $serial;
}
?>
