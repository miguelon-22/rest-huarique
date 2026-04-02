<?php
require_once '../../config/db.php';
include '../../includes/admin/header.php';

// Protegido por middleware: solo admin puede acceder
if (!has_role('admin')) { auth_required('admin'); }

$msg = '';

// Handle CRUD
if (isset($_POST['add_coupon'])) {
    $codigo = strtoupper($_POST['codigo']);
    $tipo = $_POST['tipo'];
    $valor = $_POST['valor'];
    $expiracion = $_POST['expiracion'] ?: null;
    $limite = $_POST['limite'] ?: 100;

    db_execute("INSERT INTO public.cupones (codigo, tipo_descuento, valor, fecha_expiracion, limite_uso) VALUES (?, ?, ?, ?, ?)", 
               [$codigo, $tipo, $valor, $expiracion, $limite]);
    $msg = "Cupón '$codigo' creado con éxito.";
}

if (isset($_GET['delete'])) {
    db_execute("DELETE FROM public.cupones WHERE id = ?", [$_GET['delete']]);
    $msg = "Cupón eliminado.";
}

$cupones = db_get_all("SELECT * FROM public.cupones ORDER BY creado_en DESC");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
    <h2 style="letter-spacing: 2px; font-weight: 900;">CUPONES PROMOCIONALES</h2>
    <a href="#add-form" class="cyber-btn" style="padding: 10px 25px; background: var(--primary); color: white;">+ NUEVO CUPÓN</a>
</div>

<?php if ($msg): ?>
    <div style="background: rgba(46, 213, 115, 0.1); color: #2ed573; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?= $msg ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 30px;">
    <!-- Form -->
    <div class="glass" id="add-form" style="padding: 30px; height: fit-content;">
        <h3 style="margin-bottom: 20px;">CREAR CUPÓN</h3>
        <form method="POST">
            <div class="form-group">
                <label>CÓDIGO (Ej: POLLO40)</label>
                <input type="text" name="codigo" class="form-control" required style="text-transform: uppercase;">
            </div>
            <div class="form-group">
                <label>TIPO</label>
                <select name="tipo" class="form-control">
                    <option value="porcentaje">PORCENTAJE (%)</option>
                    <option value="fijo">MONTO FIJO (S/)</option>
                </select>
            </div>
            <div class="form-group">
                <label>VALOR</label>
                <input type="number" name="valor" class="form-control" step="0.01" required>
            </div>
            <div class="form-group">
                <label>LÍMITE DE USO</label>
                <input type="number" name="limite" class="form-control" value="100">
            </div>
            <div class="form-group">
                <label>EXPIRACIÓN (OPCIONAL)</label>
                <input type="date" name="expiracion" class="form-control">
            </div>
            <button type="submit" name="add_coupon" class="cyber-btn" style="width: 100%; margin-top: 20px; background: var(--accent); color: black;">GUARDAR CUPÓN</button>
        </form>
    </div>

    <!-- List -->
    <div class="glass" style="padding: 30px;">
        <h3 style="margin-bottom: 20px;">CUPONES ACTIVOS</h3>
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Valor</th>
                    <th>Usos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cupones as $c): ?>
                    <tr>
                        <td style="font-weight: bold; color: var(--accent);"><?= $c['codigo'] ?></td>
                        <td><?= $c['tipo_descuento'] == 'porcentaje' ? $c['valor'].'%' : 'S/ '.$c['valor'] ?></td>
                        <td><?= $c['usos_actuales'] ?> / <?= $c['limite_uso'] ?></td>
                        <td>
                            <span class="tag <?= $c['activo'] ? 'tag-success' : 'tag-error' ?>">
                                <?= $c['activo'] ? 'ACTIVO' : 'INACTIVO' ?>
                            </span>
                        </td>
                        <td>
                            <a href="?delete=<?= $c['id'] ?>" style="color: var(--primary);" onclick="return confirm('¿Seguro?')">
                                <i data-lucide="trash-2"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/admin/footer.php'; ?>
