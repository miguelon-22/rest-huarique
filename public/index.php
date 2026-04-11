<?php
require_once '../config/db.php';

// Handle Testimonial Submission BEFORE any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_testimonio'])) {
    $tnombre = $_POST['autor_nombre'] ?? '';
    $tcontenido = $_POST['autor_resena'] ?? '';
    $testrellas = (int)($_POST['autor_estrellas'] ?? 5);
    if (!empty($tnombre) && !empty($tcontenido)) {
        try {
            db_execute("INSERT INTO public.testimonios (nombre, contenido, calificacion) VALUES (?, ?, ?)", [$tnombre, $tcontenido, $testrellas]);
            header("Location: index.php?testimonio=success#blog");
            exit;
        } catch (Exception $e) { /* silent fail for public form */ }
    }
}

include '../includes/header.php';

// Safe Data Fetching System
$config = ['site_name' => 'HUARIQUE QUANTUM', 'hero_title' => 'SABOR EN OTRA DIMENSIÓN', 'hero_subtitle' => 'Protocolos de sabor optimizados.'];
$categories = [];
$featured_menus = [];
$testimonials = [];
$db_err = '';

try {
    // 1. Try Config (Optional)
    try {
        $raw_config = db_get_all("SELECT clave, valor FROM public.configuraciones");
        foreach ($raw_config as $c) { $config[$c['clave']] = $c['valor']; }
    } catch (Exception $e) { /* Config missing, ignore and use defaults */ }

    // 2. Main Menu Fetch (Strict)
    $featured_menus = db_get_all("SELECT m.*, COALESCE(c.nombre, 'General') as category_name 
                                  FROM public.menus m 
                                  LEFT JOIN public.categorias c ON m.categoria_id = c.id 
                                  LIMIT 8");
    
    // 3. Testimonials & Blogs
    $categories = db_get_all("SELECT * FROM public.categorias ORDER BY nombre ASC");
    $testimonials = db_get_all("SELECT * FROM public.testimonios ORDER BY creado_en DESC LIMIT 5");
    $blogs = db_get_all("SELECT * FROM public.blogs ORDER BY creado_en DESC LIMIT 3");

} catch (Exception $e) {
    $db_err = $e->getMessage();
    error_log("DB Fetch Error: " . $db_err);
}
?>

<!-- Futuristic VFX Core -->
<div id="vfx-root">
    <div class="starfield"></div>
    <div class="neon-grid"></div>
    <div class="scanlines"></div>
</div>

<style>
#vfx-root { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background: #020202; overflow: hidden; pointer-events: none; }
.starfield { width: 100%; height: 100%; position: absolute; background: radial-gradient(ellipse at bottom, #1B2735 0%, #090A0F 100%); }
.neon-grid { 
    width: 200%; height: 200%; position: absolute; top: -50%; left: -50%;
    background-image: 
        linear-gradient(rgba(0, 245, 255, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 245, 255, 0.05) 1px, transparent 1px);
    background-size: 50px 50px;
    transform: perspective(500px) rotateX(60deg);
    animation: gridMove 20s linear infinite;
}
@keyframes gridMove { from { background-position: 0 0; } to { background-position: 0 100%; } }
.scanlines { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: repeating-linear-gradient(0deg, rgba(0,0,0,0.1), rgba(0,0,0,0.1) 1px, transparent 1px, transparent 2px); z-index: 10; opacity: 0.3; }
</style>

<!-- Futuristic Status HUD (Simplified for Clarity) -->
<div class="hud-top-left" style="position: fixed; top: 100px; left: 30px; z-index: 14000; font-family: sans-serif; font-size: 0.75rem; color: var(--accent); font-weight: bold; pointer-events: none;">
    <div>✓ POLLERÍA ABIERTA</div>
    <div>★ CALIDAD 100%</div>
</div>

<div class="hud-bottom-right" style="position: fixed; bottom: 30px; right: 30px; z-index: 14000; font-family: sans-serif; font-size: 0.75rem; color: var(--primary); font-weight: bold; pointer-events: none; text-align: right;">
    <div>UBICACIÓN: HUARIQUE</div>
    <div>ESTADO: EN LÍNEA</div>
</div>

<!-- Hero Section -->
<header class="hero">
    <div class="container" style="display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 20; gap: 50px; flex-wrap: wrap;">
        <div class="hero-content animate__animated animate__fadeInLeft" style="flex: 1; min-width: 300px;">
            <div class="glass neon-glow" style="display: inline-block; padding: 10px 20px; margin-bottom: 20px; border-radius: 50px; font-size: 0.8rem; letter-spacing: 2px; color: var(--accent); border-color: var(--accent); font-weight: bold;">
                LA MEJOR POLLERÍA DE LA CIUDAD
            </div>
            <h1 class="hero-title neon-glow glitch" data-text="POLLERIA HUARIQUE" style="font-size: clamp(3rem, 5vw, 5.5rem); line-height: 0.85; margin-bottom: 15px;">
                POLLERIA <br> HUARIQUE
            </h1>
            <div style="background: var(--primary); color: white; padding: 5px 15px; display: inline-block; font-size: 0.7rem; letter-spacing: 2px; font-weight: 900; margin-bottom: 20px;">DELIVERY ACTIVO AHORA</div>
            <p class="hero-subtitle" style="max-width: 520px; font-size: 1.2rem; opacity: 0.9; color: var(--text-secondary); line-height: 1.4;">
                Pollo a la brasa jugoso y crocante hecho al carbón con nuestra receta secreta. <br> 
                <strong>¡EL SABOR QUE YA CONOCES, AHORA MEJORADO!</strong>
            </p>
            <div class="hero-buttons" style="display: flex; gap: 20px; margin-top: 50px; flex-wrap: wrap;">
                <a href="#menu" class="cyber-btn" style="padding: 20px 45px; background: var(--primary); color: white; clip-path: polygon(10% 0, 100% 0, 90% 100%, 0% 100%);">PEDIR MI POLLO</a>
                <a href="#reservas" class="cyber-btn" style="padding: 20px 45px; border: 2px solid var(--accent); color: var(--accent); clip-path: polygon(10% 0, 100% 0, 90% 100%, 0% 100%);">RESERVAR MESA</a>
            </div>
        </div>
        
        <div class="hero-image" style="position: relative; width: 400px; height: 280px; flex-shrink: 0; transform: translateY(30px);">
            <div class="carousel-container" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; transform: skewY(-2deg) rotateX(15deg); border-radius: 4px; box-shadow: 0 0 50px var(--primary-glow); border-left: 5px solid var(--primary); background: #000;">
                <img class="cyber-slide active" src="https://images.unsplash.com/photo-1598103442097-8b74394b95c6?auto=format&fit=crop&q=80&w=800" alt="Pollo Fusión">
                <img class="cyber-slide" src="https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&q=80&w=800" alt="Pollo a la brasa"> 
                <img class="cyber-slide" src="https://images.unsplash.com/photo-1549488344-c6a6d6556e4c?auto=format&fit=crop&q=80&w=800" alt="Experiencia Huarique">
            </div>
            
            <div class="radar-scan" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; transform: skewY(-2deg) rotateX(15deg); border-radius: 4px; pointer-events: none;"></div>
            
            <div class="cyber-pagination" style="position: absolute; bottom: -30px; left: 50%; transform: translateX(-50%) skewY(-2deg); display: flex; gap: 10px; z-index: 10;">
                <div class="cyber-dot active"></div>
                <div class="cyber-dot"></div>
                <div class="cyber-dot"></div>
            </div>
        </div>
    </div>
</header>

<style>
.radar-scan {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    background: radial-gradient(circle, var(--accent) 0%, transparent 60%);
    opacity: 0.1; animation: radarPulse 4s infinite; z-index: 5;
}
@keyframes radarPulse { 0% { transform: scale(0.5); opacity: 0.1; } 100% { transform: scale(1.5); opacity: 0; } }
.cyber-btn {
    text-decoration: none; font-weight: 900; letter-spacing: 2px; font-size: 0.8rem;
    transition: 0.3s; cursor: pointer; display: flex; align-items: center; justify-content: center;
}
.cyber-btn:hover { transform: translateY(-5px) scale(1.05); box-shadow: 0 0 30px var(--primary-glow); }

/* CAROUSEL ESTILOS AVANZADOS */
.cyber-slide {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    border-radius: 4px;
    opacity: 0;
    transition: opacity 1.5s ease-in-out;
}
.cyber-slide.active {
    opacity: 1;
    z-index: 2;
}

.cyber-dot {
    width: 20px; height: 6px;
    background: rgba(255,255,255,0.2);
    cursor: pointer; 
    transition: 0.3s;
    border-radius: 2px;
}
.cyber-dot.active {
    background: var(--accent);
    box-shadow: 0 0 10px var(--accent);
    width: 40px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let currentSlide = 0;
    const slides = document.querySelectorAll('.cyber-slide');
    const dots = document.querySelectorAll('.cyber-dot');
    
    function showSlide(index) {
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        slides[index].classList.add('active');
        dots[index].classList.add('active');
    }

    dots.forEach((dot, idx) => {
        dot.addEventListener('click', () => {
            currentSlide = idx;
            showSlide(currentSlide);
        });
    });

    setInterval(() => {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }, 4500);
});
</script>

<style>
.glitch { position: relative; }
.glitch::before, .glitch::after { content: attr(data-text); position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.8; }
.glitch::before { left: 2px; text-shadow: -2px 0 var(--primary); animation: glitch-anim 2s infinite linear alternate-reverse; }
.glitch::after { left: -2px; text-shadow: 2px 0 var(--accent); animation: glitch-anim2 3s infinite linear alternate-reverse; }
@keyframes glitch-anim { 0% { clip: rect(10px, 9999px, 50px, 0); } 100% { clip: rect(80px, 9999px, 30px, 0); } }
@keyframes glitch-anim2 { 0% { clip: rect(40px, 9999px, 90px, 0); } 100% { clip: rect(20px, 9999px, 60px, 0); } }

.scanning-effect {
    position: absolute; top: 0; left: 0; width: 100%; height: 5px;
    background: var(--accent); box-shadow: 0 0 20px var(--accent);
    z-index: 5; animation: scanMove 4s linear infinite;
}
@keyframes scanMove { from { top: 0; } to { top: 100%; } }
</style>

<!-- Featured Menu -->
<section id="menu">
    <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 60px;">
            <h2 class="neon-glow" style="font-size: 3rem; margin-bottom: 10px;">NUESTROS PLATOS</h2>
            <p style="color: var(--accent); font-weight: bold;">El mejor sabor de Pollo a la Brasa preparado para ti.</p>
        </div>
        
        <div class="menu-grid">
            <?php if (empty($featured_menus)): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 60px;" class="glass">
                    <img src="https://images.unsplash.com/photo-1615719413546-198b25453f85?auto=format&fit=crop&q=80&w=400" alt="No data" style="width: 150px; height: 150px; border-radius: 50%; opacity: 0.5; margin-bottom: 20px; filter: grayscale(1);">
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 20px;">
                        <span class="glass" style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border-color: var(--primary); box-shadow: 0 0 20px var(--primary-glow);">
                            <i data-lucide="database-zap" style="width: 40px; height: 40px; color: var(--primary);"></i>
                        </span>
                        <h3 class="neon-glow" style="margin: 0; letter-spacing: 5px;">MENÚ NO DISPONIBLE</h3>
                        <p style="color: var(--text-secondary); max-width: 400px; line-height: 1.6;">
                            <?php if ($db_err): ?>
                                <strong style="color: var(--primary);">ERROR:</strong><br>
                                <span style="font-family: monospace; font-size: 0.75rem;"><?= htmlspecialchars($db_err) ?></span>
                            <?php else: ?>
                                Estamos actualizando nuestra carta para ofrecerte lo mejor.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($featured_menus as $menu): 
                    $img_src = $menu['imagen'];
                    if (strpos($img_src, 'http') !== 0 && strpos($img_src, 'uploads/') !== 0) {
                        $img_src = 'uploads/' . $img_src;
                    }
                ?>
                    <div class="menu-card glass" style="position: relative; padding-top: 30px;">
                        <span style="position: absolute; top: 15px; left: 15px; background: var(--primary); font-size: 0.6rem; padding: 3px 10px; border-radius: 4px; font-weight: 800; letter-spacing: 1px; z-index: 5;">
                            <?= strtoupper(htmlspecialchars($menu['category_name'])) ?>
                        </span>
                        <img src="<?= htmlspecialchars($img_src) ?>" 
                             alt="<?= htmlspecialchars($menu['nombre']) ?>" 
                             onerror="this.src='https://images.unsplash.com/photo-1598103442097-8b74394b95c6?auto=format&fit=crop&q=80&w=400'">
                        <h3 class="accent-glow" style="font-size: 1.1rem;"><?= htmlspecialchars($menu['nombre']) ?></h3>
                        <p style="color: var(--text-secondary); margin-bottom: 20px; font-size: 0.8rem; height: 40px; overflow: hidden;"><?= htmlspecialchars($menu['descripcion']) ?></p>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div class="price neon-glow">S/ <?= number_format($menu['precio'], 2) ?></div>
                            <button class="add-to-cart glass" 
                                    data-id="<?= $menu['id'] ?>" 
                                    data-name="<?= htmlspecialchars($menu['nombre']) ?>" 
                                    data-price="<?= $menu['precio'] ?>" 
                                    data-image="<?= htmlspecialchars($img_src) ?>"
                                    style="width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--accent); border-color: var(--accent); cursor: pointer; transition: 0.3s;">
                                <i data-lucide="plus" style="width: 20px;"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 60px;">
            <a href="menu_completo.php" class="cyber-btn" style="display: inline-flex; padding: 20px 50px; border: 2px solid var(--primary); color: var(--primary);">VER TODO EL MENÚ</a>
        </div>
    </div>
</section>

<!-- Table Reservation -->
<section id="reservas" style="background: rgba(10, 10, 10, 0.8); padding: 100px 0; border-top: 1px solid var(--glass-border);">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center;">
            <div class="animate__animated animate__fadeInLeft">
                <h2 class="neon-glow" style="font-size: 4rem; margin-bottom: 30px; line-height: 0.9;">RESERVAR <br> TU MESA</h2>
                <p style="font-size: 1.2rem; margin-bottom: 40px; color: var(--text-secondary);">¿Tienes una celebración especial? Asegura tu lugar con nosotros ahora mismo.</p>
                
                <div class="glass" style="padding: 30px; border-left: 5px solid var(--primary); margin-bottom: 40px;">
                    <p style="font-weight: 900; color: var(--primary); font-size: 0.9rem; margin-bottom: 10px;">NOTA IMPORTANTE:</p>
                    <p style="font-size: 0.9rem; line-height: 1.6;">Se requiere un pago de **S/ 25.00** adelantado para confirmar tu mesa. Esto asegura tu espacio en el restaurante.</p>
                </div>
                
                <ul style="list-style: none; padding: 0;">
                    <li style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                        <i data-lucide="clock" style="color: var(--accent);"></i>
                        <span>Horario de atención: 17:00 PM - 23:00 PM</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                        <i data-lucide="check-circle" style="color: var(--accent);"></i>
                        <span>Confirmación inmediata por WhatsApp o Correo.</span>
                    </li>
                </ul>
            </div>
            
            <div class="glass" style="padding: 50px; border: 2px solid var(--glass-border); border-radius: 10px; position: relative;">
                <div style="position: absolute; top: 15px; right: 15px; font-size: 0.6rem; color: var(--accent); opacity: 0.5;">FORMULARIO DE RESERVA</div>
                
                <form action="procesar_reserva.php" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label style="font-size: 0.75rem; letter-spacing: 2px; color: var(--text-secondary);">NOMBRE COMPLETO</label>
                            <input type="text" name="nombre" class="form-control" required placeholder="Tu nombre">
                        </div>
                        <div class="form-group">
                            <label style="font-size: 0.75rem; letter-spacing: 2px; color: var(--text-secondary);">DNI / DOCUMENTO</label>
                            <input type="text" name="dni" class="form-control" required placeholder="8 dígitos" pattern="\d{8}" maxlength="8" title="Debe ingresar exactamente 8 números">
                        </div>
                        <div class="form-group">
                            <label style="font-size: 0.75rem; letter-spacing: 2px; color: var(--text-secondary);">CORREO ELECTRÓNICO</label>
                            <input type="email" name="email" class="form-control" required placeholder="ejemplo@correo.com">
                        </div>
                        <div class="form-group">
                            <label style="font-size: 0.75rem; letter-spacing: 2px; color: var(--text-secondary);">NUMERO DE CELULAR</label>
                            <input type="tel" name="telefono" class="form-control" required placeholder="9 dígitos" pattern="\d{9}" maxlength="9" title="Debe ingresar exactamente 9 números">
                        </div>
                        <div class="form-group">
                            <label style="font-size: 0.75rem; letter-spacing: 2px; color: var(--text-secondary);">FECHA</label>
                            <input type="date" name="fecha" class="form-control" required min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="form-group">
                            <label style="font-size: 0.75rem; letter-spacing: 2px; color: var(--text-secondary);">HORA</label>
                            <input type="time" name="hora" class="form-control" required min="17:00" max="23:00">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label style="font-size: 0.75rem; letter-spacing: 2px; color: var(--text-secondary);">¿CUÁNTAS PERSONAS SON?</label>
                            <select name="personas" class="form-control" required>
                                <option value="2">2 Personas</option>
                                <option value="4">4 Personas</option>
                                <option value="6">6 Personas</option>
                                <option value="8">Más de 8</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top: 40px;">
                        <p style="font-size: 0.7rem; color: var(--accent); text-align: center; margin-bottom: 20px;">AL CONTINUAR, PAGARÁS S/ 25.00 PARA ASEGURAR TU MESA</p>
                        <button type="submit" class="cyber-btn" style="width: 100%; height: 60px; background: var(--primary); color: white;">PAGAR Y RESERVAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Blog / Noticias -->
<section id="blog-section" style="padding: 100px 0; background: #050505;">
    <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 60px;">
            <h2 class="neon-glow" style="font-size: 3rem; margin-bottom: 10px;">BLOG & NOTICIAS</h2>
            <p style="color: var(--accent); font-weight: bold; letter-spacing: 2px;">PROTOCOLOS DE ACTUALIDAD HUARIQUE</p>
        </div>
        
        <div class="menu-grid">
            <?php if (empty($blogs)): ?>
                <div style="grid-column: 1/-1; text-align: center; color: var(--text-secondary);">
                    <p>> NO DATA_FOUND: Cargando noticias del futuro...</p>
                </div>
            <?php else: ?>
                <?php foreach ($blogs as $b): ?>
                    <div class="menu-card glass" style="padding: 0; overflow: hidden; border-border: 1px solid var(--accent);">
                        <img src="<?= htmlspecialchars($b['imagen']) ?>" alt="<?= htmlspecialchars($b['nombre']) ?>" style="height: 200px; width: 100%; object-fit: cover;">
                        <div style="padding: 25px;">
                            <div style="font-size: 0.7rem; color: var(--accent); margin-bottom: 10px; font-family: monospace;">
                                <?= date('d M, Y', strtotime($b['creado_en'])) ?>
                            </div>
                            <h3 style="margin: 0 0 15px 0; color: white; font-size: 1.2rem;"><?= htmlspecialchars($b['nombre']) ?></h3>
                            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 20px; height: 60px; overflow: hidden;"><?= htmlspecialchars(substr(strip_tags($b['contenido']), 0, 120)) ?>...</p>
                            <a href="blog_post.php?id=<?= $b['id'] ?>" style="color: var(--accent); text-decoration: none; font-size: 0.8rem; font-weight: bold; letter-spacing: 1px;">LEER MÁS ></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Blog / Testimonials -->
<section id="testimonios">
    <div class="container">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 50px;">
            <div>
                <h2 class="neon-glow" style="font-size: 3rem; margin-bottom: 5px;">TESTIMONIOS</h2>
                <div style="color: var(--accent); font-weight: bold; letter-spacing: 2px;">EXPERIENCIA HUARIQUE</div>
            </div>
            <div style="display: flex; gap: 15px;">
                <button onclick="document.getElementById('modal-public-test').style.display='flex'" class="cyber-btn" style="padding: 15px 30px; background: rgba(0, 245, 255, 0.1); color: var(--accent); border: 1px solid var(--accent); cursor: pointer; height: 50px;">
                    <i data-lucide="pen-tool" style="margin-right: 10px;"></i> DEJAR MI OPINIÓN
                </button>
            </div>
        </div>
        
        <div class="menu-grid">
            <?php foreach ($testimonials as $t): 
                $estrellas = $t['calificacion'] ?? 5;
            ?>
                <div class="menu-card glass" style="padding: 40px; text-align: center; border-image: linear-gradient(to bottom right, var(--primary), transparent) 1;">
                    <div style="color: #ffa502; margin-bottom: 20px;">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <i data-lucide="star" <?= ($i <= $estrellas) ? 'fill="currentColor"' : 'style="opacity: 0.3;"' ?> style="width: 16px; height: 16px;"></i>
                        <?php endfor; ?>
                    </div>
                    <p style="font-style: italic; margin-bottom: 20px; color: var(--text-primary); font-size: 0.9rem;">"<?= htmlspecialchars($t['contenido']) ?>"</p>
                    <h4 class="neon-glow" style="color: var(--primary); letter-spacing: 3px; font-size: 0.8rem;"><?= strtoupper(htmlspecialchars($t['nombre'])) ?></h4>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 50px;">
            <button onclick="window.location.href='testimonios_all.php'" class="cyber-btn" style="padding: 15px 40px; border: 1px solid rgba(255,255,255,0.2); color: white; background: transparent;">VER TODOS LOS TESTIMONIOS</button>
        </div>
    </div>
</section>

<!-- Public Testimonial Modal -->
<div id="modal-public-test" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 15000; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
    <div class="glass" style="padding: 40px; border-top: 3px solid var(--accent); max-width: 500px; width: 90%; position: relative; border-radius: 8px;">
        <button type="button" onclick="document.getElementById('modal-public-test').style.display='none'" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: var(--text-secondary); font-size: 1.5rem; cursor: pointer;">&times;</button>
        
        <h3 class="neon-glow" style="color: var(--accent); margin-top: 0; margin-bottom: 25px; letter-spacing: 2px;">TU OPINIÓN IMPORTA</h3>
        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 30px;">Comparte tu experiencia comiendo en HUARIQUE con el resto de clientes.</p>
        
        <form method="POST" action="index.php">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 0.75rem; color: var(--accent); letter-spacing: 2px; margin-bottom: 8px;">TU NOMBRE</label>
                    <input type="text" name="autor_nombre" required class="form-control" placeholder="Ej. Carlos S." style="background: rgba(0,0,0,0.5); border: 1px solid rgba(0,245,255,0.2);">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: var(--accent); letter-spacing: 1px; margin-bottom: 8px;">ESTRELLAS</label>
                    <select name="autor_estrellas" required class="form-control" style="background: rgba(0,0,0,0.5); border: 1px solid rgba(0,245,255,0.2); appearance: none; color: #ffa502; font-weight: bold; text-align: center;">
                        <option value="5">★★★★★ (5)</option>
                        <option value="4">★★★★☆ (4)</option>
                        <option value="3">★★★☆☆ (3)</option>
                        <option value="2">★★☆☆☆ (2)</option>
                        <option value="1">★☆☆☆☆ (1)</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 30px;">
                <label style="display: block; font-size: 0.75rem; color: var(--accent); letter-spacing: 2px; margin-bottom: 8px;">TU EXPERIENCIA (RESEÑA)</label>
                <textarea name="autor_resena" required class="form-control" placeholder="¡La comida estuvo increíble y llegó muy rápido!" style="background: rgba(0,0,0,0.5); border: 1px solid rgba(0,245,255,0.2); height: 100px;"></textarea>
            </div>
            <button type="submit" name="submit_testimonio" class="cyber-btn" style="width: 100%; padding: 15px; background: var(--accent); color: #000; font-weight: 900; border: none; cursor: pointer; letter-spacing: 3px; border-radius: 4px;">PUBLICAR TESTIMONIO</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
