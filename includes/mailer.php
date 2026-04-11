<?php
/**
 * Polleria Huarique - Gmail SMTP Config & Mailer (PRO VERSION)
 * Uses PHPMailer for secure SMTP delivery.
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function send_huarique_email($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv('SMTP_USER');
        $mail->Password   = getenv('SMTP_PASS');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
        $mail->Port       = getenv('SMTP_PORT') ?: 465;
        $mail->CharSet    = 'UTF-8';

        // Recipients
        $from_email = getenv('SMTP_FROM') ?: $mail->Username;
        $from_name  = getenv('SMTP_NAME') ?: 'Pollería Huarique';
        $mail->setFrom($from_email, $from_name);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("HUARIQUE_MAILER_ERROR: " . $mail->ErrorInfo);
        return false;
    }
}

function get_order_email_template($order_data, $items) {
    $html = "
    <div style='background: #050505; color: white; padding: 40px; font-family: monospace; border: 1px solid #ff4757;'>
        <h1 style='color: #ff4757; border-bottom: 2px solid #ff4757; padding-bottom: 10px;'>HUARIQUE_RECEIPT_COMPLETED</h1>
        <p style='color: #00f5ff;'>SOLICITUD: {$order_data['numero_pedido']}</p>
        <p>CLIENTE: {$order_data['nombre']}</p>
        <hr style='border-color: #333;'>
        <table style='width: 100%; color: #eee; border-collapse: collapse;'>
            <thead>
                <tr style='background: #111;'>
                    <th style='text-align: left; padding: 10px;'>PRODUCTO</th>
                    <th style='padding: 10px;'>CANT</th>
                    <th style='text-align: right; padding: 10px;'>SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>";
    
    foreach ($items as $item) {
        $name = $item['nombre_menu'] ?? $item['name'];
        $qty = $item['cantidad'] ?? $item['qty'];
        $price = $item['subtotal'] ?? ($item['price'] * $qty);
        $html .= "
        <tr>
            <td style='padding: 10px; border-bottom: 1px solid #222;'>{$name}</td>
            <td style='padding: 10px; border-bottom: 1px solid #222; text-align: center;'>{$qty}</td>
            <td style='padding: 10px; border-bottom: 1px solid #222; text-align: right;'>S/ " . number_format($price, 2) . "</td>
        </tr>";
    }
    
    $html .= "
            </tbody>
        </table>
        <div style='text-align: right; margin-top: 30px;'>
            <div style='color: #888;'>SUBTOTAL: S/ " . number_format($order_data['monto'], 2) . "</div>
            <h2 style='color: #00ff88;'>TOTAL_PAGADO: S/ " . number_format($order_data['monto'], 2) . "</h2>
        </div>
        <div style='margin-top: 50px; padding-top: 20px; border-top: 1px dashed #333; font-size: 0.7rem; color: #555; text-align: center;'>
            ESTE ES UN COMPROBANTE DIGITAL GENERADO POR HUARIQUE OS. <br>
            ¡GRACIAS POR TU PREFERENCIA!
        </div>
    </div>";
    
    return $html;
}

function get_reservation_email_template($res) {
    return "
    <div style='background: #050505; color: white; padding: 40px; font-family: monospace; border: 1px solid #00f5ff;'>
        <h1 style='color: #00f5ff; border-bottom: 2px solid #00f5ff; padding-bottom: 10px;'>RESERVA_CONFIRMADA_HUARIQUE</h1>
        <p>HOLA, <strong>{$res['nombre']}</strong></p>
        <p>Tu mesa ha sido reservada con éxito para el protocolo de sabor.</p>
        
        <div style='background: #111; padding: 20px; border-radius: 4px; border-left: 5px solid #00f5ff; margin: 30px 0;'>
            <p><strong>CÓDIGO:</strong> REV-{$res['id']}</p>
            <p><strong>FECHA:</strong> {$res['fecha']}</p>
            <p><strong>HORA:</strong> {$res['hora']}</p>
            <p><strong>PERSONAS:</strong> {$res['cantidad_personas']}</p>
        </div>

        <p style='color: #ffcc00;'>* Recuerda llegar 10 minutos antes de tu reserva.</p>
        
        <div style='margin-top: 50px; font-size: 0.7rem; color: #555; text-align: center;'>
            HUARIQUE RESTAURANTE - Protokolo de sabor activo.
        </div>
    </div>";
}
