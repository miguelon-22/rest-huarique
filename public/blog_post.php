<?php
require_once '../config/db.php';
include '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = db_get_one("SELECT * FROM public.blogs WHERE id = ?", [$id]);

if (!$post) {
    echo "<div class='container' style='padding: 200px 0; text-align: center;'><h2>Post no encontrado.</h2><a href='index.php' class='cyber-btn'>Volver al inicio</a></div>";
    include '../includes/footer.php';
    exit;
}
?>

<div class="container" style="padding: 150px 0;">
    <div style="max-width: 800px; margin: 0 auto;">
        <div class="glass" style="padding: 0; overflow: hidden; border-radius: 12px; margin-bottom: 40px;">
            <img src="<?= htmlspecialchars($post['imagen']) ?>" alt="<?= htmlspecialchars($post['nombre']) ?>" style="width: 100%; height: 400px; object-fit: cover;">
            <div style="padding: 50px;">
                <div style="color: var(--accent); font-family: monospace; letter-spacing: 2px; margin-bottom: 15px;">
                    > FECHA_PUBLIK_PROTO: <?= date('d M, Y', strtotime($post['creado_en'])) ?>
                </div>
                <h1 class="neon-glow" style="font-size: 3rem; margin-bottom: 30px; line-height: 1.1;"><?= htmlspecialchars($post['nombre']) ?></h1>
                
                <div style="color: var(--text-secondary); line-height: 1.8; font-size: 1.1rem;" class="blog-content">
                    <?= nl2br($post['contenido']) ?>
                </div>
            </div>
        </div>
        
        <div style="text-align: center;">
            <a href="index.php#blog-section" class="cyber-btn" style="display: inline-flex; padding: 15px 40px; border: 1px solid var(--primary); color: var(--primary);">VOLVER A NOTICIAS</a>
        </div>
    </div>
</div>

<style>
.blog-content p { margin-bottom: 20px; }
</style>

<?php include '../includes/footer.php'; ?>
