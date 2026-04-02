<?php
require_once '../../config/db.php';
include '../../includes/admin/header.php';

// Protegido por middleware: solo admin puede acceder
if (!has_role('admin')) { auth_required('admin'); }

$msg = '';

// Handle Add Employee
if (isset($_POST['save_empleado'])) {
    $primer_nombre = $_POST['primer_nombre'] ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? 'admin';
    
    if (!empty($primer_nombre) && !empty($email) && !empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        try {
            db_execute("INSERT INTO public.usuarios (primer_nombre, apellido, email, password, rol, estado) VALUES (?, ?, ?, ?, ?, 1)",
                [$primer_nombre, $apellido, $email, $hashed, $rol]);
            $msg = 'Empleado registrado con éxito.';
        } catch (Exception $e) {
            $msg = 'Error al registrar. Verifica si el correo ya está en uso.';
        }
    }
}

// Handle Delete Employee
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    if ($delete_id != ($_SESSION['admin_id'] ?? 0)) {
        db_execute("DELETE FROM public.usuarios WHERE id = ?", [$delete_id]);
        $msg = 'Empleado eliminado del sistema.';
    } else {
        $msg = 'Error: No puedes eliminar tu propia cuenta.';
    }
}

$empleados = db_get_all("SELECT * FROM public.usuarios ORDER BY creado_en DESC");
?>

<!-- Empleados Management UI -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px;">
    <h2 style="letter-spacing: 5px; font-weight: 900; margin: 0; color: white;">GESTIÓN DE EMPLEADOS</h2>
    <button onclick="document.getElementById('modal-add-empleado').style.display='flex'" class="cyber-btn" style="padding: 15px 35px; background: var(--primary); color: white; border: none; cursor: pointer; clip-path: polygon(10% 0, 100% 0, 90% 100%, 0% 100%);">+ AGREGAR EMPLEADO</button>
</div>

<?php if ($msg): ?>
    <div class="alert-cyber">
        <i data-lucide="terminal"></i>
        <span>> STATUS_SYS: <?= strtoupper($msg) ?></span>
    </div>
<?php endif; ?>

<div class="glass" style="padding: 40px; margin-bottom: 60px; border-top: 2px solid var(--primary);">
    <h3 style="letter-spacing: 3px; font-weight: 800; border-bottom: 1px solid var(--glass-border); padding-bottom: 15px; margin-bottom: 30px;">PERSONAL_AUTORIZADO</h3>
    <table class="table-admin">
        <thead>
            <tr>
                <th>NOMBRE</th>
                <th>APELLIDO</th>
                <th>EMAIL_CORP</th>
                <th>NIVEL_ACCESO</th>
                <th>GESTIÓN</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($empleados as $e): ?>
                <tr>
                    <td style="font-weight: 800; color: white;"><?= strtoupper(htmlspecialchars($e['primer_nombre'])) ?></td>
                    <td style="color: var(--text-secondary);"><?= strtoupper(htmlspecialchars($e['apellido'])) ?></td>
                    <td style="color: var(--accent); font-weight: 900;"><?= htmlspecialchars($e['email']) ?></td>
                    <td><span class="tag <?= $e['rol'] === 'super_admin' ? 'tag-success' : 'tag-warning' ?>"><?= strtoupper(htmlspecialchars($e['rol'])) ?></span></td>
                    <td>
                        <?php if ($e['id'] != ($_SESSION['admin_id'] ?? 0)): ?>
                            <a href="?delete=<?= $e['id'] ?>" onclick="return confirm('¿CONFIRMAR_ELIMINACIÓN_ACCESO?')" class="btn-delete" style="color: var(--primary); border: 1px solid var(--primary); padding: 10px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s;">
                                <i data-lucide="trash-2" style="width: 20px;"></i>
                            </a>
                        <?php else: ?>
                            <span style="font-size: 0.6rem; color: #555;">[USTED]</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="modal-add-empleado" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="glass" style="padding: 35px 30px; border-top: 3px solid var(--primary); box-shadow: 0 0 40px rgba(255, 71, 87, 0.15); max-width: 450px; width: 90%; position: relative; border-radius: 8px;">
        <button type="button" onclick="document.getElementById('modal-add-empleado').style.display='none'" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: var(--text-secondary); font-size: 1.5rem; cursor: pointer; transition: 0.3s;">&times;</button>
        
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px;">
            <i data-lucide="user-plus" style="color: var(--primary); width: 20px;"></i>
            <h3 style="letter-spacing: 2px; font-weight: 800; color: white; margin: 0; font-size: 1rem;">NUEVO OPERADOR</h3>
        </div>

        <form method="POST" style="display: grid; grid-template-columns: 1fr; gap: 15px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px;">NOMBRE</label>
                    <input type="text" name="primer_nombre" class="form-control" required placeholder="Nombres" style="height: 42px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);">
                </div>
                <div class="form-group">
                    <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px;">APELLIDOS</label>
                    <input type="text" name="apellido" class="form-control" required placeholder="Apellidos" style="height: 42px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);">
                </div>
            </div>
            
            <div class="form-group">
                <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px;">EMAIL CORPORTATIVO</label>
                <input type="email" name="email" class="form-control" required placeholder="operador@huarique.com" style="height: 42px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px;">CREAR PASSWORD</label>
                    <input type="password" name="password" class="form-control" required placeholder="********" style="height: 42px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);">
                </div>
                <div class="form-group">
                    <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px;">NIVEL ACCESO</label>
                    <select name="rol" class="form-control" style="background: rgba(0,0,0,0.6); height: 42px; font-size: 0.85rem; border: 1px solid rgba(255,255,255,0.1);" required>
                        <option value="admin">ADMINISTRADOR</option>
                        <option value="super_admin">SUPER ADMIN</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-top: 10px;">
                <button type="submit" name="save_empleado" class="cyber-btn" style="width: 100%; height: 45px; background: var(--primary); color: white; font-weight: 800; letter-spacing: 2px; font-size: 0.85rem; border: none; cursor: pointer; border-radius: 4px; box-shadow: 0 0 15px rgba(255, 71, 87, 0.3);">
                    REGISTRAR EN SISTEMA
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .btn-delete:hover { background: var(--primary); color: white !important; box-shadow: 0 0 20px var(--primary-glow); transform: scale(1.1); }
</style>

<?php include '../../includes/admin/footer.php'; ?>
