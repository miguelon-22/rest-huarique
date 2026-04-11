// Futuristic Shopping Cart System - Huarique REST
const Cart = {
    items: [],
    
    init() {
        console.log("Cart System: Initializing...");
        const saved = localStorage.getItem('huarique_cart');
        if (saved) {
            this.items = JSON.parse(saved);
            this.updateCounter();
            this.render();
        }
        
        // Listen for Add to Cart buttons
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.add-to-cart');
            if (btn) {
                const product = {
                    id: btn.dataset.id,
                    name: btn.dataset.name,
                    price: parseFloat(btn.dataset.price),
                    image: btn.dataset.image,
                    qty: 1
                };
                this.add(product);
                this.showDrawer();
            }
        });
    },
    
    add(product) {
        const existing = this.items.find(i => i.id === product.id);
        if (existing) {
            existing.qty++;
        } else {
            this.items.push(product);
        }
        this.save();
        this.updateCounter();
        this.render();
        this.notify(`+1 ${product.name}`);
    },
    
    remove(id) {
        this.items = this.items.filter(i => i.id !== id);
        this.save();
        this.updateCounter();
        this.render();
    },
    
    save() {
        localStorage.setItem('huarique_cart', JSON.stringify(this.items));
    },
    
    updateCounter() {
        const totalQty = this.items.reduce((sum, item) => sum + item.qty, 0);
        const counters = document.querySelectorAll('.cart-counter');
        counters.forEach(c => {
            c.textContent = totalQty;
            c.style.display = totalQty > 0 ? 'flex' : 'none';
        });
    },
    
    render() {
        const container = document.getElementById('cart-items-list');
        if (!container) return;
        
        if (this.items.length === 0) {
            container.innerHTML = '<p style="text-align:center; color: var(--text-secondary); padding: 40px; opacity: 0.5;">SISTEMA_VACÍO: SIN_DATOS_PEDIDO</p>';
            document.getElementById('cart-total').textContent = 'S/ 0.00';
            return;
        }
        
        let html = '';
        let total = 0;
        
        this.items.forEach(item => {
            total += item.price * item.qty;
            html += `
                <div class="cart-item" style="display: flex; gap: 15px; padding: 15px; margin-bottom: 15px; align-items: center; background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border); border-radius: 8px;">
                    <img src="${item.image}" style="width: 60px; height: 60px; border-radius: 4px; object-fit: cover; border: 1px solid var(--accent);">
                    <div style="flex: 1;">
                        <div style="font-size: 0.85rem; font-weight: 800; color: white;">${item.name}</div>
                        <div style="font-size: 0.75rem; color: var(--accent);">ID_PROT: ${item.id}</div>
                        <div style="font-size: 0.8rem; color: var(--primary); font-weight: bold;">S/ ${item.price.toFixed(2)} x ${item.qty}</div>
                    </div>
                    <button onclick="Cart.remove('${item.id}')" style="background: none; border: none; color: #ff4757; cursor: pointer;">
                        <i data-lucide="trash-2" style="width: 18px;"></i>
                    </button>
                </div>
            `;
        });
        
        container.innerHTML = html;
        document.getElementById('cart-total').textContent = `S/ ${total.toFixed(2)}`;
        if(window.lucide) window.lucide.createIcons();
    },
    
    showDrawer() {
        console.log("Cart System: Opening Drawer...");
        const drawer = document.getElementById('cart-drawer');
        if (drawer) drawer.classList.add('active');
    },
    
    hideDrawer() {
        const drawer = document.getElementById('cart-drawer');
        if (drawer) drawer.classList.remove('active');
    },
    
    notify(text) {
        const toast = document.createElement('div');
        toast.style = `
            position: fixed; bottom: 30px; left: 30px; 
            padding: 15px 30px; z-index: 20000; 
            background: var(--dark); border-left: 5px solid var(--accent);
            color: var(--accent); box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            font-family: monospace; font-size: 0.8rem;
            animation: slideIn 0.3s ease-out;
        `;
        toast.innerHTML = `> UPDATE: ${text}`;
        document.body.appendChild(toast);
        
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = '0.5s'; setTimeout(() => toast.remove(), 500); }, 3000);
    }
};

// Global Exposure
window.Cart = Cart;
document.addEventListener('DOMContentLoaded', () => Cart.init());
