<?php
require_once '../../config/db.php';
include '../../includes/admin/header.php';

// Protegido por middleware via header.php (auth_required)
$msg = '';

if (isset($_POST['save_config'])) {
    foreach ($_POST['config'] as $clave => $valor) {
        $check = db_get_all("SELECT id FROM public.configuraciones WHERE clave = ?", [$clave]);
        if (count($check) > 0) {
            db_execute("UPDATE public.configuraciones SET valor = ? WHERE clave = ?", [$valor, $clave]);
        } else {
            db_execute("INSERT INTO public.configuraciones (clave, valor) VALUES (?, ?)", [$clave, $valor]);
        }
    }
    $msg = 'Parámetros del sistema actualizados.';
}

$config_data = db_get_all("SELECT clave, valor FROM public.configuraciones");
$config = [];
foreach ($config_data as $row) {
    $config[$row['clave']] = $row['valor'];
}

function get_conf($key, $default = '') {
    global $config;
    return $config[$key] ?? $default;
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px;">
    <h2 style="letter-spacing: 5px; font-weight: 900; margin: 0; color: white;">CONFIGURACIÓN GLOBAL</h2>
</div>

<?php if ($msg): ?>
    <div class="alert-cyber">
        <i data-lucide="terminal"></i>
        <span>> STATUS_SYS: <?= strtoupper($msg) ?></span>
    </div>
<?php endif; ?>

<form method="POST">
    <div class="glass" style="padding: 40px; margin-bottom: 40px; border-top: 2px solid var(--primary);">
        <h3 style="letter-spacing: 2px; font-weight: 800; border-bottom: 1px solid var(--glass-border); padding-bottom: 15px; margin-bottom: 30px; color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="monitor" style="color: var(--primary);"></i> SEO & TEXTOS PRINCIPALES
        </h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div class="form-group">
                <label style="color: var(--accent); font-size: 0.7rem; letter-spacing: 1px;">NOMBRE DE LA MARCA</label>
                <input type="text" name="config[site_name]" class="form-control" value="<?= htmlspecialchars(get_conf('site_name', 'HUARIQUE RESTAURANTE')) ?>" style="background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1);">
            </div>
            <div class="form-group">
                <label style="color: var(--accent); font-size: 0.7rem; letter-spacing: 1px;">FRASE HÉROE (INICIO)</label>
                <input type="text" name="config[hero_title]" class="form-control" value="<?= htmlspecialchars(get_conf('hero_title', 'EL SABOR QUE TRASCIENDE EL TIEMPO')) ?>" style="background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1);">
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label style="color: var(--accent); font-size: 0.7rem; letter-spacing: 1px;">SUBTÍTULO HÉROE</label>
                <input type="text" name="config[hero_subtitle]" class="form-control" value="<?= htmlspecialchars(get_conf('hero_subtitle', 'Sabor 2.0: Tradición milenaria, algoritmos de sabor modernos.')) ?>" style="background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1);">
            </div>
        </div>
    </div>

    <div class="glass" style="padding: 40px; margin-bottom: 60px; border-top: 2px solid var(--accent);">
        <h3 style="letter-spacing: 2px; font-weight: 800; border-bottom: 1px solid var(--glass-border); padding-bottom: 15px; margin-bottom: 30px; color: white; display: flex; align-items: center; gap: 10px;">
            <i data-lucide="phone-call" style="color: var(--accent);"></i> DATOS DE CONTACTO CENTRAL
        </h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div class="form-group">
                <label style="color: var(--primary); font-size: 0.7rem; letter-spacing: 1px;">CORREO CORPORATIVO</label>
                <input type="email" name="config[email_contacto]" class="form-control" value="<?= htmlspecialchars(get_conf('email_contacto', 'contacto@huarique.com')) ?>" style="background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1);">
            </div>
            <div class="form-group">
                <label style="color: var(--primary); font-size: 0.7rem; letter-spacing: 1px;">TELÉFONO DE RESERVAS/DELIVERY</label>
                <input type="text" name="config[telefono_contacto]" class="form-control" value="<?= htmlspecialchars(get_conf('telefono_contacto', '+51 987 654 321')) ?>" style="background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1);">
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label style="color: var(--primary); font-size: 0.7rem; letter-spacing: 1px;">DIRECCIÓN PRINCIPAL</label>
                <input type="text" name="config[direccion_contacto]" class="form-control" value="<?= htmlspecialchars(get_conf('direccion_contacto', 'Av. Ciberespacio 404, Lima')) ?>" style="background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1);">
            </div>
        </div>
    </div>

    <button type="submit" name="save_config" class="cyber-btn" style="width: 100%; height: 60px; background: var(--primary); color: white; font-weight: 800; letter-spacing: 4px; font-size: 1.1rem; border: none; cursor: pointer; border-radius: 4px; box-shadow: 0 0 15px rgba(255, 71, 87, 0.3); margin-bottom: 60px;">
        SOBRESCRIBIR PARÁMETROS GLOBALES
    </button>
</form>

<?php include '../../includes/admin/footer.php'; ?>
