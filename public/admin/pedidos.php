<?php
require_once '../../config/db.php';
include '../../includes/admin/header.php';

$msg = '';

// Handle Status Update
if (isset($_GET['status']) && isset($_GET['id'])) {
    $status = $_GET['status'];
    $id = intval($_GET['id']);
    db_execute("UPDATE public.pedidos SET estado = ? WHERE id = ?", [$status, $id]);
    $msg = 'Estado del pedido actualizado correctamente.';
}

$pedidos = db_get_all("SELECT p.*, c.nombre as cliente_nombre, c.dni as cliente_dni, c.correo as cliente_correo, c.telefono as cliente_telefono, c.direccion as cliente_direccion FROM public.pedidos p LEFT JOIN public.clientes c ON p.cliente_id = c.id ORDER BY p.creado_en DESC");

// Fetch all details to avoid multiple queries
$all_detalles = db_get_all("SELECT * FROM public.detalle_pedidos");
$detalles_map = [];
foreach ($all_detalles as $det) {
    $detalles_map[$det['pedido_id']][] = $det;
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
    <h2 style="letter-spacing: 2px; font-weight: 900;">PEDIDOS DE CLIENTES</h2>
    <div style="font-size:0.7rem; color:var(--text-secondary); font-family:monospace;">
        TOTAL: <span style="color:var(--accent); font-weight:bold;"><?= count($pedidos) ?></span> REGISTRO(S)
    </div>
</div>

<?php if ($msg): ?>
    <div class="alert-cyber" style="margin-bottom:25px;">
        <i data-lucide="check-circle"></i>
        <span>&gt; STATUS_SYS: <?= strtoupper($msg) ?></span>
    </div>
<?php endif; ?>

<div class="glass" style="padding: 30px; overflow: hidden;">
    <h3 style="letter-spacing: 2px; margin-bottom: 20px;">LISTADO DE PEDIDOS</h3>
    <!-- Wrapper with full overflow scroll -->
    <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <table class="table-admin" style="min-width: 1000px; width: 100%;">
            <thead>
                <tr>
                    <th>NRO PEDIDO</th>
                    <th>CLIENTE</th>
                    <th>TIPO / DIRECCIÓN</th>
                    <th>TOTAL</th>
                    <th>ESTADO PEDIDO</th>
                    <th>FECHA</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pedidos)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 40px;">
                            <i data-lucide="inbox" style="display:block; margin: 0 auto 10px; width:32px; height:32px;"></i>
                            No hay pedidos registrados en el sistema.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pedidos as $p): 
                        $pedido_id = $p['id'];
                        $items = $detalles_map[$pedido_id] ?? [];
                    ?>
                        <tr>
                            <td style="font-weight: bold; color: var(--accent); white-space: nowrap;">
                                #<?= htmlspecialchars($p['numero_pedido']) ?>
                            </td>
                            <td style="white-space: nowrap;">
                                <div style="font-weight:700;"><?= htmlspecialchars($p['cliente_nombre'] ?? 'Invitado') ?></div>
                                <div style="font-size: 0.65rem; color: var(--accent);">DNI: <?= htmlspecialchars($p['cliente_dni'] ?? 'N/A') ?></div>
                            </td>
                            <td style="max-width: 280px;">
                                <span style="font-size: 0.7rem; color: #ccc; font-weight:700;"><?= strtoupper($p['tipo_pedido']) ?></span>
                                <div style="font-size: 0.65rem; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;" title="<?= htmlspecialchars($p['informacion_adicional'] ?? '') ?>">
                                    <?= htmlspecialchars($p['informacion_adicional'] ?? '—') ?>
                                </div>
                            </td>
                            <td style="font-weight: bold; white-space: nowrap;">
                                S/ <?= number_format($p['precio_total'], 2) ?>
                            </td>
                            <td style="white-space: nowrap;">
                                <?php 
                                    $status_class = 'tag-warning';
                                    if ($p['estado'] === 'completado') $status_class = 'tag-success';
                                    if ($p['estado'] === 'cancelado')  $status_class = 'tag-error';
                                ?>
                                <span class="tag <?= $status_class ?>"><?= strtoupper($p['estado'] ?? 'pendiente') ?></span>
                            </td>
                            <td style="font-size:0.65rem; color:var(--text-secondary); white-space: nowrap;">
                                <?= isset($p['creado_en']) ? date('d/m/Y H:i', strtotime($p['creado_en'])) : '—' ?>
                            </td>
                            <td style="white-space: nowrap;">
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <!-- Eye icon to view details -->
                                    <button onclick='viewOrder(<?= json_encode($p) ?>, <?= json_encode($items) ?>)'
                                            title="Ver Detalle"
                                            style="color: var(--accent); cursor:pointer; display:inline-flex; align-items:center; padding:6px; border:1px solid rgba(0,245,255,0.3); border-radius:4px; background:transparent; transition:0.3s;"
                                            onmouseover="this.style.background='rgba(0,245,255,0.1)'"
                                            onmouseout="this.style.background='transparent'">
                                        <i data-lucide="eye" style="width:16px;height:16px;"></i>
                                    </button>

                                    <a href="?status=completado&id=<?= $p['id'] ?>" 
                                       title="Marcar Completado"
                                       onclick="return confirm('¿Marcar pedido como COMPLETADO?')"
                                       style="color: #2ed573; display:inline-flex; align-items:center; padding:6px; border:1px solid rgba(46,213,115,0.3); border-radius:4px; transition:0.3s;"
                                       onmouseover="this.style.background='rgba(46,213,115,0.15)'"
                                       onmouseout="this.style.background='transparent'">
                                        <i data-lucide="check-square" style="width:16px;height:16px;"></i>
                                    </a>
                                    <a href="?status=cancelado&id=<?= $p['id'] ?>" 
                                       title="Marcar Cancelado"
                                       onclick="return confirm('¿Marcar pedido como CANCELADO?')"
                                       style="color: var(--primary); display:inline-flex; align-items:center; padding:6px; border:1px solid rgba(255,71,87,0.3); border-radius:4px; transition:0.3s;"
                                       onmouseover="this.style.background='rgba(255,71,87,0.15)'"
                                       onmouseout="this.style.background='transparent'">
                                        <i data-lucide="x-square" style="width:16px;height:16px;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Pedido Detalle -->
