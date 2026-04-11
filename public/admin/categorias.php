<?php
require_once '../../config/db.php';
include '../../includes/admin/header.php';

$msg = '';

// Handle Add Category
if (isset($_POST['add_category'])) {
    $nombre = $_POST['nombre'] ?? '';
    if (!empty($nombre)) {
        db_execute("INSERT INTO public.categorias (nombre) VALUES (?)", [$nombre]);
        $msg = 'Categoría añadida con éxito.';
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        db_execute("DELETE FROM public.categorias WHERE id = ?", [$id]);
        $msg = 'Categoría eliminada.';
    } catch (Exception $e) {
        $msg = 'No se puede eliminar: tiene platos vinculados.';
    }
}

$categories = db_get_all("SELECT * FROM public.categorias ORDER BY id DESC");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
    <h2>Gestionar Categorías</h2>
</div>

<?php if ($msg): ?>
    <div style="background: rgba(46, 213, 115, 0.1); color: #2ed573; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?= $msg ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 40px;">
    <!-- Form Side -->
    <div class="glass" style="padding: 30px; height: fit-content;">
        <h3>Nueva Categoría</h3>
        <form method="POST" style="margin-top: 20px;">
            <div class="form-group">
                <label>Nombre de la Categoría</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ej. Pollo a la Brasa" required>
            </div>
            <button type="submit" name="add_category" class="btn btn-primary" style="width: 100%;">Añadir Categoría</button>
        </form>
    </div>

    <!-- List Side -->
    <div class="glass" style="padding: 30px;">
        <h3>Categorías Existentes</h3>
        <table class="table-admin">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><?= htmlspecialchars($cat['nombre']) ?></td>
                        <td>
                            <a href="?delete=<?= $cat['id'] ?>" onclick="return confirm('¿Estás seguro?')" style="color: var(--primary);"><i data-lucide="trash-2"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/admin/footer.php'; ?>
