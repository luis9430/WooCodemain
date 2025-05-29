<?php
namespace WooCodePlugin\Controller;

use WooCodePlugin\Core\Container;

class AdminController {
    /**
     * @var Container
     */
    protected $container;
    
    /**
     * Constructor
     *
     * @param Container|null $container
     */
    public function __construct(Container $container = null) {
        $this->container = $container;
    }
    
    /**
     * Inicializar controlador
     */
    public function init() {
        // Añadir menú de administración
        add_action('admin_menu', [$this, 'register_admin_menu']);
        
        // Añadir meta box de historial de workflow
        add_action('add_meta_boxes', [$this, 'register_workflow_history_meta_box']);
        
        // Registrar endpoints de REST API para los datos
        add_action('rest_api_init', [$this, 'register_rest_endpoints']);
    }
    
    /**
     * Registrar menú de administración
     */
    public function register_admin_menu() {
        add_menu_page(
            'Mi Plugin',
            'Mi Plugin',
            'manage_options',
            'mi-plugin',
            [$this, 'render_admin_page'],
            'dashicons-admin-generic',
            30
        );
        
        // Submenú para diagnóstico de workflow
        add_submenu_page(
            'mi-plugin',
            'Workflows',
            'Workflows',
            'manage_options',
            'workflow-dashboard',
            [$this, 'render_workflow_dashboard_page']
        );

        
    }
    
    /**
     * Renderizar página de administración
     */
    public function render_admin_page() {
        echo '<div id="mi-plugin-admin-app"></div>'; // Contenedor para React
    }
    
    
    /**
     * Renderizar página de diagnóstico de workflow
     */
    public function render_workflow_dashboard_page() {
        echo '<div id="workflow-dashboard"></div>'; // Contenedor para React
    }
    
  

 



   
}