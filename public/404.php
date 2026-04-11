<?php
require_once __DIR__ . '/../config/db.php';
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - SECTOR NO ENCONTRADO</title>
    <!-- CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            background: #020202;
            color: white;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            background-image: 
                radial-gradient(circle at center, rgba(255, 71, 87, 0.05) 0%, transparent 60%);
        }
        .container-404 {
            text-align: center;
            max-width: 600px;
            padding: 40px;
            position: relative;
        }
        .glitch-wrapper {
            position: relative;
            display: inline-block;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            margin: 0;
            line-height: 1;
            color: transparent;
            -webkit-text-stroke: 2px var(--primary);
            position: relative;
            letter-spacing: -5px;
        }
        .error-code::before, .error-code::after {
            content: "404";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.8;
        }
        .error-code::before {
            color: var(--accent);
            z-index: -1;
            transform: translate(-5px, 2px);
            animation: glitch-anim 2s infinite linear alternate-reverse;
        }
        .error-code::after {
            color: transparent;
            -webkit-text-stroke: 2px rgba(0, 245, 255, 0.5);
            z-index: -2;
            transform: translate(5px, -2px);
            animation: glitch-anim 3s infinite linear alternate-reverse;
        }
        @keyframes glitch-anim {
            0% { clip-path: inset(10% 0 30% 0); transform: translate(-2px, 1px); }
            20% { clip-path: inset(80% 0 5% 0); transform: translate(2px, -1px); }
            40% { clip-path: inset(40% 0 50% 0); transform: translate(-2px, 2px); }
            60% { clip-path: inset(10% 0 80% 0); transform: translate(2px, -2px); }
            80% { clip-path: inset(60% 0 10% 0); transform: translate(-1px, 1px); }
            100% { clip-path: inset(30% 0 40% 0); transform: translate(1px, -1px); }
        }
        .message-title {
            font-size: 1.5rem;
            letter-spacing: 5px;
            margin-top: 20px;
            color: white;
            text-transform: uppercase;
        }
        .message-subtitle {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin: 20px 0 40px;
            letter-spacing: 1px;
            line-height: 1.6;
            font-family: monospace;
        }
    </style>
</head>
<body>

<div class="container-404">
    <div style="margin-bottom: 20px;">
        <i data-lucide="cpu" style="width: 50px; height: 50px; color: var(--primary);"></i>
    </div>
    
    <div class="glitch-wrapper">
        <h1 class="error-code">404</h1>
    </div>
    
    <h2 class="message-title">SECTOR_DESCONOCIDO</h2>
    <p class="message-subtitle">
        > TRACE_ERROR: La ruta especificada no existe en el kernel.<br>
        > SUGGESTION: Retornar a una coordenada segura.
    </p>
    
    <a href="<?= BASE_URL ?>index.php" class="cyber-btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 15px 40px; font-size: 0.85rem; letter-spacing: 3px; background: var(--primary); color: white; text-decoration: none; font-weight: 800; border: none; cursor: pointer; border-radius: 4px; box-shadow: 0 0 20px rgba(255, 71, 87, 0.3); transition: 0.3s;">
        <i data-lucide="corner-down-left"></i>
        VOLVER AL SISTEMA
    </a>
</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>
