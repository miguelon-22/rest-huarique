<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $hora = $_POST['hora'] ?? '';
    $cantidad_personas = $_POST['cantidad_personas'] ?? 2;
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
                'success_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/rest-huarique/public/pago_exitoso.php?external_reference=' . $numero_reserva,
                'cancel_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/rest-huarique/public/index.php?reserva=error',
            ]));
            
            $res = curl_exec($ch);
            $response = json_decode($res, true);
            
            if (isset($response['url'])) {
                header("Location: " . $response['url']);
                exit;
            } else {
                error_log("Stripe Error (Reservation): " . ($response['error']['message'] ?? 'Unknown'));
                die("Error de Pago: No se pudo conectar con la pasarela real.");
            }
        } else {
            header("Location: index.php?reserva=error");
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        header("Location: index.php?reserva=error");
    }
} else {
    header("Location: index.php");
}
exit;
