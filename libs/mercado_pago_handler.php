<?php
/**
 * Polleria Huarique - REAL Mercado Pago Integration
 * 
 * Instructions:
 * 1. Go to https://www.mercadopago.com.pe/developers/panel/credentials/
 * 2. Get your Access Token (Test or Production)
 * 3. Replace the token below.
 */

class MercadoPagoProtocol {
    private static function getAccessToken() {
        return getenv('MP_ACCESS_TOKEN');
    }

    public static function create_payment_link($order_data, $items) {
        $access_token = self::getAccessToken();
        if (!$access_token) {
            error_log("Mercado Pago Error: MP_ACCESS_TOKEN not found in .env");
            return null;
        }
        $url = 'https://api.mercadopago.com/checkout/preferences';
        
        $mp_items = [];
        foreach ($items as $item) {
            $mp_items[] = [
                "title" => "HUARIQUE: " . $item['name'],
                "quantity" => (int)$item['qty'],
                "unit_price" => (float)$item['price'],
                "currency_id" => "PEN"
            ];
        }

        $body = [
            "items" => $mp_items,
            "payer" => [
                "name" => $order_data['nombre'],
                "email" => $order_data['email'] ?? 'guest@huarique.com'
            ],
            "back_urls" => [
                "success" => "http://localhost/rest-huarique/public/pago_exitoso.php",
                "failure" => "http://localhost/rest-huarique/public/pago_fallido.php",
                "pending" => "http://localhost/rest-huarique/public/pago_pendiente.php"
            ],
            "auto_return" => "approved",
            "external_reference" => $order_data['numero_pedido']
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json'
        ]);

        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status == 200 || $status == 201) {
            $response = json_decode($result, true);
            return $response['init_point']; // Link for redirection (Real Payment)
        } else {
            error_log("Mercado Pago API Error: " . $result);
            return null;
        }
    }
}
