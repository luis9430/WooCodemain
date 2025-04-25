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

// Definir constantes
define('MI_PLUGIN_VERSION', '1.0.0');
define('MI_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MI_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoload con Composer
if (file_exists(MI_PLUGIN_PATH . 'vendor/autoload.php')) {
    require_once MI_PLUGIN_PATH . 'vendor/autoload.php';
} else {
    wp_die('Por favor, ejecuta "composer install" en el directorio del plugin.');
}

// Importar dependencias
use App\Core\Plugin;
use App\Core\Bootstrap;

/**
 * Iniciar el plugin
 */
function run_mi_plugin() {
    // Inicializar el plugin
    $plugin = new Plugin();
    
    // Inicializar el bootstrap
    $bootstrap = new Bootstrap($plugin);
    $bootstrap->init();
}




// Activar el plugin
run_mi_plugin();