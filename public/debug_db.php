<?php
require_once 'config/db.php';

echo "<h1>DEPURACIÓN DE BASE DE DATOS</h1>";

try {
    // 1. Check schemas
    echo "<h3>1. Verificando Tablas:</h3>";
    $tables = db_get_all("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    if (empty($tables)) {
        echo "<p style='color:red;'>ERROR: No se encontraron tablas en el esquema 'public'.</p>";
    } else {
        echo "<ul>";
        foreach ($tables as $t) echo "<li>" . $t['table_name'] . "</li>";
        echo "</ul>";
    }

    // 2. Check Menus
    echo "<h3>2. Contenido de 'menus':</h3>";
    $menus = db_get_all("SELECT COUNT(*) as total FROM public.menus");
    echo "<p>Total de platos en la tabla menus: " . $menus[0]['total'] . "</p>";

    if ($menus[0]['total'] > 0) {
        $items = db_get_all("SELECT * FROM public.menus LIMIT 5");
        echo "<pre>";
        print_r($items);
        echo "</pre>";
    }

    // 3. Check Categories
    echo "<h3>3. Contenido de 'categorias':</h3>";
    $cats = db_get_all("SELECT COUNT(*) as total FROM public.categorias");
    echo "<p>Total de categorías: " . $cats[0]['total'] . "</p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>ERROR FATAL: " . $e->getMessage() . "</p>";
}
