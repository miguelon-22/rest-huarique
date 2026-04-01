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

if (isset($_POST['save_testimonio'])) {
    $nombre = $_POST['nombre'] ?? '';
    $contenido = $_POST['contenido'] ?? '';

    if (!empty($nombre) && !empty($contenido)) {
        db_execute("INSERT INTO public.testimonios (nombre, contenido) VALUES (?, ?)", 
                   [$nombre, $contenido]);
        $msg = 'Testimonio registrado con éxito.';
    }
}

if (isset($_GET['delete'])) {
    db_execute("DELETE FROM public.testimonios WHERE id = ?", [$_GET['delete']]);
    $msg = 'Registro eliminado.';
}

$testimonios = db_get_all("SELECT * FROM public.testimonios ORDER BY creado_en DESC");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px;">
    <h2 style="letter-spacing: 5px; font-weight: 900; margin: 0; color: white;">TESTIMONIOS DE CLIENTES</h2>
    <button onclick="document.getElementById('modal-add-test').style.display='flex'" class="cyber-btn" style="padding: 15px 35px; background: var(--primary); color: white; border: none; cursor: pointer; clip-path: polygon(10% 0, 100% 0, 90% 100%, 0% 100%);">+ AGREGAR TESTIMONIO</button>
</div>

<?php if ($msg): ?>
    <div class="alert-cyber">
        <i data-lucide="terminal"></i>
        <span>> STATUS_SYS: <?= strtoupper($msg) ?></span>
    </div>
<?php endif; ?>

<div class="glass" style="padding: 40px; margin-bottom: 60px; border-top: 2px solid var(--primary);">
    <h3 style="letter-spacing: 3px; font-weight: 800; border-bottom: 1px solid var(--glass-border); padding-bottom: 15px; margin-bottom: 30px;">REGISTROS_PÚBLICOS</h3>
    <div style="overflow-x: auto;">
        <table class="table-admin" style="min-width: 800px;">
            <thead>
                <tr>
                    <th>CLIENTE (AUTOR)</th>
                    <th>CONTENIDO (RESEÑA)</th>
                    <th>FECHA_PUBLICACIÓN</th>
                    <th>GESTIÓN</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($testimonios as $t): ?>
                    <tr>
                        <td style="font-weight: 800; color: white;"><?= strtoupper(htmlspecialchars($t['nombre'])) ?></td>
                        <td>
                            <div style="font-size: 0.8rem; color: var(--text-secondary); max-width: 400px; line-height: 1.5;">
                                "<?= htmlspecialchars($t['contenido']) ?>"
                            </div>
                        </td>
                        <td style="color: var(--accent); font-size: 0.7rem;"><?= date('d/m/Y H:i', strtotime($t['creado_en'])) ?></td>
                        <td>
                            <a href="?delete=<?= $t['id'] ?>" onclick="return confirm('¿CONFIRMAR_ELIMINACIÓN_RESEÑA?')" class="btn-delete" style="color: var(--primary); border: 1px solid var(--primary); padding: 10px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s;">
                                <i data-lucide="trash-2" style="width: 20px;"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($testimonios)): ?>
                <tr><td colspan="4" style="text-align:center; color: var(--text-secondary);">NO_DATA_DETECTED</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modal-add-test" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="glass" style="padding: 35px 30px; border-top: 3px solid var(--primary); box-shadow: 0 0 40px rgba(255, 71, 87, 0.15); max-width: 450px; width: 90%; position: relative; border-radius: 8px;">
        <button type="button" onclick="document.getElementById('modal-add-test').style.display='none'" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: var(--text-secondary); font-size: 1.5rem; cursor: pointer; transition: 0.3s;">&times;</button>
        
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px;">
            <i data-lucide="message-square" style="color: var(--primary); width: 20px;"></i>
            <h3 style="letter-spacing: 2px; font-weight: 800; color: white; margin: 0; font-size: 1rem;">NUEVO TESTIMONIO</h3>
        </div>

        <form method="POST" style="display: grid; grid-template-columns: 1fr; gap: 18px;">
            <div class="form-group">
                <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px;">AUTOR_CLIENTE</label>
                <input type="text" name="nombre" class="form-control" required placeholder="Nombre Apellido" style="height: 42px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);">
            </div>
            
            <div class="form-group">
                <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px;">CONTENIDO (RESEÑA)</label>
                <textarea name="contenido" required class="form-control" style="height: 120px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);" placeholder="Me encantó el servicio..."></textarea>
            </div>
            
            <div style="margin-top: 10px;">
                <button type="submit" name="save_testimonio" class="cyber-btn" style="width: 100%; height: 45px; background: var(--primary); color: white; font-weight: 800; letter-spacing: 2px; font-size: 0.85rem; border: none; cursor: pointer; border-radius: 4px; box-shadow: 0 0 15px rgba(255, 71, 87, 0.3);">
                    AGREGAR A PORTAL
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .btn-delete:hover { background: var(--primary); color: white !important; box-shadow: 0 0 20px var(--primary-glow); transform: scale(1.1); }
</style>

<?php include '../../includes/admin/footer.php'; ?>
