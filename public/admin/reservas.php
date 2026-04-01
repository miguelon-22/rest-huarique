<?php
require_once '../../config/db.php';
include '../../includes/admin/header.php';

$msg = '';

// Handle Delete Reservation
if (isset($_GET['delete'])) {
    db_execute("DELETE FROM public.reservas_mesa WHERE id = ?", [$_GET['delete']]);
    $msg = 'Reserva eliminada.';
}

$reservations = db_get_all("SELECT * FROM public.reservas_mesa WHERE estado_pago = 'pagado' ORDER BY fecha DESC, hora DESC");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
    <h2>Gestionar Reservas</h2>
</div>

<?php if ($msg): ?>
    <div style="background: rgba(46, 213, 115, 0.1); color: #2ed573; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?= $msg ?>
    </div>
<?php endif; ?>

<div class="glass" style="padding: 30px;">
    <h3>Lista de Reservas</h3>
    <table class="table-admin">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>DNI</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Personas</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reservations)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--text-secondary);">No hay reservas pendientes.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($reservations as $res): ?>
                    <tr>
                        <td><?= htmlspecialchars($res['nombre']) ?></td>
                        <td><?= htmlspecialchars($res['dni']) ?></td>
                        <td><?= htmlspecialchars($res['email']) ?></td>
                        <td><?= htmlspecialchars($res['telefono']) ?></td>
                        <td><?= date('d/m/Y', strtotime($res['fecha'])) ?></td>
                        <td><?= date('H:i', strtotime($res['hora'])) ?></td>
                        <td><?= $res['cantidad_personas'] ?></td>
                        <td>
                            <a href="?delete=<?= $res['id'] ?>" onclick="return confirm('¿Eliminar reserva?')" style="color: var(--primary);">
                                <i data-lucide="trash-2"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/admin/footer.php'; ?>
