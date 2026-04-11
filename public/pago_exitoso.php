<?php
session_start();
require_once '../config/db.php';

$ref = $_GET['external_reference'] ?? ($_GET['ref'] ?? '');
$tipo_confirmacion = 'PEDIDO';

if (!empty($ref)) {
    require_once '../includes/mailer.php';
    if (strpos($ref, 'REV-') === 0) {
        $tipo_confirmacion = 'RESERVA';
        $reserva_id = intval(substr($ref, 4));
        db_execute("UPDATE public.reservas_mesa SET estado_pago = 'pagado' WHERE id = ?", [$reserva_id]);
        
        // Send Reservation Email
        $res = db_get_one("SELECT * FROM public.reservas_mesa WHERE id = ?", [$reserva_id]);
        if ($res && !empty($res['email'])) {
            $body = get_reservation_email_template($res);
            send_huarique_email($res['email'], "TU RESERVA ESTÁ CONFIRMADA - REV-{$reserva_id}", $body);
        }
    } else if (strpos($ref, 'HUAR-') === 0) {
        $tipo_confirmacion = 'PEDIDO';
        db_execute("UPDATE public.pedidos SET estado_pago_online = 'pagado' WHERE numero_pedido = ?", [$ref]);
        
        // Note: For orders, the user requested the email specifically when MARKED AS COMPLETED in admin.
        // However, it's good practice to send a 'Received/Paid' confirmation here too.
        // But to follow instructions strictly, I'll only add reservation email here if they meant 'on success'.
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¡<?= $tipo_confirmacion ?> RECIBIDA! - Pollería Huarique</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        // Clear cart immediately on success
        localStorage.removeItem('huarique_cart');
    </script>
    <style>
        body { background: #020202; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Inter', sans-serif; text-align: center; }
        .success-box { border: 2px solid var(--accent); padding: 50px; background: rgba(0, 255, 136, 0.05); box-shadow: 0 0 50px var(--accent-glow); clip-path: polygon(0 0, 90% 0, 100% 10%, 100% 100%, 10% 100%, 0 90%); }
    </style>
</head>
<body>
    <div class="success-box">
        <h1 class="neon-glow" style="color: var(--accent); font-size: 3rem;">¡PEDIDO CONFIRMADO!</h1>
        <p style="font-size: 0.8rem; color: #888;">Recibirás un correo electrónico con los detalles de tu boleta en breve.</p>
        
        <a href="index.php" class="cyber-btn" style="margin-top: 40px; display: inline-block; padding: 15px 40px; background: var(--primary); color: white; text-decoration: none;">VOLVER AL INICIO</a>
    </div>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
