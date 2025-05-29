<?php
namespace WooCodePlugin\Core;


use WooCodePlugin\Core\Container;
use WooCodePlugin\Controller\AdminController;
use WooCodePlugin\PostType\ProductPostType;
use WooCodePlugin\Taxonomy\ProductCategory;
use WooCodePlugin\Database\Migration\Migrator;
use ApWooCodePluginp\Database\Migration\CreateWorkflowHistoryTable;
use WooCodePlugin\Workflow\Registry;

/**
 * Inicialización del Plugin
 */
class Bootstrap {
    /**
     * @var Plugin
     */
    private $plugin;
    
    /**
     * @var Container
     */
    private $container;
    
    /**
     * Constructor
     * 
     * @param Plugin $plugin
     */
    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
        $this->container = $this->createContainer();
    }
    
    /**
     * Crear contenedor de dependencias
     * 
     * @return Container
     */
    private function createContainer() {
        $container = new Container();
        
        // Registrar el plugin principal
        $container->set('plugin', $this->plugin);
        
        // Registrar dependencias de workflow
        $container->set('workflow.registry', function($container) {
            return new Registry($container);
        });
        
        $container->set('workflow.order', function($container) {
            return $container->get('workflow.registry')->get('order');
        });
        
        return $container;
    }
    
    /**
     * Inicializar el plugin
     */
    public function init() {
        // Registrar hooks
        $this->register_hooks();
        
        // Registrar tipos de contenido
        $this->register_post_types();
        
        // Registrar taxonomías
        $this->register_taxonomies();
        
        // Inicializar controladores
        $this->init_controllers();
        
        // Cargar assets
        $this->load_assets();
        
        // Inicializar workflows
        $this->initWorkflows();
    }
    
    /**
     * Registrar hooks de WordPress
     */
    private function register_hooks() {
        // Activación del plugin
        register_activation_hook(MI_PLUGIN_PATH . 'mi-plugin.php', [$this, 'activate']);
        
        // Desactivación del plugin
        register_deactivation_hook(MI_PLUGIN_PATH . 'mi-plugin.php', [$this, 'deactivate']);
    }
    
    /**
     * Método de activación
     */
    public function activate() {
        // Ejecutar migraciones base
        $migrator = new Migrator();
        $migrator->migrate();
        
        // Ejecutar migración para la tabla de historial de workflow
        $workflowHistoryMigration = new CreateWorkflowHistoryTable();
        $workflowHistoryMigration->up();
        
        // Limpiar cache de permalinks
        flush_rewrite_rules();
    }
    
    /**
     * Método de desactivación
     */
    public function deactivate() {
        // Limpiar cache de permalinks
        flush_rewrite_rules();
    }
    
    /**
     * Registrar Custom Post Types
     */
    private function register_post_types() {
        // $product = new ProductPostType();
        // $product->register();
    }
    
    /**
     * Registrar Taxonomías
     */
    private function register_taxonomies() {
        // $category = new ProductCategory();
        // $category->register();
    }
    
    /**
     * Inicializar controladores
     */
    private function init_controllers() {
        $admin = new AdminController($this->container);
        $admin->init();
    }
    
    /**
     * Cargar assets (CSS y JS)
     */
    private function load_assets() {
        add_action('admin_enqueue_scripts', function() {
            wp_enqueue_style(
                'mi-plugin-admin',
                MI_PLUGIN_URL . 'dist/css/admin.css',
                [],
                MI_PLUGIN_VERSION
            );
            
            wp_enqueue_script(
                'mi-plugin-admin',
                MI_PLUGIN_URL . 'dist/js/app.js',
                ['jquery', 'wp-element'],
                MI_PLUGIN_VERSION,
                true
            );
            
            // Localizar script para React
            wp_localize_script('mi-plugin-admin', 'miPluginData', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mi-plugin-nonce'),
                'rest_url' => get_rest_url(null, 'mi-plugin/v1')
            ]);
        });
    }
    
    /**
     * Inicializar los workflows
     */
    private function initWorkflows() {
        try {
            // Registrar evento para cambios de estado de WooCommerce
            add_action('woocommerce_order_status_changed', function($orderId, $oldStatus, $newStatus) {
                try {
                    // No hacemos nada específico aquí porque el subscriber ya maneja los eventos
                    // El suscriptor ya está escuchando los eventos del workflow a través del EventDispatcher
                } catch (\Exception $e) {
                    error_log('Error en manejo de workflow: ' . $e->getMessage());
                }
            }, 10, 3);
        } catch (\Exception $e) {
            error_log('Error al inicializar workflows: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtener el contenedor
     * 
     * @return Container
     */
    public function getContainer() {
        return $this->container;
    }
}