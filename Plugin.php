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
if (!defined('WPINC')) {
    die;
}

// Definir constantes del plugin
if (!defined('MI_PLUGIN_VERSION')) {
    define('MI_PLUGIN_VERSION', '1.0.0');
}
if (!defined('MI_PLUGIN_PATH')) {
    define('MI_PLUGIN_PATH', plugin_dir_path(__FILE__));
}
if (!defined('MI_PLUGIN_URL')) {
    define('MI_PLUGIN_URL', plugin_dir_url(__FILE__));
}

// Autoload con Composer
// Es crucial que esto se cargue ANTES de intentar usar cualquier clase con namespace.
if (file_exists(MI_PLUGIN_PATH . 'vendor/autoload.php')) {
    require_once MI_PLUGIN_PATH . 'vendor/autoload.php';
} else {
    // Es una buena práctica notificar al administrador si el autoloader no está.
    add_action('admin_notices', function() {
        echo '<div class="error"><p>El plugin "Mi Plugin Avanzado" requiere que ejecutes "composer install". El archivo autoload no se encuentra.</p></div>';
    });
    return; // Detener la ejecución si el autoloader no está.
}

// Importar las clases que vas a usar con su namespace completo y correcto.
use WooCodePlugin\Core\Plugin as PluginCore; // Usamos un alias para evitar conflicto si tuvieras una clase global Plugin
use WooCodePlugin\Core\Bootstrap;

/**
 * La función principal que inicializa y ejecuta el plugin.
 * Es buena práctica darle un prefijo único para evitar colisiones con otros plugins/temas.
 */
function woocode_main_run_plugin() {
    // Verificar si las clases existen antes de instanciarlas, como una medida de seguridad adicional.
    if (!class_exists(PluginCore::class)) {
        // Loguear o mostrar un error si la clase principal del plugin no se encuentra.
        // Esto podría indicar un problema con el autoloader o el namespace.
        error_log('Error: La clase WooCodePlugin\Core\Plugin no se encuentra.');
        return;
    }
    if (!class_exists(Bootstrap::class)) {
        error_log('Error: La clase WooCodePlugin\Core\Bootstrap no se encuentra.');
        return;
    }

    // Inicializar el plugin
    // Asumiendo que tu clase WooCodePlugin\Core\Plugin tiene un constructor que no requiere argumentos,
    // o un método estático para obtener la instancia (ej. getInstance()).
    // Si tu clase PluginCore (src/Core/Plugin.php) es la que tiene la lógica principal y el contenedor:
    $pluginInstance = new PluginCore(); // O PluginCore::getInstance() si es un singleton

    // Inicializar el bootstrap
    // Asumiendo que Bootstrap espera una instancia de tu clase PluginCore
    $bootstrap = new Bootstrap($pluginInstance->get_container()); // Si Bootstrap necesita el contenedor
    // O si Bootstrap necesita la instancia de PluginCore:
    // $bootstrap = new Bootstrap($pluginInstance);
    $bootstrap->init(); // Asegúrate de que este método exista en tu clase Bootstrap
}

/**
 * Hook para ejecutar el plugin cuando WordPress se haya cargado completamente
 * y todos los plugins estén cargados.
 */
add_action('plugins_loaded', 'woocode_main_run_plugin');

// Opcional: Hooks de activación y desactivación
function woocode_main_activate_plugin() {
    // Código a ejecutar en la activación (ej. crear tablas, flush rewrite rules)
    // Asegúrate de que el autoloader esté disponible aquí también si necesitas tus clases.
    if (file_exists(MI_PLUGIN_PATH . 'vendor/autoload.php')) {
        require_once MI_PLUGIN_PATH . 'vendor/autoload.php';
        // Ejemplo: \WooCodePlugin\Database\MigrationRunner::runActivations();
    }
}
register_activation_hook(__FILE__, 'woocode_main_activate_plugin');

function woocode_main_deactivate_plugin() {
    // Código a ejecutar en la desactivación
    if (file_exists(MI_PLUGIN_PATH . 'vendor/autoload.php')) {
        require_once MI_PLUGIN_PATH . 'vendor/autoload.php';
        // Ejemplo: \WooCodePlugin\Database\MigrationRunner::runDeactivations();
    }
}
register_deactivation_hook(__FILE__, 'woocode_main_deactivate_plugin');