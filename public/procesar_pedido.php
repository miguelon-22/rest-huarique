<?php
require_once '../config/db.php';
require_once '../includes/mailer.php';
require_once '../libs/mercado_pago_handler.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. DATA_RECEPTION
    $nombre = $_POST['nombre'] ?? 'Cliente Huarique';
    $dni = $_POST['dni'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $email = $_POST['email'] ?? 'ventas@huarique.com'; // Default if missing
    $direccion = $_POST['direccion'] ?? '';
    $referencia = $_POST['referencia'] ?? '';
    $metodo_pago = $_POST['metodo_pago'] ?? 'mercadopago';
    $tipo_entrega = $_POST['tipo_entrega'] ?? 'delivery'; // delivery o tienda
    
    // JSON Data from hidden fields
    $raw_cart = $_POST['cart_data'] ?? '[]';
    $cart_items = json_decode($raw_cart, true);
    $total_final = floatval($_POST['total_amount'] ?? 0);
    $cupon_id = !empty($_POST['cupon_id']) ? intval($_POST['cupon_id']) : null;
    $monto_descuento = floatval($_POST['monto_descuento'] ?? 0);

    if (empty($cart_items) || $total_final <= 0) {
        header("Location: checkout.php?error=CART_EMPTY");
        exit();
    }

    $numero_pedido = 'HUAR-' . strtoupper(substr(uniqid(), 8));

    try {
        $pdo->beginTransaction();

        // 2. CLIENT_REGISTRATION (Check if exists, or create)
        $sql_client = "INSERT INTO public.clientes (nombre, email, telefono, direccion, dni) 
                       VALUES (?, ?, ?, ?, ?) RETURNING id";
        $stmt_client = $pdo->prepare($sql_client);
        $stmt_client->execute([$nombre, $email, $telefono, $direccion, $dni]);
        $cliente_id = $stmt_client->fetchColumn();

        // 3. PEDIDO_STORAGE
        $info_completa = "MODO: " . strtoupper($tipo_entrega) . " | DIR: $direccion | REF: $referencia | TEL: $telefono | DNI: $dni";
        $sql_pedido = "INSERT INTO public.pedidos 
                       (numero_pedido, precio_total, estado, metodo_pago, informacion_adicional, cliente_id, tipo_pedido, cupon_id, monto_descuento, estado_pago_online) 
                       VALUES (?, ?, 'pendiente', ?, ?, ?, 'online', ?, ?, 'no_pagado') RETURNING id";
        
        $stmt_pedido = $pdo->prepare($sql_pedido);
        $stmt_pedido->execute([$numero_pedido, $total_final, strtoupper($metodo_pago), $info_completa, $cliente_id, $cupon_id, $monto_descuento]);
        $pedido_id = $stmt_pedido->fetchColumn();

        // 4. ITEMS_STORAGE
        $sql_item = "INSERT INTO public.detalle_pedidos (pedido_id, cantidad, subtotal, nombre_menu) VALUES (?, ?, ?, ?)";
        $stmt_item = $pdo->prepare($sql_item);
        foreach ($cart_items as $item) {
            $stmt_item->execute([$pedido_id, $item['qty'], $item['price'] * $item['qty'], $item['name']]);
        }

        $pdo->commit();

        // 5. GMAIL_NOTIFICATION
        // Assuming your mailer has these functions
        try {
            $email_subject = "NUEVO_PEDIDO_HUARIQUE: $numero_pedido";
            @send_huarique_email($email, $email_subject, "<h1>Hola $nombre</h1><p>Tu pedido $numero_pedido por S/ $total_final está en proceso.</p>");
        } catch (Exception $e) { /* Log error but continue flow */ }

        // 6. PAYMENT_REDIRECT (Real Gateway)
        // 6. STRIPE INTEGRATION (Real cURL Gateway)
        if ($metodo_pago === 'stripe') {
            $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, getenv('STRIPE_SECRET_KEY') . ':');
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'pen',
                        'product_data' => ['name' => 'Pedido ' . $numero_pedido],
                        'unit_amount' => intval($total_final * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/rest-huarique/public/pago_exitoso.php?external_reference=' . $numero_pedido,
                'cancel_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/rest-huarique/public/checkout.php?cancel=1',
            ]));
            $response = json_decode(curl_exec($ch), true);
            if (isset($response['url'])) { header("Location: " . $response['url']); exit(); }
        }

        // 7. PAYPAL INTEGRATION (Real OAuth + Order Gateway)
        if ($metodo_pago === 'paypal') {
            $ch = curl_init('https://api-m.sandbox.paypal.com/v1/oauth2/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, getenv('PAYPAL_CLIENT_ID') . ':' . getenv('PAYPAL_SECRET'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
            $token_res = json_decode(curl_exec($ch), true);
            
            if (isset($token_res['access_token'])) {
                $ch_order = curl_init('https://api-m.sandbox.paypal.com/v2/checkout/orders');
                curl_setopt($ch_order, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_order, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token_res['access_token']
                ]);
                $order_data = json_encode([
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'reference_id' => $numero_pedido,
                        'amount' => ['currency_code' => 'USD', 'value' => number_format($total_final / 3.75, 2, '.', '')]
                    ]],
                    'application_context' => [
                        'return_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/rest-huarique/public/pago_exitoso.php?external_reference=' . $numero_pedido,
                        'cancel_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/rest-huarique/public/checkout.php?cancel=1'
                    ]
                ]);
                curl_setopt($ch_order, CURLOPT_POSTFIELDS, $order_data);
                $res_order = json_decode(curl_exec($ch_order), true);
                
                if (isset($res_order['links'])) {
                    foreach ($res_order['links'] as $link) {
                        if ($link['rel'] === 'approve') {
                            header("Location: " . $link['href']);
                            exit();
                        }
                    }
                }
            }
        }

        // Fallback Simulator if APIs fail
        header("Location: pagar.php?type=order&method=" . $metodo_pago . "&amount=" . $total_final . "&id=" . $numero_pedido);


    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        die("FATAL_CORE_ERROR: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
}
exit();
