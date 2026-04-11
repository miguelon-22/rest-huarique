ob_start();
session_start();
require_once '../config/db.php';

$ref = $_GET['external_reference'] ?? ($_GET['ref'] ?? '');
$tipo_confirmacion = 'PEDIDO';

if (empty($ref)) {
    header("Location: index.php");
    exit();
}

try {
    require_once '../includes/mailer.php';
    if (strpos($ref, 'REV-') === 0) {
        $tipo_confirmacion = 'RESERVA';
        $reserva_id = intval(substr($ref, 4));
        db_execute("UPDATE public.reservas_mesa SET estado_pago = 'pagado' WHERE id = ?", [$reserva_id]);
        
        $res = db_get_one("SELECT * FROM public.reservas_mesa WHERE id = ?", [$reserva_id]);
        if ($res && !empty($res['email'])) {
            $body = get_reservation_email_template($res);
            @send_huarique_email($res['email'], "TU RESERVA ESTÁ CONFIRMADA - REV-{$reserva_id}", $body);
        }
    } else if (strpos($ref, 'HUAR-') === 0) {
        $tipo_confirmacion = 'PEDIDO';
        db_execute("UPDATE public.pedidos SET estado_pago_online = 'pagado' WHERE numero_pedido = ?", [$ref]);
        
        // Fetch order data for email
        $pedido = db_get_one("SELECT p.*, c.email, c.nombre FROM public.pedidos p JOIN public.clientes c ON p.cliente_id = c.id WHERE p.numero_pedido = ?", [$ref]);
        if ($pedido) {
            $items = db_get_all("SELECT * FROM public.detalle_pedidos WHERE pedido_id = ?", [$pedido['id']]);
            $email_data = [
                'numero_pedido' => $pedido['numero_pedido'],
                'nombre' => $pedido['nombre'],
                'monto' => $pedido['precio_total']
            ];
            $body = get_order_email_template($email_data, $items);
            @send_huarique_email($pedido['email'], "TU PEDIDO HA SIDO RECIBIDO - {$ref}", $body);
        }
    }
} catch (Throwable $e) {
    error_log("SUCCESS_PAGE_ERROR: " . $e->getMessage());
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¡<?= $tipo_confirmacion ?> RECIBIDA! - Pollería Huarique</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        localStorage.removeItem('huarique_cart');
    </script>
    <style>
        :root { --accent: #00ff88; --primary: #ff4757; }
        body { background: #020202; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Inter', sans-serif; text-align: center; margin: 0; }
        .success-box { border: 2px solid var(--accent); padding: 50px; background: rgba(0, 255, 136, 0.05); box-shadow: 0 0 50px rgba(0, 255, 136, 0.2); clip-path: polygon(0 0, 90% 0, 100% 10%, 100% 100%, 10% 100%, 0 90%); max-width: 600px; width: 90%; }
        .neon-glow { text-shadow: 0 0 10px var(--accent); }
        .cyber-btn { text-decoration: none; font-weight: 800; letter-spacing: 2px; transition: 0.3s; padding: 20px 50px; display: inline-block; background: var(--primary); color: white; margin-top: 30px; }
        .cyber-btn:hover { transform: scale(1.05); box-shadow: 0 0 30px var(--primary); }
    </style>
</head>
<body>
    <div class="success-box">
        <h1 class="neon-glow" style="color: var(--accent); font-size: 2.5rem; margin-bottom: 20px;">¡<?= $tipo_confirmacion ?> CONFIRMADA!</h1>
        <p style="font-size: 1.1rem; color: #fff; margin-bottom: 10px;">REFERENCIA: <span style="color: var(--accent); font-weight: bold;"><?= htmlspecialchars($ref) ?></span></p>
        <p style="font-size: 0.9rem; color: #888;">Hemos enviado un correo electrónico con los detalles a tu bandeja de entrada.</p>
        
        <a href="index.php" class="cyber-btn">VOLVER AL INICIO</a>
    </div>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
