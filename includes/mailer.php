<?php
/**
 * Polleria Huarique - Gmail SMTP Config & Mailer
 * Note: Require PHPMailer or use native mail() with SMTP configuration.
 * For true production, please download PHPMailer to /libs/PHPMailer/
 */

function send_huarique_email($to, $subject, $body) {
    // Real Credentials from .env
    $smtp_user = getenv('SMTP_USER') ?: 'ventas@huarique.com';
    $smtp_pass = getenv('SMTP_PASS');
    $smtp_from = getenv('SMTP_FROM') ?: $smtp_user;
    $smtp_name = getenv('SMTP_NAME') ?: 'Pollería Huarique';
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: $smtp_name <$smtp_from>" . "\r\n";

    // Recommending PHPMailer for real environments.
    // For now, attempting native mail() with headers.
    return @mail($to, $subject, $body, $headers);
}

function get_order_email_template($order_data, $items) {
    $html = "
    <div style='background: #050505; color: white; padding: 40px; font-family: monospace;'>
        <h1 style='color: #ff4757; border-bottom: 2px solid #ff4757;'>HUARIQUE_ORDER_CONFIRMATION</h1>
        <p>RECIPIENTE: {$order_data['nombre']}</p>
        <p>IDENT_ID: {$order_data['numero_pedido']}</p>
        <hr style='border-color: #333;'>
        <table style='width: 100%; color: #eee;'>
            <thead>
                <tr>
                    <th style='text-align: left;'>UNIT</th>
                    <th>QTY</th>
                    <th style='text-align: right;'>PRICE</th>
                </tr>
            </thead>
            <tbody>";
    
    foreach ($items as $item) {
        $html .= "
        <tr>
            <td>{$item['name']}</td>
            <td style='text-align: center;'>{$item['qty']}</td>
            <td style='text-align: right;'>S/ " . number_format($item['price'], 2) . "</td>
        </tr>";
    }
    
    $html .= "
            </tbody>
        </table>
        <h2 style='text-align: right; color: #00ff88;'>TOTAL: S/ " . number_format($order_data['monto'], 2) . "</h2>
        <p style='font-size: 0.8rem; color: #888;'>STATUS: PROCESSED_BY_HUARIQUE_OS_GATEWAY</p>
    </div>";
    
    return $html;
}