<div id="modalOrder" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(10px);">
    <div class="glass" style="width:90%; max-width:700px; max-height:90vh; overflow-y:auto; padding:40px; position:relative; border-top:3px solid var(--accent);">
        <button onclick="document.getElementById('modalOrder').style.display='none'" style="position:fixed; top:20px; right:20px; background:none; border:none; color:white; font-size:2rem; cursor:pointer; font-family:monospace;">&times;</button>
        
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:30px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:20px;">
            <div>
                <h2 id="modalTitle" style="color:var(--accent); letter-spacing:3px; margin:0;">PEDIDO #----</h2>
                <div id="modalDate" style="font-size:0.7rem; color:var(--text-secondary); margin-top:5px;">FECHA: --/--/---- --:--</div>
            </div>
            <div id="modalStatus" class="tag tag-warning">PENDIENTE</div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:30px; margin-bottom:30px;">
            <div>
                <h4 style="font-size:0.75rem; color:var(--accent); letter-spacing:2px; margin-bottom:10px;">DATOS DEL CLIENTE</h4>
                <div id="modalClientName" style="font-weight:bold; font-size:1.1rem; margin-bottom:5px;">---</div>
                <div id="modalClientInfo" style="font-size:0.8rem; color:var(--text-secondary); line-height:1.6;">
                    DNI: --<br>
                    TEL: --<br>
                    CORREO: --
                </div>
            </div>
            <div>
                <h4 style="font-size:0.75rem; color:var(--accent); letter-spacing:2px; margin-bottom:10px;">ENTREGA Y PAGO</h4>
                <div id="modalDeliveryType" style="font-weight:bold; margin-bottom:5px;">---</div>
                <div id="modalDeliveryInfo" style="font-size:0.8rem; color:var(--text-secondary); line-height:1.6;">
                    Dirección/Info: --- <br>
                    Método: --- <br>
                    Pago Online: ---
                </div>
            </div>
        </div>

        <h4 style="font-size:0.75rem; color:var(--accent); letter-spacing:2px; margin-bottom:15px; border-top:1px solid rgba(255,255,255,0.1); padding-top:20px;">CONTENIDO DEL PEDIDO</h4>
        <div style="background:rgba(0,0,0,0.3); border-radius:4px; overflow:hidden;">
            <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
                <thead style="background:rgba(255,255,255,0.05);">
                    <tr>
                        <th style="padding:12px; text-align:left; color:rgba(255,255,255,0.5);">PRODUCTO</th>
                        <th style="padding:12px; text-align:center; color:rgba(255,255,255,0.5);">CANT</th>
                        <th style="padding:12px; text-align:right; color:rgba(255,255,255,0.5);">SUBTOTAL</th>
                    </tr>
                </thead>
                <tbody id="modalItemsBody">
                    <!-- Items go here -->
                </tbody>
                <tfoot style="background:rgba(255,255,255,0.05); font-weight:bold;">
                    <tr>
                        <td colspan="2" style="padding:15px; text-align:right;">DESCUENTO:</td>
                        <td id="modalDiscount" style="padding:15px; text-align:right; color:var(--primary);">- S/ 0.00</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:15px; text-align:right;">TOTAL A PAGAR:</td>
                        <td id="modalTotal" style="padding:15px; text-align:right; color:var(--accent); font-size:1.3rem;">S/ 0.00</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
