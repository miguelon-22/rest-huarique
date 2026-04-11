<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = $_POST['new_password'] ?? '';
    $conf_pass = $_POST['confirm_password'] ?? '';

    if (empty($new_pass) || strlen($new_pass) < 6) {
        $error = 'LA CONTRASEÑA DEBE TENER AL MENOS 6 CARACTERES.';
    } elseif ($new_pass !== $conf_pass) {
        $error = 'LAS CONTRASEÑAS NO COINCIDEN.';
    } elseif ($new_pass === $_SESSION['admin_email']) {
        $error = 'LA NUEVA CONTRASEÑA NO PUEDE SER IGUAL AL CORREO.';
    } else {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        db_execute("UPDATE public.usuarios SET password = ? WHERE id = ?", [$hashed, $_SESSION['admin_id']]);
        
        unset($_SESSION['must_change_password']);
        $success = 'CONTRASEÑA ACTUALIZADA CON ÉXITO. REDIRIGIENDO...';
        header("Refresh: 2; url=index.php");
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SEGURIDAD - Pollería Huarique</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #020202; color: white; font-family: monospace; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .box { border: 1px solid var(--primary); padding: 50px; background: rgba(10,10,10,0.9); max-width: 400px; width: 100%; box-shadow: 0 0 30px var(--primary-glow); }
        .form-control { width: 100%; padding: 15px; margin: 10px 0; background: #111; border: 1px solid #333; color: white; }
    </style>
</head>
<body>
    <div class="box">
        <h2 style="color: var(--primary); letter-spacing: 2px;">DETECTADA CLAVE INICIAL</h2>
        <p style="font-size: 0.8rem; color: #888; margin-bottom: 30px;">POR SEGURIDAD, DEBES ACTUALIZAR TU CONTRASEÑA ANTES DE ACCEDER AL SISTEMA.</p>

        <?php if ($error): ?>
            <div style="color: #ff4757; margin-bottom: 20px;">> <?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div style="color: var(--accent); margin-bottom: 20px;">> <?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <label style="font-size: 0.7rem; color: var(--accent);">NUEVA CONTRASEÑA</label>
            <input type="password" name="new_password" class="form-control" required placeholder="******">
            
            <label style="font-size: 0.7rem; color: var(--accent);">CONFIRMAR CONTRASEÑA</label>
            <input type="password" name="confirm_password" class="form-control" required placeholder="******">
            
            <button type="submit" class="cyber-btn" style="width: 100%; margin-top: 20px; background: var(--primary); color: white; border: none; padding: 15px; cursor: pointer;">
                ACTUALIZAR Y ENTRAR
            </button>
        </form>
    </div>
</body>
</html>
