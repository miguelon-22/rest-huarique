<?php
require_once '../config/db.php';
include '../includes/header.php';

$testimonios = db_get_all("SELECT * FROM public.testimonios ORDER BY creado_en DESC");
?>

<div class="container" style="padding: 150px 0;">
    <div class="section-header" style="text-align: center; margin-bottom: 60px;">
        <h2 class="neon-glow" style="font-size: 3rem; margin-bottom: 10px;">TODOS LOS TESTIMONIOS</h2>
        <p style="color: var(--accent); font-weight: bold; letter-spacing: 2px;">PROTOCOLOS DE SATISFACCIÓN TOTAL</p>
    </div>

    <div class="menu-grid">
        <?php foreach ($testimonios as $t): 
            $estrellas = $t['calificacion'] ?? 5;
        ?>
            <div class="menu-card glass" style="padding: 40px; text-align: center; border-image: linear-gradient(to bottom right, var(--primary), transparent) 1;">
                <div style="color: #ffa502; margin-bottom: 20px;">
                    <?php for($i=1; $i<=5; $i++): ?>
                        <i data-lucide="star" <?= ($i <= $estrellas) ? 'fill="currentColor"' : 'style="opacity: 0.3;"' ?> style="width: 16px; height: 16px;"></i>
                    <?php endfor; ?>
                </div>
                <p style="font-style: italic; margin-bottom: 20px; color: var(--text-primary); font-size: 0.9rem;">"<?= htmlspecialchars($t['contenido']) ?>"</p>
                <h4 class="neon-glow" style="color: var(--primary); letter-spacing: 3px; font-size: 0.8rem;"><?= strtoupper(htmlspecialchars($t['nombre'])) ?></h4>
                <div style="font-size: 0.6rem; color: var(--text-secondary); margin-top: 15px;">PUBLICADO: <?= date('d/m/Y', strtotime($t['creado_en'])) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div style="text-align: center; margin-top: 60px;">
        <a href="index.php" class="cyber-btn" style="display: inline-flex; padding: 20px 50px; background: var(--primary); color: white;">VOLVER AL INICIO</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