function viewOrder(p, items) {
    document.getElementById('modalTitle').innerText = 'PEDIDO #' + p.numero_pedido;
    document.getElementById('modalDate').innerText = 'FECHA: ' + (p.creado_en ? p.creado_en : '—');
    
    // Status
    const statusEl = document.getElementById('modalStatus');
    statusEl.innerText = p.estado.toUpperCase();
    statusEl.className = 'tag ' + (p.estado === 'completado' ? 'tag-success' : (p.estado === 'cancelado' ? 'tag-error' : 'tag-warning'));

    // Client
    document.getElementById('modalClientName').innerText = p.cliente_nombre || 'Invitado';
    document.getElementById('modalClientInfo').innerHTML = 
        'DNI: ' + (p.cliente_dni || '—') + '<br>' +
        'TEL: ' + (p.cliente_telefono || '—') + '<br>' +
        'CORREO: ' + (p.cliente_correo || '—');

    // Delivery
    document.getElementById('modalDeliveryType').innerText = p.tipo_pedido.toUpperCase();
    document.getElementById('modalDeliveryInfo').innerHTML = 
        'Info: ' + (p.informacion_adicional || '—') + '<br>' +
        'Método: ' + (p.metodo_pago || '—') + '<br>' +
        'Pago Online: ' + (p.estado_pago_online === 'pagado' ? '<span style="color:#00ff88">PAGADO</span>' : '<span style="color:#ffcc00">PENDIENTE</span>');

    // Items
    const itemsBody = document.getElementById('modalItemsBody');
    itemsBody.innerHTML = '';
    items.forEach(item => {
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid rgba(255,255,255,0.05)';
        tr.innerHTML = `
            <td style="padding:12px; font-weight:bold;">${item.nombre_menu}</td>
            <td style="padding:12px; text-align:center;">${item.cantidad}</td>
            <td style="padding:12px; text-align:right;">S/ ${parseFloat(item.subtotal).toFixed(2)}</td>
        `;
        itemsBody.appendChild(tr);
    });

    document.getElementById('modalDiscount').innerText = '- S/ ' + parseFloat(p.monto_descuento || 0).toFixed(2);
    document.getElementById('modalTotal').innerText = 'S/ ' + parseFloat(p.precio_total).toFixed(2);

    document.getElementById('modalOrder').style.display = 'flex';
}
</script>

<style>
    .tag-warning { background: rgba(255,165,2,0.1); color: #ffa502; border: 1px solid rgba(255,165,2,0.4); }
    .tag-error   { background: rgba(255,71,87,0.1);  color: var(--primary); border: 1px solid rgba(255,71,87,0.4); }
    /* Scrollbar for the table wrapper */
    div[style*="overflow-x: auto"]::-webkit-scrollbar { height: 4px; }
    div[style*="overflow-x: auto"]::-webkit-scrollbar-track { background: rgba(255,255,255,0.03); }
    div[style*="overflow-x: auto"]::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 2px; }
</style>

<?php include '../../includes/admin/footer.php'; ?>

