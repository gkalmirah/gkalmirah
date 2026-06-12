<?php
// include/notification_service.php
require_once __DIR__ . '/mail_service.php';

function sendOrderConfirmationEmail($to, $customerName, $invoiceNo, $orderDetails, $totalAmount, $paymentMethod, $deliveryAddress, $estDelivery) {
    $subject = "GK Almirah Order Confirmation - #$invoiceNo";
    $htmlContent = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd;'>
            <h2 style='color: #0F172A; text-align: center;'>GK <span style='color: #D4AF37;'>Almirah</span></h2>
            <h3 style='color: #333;'>Order Confirmation</h3>
            <p>Dear $customerName,</p>
            <p>Thank you for shopping with GK Almirah. Your order has been placed successfully!</p>
            
            <div style='background: #f8f8f8; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>
                <p><strong>Order ID:</strong> #$invoiceNo</p>
                <p><strong>Amount:</strong> ₹" . number_format($totalAmount, 2) . "</p>
                <p><strong>Payment Method:</strong> $paymentMethod</p>
                <p><strong>Estimated Delivery:</strong> $estDelivery</p>
            </div>
            
            <h4>Delivery Address:</h4>
            <p>$deliveryAddress</p>
            
            <h4>Order Summary:</h4>
            $orderDetails
            
            <p style='margin-top: 30px; font-size: 13px; color: #777;'>If you have any questions, please reply to this email or contact our support team.</p>
        </div>
    ";
    
    return sendHtmlEmail($to, $subject, $htmlContent);
}

function sendAdminOrderEmail($customerName, $customerEmail, $customerPhone, $invoiceNo, $orderDetails, $totalAmount, $paymentMethod) {
    // Admin email is fetched from config, or fallback
    $adminEmail = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'admin@gkalmirah.com';
    $subject = "New Order Placed - #$invoiceNo";
    
    $htmlContent = "
        <div style='font-family: Arial, sans-serif;'>
            <h3>New Order Received</h3>
            <p><strong>Order ID:</strong> #$invoiceNo</p>
            <p><strong>Customer Name:</strong> $customerName</p>
            <p><strong>Customer Email:</strong> $customerEmail</p>
            <p><strong>Customer Phone:</strong> $customerPhone</p>
            <p><strong>Total Amount:</strong> ₹" . number_format($totalAmount, 2) . "</p>
            <p><strong>Payment Method:</strong> $paymentMethod</p>
            
            <h4>Order Details:</h4>
            $orderDetails
        </div>
    ";
    
    return sendHtmlEmail($adminEmail, $subject, $htmlContent);
}

// WhatsApp Integration Architecture
interface WhatsAppProvider {
    public function sendMessage($phone, $message);
}

class LogWhatsAppProvider implements WhatsAppProvider {
    public function sendMessage($phone, $message) {
        // Logs the message to a file for testing without an API key
        $logFile = __DIR__ . '/../whatsapp_log.txt';
        $logEntry = "[" . date('Y-m-d H:i:s') . "] To: $phone\nMessage: $message\n-------------------------\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);
        return true;
    }
}

// Placeholder for Cloud API
class CloudApiWhatsAppProvider implements WhatsAppProvider {
    public function sendMessage($phone, $message) {
        // TODO: Implement Meta Cloud API cURL request
        return true;
    }
}

function sendWhatsAppNotification($phone, $customerName, $invoiceNo, $totalAmount, $estDelivery) {
    // Determine provider based on config (hardcoded to Log for now to avoid errors)
    $provider = new LogWhatsAppProvider();
    
    $message = "Thank you for your order with GK Almirah.\n\n" .
               "Order ID: #$invoiceNo\n" .
               "Your order has been placed successfully.\n\n" .
               "Amount: ₹" . number_format($totalAmount, 2) . "\n" .
               "Estimated Delivery: $estDelivery\n";
               
    return $provider->sendMessage($phone, $message);
}
?>
