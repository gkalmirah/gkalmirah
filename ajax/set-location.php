<?php
session_start();

if (isset($_POST['pincode'])) {
    $pincode = preg_replace('/[^0-9]/', '', $_POST['pincode']);
    
    if (isset($_POST['city_state']) && !empty(trim($_POST['city_state']))) {
        $_SESSION['city_state'] = trim($_POST['city_state']);
    }

    if (strlen($pincode) == 6 || !empty($_SESSION['city_state'])) {
        if(strlen($pincode) == 6) {
            $_SESSION['pincode'] = $pincode;
        }
        $cityState = isset($_SESSION['city_state']) ? $_SESSION['city_state'] : $pincode;
        echo json_encode(['success' => true, 'pincode' => $pincode, 'city_state' => $cityState]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid location format']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Location not provided']);
}
?>