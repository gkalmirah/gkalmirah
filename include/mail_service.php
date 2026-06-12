<?php
// include/mail_service.php

// Required for PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Include email configuration with graceful fallback if file is missing (e.g. when cloned from Git)
if (file_exists(__DIR__ . '/email_config.php')) {
    require_once __DIR__ . '/email_config.php';
} else {
    if (!defined('SMTP_HOST')) {
        define('SMTP_HOST', 'smtp.gmail.com');
        define('SMTP_PORT', 587);
        define('SMTP_USER', 'your-email@gmail.com');
        define('SMTP_PASS', 'your-app-password');
        define('SMTP_FROM_EMAIL', 'your-email@gmail.com');
        define('SMTP_FROM_NAME', 'GK Almirah');
        define('SMTP_REPLY_TO', 'your-email@gmail.com');
    }
}

function sendHtmlEmail($to, $subject, $htmlContent) {
    // Include PHPMailer manually
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
        require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
        require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';
    }

    $mail = new PHPMailer(true);

    try {
        // ── Server Settings ──
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                 // Disable verbose debug output
        $mail->isSMTP();                                    // Send using SMTP
        $mail->Host       = SMTP_HOST;                      // Set the SMTP server
        $mail->SMTPAuth   = true;                           // Enable SMTP authentication
        $mail->Username   = SMTP_USER;                      // SMTP username
        $mail->Password   = SMTP_PASS;                      // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption (Port 587)
        $mail->Port       = SMTP_PORT;                      // TCP port to connect to

        // ── Recipients ──
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);                             // Add a recipient
        $mail->addReplyTo(SMTP_REPLY_TO, SMTP_FROM_NAME);

        // ── Content ──
        $mail->isHTML(true);                                // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $htmlContent;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], "\n", $htmlContent));

        // Send the email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>
