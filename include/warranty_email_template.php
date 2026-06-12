<?php
// include/warranty_email_template.php

function getWarrantyEmailTemplate($data) {
    $year = date('Y');
    
    return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Warranty Confirmation</title>
        <style>
            body { 
                font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; 
                background-color: #f3f4f6; 
                margin: 0; 
                padding: 0; 
            }
            .email-wrapper {
                width: 100%;
                background-color: #f3f4f6;
                padding: 30px 0;
            }
            .email-container { 
                max-width: 600px; 
                margin: 0 auto; 
                background-color: #ffffff; 
                border-radius: 12px; 
                overflow: hidden; 
                box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            }
            .header { 
                background-color: #0f172a; 
                padding: 35px 20px; 
                text-align: center; 
            }
            .header h1 { 
                color: #ffffff; 
                margin: 0; 
                font-size: 26px; 
                font-weight: 700; 
                letter-spacing: 1px;
            }
            .header h2 { 
                color: #94a3b8; 
                margin: 8px 0 0 0; 
                font-size: 16px; 
                font-weight: 400; 
                letter-spacing: 0.5px;
            }
            .content { 
                padding: 40px 35px; 
                color: #334155; 
            }
            .content p { 
                font-size: 15px; 
                line-height: 1.7; 
                margin-top: 0; 
            }
            .warranty-badge { 
                display: inline-block; 
                background-color: #dcfce7; 
                color: #166534; 
                padding: 8px 16px; 
                border-radius: 20px; 
                font-weight: 600; 
                font-size: 13px; 
                margin-bottom: 25px; 
                border: 1px solid #bbf7d0;
            }
            .details-card { 
                background-color: #f8fafc; 
                border-left: 4px solid #0f172a; 
                padding: 25px; 
                margin: 25px 0; 
                border-radius: 0 8px 8px 0; 
            }
            .details-row {
                display: flex;
                margin-bottom: 12px;
            }
            .details-row:last-child {
                margin-bottom: 0;
            }
            .details-label { 
                color: #64748b; 
                font-weight: 600; 
                width: 140px; 
                font-size: 14px;
            }
            .details-value {
                color: #0f172a;
                font-weight: 500;
                font-size: 14px;
            }
            .highlight-id {
                color: #2563eb;
                font-weight: 700;
                font-size: 16px;
            }
            .support-box {
                margin-top: 35px;
                padding-top: 25px;
                border-top: 1px solid #e2e8f0;
            }
            .footer { 
                background-color: #f8fafc; 
                padding: 25px; 
                text-align: center; 
                font-size: 13px; 
                color: #64748b; 
                border-top: 1px solid #e2e8f0;
            }
            .footer p { margin: 5px 0; }
            .footer a { color: #2563eb; text-decoration: none; font-weight: 600; }
        </style>
    </head>
    <body>
        <div class='email-wrapper'>
            <div class='email-container'>
                <div class='header'>
                    <h1>GK Almirah</h1>
                    <h2>Official Warranty Registration</h2>
                </div>
                
                <div class='content'>
                    <div class='warranty-badge'>✓ Warranty Successfully Activated</div>
                    
                    <p>Dear <strong>{$data['customer_name']}</strong>,</p>
                    <p>Thank you for choosing GK Almirah. We are pleased to confirm that your product warranty has been successfully registered and activated in our system.</p>
                    
                    <div class='details-card'>
                        <table width='100%' cellpadding='0' cellspacing='0' style='border-collapse: collapse;'>
                            <tr>
                                <td class='details-label' style='padding-bottom: 12px;'>Warranty ID:</td>
                                <td class='details-value highlight-id' style='padding-bottom: 12px;'>{$data['warranty_id']}</td>
                            </tr>
                            <tr>
                                <td class='details-label' style='padding-bottom: 12px;'>Serial Number:</td>
                                <td class='details-value' style='padding-bottom: 12px;'>{$data['serial_number']}</td>
                            </tr>
                            <tr>
                                <td class='details-label' style='padding-bottom: 12px;'>Product:</td>
                                <td class='details-value' style='padding-bottom: 12px;'>{$data['product_name']}</td>
                            </tr>
                            <tr>
                                <td class='details-label' style='padding-bottom: 12px;'>Purchase Date:</td>
                                <td class='details-value' style='padding-bottom: 12px;'>{$data['purchase_date']}</td>
                            </tr>
                            <tr>
                                <td class='details-label'>Valid Until:</td>
                                <td class='details-value' style='color: #059669; font-weight: 600;'>{$data['expiry_date']}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <p>Please keep this email and your original invoice safe. Your <strong>Warranty ID</strong> or <strong>Serial Number</strong> will be required for any future service or support requests.</p>
                    
                    <div class='support-box'>
                        <p style='margin-bottom: 10px;'><strong>Need Support?</strong></p>
                        <p style='margin-top: 0; font-size: 14px;'>Our dedicated customer service team is always ready to assist you with any questions or service needs.</p>
                        <p style='font-size: 14px; margin-bottom: 5px;'>✉️ <a href='mailto:support@gkalmirah.com'>support@gkalmirah.com</a></p>
                        <p style='font-size: 14px; margin-top: 0;'>📞 +91 9682021084</p>
                    </div>
                </div>
                
                <div class='footer'>
                    <p>&copy; {$year} GK Almirah Manufacturing. All rights reserved.</p>
                    <p>Kannauj, Uttar Pradesh, India</p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";
}
?>
