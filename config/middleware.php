<?php
/**
 * =====================================================
 * MIDDLEWARE DE AUTENTICACIÓN — Pollería Huarique
 * =====================================================
 * Archivo: config/middleware.php
 *
 * Uso:
 *   require_once __DIR__ . '/../../config/middleware.php';
 *   auth_required();           // Solo requiere login
 *   auth_required('admin');    // Solo permite rol 'admin'
 *   auth_required(['admin','empleado']); // Permite varios roles
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica que el usuario esté autenticado.
 * Opcionalmente valida el rol.
 *
 * @param string|array|null $roles  Rol o arreglo de roles permitidos.
 * @param string $redirect          URL de redirección si no está autenticado.
 */
function auth_required($roles = null, string $redirect = 'login.php'): void
{
    // 1. Verificar sesión activa
    if (empty($_SESSION['admin_logged_in'])) {
        header("Location: $redirect");
        exit;
    }

    // 2. Verificar rol si se indicó
    if ($roles !== null) {
        $allowed = is_array($roles) ? $roles : [$roles];
        if (!in_array($_SESSION['admin_role'] ?? '', $allowed, true)) {
            http_response_code(403);
            echo "<!DOCTYPE html><html lang='es'><head>
                <meta charset='UTF-8'>
                <title>Acceso Denegado — Huarique OS</title>
                <style>
                    body { background: #020202; color: #ff4757; font-family: monospace; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; flex-direction: column; gap: 20px; }
                    h1 { font-size: 1.5rem; letter-spacing: 4px; border-bottom: 2px solid #ff4757; padding-bottom: 15px; }
                    p  { font-size: 0.8rem; color: #aaa; letter-spacing: 2px; }
                    a  { color: #00f5ff; text-decoration: none; font-size: 0.75rem; letter-spacing: 2px; }
                    a:hover { text-decoration: underline; }
                </style>
            </head><body>
                <h1>⛔ ACCESO DENEGADO</h1>
                <p>> ERROR_403: Nivel de autorización insuficiente.</p>
                <p>ROL ACTUAL: <strong style='color:#ff4757'>" . strtoupper($_SESSION['admin_role'] ?? 'DESCONOCIDO') . "</strong></p>
                <p>SE REQUIERE: <strong style='color:#00f5ff'>" . strtoupper(implode(' | ', $allowed)) . "</strong></p>
                <a href='javascript:history.back()'>← VOLVER ATRÁS</a>
            </body></html>";
            exit;
        }
    }
}

/**
 * Verifica si el usuario tiene el rol dado (sin detener ejecución).
 *
 * @param string $role
 * @return bool
 */
function has_role(string $role): bool
{
    return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === $role;
}

/**
 * Verifica si el usuario está autenticado (sin detener ejecución).
 *
 * @return bool
 */
function is_logged_in(): bool
{
    return !empty($_SESSION['admin_logged_in']);
}

/**
 * Cierra la sesión del admin de forma segura.
 *
 * @param string $redirect
 */
function auth_logout(string $redirect = 'login.php'): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
    header("Location: $redirect");
    exit;
}
