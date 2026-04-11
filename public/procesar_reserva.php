<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $cantidad_personas = $_POST['personas'] ?? 2;
    $informacion_adicional = $_POST['informacion_adicional'] ?? '';

    if (empty($nombre) || empty($dni) || empty($email) || empty($fecha) || empty($hora)) {
        header("Location: index.php?reserva=error");
        exit;
    }

    try {
        require_once '../libs/mercado_pago_handler.php';

        $sql = "INSERT INTO public.reservas_mesa (nombre, dni, email, telefono, fecha, hora, cantidad_personas, informacion_adicional, monto_adelanto, estado_pago) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 25.00, 'pendiente') RETURNING id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nombre, 
            $dni, 
            $email, 
            $telefono, 
            $fecha, 
            $hora, 
            $cantidad_personas, 
            $informacion_adicional
        ]);
        $reserva_id = $stmt->fetchColumn();

        if ($reserva_id) {
            $numero_reserva = 'REV-' . str_pad($reserva_id, 6, "0", STR_PAD_LEFT);
            
            // Generate real payment link for the reservation deposit using Stripe
            $stripe_key = $_ENV['STRIPE_SECRET_KEY'] ?? getenv('STRIPE_SECRET_KEY');
            $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERPWD, $stripe_key . ':');
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'pen',
                        'product_data' => ['name' => 'Adelanto Reserva Mesa'],
                        'unit_amount' => 25 * 100, // S/ 25.00
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('procesar_reserva.php', 'pago_exitoso.php', $_SERVER['REQUEST_URI']) . (strpos($_SERVER['REQUEST_URI'], '?') !== false ? '&' : '?') . 'external_reference=' . $numero_reserva,
                'cancel_url' => (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('procesar_reserva.php', 'index.php', $_SERVER['REQUEST_URI']) . (strpos($_SERVER['REQUEST_URI'], '?') !== false ? '&' : '?') . 'reserva=error',
            ]));
            
            $res = curl_exec($ch);
            $curl_err = curl_error($ch);
            $response = json_decode($res, true);
            
            if (isset($response['url'])) {
                header("Location: " . $response['url']);
                exit;
            } else {
                $msg = $response['error']['message'] ?? ($curl_err ?: 'Error desconocido al conectar con Stripe.');
                error_log("Stripe Error (Reservation): " . $res . " | Curl: " . $curl_err);
                die("<div style='background:#fde8e8; color:#9b1c1c; padding:20px; border-radius:8px; font-family:sans-serif;'>
                        <h3>Error en Pago de Reserva (Stripe)</h3>
                        <p>$msg</p>
                        <a href='index.php#reservas'>Intentar de Nuevo</a>
                    </div>");
            }
        } else {
            header("Location: index.php?reserva=error&msg=no_insert");
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        header("Location: index.php?reserva=error");
    }
} else {
    header("Location: index.php");
}
exit;
