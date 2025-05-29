<?php
/**
 * Plugin Name: Mi Plugin Avanzado
 * Plugin URI: https://tuplugin.com
 * Description: Plugin WordPress basado en Symfony Components con React
 * Version: 1.0.0
 * Author: Tu Nombre
 * Author URI: https://tuwebsite.com
 * Text Domain: mi-plugin
 * Domain Path: /languages
 */

// Si este archivo es llamado directamente, abortar.
if (!defined('WPINC')) { // Es más común usar WPINC o ABSPATH aquí
    die;
}

// Definir constantes del plugin
if (!defined('MI_PLUGIN_VERSION')) {
    define('MI_PLUGIN_VERSION', '1.0.0');
}
// __FILE__ en el archivo raíz apunta a este mismo archivo.
if (!defined('MI_PLUGIN_FILE')) {
    define('MI_PLUGIN_FILE', __FILE__);
}
if (!defined('MI_PLUGIN_PATH')) {
    define('MI_PLUGIN_PATH', plugin_dir_path(__FILE__));
}
if (!defined('MI_PLUGIN_URL')) {
    define('MI_PLUGIN_URL', plugin_dir_url(__FILE__));
}

// Autoload con Composer
if (file_exists(MI_PLUGIN_PATH . 'vendor/autoload.php')) {
    require_once MI_PLUGIN_PATH . 'vendor/autoload.php';
} else {
    add_action('admin_notices', function() {
        echo '<div class="error"><p>El plugin "Mi Plugin Avanzado" no puede encontrar el autoloader de Composer. Por favor, ejecuta "composer install" en el directorio del plugin: ' . esc_html(MI_PLUGIN_PATH) . '</p></div>';
    });
    return;
}

// Importar las clases que vas a usar con su namespace completo y correcto.
// Es buena práctica usar un alias si el nombre de la clase es común como 'Plugin'.
use WooCodePlugin\Core\Plugin as PluginCore;
use WooCodePlugin\Core\Bootstrap;

/**
 * La función principal que inicializa y ejecuta el plugin.
 * Dale un prefijo único a esta función.
 */
function mi_avanzado_plugin_run() {
    // Verificar si las clases existen antes de instanciarlas.
    if (!class_exists(PluginCore::class)) {
        error_log('Error Crítico: La clase principal del plugin (WooCodePlugin\Core\Plugin) no se encuentra. Verifica el autoloader y los namespaces.');
        return;
    }
    if (!class_exists(Bootstrap::class)) {
        error_log('Error Crítico: La clase Bootstrap (WooCodePlugin\Core\Bootstrap) no se encuentra. Verifica el autoloader y los namespaces.');
        return;
    }

    try {
        // Inicializar la clase principal del plugin (de src/Core/Plugin.php)
        $pluginCoreInstance = new PluginCore(); // Esta es tu clase con el contenedor Symfony
        
        // Inicializar el bootstrap, pasándole la instancia de PluginCore
        $bootstrap = new Bootstrap($pluginCoreInstance);
        $bootstrap->init();

    } catch (\Throwable $e) {
        error_log('Error al inicializar Mi Plugin Avanzado: ' . $e->getMessage() . "\nStack Trace:\n" . $e->getTraceAsString());
        add_action('admin_notices', function() use ($e) {
            echo '<div class="error"><p>Ocurrió un error al inicializar "Mi Plugin Avanzado": ' . esc_html($e->getMessage()) . '</p></div>';
        });
    }
}

/**
 * Hook para ejecutar el plugin cuando WordPress se haya cargado completamente
 * y todos los plugins estén cargados.
 */
add_action('plugins_loaded', 'mi_avanzado_plugin_run');

// Hooks de activación y desactivación (opcional pero recomendado)
function mi_avanzado_plugin_activate() {
    // Incluir autoloader por si no está cargado aún en este contexto
    if (file_exists(MI_PLUGIN_PATH . 'vendor/autoload.php')) {
        require_once MI_PLUGIN_PATH . 'vendor/autoload.php';
    }
    // Es mejor obtener el bootstrap e invocar su método activate si la lógica es compleja
    // Pero para ello necesitarías instanciar PluginCore y Bootstrap aquí también, o tener un método estático
    // Por ahora, puedes poner lógica simple aquí o llamar a un método estático si lo tienes.
    // Ejemplo, si el método activate en Bootstrap es estático o no depende de la instancia:
    // if (class_exists(\WooCodePlugin\Core\Bootstrap::class)) {
    //     \WooCodePlugin\Core\Bootstrap::activate_plugin_logic();
    // }
    flush_rewrite_rules();
}
register_activation_hook(MI_PLUGIN_FILE, 'mi_avanzado_plugin_activate');

function mi_avanzado_plugin_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(MI_PLUGIN_FILE, 'mi_avanzado_plugin_deactivate');