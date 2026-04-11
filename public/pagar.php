<?php
require_once '../config/db.php';
include '../includes/header.php';

$type = $_GET['type'] ?? 'order'; // order or reservation
$id = $_GET['id'] ?? 0;
$method = $_GET['method'] ?? 'mercadopago';
$amount = $_GET['amount'] ?? '0.00';
?>

<div class="container" style="padding: 150px 0; text-align: center;">
    <div class="glass" style="max-width: 600px; margin: 0 auto; padding: 60px;">
        <div class="loader-neon" style="margin-bottom: 40px;"></div>
        <h2 class="neon-glow" style="margin-bottom: 20px;">INICIALIZANDO PASARELA</h2>
        <p style="color: var(--text-secondary); margin-bottom: 40px;">
            Conectando con el nodo de pago: <strong style="color: var(--primary);"><?= strtoupper($method) ?></strong>...<br>
            Monto a procesar: <span style="color: var(--accent);">S/ <?= number_format($amount, 2) ?></span>
        </p>
        
        <div style="padding: 20px; background: rgba(255, 255, 255, 0.02); border-radius: 12px; font-family: monospace; font-size: 0.8rem; text-align: left; margin-bottom: 40px;">
            > PING <?= strtoupper($method) ?>.GATEWAY... OK<br>
            > VALIDATING SESSION_ID... OK<br>
            > ENCRYPTING DATA... OK<br>
            > READY FOR HANDSHAKE...
        </div>

        <a href="pago_exitoso.php?type=<?= $type ?>&id=<?= $id ?>" class="btn btn-primary" style="width: 100%;">SALTAR SIMULACIÓN (PAGAR)</a>
    </div>
</div>

<style>
.loader-neon {
    width: 60px;
    height: 60px;
    border: 3px solid transparent;
    border-top-color: var(--primary);
    border-bottom-color: var(--accent);
    border-radius: 50%;
    margin: 0 auto;
    animation: spin 1s linear infinite;
    position: relative;
}
.loader-neon::after {
    content: '';
    position: absolute;
    top: 5px; left: 5px; right: 5px; bottom: 5px;
    border: 3px solid transparent;
    border-left-color: var(--secondary);
    border-radius: 50%;
    animation: spin 2s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<?php include '../includes/footer.php'; ?>
