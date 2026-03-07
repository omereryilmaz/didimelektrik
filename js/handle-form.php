<?php
/**
 * Didim Emek Elektrik Contact Form Handler
 * Processes form submissions and sends email notifications
 */

// Set response header to JSON
header('Content-Type: application/json; charset=utf-8');

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Define error log file
$error_log = __DIR__ . '/form-errors.log';

/**
 * Log errors to file
 */
function logError($message) {
    global $error_log;
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[{$timestamp}] {$message}\n";
    error_log($log_message, 3, $error_log);
}

/**
 * Validate and sanitize input
 */
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Validate email address
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (Turkish format)
 */
function isValidPhone($phone) {
    $phone = preg_replace('/[^\d+]/', '', $phone);
    return preg_match('/^\+?90?[0-9]{10}$/', $phone);
}

try {
    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and validate form data
    $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
    $service = isset($_POST['service']) ? sanitizeInput($_POST['service']) : '';
    $message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';
    $agree_terms = isset($_POST['agree_terms']) ? true : false;

    // Validate required fields
    $errors = array();

    if (empty($name) || strlen($name) < 2) {
        $errors[] = 'Ad Soyad alanı gerekli ve en az 2 karakter olmalıdır.';
    }

    if (empty($email) || !isValidEmail($email)) {
        $errors[] = 'Geçerli bir e-posta adresi giriniz.';
    }

    if (empty($message) || strlen($message) < 10) {
        $errors[] = 'Mesaj alanı gerekli ve en az 10 karakter olmalıdır.';
    }

    if (!empty($phone) && !isValidPhone($phone)) {
        $errors[] = 'Geçerli bir telefon numarası giriniz.';
    }

    if (!$agree_terms) {
        $errors[] = 'Kişisel veri işleme rızasını onaylamanız gerekmektedir.';
    }

    // If validation fails, return errors
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => implode(' ', $errors)
        ]);
        exit;
    }

    // Prepare email headers
    $to_email = 'info@didimelektrik.com'; // Change to your email
    $subject = 'Yeni İletişim Formu - Didim Emek Elektrik';
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: <noreply@didimelektrik.com>" . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";

    // Prepare email body
    $email_body = "
    <!DOCTYPE html>
    <html dir='ltr'>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: 'Poppins', Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #023981 0%, #034fa9 100%); color: white; padding: 20px; border-radius: 4px 4px 0 0; }
            .header h2 { margin: 0; font-size: 24px; }
            .content { background: #f9f9f9; padding: 20px; border: 1px solid #e0e0e0; }
            .field { margin-bottom: 15px; }
            .field-label { font-weight: 600; color: #023981; margin-bottom: 5px; }
            .field-value { color: #555; padding: 10px; background: white; border-left: 3px solid #ffcd00; }
            .footer { background: #f0f0f0; padding: 15px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 4px 4px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Yeni İletişim Formu Bildirimi</h2>
            </div>
            <div class='content'>
                <p>Didim Emek Elektrik web sitesinden yeni bir mesaj alındı:</p>
                
                <div class='field'>
                    <div class='field-label'>Ad Soyad:</div>
                    <div class='field-value'>{$name}</div>
                </div>
                
                <div class='field'>
                    <div class='field-label'>E-Posta:</div>
                    <div class='field-value'><a href='mailto:{$email}'>{$email}</a></div>
                </div>
    ";

    if (!empty($phone)) {
        $email_body .= "
                <div class='field'>
                    <div class='field-label'>Telefon:</div>
                    <div class='field-value'><a href='tel:{$phone}'>{$phone}</a></div>
                </div>
        ";
    }

    if (!empty($service)) {
        $service_names = array(
            'elektrik-tesisati' => 'Elektrik Tesisatı Bakım ve Tamirat',
            'pano-bakimi' => 'Pano Bakım ve Tamirat',
            'aydinlatma' => 'Aydınlatma ve LED Hizmetleri',
            'paratoner' => 'Paratoner Hizmetleri',
            'acil-destek' => 'Acil Elektrik Destek',
            'diger' => 'Diğer Hizmetler'
        );
        $service_name = isset($service_names[$service]) ? $service_names[$service] : $service;
        $email_body .= "
                <div class='field'>
                    <div class='field-label'>Hizmet Türü:</div>
                    <div class='field-value'>{$service_name}</div>
                </div>
        ";
    }

    $email_body .= "
                <div class='field'>
                    <div class='field-label'>Mesaj:</div>
                    <div class='field-value'>" . nl2br($message) . "</div>
                </div>
                
                <div class='field' style='margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;'>
                    <p style='font-size: 12px; color: #666; margin: 0;'>
                        <strong>Form Gönderim Tarihi:</strong> " . date('d.m.Y H:i:s') . "<br>
                        <strong>Gönderen IP Adresi:</strong> " . $_SERVER['REMOTE_ADDR'] . "
                    </p>
                </div>
            </div>
            <div class='footer'>
                <p>Bu mesaj Didim Emek Elektrik web sitesinden otomatik olarak gönderilmiştir.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Send email to business
    $mail_sent = mail($to_email, $subject, $email_body, $headers);

    if (!$mail_sent) {
        logError("Email gönderilmedi - To: {$to_email}, From: {$email}");
        throw new Exception('Email gönderirken bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.');
    }

    // Send confirmation email to user
    $user_subject = 'Formu Aldık - Didim Emek Elektrik';
    $user_headers = "MIME-Version: 1.0" . "\r\n";
    $user_headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $user_headers .= "From: <info@didimelektrik.com>" . "\r\n";

    $user_email_body = "
    <!DOCTYPE html>
    <html dir='ltr'>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: 'Poppins', Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #023981 0%, #034fa9 100%); color: white; padding: 20px; border-radius: 4px 4px 0 0; text-align: center; }
            .header h2 { margin: 0; font-size: 24px; }
            .content { background: #f9f9f9; padding: 20px; border: 1px solid #e0e0e0; }
            .footer { background: #f0f0f0; padding: 15px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 4px 4px; }
            .cta-button { display: inline-block; background: #023981; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; margin-top: 15px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Mesajınızı Aldık ✓</h2>
            </div>
            <div class='content'>
                <p>Merhaba {$name},</p>
                <p>Didim Emek Elektrik ile iletişime geçtiğiniz için teşekkür ederiz. Mesajınız başarıyla alınmış ve en kısa sürede size cevap vermeye çalışacağız.</p>
                
                <p><strong>Mesajınızın Detayları:</strong></p>
                <ul style='line-height: 1.8;'>
                    <li><strong>Ad Soyad:</strong> {$name}</li>
                    <li><strong>E-Posta:</strong> {$email}</li>
    ";

    if (!empty($phone)) {
        $user_email_body .= "                    <li><strong>Telefon:</strong> {$phone}</li>";
    }

    if (!empty($service)) {
        $service_name = isset($service_names[$service]) ? $service_names[$service] : $service;
        $user_email_body .= "                    <li><strong>Hizmet Türü:</strong> {$service_name}</li>";
    }

    $user_email_body .= "
                </ul>
                
                <p><strong>Hızlı İletişim İçin:</strong></p>
                <p>
                    <strong>Telefon:</strong> <a href='tel:+905336217745'>(533) 621 77 45</a><br>
                    <strong>Email:</strong> <a href='mailto:info@didimelektrik.com'>info@didimelektrik.com</a><br>
                    <strong>WhatsApp:</strong> <a href='https://wa.me/905336217745'>WhatsApp ile yazın</a>
                </p>
                
                <p>En yakında görüşmek dileğiyle,<br>
                <strong>Didim Emek Elektrik Ekibi</strong></p>
            </div>
            <div class='footer'>
                <p>Bu mesaj otomatik olarak gönderilmiştir. Lütfen cevap vermeyin.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Send confirmation email to user (silently fail if it doesn't work)
    @mail($email, $user_subject, $user_email_body, $user_headers);

    // Log successful submission
    logError("Form başarıyla gönderildi - From: {$name} ({$email})");

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Mesajınız başarıyla gönderildi!'
    ]);

} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
