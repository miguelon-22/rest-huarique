<?php
require_once '../config/db.php';
include '../includes/header.php';

$content = db_get_one("SELECT contenido FROM public.politicas_privacidad ORDER BY id DESC LIMIT 1");
$legal_text = $content ? $content['contenido'] : "<h2>TEXTOS LEGALES Y POLÍTICAS DE PRIVACIDAD</h2><p>En Pollería Huarique, protegemos tus datos personales conforme a la ley...</p>";
?>

<div class="container" style="padding: 150px 0;">
    <div class="glass" style="max-width: 900px; margin: 0 auto; padding: 60px; line-height: 1.8;">
        <h1 class="neon-glow" style="margin-bottom: 40px; border-bottom: 2px solid var(--accent); padding-bottom: 20px;">POLÍTICAS DE PRIVACIDAD</h1>
        <div style="color: var(--text-secondary);">
            <?= $legal_text ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
