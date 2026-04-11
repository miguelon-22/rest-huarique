<?php
require_once '../config/db.php';
include '../includes/header.php';

$content = db_get_one("SELECT contenido FROM public.terminos_condiciones ORDER BY id DESC LIMIT 1");
$legal_text = $content ? $content['contenido'] : "<h2>TÉRMINOS Y CONDICIONES GLOBALES</h2><p>Bienvenido a Pollería Huarique. Al acceder a nuestro sitio web y realizar un pedido o reserva, usted acepta nuestros términos...</p>";
?>

<div class="container" style="padding: 150px 0;">
    <div class="glass" style="max-width: 900px; margin: 0 auto; padding: 60px; line-height: 1.8;">
        <h1 class="neon-glow" style="margin-bottom: 40px; border-bottom: 2px solid var(--primary); padding-bottom: 20px;">TÉRMINOS Y CONDICIONES</h1>
        <div style="color: var(--text-secondary);">
            <?= $legal_text ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
