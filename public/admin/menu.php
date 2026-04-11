<?php
require_once '../../config/db.php';
include '../../includes/admin/header.php';

$msg = '';

// Handle Add/Edit Menu
if (isset($_POST['save_menu'])) {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $categoria_id = $_POST['categoria_id'] ?? 0;
    $imagen_db = '';

    // Handle File Upload
    if (isset($_FILES['imagen_file']) && $_FILES['imagen_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../public/uploads/';
        $file_extension = pathinfo($_FILES['imagen_file']['name'], PATHINFO_EXTENSION);
        $file_name = 'menu_' . uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['imagen_file']['tmp_name'], $upload_path)) {
            $imagen_db = 'uploads/' . $file_name;
        }
    } else {
        $imagen_db = $_POST['imagen_current'] ?? 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?auto=format&fit=crop&q=80&w=400';
    }

    if (!empty($nombre)) {
        db_execute("INSERT INTO public.menus (nombre, descripcion, precio, imagen, categoria_id) VALUES (?, ?, ?, ?, ?)", 
                   [$nombre, $descripcion, $precio, $imagen_db, $categoria_id]);
        $msg = 'Plato añadido con éxito al sistema.';
    }
}

// Handle Delete Menu
if (isset($_GET['delete'])) {
    db_execute("DELETE FROM public.menus WHERE id = ?", [$_GET['delete']]);
    $msg = 'Plato eliminado de la carta.';
}

$categories = db_get_all("SELECT * FROM public.categorias ORDER BY nombre ASC");
$menus = db_get_all("SELECT m.*, c.nombre as categoria_nombre FROM public.menus m JOIN public.categorias c ON m.categoria_id = c.id ORDER BY c.nombre, m.nombre");
?>

<!-- Menu Management UI 3.0 -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px;">
    <h2 style="letter-spacing: 5px; font-weight: 900; margin: 0; color: white;">GESTIÓN DE PLATOS / CARTA</h2>
    <button onclick="document.getElementById('modal-add-menu').style.display='flex'" class="cyber-btn" style="padding: 15px 35px; background: var(--primary); color: white; border: none; cursor: pointer; clip-path: polygon(10% 0, 100% 0, 90% 100%, 0% 100%);">+ AGREGAR PLATO</button>
</div>

<?php if ($msg): ?>
    <div class="alert-cyber">
        <i data-lucide="terminal"></i>
        <span>> STATUS_SYS: <?= strtoupper($msg) ?></span>
    </div>
<?php endif; ?>

<div class="glass" style="padding: 40px; margin-bottom: 60px; border-top: 2px solid var(--primary);">
    <h3 style="letter-spacing: 3px; font-weight: 800; border-bottom: 1px solid var(--glass-border); padding-bottom: 15px; margin-bottom: 30px;">REGISTRO_ACTIVO_PRODUCTOS</h3>
    <table class="table-admin">
        <thead>
            <tr>
                <th>PREVISUALIZACIÓN</th>
                <th>IDENTIFICADOR_PROTOCOLO</th>
                <th>ASIGNACIÓN_NÚCLEO</th>
                <th>VALOR_S/</th>
                <th>GESTIÓN</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menus as $m): 
                $img_path = $m['imagen'];
                $display_img = (strpos($img_path, 'http') === 0) ? $img_path : '../' . $img_path;
            ?>
                <tr>
                    <td><img src="<?= htmlspecialchars($display_img) ?>" alt="" style="width: 60px; height: 60px; border: 1px solid var(--accent); padding: 2px; border-radius: 4px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/60'"></td>
                    <td style="font-weight: 800; color: white;"><?= strtoupper(htmlspecialchars($m['nombre'])) ?></td>
                    <td style="color: var(--text-secondary);"><?= strtoupper(htmlspecialchars($m['categoria_nombre'])) ?></td>
                    <td style="color: var(--accent); font-weight: 900;">S/ <?= number_format($m['precio'], 2) ?></td>
                    <td>
                        <a href="?delete=<?= $m['id'] ?>" onclick="return confirm('¿CONFIRMAR_ELIMINACIÓN_PROTOCOLO?')" class="btn-delete" style="color: var(--primary); border: 1px solid var(--primary); padding: 10px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s;">
                            <i data-lucide="trash-2" style="width: 20px;"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="modal-add-menu" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
    <div id="add-menu-form" class="glass" style="padding: 35px 30px; border-top: 3px solid var(--primary); box-shadow: 0 0 40px rgba(255, 71, 87, 0.15); max-width: 450px; width: 90%; position: relative; border-radius: 8px;">
        <button type="button" onclick="document.getElementById('modal-add-menu').style.display='none'" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: var(--text-secondary); font-size: 1.5rem; cursor: pointer; transition: 0.3s; hover: color: white;">&times;</button>
        
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px;">
            <i data-lucide="cpu" style="color: var(--primary); width: 20px;"></i>
            <h3 style="letter-spacing: 2px; font-weight: 800; color: white; margin: 0; font-size: 1rem;">NUEVO PLATO</h3>
        </div>

        <form method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr; gap: 18px;">
            <div class="form-group">
                <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px;">NOMBRE_PRODUCTO</label>
                <input type="text" name="nombre" class="form-control" required placeholder="Identificador" style="height: 42px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px;">CATEGORÍA</label>
                    <select name="categoria_id" class="form-control" style="background: rgba(0,0,0,0.6); height: 42px; font-size: 0.85rem; border: 1px solid rgba(255,255,255,0.1);" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= strtoupper(htmlspecialchars($cat['nombre'])) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px;">PRECIO (S/)</label>
                    <input type="number" step="0.01" name="precio" class="form-control" required placeholder="0.00" style="height: 42px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);">
                </div>
            </div>

            <div class="form-group">
                <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px;">DESCRIPCIÓN</label>
                <textarea name="descripcion" class="form-control" style="height: 70px; font-size: 0.85rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);" placeholder="Detalles del plato..."></textarea>
            </div>
            
            <div class="form-group">
                <label style="font-size: 0.65rem; color: var(--accent); letter-spacing: 1px;">IMAGEN</label>
                <input type="file" name="imagen_file" class="form-control" accept="image/*" style="padding-top: 8px; height: 42px; font-size: 0.75rem; background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);">
            </div>
            
            <div style="margin-top: 10px;">
                <button type="submit" name="save_menu" class="cyber-btn" style="width: 100%; height: 45px; background: var(--primary); color: white; font-weight: 800; letter-spacing: 2px; font-size: 0.85rem; border: none; cursor: pointer; border-radius: 4px; box-shadow: 0 0 15px rgba(255, 71, 87, 0.3);">
                    GUARDAR EN SISTEMA
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .btn-delete:hover { background: var(--primary); color: white !important; box-shadow: 0 0 20px var(--primary-glow); transform: scale(1.1); }
</style>

<?php include '../../includes/admin/footer.php'; ?>

<?php include '../../includes/admin/footer.php'; ?>
