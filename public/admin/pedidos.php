<?php
require_once '../../config/db.php';
include '../../includes/admin/header.php';

$msg = '';

// Handle Status Update
if (isset($_GET['status']) && isset($_GET['id'])) {
    $status = $_GET['status'];
    $id = $_GET['id'];
    db_execute("UPDATE public.pedidos SET estado = ? WHERE id = ?", [$status, $id]);
    $msg = 'Estado del pedido actualizado.';
}

$pedidos = db_get_all("SELECT p.*, c.nombre as cliente_nombre, c.dni as cliente_dni FROM public.pedidos p LEFT JOIN public.clientes c ON p.cliente_id = c.id ORDER BY p.creado_en DESC");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
    <h2 style="letter-spacing: 2px; font-weight: 900;">PEDIDOS DE CLIENTES</h2>
</div>

<?php if ($msg): ?>
    <div style="background: rgba(46, 213, 115, 0.1); color: #2ed573; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?= $msg ?>
    </div>
<?php endif; ?>

<div class="glass" style="padding: 30px;">
    <h3 style="letter-spacing: 2px;">LISTADO DE PEDIDOS</h3>
    <div style="overflow-x: auto;">
        <table class="table-admin" style="min-width: 1100px;">
            <thead>
                <tr>
                    <th>Nro Pedido</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Descuento</th>
                    <th>Total</th>
                    <th>Pago Online</th>
                    <th>Estado Pedido</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pedidos)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-secondary);">No hay pedidos en el sistema.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pedidos as $p): ?>
                        <tr>
                            <td style="font-weight: bold; color: var(--accent);">#<?= htmlspecialchars($p['numero_pedido']) ?></td>
                            <td>
                                <div><?= htmlspecialchars($p['cliente_nombre'] ?? 'Invitado') ?></div>
                                <div style="font-size: 0.7rem; color: var(--accent);">DNI: <?= htmlspecialchars($p['cliente_dni'] ?? 'N/A') ?></div>
                            </td>
                            <td>
                                <span style="font-size: 0.7rem; color: #ccc;"><?= strtoupper($p['tipo_pedido']) ?></span>
                                <div style="font-size: 0.65rem; color: var(--text-secondary); max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($p['informacion_adicional']) ?>">
                                    <?= htmlspecialchars($p['informacion_adicional'] ?? '') ?>
                                </div>
                            </td>
                            <td style="color: var(--accent);">- S/ <?= number_format($p['monto_descuento'], 2) ?></td>
                            <td style="font-weight: bold;">S/ <?= number_format($p['precio_total'], 2) ?></td>
                            <td>
                                <?php 
                                    $pay_status = ($p['estado_pago_online'] === 'pagado') ? 'tag-success' : 'tag-warning';
                                    $pay_text = ($p['estado_pago_online'] === 'pagado') ? 'PAGADO' : 'PENDIENTE';
                                ?>
                                <span class="tag <?= $pay_status ?>"><?= $pay_text ?></span>
                            </td>
                            <td>
                                <?php 
                                    $status_class = 'tag-warning';
                                    if ($p['estado'] === 'completado') $status_class = 'tag-success';
                                    if ($p['estado'] === 'cancelado') $status_class = 'tag-error';
                                ?>
                                <span class="tag <?= $status_class ?>"><?= strtoupper($p['estado'] ?? 'pendiente') ?></span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 10px;">
                                    <a href="?status=completado&id=<?= $p['id'] ?>" title="Completar" style="color: #2ed573;"><i data-lucide="check-square"></i></a>
                                    <a href="?status=cancelado&id=<?= $p['id'] ?>" title="Cancelar" style="color: var(--primary);"><i data-lucide="x-square"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/admin/footer.php'; ?>
