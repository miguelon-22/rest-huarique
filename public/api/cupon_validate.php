<?php
require_once '../../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'] ?? '';
    
    if (empty($codigo)) {
        echo json_encode(['success' => false, 'message' => 'Código vacío']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM public.cupones WHERE codigo = ? AND activo = true AND (fecha_expiracion IS NULL OR fecha_expiracion > NOW()) AND (limite_uso > usos_actuales)");
        $stmt->execute([$codigo]);
        $cupon = $stmt->fetch();

        if ($cupon) {
            echo json_encode([
                'success' => true,
                'id' => $cupon['id'],
                'tipo' => $cupon['tipo_descuento'],
                'valor' => (float)$cupon['valor'],
                'message' => 'Cupón aplicado correctamente'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cupón inválido o expirado']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error de servidor']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
exit();
