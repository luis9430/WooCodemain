<?php
/**
 * Función para registrar y cargar scripts de Vue
 */
function mi_plugin_enqueue_vue_scripts($hook) {
    // Lista de páginas donde queremos cargar los scripts
    $allowed_pages = [
        'toplevel_page_mi-plugin',
        'mi-plugin_page_workflow-diagnostico'
    ];
    
    // Verifiquemos si estamos en una de esas páginas
    $is_plugin_page = false;
    foreach ($allowed_pages as $page) {
        if (strpos($hook, $page) !== false) {
            $is_plugin_page = true;
            break;
        }
    }
    
    if (!$is_plugin_page) {
        return;
    }
    
    // Registremos paths absolutos y URLs
    $plugin_url = plugin_dir_url(dirname(__FILE__));
    $plugin_path = plugin_dir_path(dirname(__FILE__));
    
    // Verificar y encolar el archivo vendor.js si existe
    $vendor_path = $plugin_path . 'dist/js/vendor.js';
    if (file_exists($vendor_path)) {
        wp_enqueue_script(
            'mi-plugin-vendor',
            $plugin_url . 'dist/js/vendor.js',
            [],
            filemtime($vendor_path),
            true
        );
    }
    
    // Verificar y encolar el archivo app.js
    $admin_path = $plugin_path . 'dist/js/app.js';
    if (file_exists($admin_path)) {
        wp_enqueue_script(
            'mi-plugin-admin',
            $plugin_url . 'dist/js/app.js',
            file_exists($vendor_path) ? ['mi-plugin-vendor'] : [],
            filemtime($admin_path),
            true
        );
        
        // Localizar script para pasar variables a JavaScript
        wp_localize_script('mi-plugin-admin', 'miPluginData', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => rest_url('mi-plugin/v1'),
            'nonce' => wp_create_nonce('wp_rest'),
            'plugin_url' => $plugin_url,
            'page' => isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '',
        ]);
    }
    
    // Buscar y encolar archivos CSS
    $css_path = $plugin_path . 'dist/css/js/app.css';
    if (file_exists($css_path)) {
        wp_enqueue_style(
            'mi-plugin-admin-style',
            $plugin_url . 'dist/css/js/app.css',
            [],
            filemtime($css_path)
        );
    } else {
        // Intentar buscar en otra ubicación común
        $alt_css_path = $plugin_path . 'dist/css/app.css';
        if (file_exists($alt_css_path)) {
            wp_enqueue_style(
                'mi-plugin-admin-style',
                $plugin_url . 'dist/css/app.css',
                [],
                filemtime($alt_css_path)
            );
        }
    }
}
add_action('admin_enqueue_scripts', 'mi_plugin_enqueue_vue_scripts');