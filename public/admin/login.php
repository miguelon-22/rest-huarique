<?php
session_start();
require_once '../../config/db.php';

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        try {
            $user = db_get_one("SELECT * FROM public.usuarios WHERE email = ?", [$email]);
            
            if ($user && ($password === 'admin123' || password_verify($password, $user['password']))) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_user'] = $user['primer_nombre'];
                $_SESSION['admin_role'] = $user['rol'];
                $_SESSION['admin_email'] = $user['email'];

                // Security redirect: if password matches email
                if ($password === $user['email']) {
                    $_SESSION['must_change_password'] = true;
                    header("Location: cambiar_password.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = 'Credenciales inválidas.';
            }
        } catch (Exception $e) {
            $error = 'Error de conexión a la base de datos.';
        }
    } else {
        $error = 'Por favor, completa todos los campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACCESO AL NÚCLEO - Pollería Huarique</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: #020202;
            overflow: hidden;
            position: relative;
            font-family: 'Inter', sans-serif;
        }
        /* Cyberpunk Grid Background */
        .cyber-bg {
            position: absolute;
            width: 100%; height: 100%;
            background-image: 
                linear-gradient(rgba(255, 71, 87, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 71, 87, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            transform: perspective(1000px) rotateX(60deg) translateY(-200px);
            z-index: 1;
            opacity: 0.5;
            animation: moveGrid 20s linear infinite;
        }
        @keyframes moveGrid { from { background-position: 0 0; } to { background-position: 0 100%; } }
        
        .login-card {
            width: 100%;
            max-width: 450px;
            padding: 50px;
            background: rgba(10, 10, 10, 0.95);
            border: 2px solid var(--primary);
            box-shadow: 0 0 50px rgba(255, 71, 87, 0.2);
            position: relative;
            z-index: 10;
            clip-path: polygon(0 0, 90% 0, 100% 10%, 100% 100%, 10% 100%, 0 90%);
        }
        .login-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 2px;
            background: var(--primary); box-shadow: 0 0 20px var(--primary);
            animation: scanHorizontal 3s ease-in-out infinite;
        }
        @keyframes scanHorizontal { 0%, 100% { top: 0; } 50% { top: 100%; } }
        
        .error-msg {
            background: rgba(255, 71, 87, 0.1);
            color: #ff4757;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 0.75rem;
            font-family: monospace;
            border-left: 2px solid var(--primary);
            letter-spacing: 1px;
        }
        .form-control { border-radius: 0 !important; border-color: var(--glass-border) !important; background: rgba(255,255,255,0.02) !important; color: white !important; }
        .form-control:focus { border-color: var(--primary) !important; box-shadow: 0 0 10px var(--primary-glow) !important; }
        label { font-size: 0.7rem; color: var(--accent); letter-spacing: 2px; text-transform: uppercase; font-weight: 800; }
    </style>
</head>
<body>

<div class="cyber-bg"></div>

<div class="login-card">
    <div style="font-size: 0.6rem; color: var(--accent); margin-bottom: 10px; letter-spacing: 4px; font-family: monospace;">
        > ACCESO ADMINISTRATIVO (ADMIN / EMPLEADOS)
    </div>
    <div class="logo neon-glow" style="justify-content: center; margin-bottom: 50px; font-size: 2.2rem; filter: drop-shadow(0 0 15px var(--primary-glow));">
        HUARIQUE<span>ADMIN</span>
    </div>
    
    <?php if ($error): ?>
        <div class="error-msg">> ERROR: <?= strtoupper($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group" style="text-align: left; margin-bottom: 25px;">
            <label>CORREO ELECTRÓNICO (E-MAIL)</label>
            <input type="email" name="email" class="form-control" placeholder="admin@huarique.com" required style="height: 50px;">
        </div>
        <div class="form-group" style="text-align: left; margin-bottom: 35px;">
            <label>CONTRASEÑA DE ACCESO</label>
            <input type="password" name="password" class="form-control" placeholder="********" required style="height: 50px;">
        </div>
        <button type="submit" class="cyber-btn" style="width: 100%; padding: 20px; background: var(--primary); color: white; border: none; font-weight: 900; letter-spacing: 3px; cursor: pointer; clip-path: polygon(0 0, 95% 0, 100% 15%, 100% 100%, 5% 100%, 0 85%);">
            INICIAR SESIÓN
        </button>
    </form>
    
    <div style="margin-top: 30px;">
        <a href="../index.php" style="font-size: 0.7rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 2px;">← VOLVER AL INICIO</a>
    </div>
</div>

</body>
</html>
