<?php
require_once '../../config/db.php';
include '../../includes/admin/header.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php"); exit;
}
if ($_SESSION['admin_role'] !== 'admin') {
    die("<div style='background: #020202; color: #ff4757; padding: 50px; font-family: monospace; font-size: 1.2rem; text-align: center;'>ACCESO DENEGADO. Nivel de autorización insuficiente.</div>");
}

$msg = '';

if (isset($_GET['delete'])) {
    try {
        db_execute("DELETE FROM public.clientes WHERE id = ?", [$_GET['delete']]);
        $msg = 'Cliente eliminado de la base de datos.';
    } catch(Exception $e) {
        $msg = 'Error al eliminar. Puede que tenga pedidos asociados.';
    }
}

$clientes = db_get_all("SELECT * FROM public.clientes ORDER BY creado_en DESC");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px;">
    <h2 style="letter-spacing: 5px; font-weight: 900; margin: 0; color: white;">DIRECTORIO DE CLIENTES</h2>
</div>

<?php if ($msg): ?>
    <div class="alert-cyber">
        <i data-lucide="terminal"></i>
        <span>> STATUS_SYS: <?= strtoupper($msg) ?></span>
    </div>
<?php endif; ?>

<div class="glass" style="padding: 40px; margin-bottom: 60px; border-top: 2px solid var(--primary);">
    <h3 style="letter-spacing: 3px; font-weight: 800; border-bottom: 1px solid var(--glass-border); padding-bottom: 15px; margin-bottom: 30px;">REGISTROS_SISTEMA</h3>
    <div style="overflow-x: auto;">
        <table class="table-admin" style="min-width: 900px;">
            <thead>
                <tr>
                    <th>NOMBRE_CLIENTE</th>
                    <th>DNI / DOC</th>
                    <th>CONTACTO</th>
                    <th>DIRECCIÓN</th>
                    <th>FECHA_REGISTRO</th>
                    <th>ACCIÓN</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $c): ?>
                    <tr>
                        <td style="font-weight: 800; color: white;"><?= strtoupper(htmlspecialchars($c['nombre'])) ?></td>
                        <td style="color: var(--accent);"><?= htmlspecialchars($c['dni'] ?? 'N/A') ?></td>
                        <td>
                            <div style="color: var(--text-secondary);"><?= htmlspecialchars($c['email']) ?></div>
                            <div style="font-size: 0.7rem;"><?= htmlspecialchars($c['telefono']) ?></div>
                        </td>
                        <td style="color: var(--text-secondary);"><?= htmlspecialchars($c['direccion']) ?></td>
                        <td style="font-size: 0.7rem;"><?= date('d/m/Y H:i', strtotime($c['creado_en'])) ?></td>
                        <td>
                            <a href="?delete=<?= $c['id'] ?>" onclick="return confirm('¿CONFIRMAR_ELIMINACIÓN_CLIENTE?')" class="btn-delete" style="color: var(--primary); border: 1px solid var(--primary); padding: 10px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s;">
                                <i data-lucide="trash-2" style="width: 20px;"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($clientes)): ?>
                <tr><td colspan="6" style="text-align:center; color: var(--text-secondary);">NO_DATA_DETECTED</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .btn-delete:hover { background: var(--primary); color: white !important; box-shadow: 0 0 20px var(--primary-glow); transform: scale(1.1); }
</style>

<?php include '../../includes/admin/footer.php'; ?>
