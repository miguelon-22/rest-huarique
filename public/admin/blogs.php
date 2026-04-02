<?php
require_once '../../config/db.php';
include '../../includes/admin/header.php';

// Protegido por middleware via header.php (auth_required)
$msg = '';

if (isset($_POST['save_blog'])) {
    $nombre = $_POST['nombre'] ?? '';
    $contenido = $_POST['contenido'] ?? '';
    $imagen_db = '';

    if (isset($_FILES['imagen_file']) && $_FILES['imagen_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../public/uploads/';
        $file_extension = pathinfo($_FILES['imagen_file']['name'], PATHINFO_EXTENSION);
        $file_name = 'blog_' . uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['imagen_file']['tmp_name'], $upload_path)) {
            $imagen_db = 'uploads/' . $file_name;
        }
    } else {
        $imagen_db = 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&q=80&w=600';
    }

    if (!empty($nombre)) {
        db_execute("INSERT INTO public.blogs (nombre, contenido, imagen) VALUES (?, ?, ?)", 
                   [$nombre, $contenido, $imagen_db]);
        $msg = 'Publicación registrada con éxito.';
    }
}

if (isset($_GET['delete'])) {
    db_execute("DELETE FROM public.blogs WHERE id = ?", [$_GET['delete']]);
    $msg = 'Publicación eliminada del sistema.';
}

$blogs = db_get_all("SELECT * FROM public.blogs ORDER BY creado_en DESC");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px;">
    <h2 style="letter-spacing: 5px; font-weight: 900; margin: 0; color: white;">BLOG / NOTICIAS</h2>
    <button onclick="document.getElementById('modal-add-blog').style.display='flex'" class="cyber-btn" style="padding: 15px 35px; background: var(--primary); color: white; border: none; cursor: pointer; clip-path: polygon(10% 0, 100% 0, 90% 100%, 0% 100%);">+ NUEVA PUBLICACIÓN</button>
</div>

<?php if ($msg): ?>
    <div class="alert-cyber">
        <i data-lucide="terminal"></i>
        <span>> STATUS_SYS: <?= strtoupper($msg) ?></span>
    </div>
<?php endif; ?>

<div class="glass" style="padding: 40px; margin-bottom: 60px; border-top: 2px solid var(--primary);">
    <h3 style="letter-spacing: 3px; font-weight: 800; border-bottom: 1px solid var(--glass-border); padding-bottom: 15px; margin-bottom: 30px;">REGISTROS_PUBLICADOS</h3>
    <div style="overflow-x: auto;">
        <table class="table-admin" style="min-width: 900px;">
            <thead>
                <tr>
                    <th>PREVISUALIZACIÓN</th>
                    <th>TÍTULO_ARTÍCULO</th>
                    <th>CONTENIDO</th>
                    <th>FECHA_PUBLICACIÓN</th>
                    <th>GESTIÓN</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($blogs as $b): 
                    $img_path = $b['imagen'];
                    $display_img = (strpos($img_path, 'http') === 0) ? $img_path : '../' . $img_path;
                ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($display_img) ?>" alt="" style="width: 80px; height: 50px; border: 1px solid var(--accent); padding: 2px; border-radius: 4px; object-fit: cover;"></td>
                        <td style="font-weight: 800; color: white;"><?= strtoupper(htmlspecialchars($b['nombre'])) ?></td>
                        <td>
                            <div style="font-size: 0.7rem; color: var(--text-secondary); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($b['contenido']) ?>
                            </div>
                        </td>
                        <td style="color: var(--accent); font-size: 0.7rem;"><?= date('d/m/Y H:i', strtotime($b['creado_en'])) ?></td>
                        <td>
                            <a href="?delete=<?= $b['id'] ?>" onclick="return confirm('¿CONFIRMAR_ELIMINACIÓN_ARTÍCULO?')" class="btn-delete" style="color: var(--primary); border: 1px solid var(--primary); padding: 10px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s;">
                                <i data-lucide="trash-2" style="width: 20px;"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($blogs)): ?>
                <tr><td colspan="5" style="text-align:center; color: var(--text-secondary);">NO_EXISTEN_ARTÍCULOS</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modal-add-blog" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div class="glass" style="padding: 35px 30px; border-top: 3px solid var(--primary); box-shadow: 0 0 40px rgba(255, 71, 87, 0.15); max-width: 550px; width: 90%; position: relative; border-radius: 8px;">
        <button type="button" onclick="document.getElementById('modal-add-blog').style.display='none'" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: var(--text-secondary); font-size: 1.5rem; cursor: pointer; transition: 0.3s;">&times;</button>
        
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px;">
            <i data-lucide="file-text" style="color: var(--primary); width: 20px;"></i>
            <h3 style="letter-spacing: 2px; font-weight: 800; color: white; margin: 0; font-size: 1rem;">NUEVA PUBLICACIÓN</h3>
        </div>

        <form method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr; gap: 18px;">
            <div class="form-group">
                <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px;">TÍTULO_ARTÍCULO</label>
                <input type="text" name="nombre" class="form-control" required placeholder="Titular" style="height: 42px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);">
            </div>
            
            <div class="form-group">
                <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px;">CONTENIDO (CUERPO)</label>
                <textarea name="contenido" required class="form-control" style="height: 120px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);" placeholder="Escribe el artículo aquí..."></textarea>
            </div>
            
            <div class="form-group">
                <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px;">IMAGEN PORTADA</label>
                <input type="file" name="imagen_file" class="form-control" accept="image/*" style="padding-top: 8px; height: 42px; font-size: 0.75rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);">
            </div>
            
            <div style="margin-top: 10px;">
                <button type="submit" name="save_blog" class="cyber-btn" style="width: 100%; height: 45px; background: var(--primary); color: white; font-weight: 800; letter-spacing: 2px; font-size: 0.85rem; border: none; cursor: pointer; border-radius: 4px; box-shadow: 0 0 15px rgba(255, 71, 87, 0.3);">
                    PUBLICAR EN SISTEMA
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .btn-delete:hover { background: var(--primary); color: white !important; box-shadow: 0 0 20px var(--primary-glow); transform: scale(1.1); }
</style>

<?php include '../../includes/admin/footer.php'; ?>
