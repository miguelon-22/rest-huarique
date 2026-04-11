<?php
require_once '../config/db.php';
include '../includes/header.php';

// Fetch all categories and their menu items
try {
    $categories = db_get_all("SELECT * FROM public.categorias ORDER BY nombre ASC");
    $all_menus = db_get_all("SELECT * FROM public.menus ORDER BY categoria_id, nombre ASC");
    $menus_by_category = [];
    foreach ($all_menus as $menu) {
        $menus_by_category[$menu['categoria_id']][] = $menu;
    }
} catch (Exception $e) {
    $categories = [];
    $menus_by_category = [];
}
?>

<div class="container" style="padding-top: 120px; padding-bottom: 80px;">
    <div class="section-header">
        <h1 style="font-size: 3rem; margin-bottom: 10px;">Nuestra <span style="color: var(--primary);">Carta Completa</span></h1>
        <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto;">Disfruta de la mejor variedad gastronómica con el sello de Huarique. Calidad y sabor garantizado.</p>
        <div class="line" style="margin-top: 20px;"></div>
    </div>

    <?php if (empty($categories)): ?>
        <div style="text-align: center; padding: 50px;">
            <p>Aún no hay categorías registradas.</p>
        </div>
    <?php else: ?>
        <?php foreach ($categories as $cat): ?>
            <div id="cat-<?= $cat['id'] ?>" style="margin-top: 60px;">
                <h2 style="font-size: 2rem; border-bottom: 2px solid var(--accent); display: inline-block; padding-bottom: 5px; margin-bottom: 30px;">
                    <?= htmlspecialchars($cat['nombre']) ?>
                </h2>
                
                <div class="menu-grid">
                    <?php if (isset($menus_by_category[$cat['id']])): ?>
                        <?php foreach ($menus_by_category[$cat['id']] as $menu): ?>
                             <div class="menu-card glass">
                                 <img src="<?= htmlspecialchars($menu['imagen']) ?>" alt="<?= htmlspecialchars($menu['nombre']) ?>" onerror="this.src='https://images.unsplash.com/photo-1598103442097-8b74394b95c6?auto=format&fit=crop&q=80&w=400'">
                                 <h3 class="accent-glow" style="font-size: 1.1rem;"><?= htmlspecialchars($menu['nombre']) ?></h3>
                                 <p style="color: var(--text-secondary); margin-bottom: 20px; font-size: 0.8rem; height: 40px; overflow: hidden;"><?= htmlspecialchars($menu['descripcion']) ?></p>
                                 <div style="display: flex; justify-content: space-between; align-items: center;">
                                     <div class="price neon-glow">S/ <?= number_format($menu['precio'], 2) ?></div>
                                     <button class="add-to-cart glass" 
                                             data-id="<?= $menu['id'] ?>" 
                                             data-name="<?= htmlspecialchars($menu['nombre']) ?>" 
                                             data-price="<?= $menu['precio'] ?>" 
                                             data-image="<?= htmlspecialchars($menu['imagen']) ?>"
                                             style="width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--accent); border-color: var(--accent); cursor: pointer; transition: 0.3s;">
                                         <i data-lucide="plus" style="width: 20px;"></i>
                                     </button>
                                 </div>
                             </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: var(--text-secondary);">Próximamente más productos en esta categoría.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
