<?php
require_once '../../config/db.php';
include '../../includes/admin/header.php';

// Fetch basic stats
try {
    $total_res = db_get_one("SELECT COUNT(*) as count FROM public.reservas_mesa");
    $total_menu = db_get_one("SELECT COUNT(*) as count FROM public.menus");
    $total_cat = db_get_one("SELECT COUNT(*) as count FROM public.categorias");
    $recent_reservations = db_get_all("SELECT * FROM public.reservas_mesa ORDER BY creado_en DESC LIMIT 5");
} catch (Exception $e) {
    $total_res = ['count' => 0];
    $total_menu = ['count' => 0];
    $total_cat = ['count' => 0];
    $recent_reservations = [];
}
?>

<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-bottom: 50px;">
    <div class="stat-card glass" style="border-left: 4px solid var(--accent); background: rgba(0, 255, 136, 0.02); text-align: left; padding-left: 30px;">
        <h4 style="letter-spacing: 3px; font-weight: 800;">ASIGNACIONES_MESA</h4>
        <div class="val neon-glow" style="color: var(--accent); font-size: 3rem;"><?= $total_res['count'] ?></div>
        <div style="font-size: 0.55rem; color: var(--text-secondary); margin-top: 10px;">> STATUS: ONLINE</div>
    </div>
    <div class="stat-card glass" style="border-left: 4px solid var(--primary); background: rgba(255, 71, 87, 0.02); text-align: left; padding-left: 30px;">
        <h4 style="letter-spacing: 3px; font-weight: 800;">UNIDADES_ALIMENTARIAS</h4>
        <div class="val neon-glow" style="font-size: 3rem;"><?= $total_menu['count'] ?></div>
        <div style="font-size: 0.55rem; color: var(--text-secondary); margin-top: 10px;">> CORE_SYNC: COMPLETED</div>
    </div>
    <div class="stat-card glass" style="border-left: 4px solid #ffa502; background: rgba(255, 165, 2, 0.02); text-align: left; padding-left: 30px;">
        <h4 style="letter-spacing: 3px; font-weight: 800;">SEGMENTOS_DATOS</h4>
        <div class="val neon-glow" style="color: #ffa502; font-size: 3rem;"><?= $total_cat['count'] ?></div>
        <div style="font-size: 0.55rem; color: var(--text-secondary); margin-top: 10px;">> DB_MAPPING: ACTIVE</div>
    </div>
</div>

<div class="glass" style="padding: 40px; border-top: 2px solid var(--primary);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h3 style="letter-spacing: 4px; font-weight: 900; margin: 0;">LOG_OPERACIONES_RECIENTES</h3>
        <a href="reservas.php" class="cyber-btn" style="padding: 10px 25px; font-size: 0.65rem; background: var(--primary); color: white; clip-path: polygon(10% 0, 100% 0, 90% 100%, 0% 100%);">VER_TODO_EL_HISTORIAL</a>
    </div>
    
    <table class="table-admin">
        <thead>
            <tr>
                <th>OPERADOR/CLIENTE</th>
                <th>FECHA_PROTOCOLO</th>
                <th>HORA_T</th>
                <th>UNIDADES</th>
                <th>ESTADO_SISTEMA</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recent_reservations)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 50px;">> NO_DATA_DETECTED_IN_CORE</td>
                </tr>
            <?php else: ?>
                <?php foreach ($recent_reservations as $res): ?>
                    <tr>
                        <td style="font-weight: bold; color: white;"># <?= htmlspecialchars($res['nombre']) ?></td>
                        <td><?= date('d/m/Y', strtotime($res['fecha'])) ?></td>
                        <td style="color: var(--accent);"><?= date('H:i', strtotime($res['hora'])) ?></td>
                        <td><?= $res['cantidad_personas'] ?> PAX</td>
                        <td><span class="tag tag-success">PROCESADO</span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/admin/footer.php'; ?>
