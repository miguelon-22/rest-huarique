</main>

<!-- GLOBAL CYBER MODAL SYSTEM -->
<div id="cyber-modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(8px);">
    <div class="glass" style="padding:40px; border-top:3px solid var(--primary); box-shadow:0 0 50px rgba(255,71,87,0.15); max-width:400px; text-align:center; border-radius:8px;">
        <i data-lucide="alert-triangle" style="color:var(--primary); width:50px; height:50px; margin-bottom:20px;"></i>
        <h3 style="letter-spacing:2px; font-weight:800; color:white; margin-bottom:15px; font-size:1.1rem; text-transform:uppercase;">ACCIÓN_REQUERIDA</h3>
        <p id="cyber-modal-message" style="color:var(--text-secondary); font-size:0.85rem; margin-bottom:30px; letter-spacing:1px;"></p>
        <div style="display:flex; gap:15px; justify-content:center;">
            <button id="cyber-modal-cancel" class="cyber-btn" style="padding:12px 20px; background:rgba(255,255,255,0.05); color:white; border:1px solid rgba(255,255,255,0.1); cursor:pointer; font-size:0.75rem; letter-spacing:1px; border-radius:4px; transition:0.3s; flex:1;">CANCELAR</button>
            <button id="cyber-modal-confirm" class="cyber-btn" style="padding:12px 20px; background:var(--primary); color:white; border:none; cursor:pointer; font-weight:bold; font-size:0.75rem; letter-spacing:2px; box-shadow:0 0 15px rgba(255,71,87,0.4); border-radius:4px; flex:1;">EJECUTAR</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Initialize Lucide icons safely
    try {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    } catch (e) { console.error('Lucide error:', e); }
    
    // Highlight active link
    const currentPath = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.admin-nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });
    
    // Auto-Hijack native confirms
    document.querySelectorAll('[onclick^="return confirm"]').forEach(el => {
        const onclickStr = el.getAttribute('onclick');
        const msgMatch = onclickStr.match(/return confirm\(['"](.*)['"]\)/);
        if (msgMatch && msgMatch[1]) {
            const originalMsg = msgMatch[1];
            el.removeAttribute('onclick'); // remove native
            el.addEventListener('click', function(e) {
                e.preventDefault();
                let dest = el.href ? el.href : () => { el.closest('form').submit(); };
                cyberConfirm(originalMsg, dest);
            });
        }
    });

    let cyberConfirmAction = null;
    window.cyberConfirm = function(message, actionUrl) {
        document.getElementById('cyber-modal-message').innerText = message;
        document.getElementById('cyber-modal-overlay').style.display = 'flex';
        cyberConfirmAction = actionUrl;
    };
    
    document.getElementById('cyber-modal-cancel').addEventListener('click', () => {
        document.getElementById('cyber-modal-overlay').style.display = 'none';
        cyberConfirmAction = null;
    });
    
    document.getElementById('cyber-modal-confirm').addEventListener('click', () => {
        document.getElementById('cyber-modal-overlay').style.display = 'none';
        if (typeof cyberConfirmAction === 'function') {
            cyberConfirmAction();
        } else if (typeof cyberConfirmAction === 'string') {
            window.location.href = cyberConfirmAction;
        }
    });
});
</script>
</body>
</html>
