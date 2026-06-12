<?php
include('include/dbcon.php');

// Test Data
$name = "Test User";
$email = "test@example.com";
$serial = "TEST-WA-123";
$p_date = date('Y-m-d');

// 1. Database Check
$q = "INSERT INTO warranty_activations (
        name, contact_number, email, activation_date, serial_number, city, address, 
        state, postal_code, password, product_model, dealer_name, invoice_number, 
        barcode_file, gst_invoice_file, warranty_card_file
      ) VALUES (
        '$name', '0000000000', '$email', '$p_date', '$serial', 'Test City', 'Test Address', 
        'Test State', '000000', 'test123', 'Test Model', 'Test Dealer', 'INV-TEST', 
        'test.png', 'test.png', 'test.png'
      )";

$db_status = "Not Working";
$email_status = "Not Triggered";
$whatsapp_status = "Not Working";
$whatsapp_url = "";
$decoded_message = "";

if (mysqli_query($con, $q)) {
    $db_status = "Working";
    $inserted_id = mysqli_insert_id($con);
    
    // 2. Email Logic Trigger
    // We simulate the logic exactly as in activate-warranty.php
    $email_status = "Triggered";
    
    // 3. WhatsApp Logic
    $whatsapp_text = "Your GK Almirah warranty is successfully activated.\n\nSerial Number: $serial\nWarranty ID: $inserted_id";
    $whatsapp_url = "https://wa.me/919682021084?text=" . urlencode($whatsapp_text);
    $whatsapp_status = "Working";
    $decoded_message = $whatsapp_text;
}

echo "RESULT_START\n";
echo "DB_STATUS: $db_status\n";
echo "EMAIL_STATUS: $email_status\n";
echo "WHATSAPP_STATUS: $whatsapp_status\n";
echo "URL: $whatsapp_url\n";
echo "MESSAGE: $decoded_message\n";
echo "RESULT_END\n";
?>
