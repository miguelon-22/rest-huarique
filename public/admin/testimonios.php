<?php
require_once '../../config/db.php';
include '../../includes/admin/header.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php"); exit;
}

$msg = '';

// Solo el rol 'admin' puede AGREGAR testimonios — nadie puede eliminar
if (isset($_POST['save_testimonio'])) {
    if ($_SESSION['admin_role'] !== 'admin') {
        $msg = 'Acceso denegado. Solo el administrador puede agregar testimonios.';
    } else {
        $nombre   = trim($_POST['nombre']   ?? '');
        $correo   = trim($_POST['correo']   ?? '');
        $contenido = trim($_POST['contenido'] ?? '');

        if (!empty($nombre) && !empty($contenido)) {
            db_execute(
                "INSERT INTO public.testimonios (nombre, contenido) VALUES (?, ?)",
                [$nombre, $contenido]
            );
            $msg = 'Testimonio registrado con éxito.';
        } else {
            $msg = 'Error: Nombre y contenido son obligatorios.';
        }
    }
}

// ELIMINACIÓN DESACTIVADA — el admin ya no puede borrar testimonios
// if (isset($_GET['delete'])) { ... }  ← deshabilitado intencionalmente

$testimonios = db_get_all("SELECT * FROM public.testimonios ORDER BY creado_en DESC");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px;">
    <div>
        <h2 style="letter-spacing: 5px; font-weight: 900; margin: 0; color: white;">TESTIMONIOS DE CLIENTES</h2>
        <div style="font-size:0.65rem; color:var(--text-secondary); font-family:monospace; margin-top:6px;">
            TOTAL: <span style="color:var(--accent); font-weight:bold;"><?= count($testimonios) ?></span> RESEÑA(S) REGISTRADA(S)
        </div>
    </div>
    <?php if ($_SESSION['admin_role'] === 'admin'): ?>
    <button onclick="document.getElementById('modal-add-test').style.display='flex'"
            class="cyber-btn"
            style="padding: 15px 35px; background: var(--primary); color: white; border: none; cursor: pointer; clip-path: polygon(10% 0, 100% 0, 90% 100%, 0% 100%); font-weight:800; letter-spacing:2px;">
        + AGREGAR TESTIMONIO
    </button>
    <?php endif; ?>
</div>

<?php if ($msg): ?>
    <div class="alert-cyber" style="margin-bottom:25px;">
        <i data-lucide="terminal"></i>
        <span>&gt; STATUS_SYS: <?= strtoupper($msg) ?></span>
    </div>
<?php endif; ?>

<div class="glass" style="padding: 40px; margin-bottom: 60px; border-top: 2px solid var(--primary); overflow: hidden;">
    <h3 style="letter-spacing: 3px; font-weight: 800; border-bottom: 1px solid var(--glass-border); padding-bottom: 15px; margin-bottom: 30px;">
        REGISTROS_PÚBLICOS
        <span style="font-size:0.6rem; color:var(--text-secondary); font-weight:400; margin-left:15px;">
            [SOLO LECTURA — Eliminación desactivada]
        </span>
    </h3>
    <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <table class="table-admin" style="min-width: 900px; width: 100%;">
            <thead>
                <tr>
                    <th>CLIENTE (AUTOR)</th>
                    <th>CONTENIDO (RESEÑA)</th>
                    <th>CALIFICACIÓN</th>
                    <th>FECHA_PUBLICACIÓN</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($testimonios as $t): ?>
                    <tr>
                        <td style="font-weight: 800; color: white; white-space: nowrap;">
                            <?= strtoupper(htmlspecialchars($t['nombre'])) ?>
                        </td>
                        <td>
                            <div style="font-size: 0.8rem; color: var(--text-secondary); max-width: 380px; line-height: 1.6;">
                                "<?= htmlspecialchars($t['contenido']) ?>"
                            </div>
                        </td>
                        <td style="white-space: nowrap;">
                            <?php 
                                $stars = intval($t['calificacion'] ?? 5);
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $stars 
                                        ? '<span style="color:#ffa502;">★</span>' 
                                        : '<span style="color:var(--text-secondary);">★</span>';
                                }
                            ?>
                        </td>
                        <td style="color: var(--accent); font-size: 0.7rem; white-space: nowrap;">
                            <?= date('d/m/Y H:i', strtotime($t['creado_en'])) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($testimonios)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; color: var(--text-secondary); padding:40px;">
                            <i data-lucide="message-square" style="display:block; margin: 0 auto 10px; width:32px; height:32px;"></i>
                            NO_DATA_DETECTED — Sin testimonios registrados.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL AGREGAR TESTIMONIO (solo admin) -->
<?php if ($_SESSION['admin_role'] === 'admin'): ?>
<div id="modal-add-test" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.88); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
    <div class="glass" style="padding: 35px 30px; border-top: 3px solid var(--primary); box-shadow: 0 0 50px rgba(255, 71, 87, 0.2); max-width: 480px; width: 90%; position: relative; border-radius: 8px;">
        <button type="button"
                onclick="document.getElementById('modal-add-test').style.display='none'"
                style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: var(--text-secondary); font-size: 1.5rem; cursor: pointer; transition: 0.3s; line-height:1;"
                onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-secondary)'">
            &times;
        </button>

        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px;">
            <i data-lucide="message-square" style="color: var(--primary); width: 20px;"></i>
            <h3 style="letter-spacing: 2px; font-weight: 800; color: white; margin: 0; font-size: 1rem;">NUEVO TESTIMONIO</h3>
        </div>

        <form method="POST" style="display: grid; gap: 18px;">
            <div class="form-group">
                <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px; display:block; margin-bottom:6px;">
                    AUTOR_CLIENTE <span style="color:var(--primary);">*</span>
                </label>
                <input type="text" name="nombre" class="form-control" required
                       placeholder="Nombre Apellido"
                       style="height: 42px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);">
            </div>

            <div class="form-group">
                <label style="font-size:0.65rem; color: var(--accent); letter-spacing: 1px; display:block; margin-bottom:6px;">
                    CONTENIDO (RESEÑA) <span style="color:var(--primary);">*</span>
                </label>
                <textarea name="contenido" required class="form-control"
                          style="height: 120px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); resize: vertical;"
                          placeholder="Me encantó el servicio..."></textarea>
            </div>

            <div class="form-group">
                <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px; display:block; margin-bottom:6px;">
                    CALIFICACIÓN
                </label>
                <select name="calificacion" class="form-control"
                        style="height: 42px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);">
                    <option value="5">★★★★★ — Excelente</option>
                    <option value="4">★★★★☆ — Muy bueno</option>
                    <option value="3">★★★☆☆ — Bueno</option>
                    <option value="2">★★☆☆☆ — Regular</option>
                    <option value="1">★☆☆☆☆ — Malo</option>
                </select>
            </div>

            <div style="margin-top: 5px;">
                <button type="submit" name="save_testimonio" class="cyber-btn"
                        style="width: 100%; height: 48px; background: var(--primary); color: white; font-weight: 800; letter-spacing: 2px; font-size: 0.85rem; border: none; cursor: pointer; border-radius: 4px; box-shadow: 0 0 20px rgba(255, 71, 87, 0.3);">
                    AGREGAR A PORTAL
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<style>
    div[style*="overflow-x: auto"]::-webkit-scrollbar { height: 4px; }
    div[style*="overflow-x: auto"]::-webkit-scrollbar-track { background: rgba(255,255,255,0.03); }
    div[style*="overflow-x: auto"]::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 2px; }
</style>

<?php include '../../includes/admin/footer.php'; ?>
