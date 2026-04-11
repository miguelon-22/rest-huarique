<?php
require_once '../config/db.php';
include '../includes/header.php';
?>

<div class="container" style="padding-top: 140px; padding-bottom: 80px;">
    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 50px;">
        <!-- Left: Delivery Info -->
        <div>
            <h2 class="neon-glow" style="margin-bottom: 30px;">DAtOS DE EntreGA</h2>
            <div class="glass" style="padding: 40px;">
                <form id="checkout-form" action="procesar_pedido.php" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control" required placeholder="Tu nombre">
                        </div>
                        <div class="form-group">
                            <label>DNI / Documento</label>
                            <input type="text" name="dni" class="form-control" required placeholder="8 dígitos" pattern="\d{8}" maxlength="8" title="Debe ingresar exactamente 8 números">
                        </div>
                        <div class="form-group">
                            <label>Teléfono / WhatsApp</label>
                            <input type="tel" name="telefono" class="form-control" required placeholder="9 dígitos" pattern="\d{9}" maxlength="9" title="Debe ingresar exactamente 9 números">
                        </div>
                        <div class="form-group">
                            <label>Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" required placeholder="usuario@correo.com">
                        </div>
                    </div>

                    <h3 class="neon-glow" style="margin: 40px 0 20px; font-size: 1.2rem;">¿CÓMO QUIERES TU POLLO?</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px;">
                        <label class="glass" style="cursor: pointer; padding: 20px; text-align: center; border-radius: 10px; border: 1px solid var(--glass-border);">
                            <input type="radio" name="tipo_entrega" value="delivery" checked style="margin-bottom: 10px; accent-color: var(--primary);">
                            <div style="font-weight: bold; color: white;">DELIVERY</div>
                            <div style="font-size: 0.7rem; color: var(--accent);">A TU CASA</div>
                        </label>
                        <label class="glass" style="cursor: pointer; padding: 20px; text-align: center; border-radius: 10px; border: 1px solid var(--glass-border);">
                            <input type="radio" name="tipo_entrega" value="tienda" style="margin-bottom: 10px; accent-color: var(--primary);">
                            <div style="font-weight: bold; color: white;">RECOJO</div>
                            <div style="font-size: 0.7rem; color: var(--accent);">EN LOCAL</div>
                        </label>
                    </div>

                    <div id="delivery-fields">
                        <div class="form-group">
                            <label>Dirección de Envío</label>
                            <input type="text" id="direccion-input" name="direccion" class="form-control" required placeholder="Calle, Nro, Distrito">
                        </div>
                        <div class="form-group">
                            <label>Referencia / Info Adicional</label>
                            <textarea name="referencia" class="form-control" placeholder="Ej. Puerta azul, frente al parque..."></textarea>
                        </div>
                    </div>

                    <div id="recojo-fields" style="display: none;">
                        <div class="glass" style="padding: 20px; border-left: 5px solid var(--accent); margin-bottom: 20px;">
                            <p style="font-size: 0.8rem; margin: 0; color: #ccc;">Podrás recoger tu pedido en nuestra tienda central en 30-40 minutos.</p>
                        </div>
                    </div>

                    <h3 class="neon-glow" style="margin: 40px 0 20px; font-size: 1.2rem;">CÓDIGO DE DESCUENTO</h3>
                    <div class="glass" style="padding: 20px; display: flex; gap: 10px; margin-bottom: 30px;">
                        <input type="text" id="coupon-code" class="form-control" placeholder="INGRESA TU CUPÓN" style="text-transform: uppercase;">
                        <button type="button" id="apply-coupon" class="cyber-btn" style="padding: 0 20px; background: var(--primary); color: white; border: none; font-weight: bold; cursor: pointer;">APLICAR</button>
                    </div>
                    <div id="coupon-status" style="margin-top: -15px; margin-bottom: 20px; font-size: 0.8rem; font-weight: bold; display: flex; align-items: center;"></div>

                    <h3 class="neon-glow" style="margin: 40px 0 20px; font-size: 1.2rem;">MÉTODO DE PAGO 2.0</h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <label class="payment-option glass" style="cursor: pointer; padding: 15px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 10px;">
                            <input type="radio" name="metodo_pago" value="paypal" checked style="display: none;">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" style="height: 20px;">
                            <span style="font-size: 0.7rem; color: var(--text-secondary);">PAYPAL</span>
                        </label>
                        <label class="payment-option glass" style="cursor: pointer; padding: 15px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 10px;">
                            <input type="radio" name="metodo_pago" value="stripe" style="display: none;">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/Stripe_Logo%2C_revised_2016.svg" style="height: 20px; filter: invert(1);">
                            <span style="font-size: 0.7rem; color: var(--text-secondary);">STRIPE</span>
                        </label>
                    </div>

                    <input type="hidden" name="cart_data" id="hidden-cart-data">
                    <input type="hidden" name="total_amount" id="hidden-total-amount">
                    <input type="hidden" name="cupon_id" id="hidden-cupon-id">
                    <input type="hidden" name="monto_descuento" id="hidden-discount-amount" value="0">

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 40px; height: 60px; font-size: 1.1rem; letter-spacing: 2px;">CONFIRMAR Y PAGAR</button>
                </form>
            </div>
        </div>

        <!-- Right: Order Summary -->
        <div>
            <h2 class="neon-glow" style="margin-bottom: 30px;">TU ORDEN</h2>
            <div class="glass" style="padding: 30px; position: sticky; top: 120px;">
                <div id="checkout-items" style="max-height: 400px; overflow-y: auto; margin-bottom: 20px;"></div>
                
                <div style="border-top: 1px solid var(--glass-border); padding-top: 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: var(--text-secondary);">Subtotal Platos:</span>
                        <span id="checkout-subtotal">S/ 0.00</span>
                    </div>
                    <div id="discount-row" style="display: none; justify-content: space-between; margin-bottom: 10px; color: var(--accent);">
                        <span>Descuento Cupón:</span>
                        <span id="checkout-discount">- S/ 0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                        <span style="color: var(--text-secondary);">Delivery / Envío:</span>
                        <span id="delivery-fee" style="color: var(--accent);">GRATIS</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.5rem; font-weight: bold; border-top: 1px dashed #333; padding-top: 15px;">
                        <span>TOTAL:</span>
                        <span id="checkout-total" class="neon-glow" style="color: var(--primary);">S/ 0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-option input:checked + img + span, 
    .payment-option input:checked + img {
        filter: none !important;
    }
    .payment-option:has(input:checked) {
        border-color: var(--accent);
        background: rgba(0, 255, 136, 0.05);
        box-shadow: 0 0 15px var(--accent-glow);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Fill checkout summary from localStorage
    const saved = localStorage.getItem('huarique_cart');
    if (!saved || JSON.parse(saved).length === 0) {
        window.location.href = 'index.php';
        return;
    }
    
    const items = JSON.parse(saved);
    const container = document.getElementById('checkout-items');
    let subtotal = 0;
    let discount = 0;
    let shipping = 0;
    
    function renderSummary() {
        container.innerHTML = '';
        subtotal = 0;
        items.forEach(item => {
            subtotal += item.price * item.qty;
            container.innerHTML += `
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 0.9rem;">
                    <span style="color: var(--text-secondary);">${item.qty}x ${item.name}</span>
                    <span>S/ ${(item.price * item.qty).toFixed(2)}</span>
                </div>
            `;
        });
        
        // Delivery logic: 5 if < 90, else 0
        const isDelivery = document.querySelector('input[name="tipo_entrega"]:checked').value === 'delivery';
        shipping = (isDelivery && subtotal < 90) ? 5 : 0;
        
        const finalTotal = subtotal - discount + shipping;
        
        document.getElementById('checkout-subtotal').textContent = `S/ ${subtotal.toFixed(2)}`;
        document.getElementById('delivery-fee').textContent = shipping === 0 ? 'GRATIS' : `S/ ${shipping.toFixed(2)}`;
        document.getElementById('checkout-total').textContent = `S/ ${finalTotal.toFixed(2)}`;
        
        document.getElementById('hidden-total-amount').value = finalTotal;
        document.getElementById('hidden-discount-amount').value = discount;
        document.getElementById('hidden-cart-data').value = saved;
    }

    renderSummary();

    // Shipping Toggle logic
    const shippingRadios = document.querySelectorAll('input[name="tipo_entrega"]');
    const deliveryFields = document.getElementById('delivery-fields');
    const recojoFields = document.getElementById('recojo-fields');
    const addressInput = document.getElementById('direccion-input');

    shippingRadios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (e.target.value === 'delivery') {
                deliveryFields.style.display = 'block';
                recojoFields.style.display = 'none';
                addressInput.setAttribute('required', 'required');
            } else {
                deliveryFields.style.display = 'none';
                recojoFields.style.display = 'block';
                addressInput.removeAttribute('required');
            }
            renderSummary(); // Update shipping fee
        });
    });

    // Coupon logic
    const applyBtn = document.getElementById('apply-coupon');
    const couponInput = document.getElementById('coupon-code');
    const statusDiv = document.getElementById('coupon-status');

    applyBtn.addEventListener('click', () => {
        const code = couponInput.value.trim().toUpperCase();
        if (!code) return;

        applyBtn.innerText = 'VALIDANDO...';
        const formData = new FormData();
        formData.append('codigo', code);

        // Correct API Path from public root
        fetch('api/cupon_validate.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            applyBtn.innerText = 'APLICAR';
            if (data.success) {
                statusDiv.innerHTML = `<i data-lucide="check" style="width:14px; margin-right:5px; color:var(--accent);"></i><span style="color: var(--accent);">CUPÓN '${code}' APLICADO!</span>`;
                discount = data.tipo === 'porcentaje' ? (subtotal * (data.valor / 100)) : data.valor;
                
                document.getElementById('discount-row').style.display = 'flex';
                document.getElementById('checkout-discount').textContent = `- S/ ${discount.toFixed(2)}`;
                document.getElementById('hidden-cupon-id').value = data.id;
                
                renderSummary();
                if (window.lucide) lucide.createIcons();
            } else {
                statusDiv.innerHTML = `<span style="color: #ff4757;">${data.message}</span>`;
            }
        })
        .catch(err => {
            applyBtn.innerText = 'ERROR';
            console.error(err);
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>
