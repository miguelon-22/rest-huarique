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

if (isset($_POST['save_legales'])) {
    $politicas = $_POST['politicas'] ?? '';
    $terminos = $_POST['terminos'] ?? '';

    // UPSERT politicas
    $check_pol = db_get_all("SELECT id FROM public.politicas_privacidad LIMIT 1");
    if (count($check_pol) > 0) {
        db_execute("UPDATE public.politicas_privacidad SET contenido = ?, actualizado_en = CURRENT_TIMESTAMP WHERE id = ?", [$politicas, $check_pol[0]['id']]);
    } else {
        db_execute("INSERT INTO public.politicas_privacidad (contenido) VALUES (?)", [$politicas]);
    }

    // UPSERT terminos
    $check_ter = db_get_all("SELECT id FROM public.terminos_condiciones LIMIT 1");
    if (count($check_ter) > 0) {
        db_execute("UPDATE public.terminos_condiciones SET contenido = ?, actualizado_en = CURRENT_TIMESTAMP WHERE id = ?", [$terminos, $check_ter[0]['id']]);
    } else {
        db_execute("INSERT INTO public.terminos_condiciones (contenido) VALUES (?)", [$terminos]);
    }

    $msg = 'Textos legales actualizados en el sistema central.';
}

$politicas_data = db_get_all("SELECT contenido FROM public.politicas_privacidad LIMIT 1");
$terminos_data = db_get_all("SELECT contenido FROM public.terminos_condiciones LIMIT 1");

$politicas = $politicas_data[0]['contenido'] ?? '';
$terminos = $terminos_data[0]['contenido'] ?? '';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px;">
    <h2 style="letter-spacing: 5px; font-weight: 900; margin: 0; color: white;">TEXTOS LEGALES Y POLÍTICAS</h2>
</div>

<?php if ($msg): ?>
    <div class="alert-cyber">
        <i data-lucide="terminal"></i>
        <span>> STATUS_SYS: <?= strtoupper($msg) ?></span>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="glass" style="padding: 40px; margin-bottom: 30px; border-top: 2px solid var(--primary);">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px;">
            <i data-lucide="shield" style="color: var(--primary); width: 20px;"></i>
            <h3 style="letter-spacing: 2px; font-weight: 800; color: white; margin: 0; font-size: 1rem;">PROTOCOLOS DE PRIVACIDAD</h3>
        </div>
        <p style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 20px;">Define cómo se manejan los datos del usuario en la plataforma.</p>
        <textarea name="politicas" class="form-control" style="height: 250px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); font-family: monospace;" placeholder="Ingrese las políticas de privacidad detalladas..."><?= htmlspecialchars($politicas) ?></textarea>
    </div>

    <div class="glass" style="padding: 40px; margin-bottom: 60px; border-top: 2px solid var(--accent);">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px;">
            <i data-lucide="file-text" style="color: var(--accent); width: 20px;"></i>
            <h3 style="letter-spacing: 2px; font-weight: 800; color: white; margin: 0; font-size: 1rem;">TÉRMINOS Y CONDICIONES GLOBALES</h3>
        </div>
        <p style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 20px;">Reglamento legal y condiciones de uso del servicio o compras online.</p>
        <textarea name="terminos" class="form-control" style="height: 250px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); font-family: monospace;" placeholder="Ingrese los términos y condiciones..."><?= htmlspecialchars($terminos) ?></textarea>
    </div>

    <button type="submit" name="save_legales" class="cyber-btn" style="width: 100%; height: 60px; background: var(--primary); color: white; font-weight: 800; letter-spacing: 4px; font-size: 1.1rem; border: none; cursor: pointer; border-radius: 4px; box-shadow: 0 0 15px rgba(255, 71, 87, 0.3); margin-bottom: 60px;">
        SINCRONIZAR MARCO LEGAL
    </button>
</form>

<?php include '../../includes/admin/footer.php'; ?>
