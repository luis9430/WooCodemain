<?php
namespace App\Controller;

use App\Core\Container;

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
            'Diagnóstico de Workflow',
            'Diagnóstico de Workflow',
            'manage_options',
            'workflow-diagnostico',
            [$this, 'render_workflow_diagnostic_page']
        );

        
    }
    
    /**
     * Renderizar página de administración
     */
    public function render_admin_page() {
        echo '<div class="wrap">';
        echo '<h1>Mi Plugin</h1>';
        echo '<div id="mi-plugin-admin-app"></div>'; // Contenedor para React
        echo '</div>';
    }
    

    
    /**
     * Registrar meta box de historial de workflow
     */
    public function register_workflow_history_meta_box() {
        add_meta_box(
            'workflow_history_meta_box',
            'Historial de Workflow',
            [$this, 'render_workflow_history_meta_box'],
            'shop_order',
            'normal',
            'default'
        );
    }
    
    /**
     * Renderizar meta box de historial de workflow
     *
     * @param \WP_Post $post
     */
    public function render_workflow_history_meta_box($post) {
        // Solo crear el contenedor para React y pasar el ID del pedido
        echo '<div id="mi-plugin-workflow-history" data-order-id="' . esc_attr($post->ID) . '"></div>';
    }
    
    /**
     * Renderizar página de diagnóstico de workflow
     */
    public function render_workflow_diagnostic_page() {
        echo '<div class="wrap">';
        echo '<div id="workflow-diagnostic"></div>'; // Contenedor para React
        echo '</div>';
    }
    
  
    public function get_order_workflow_history($request) {
        $order_id = $request->get_param('id');
        
        $entityManager = \App\Database\EntityManager::getInstance();
        $repository = $entityManager->getRepository(\App\Model\Entity\WorkflowHistory::class);
        
        $history = $repository->findByObject($order_id, 'order');
        
        // Procesar resultados para incluir información de usuario
        $result = [];
        foreach ($history as $record) {
            $user = get_user_by('id', $record->getUserId());
            $item = [
                'id' => $record->getId(),
                'object_id' => $record->getObjectId(),
                'object_type' => $record->getObjectType(),
                'workflow' => $record->getWorkflow(),
                'transition' => $record->getTransition(),
                'from_state' => $record->getFromState(),
                'to_state' => $record->getToState(),
                'user_id' => $record->getUserId(),
                'user_name' => $user ? $user->display_name : 'Sistema',
                'metadata' => $record->getMetadata(),
                'created_at' => $record->getCreatedAt()->format('Y-m-d H:i:s')
            ];
            $result[] = $item;
        }
        
        return rest_ensure_response($result);
    }

    public function get_workflow_history($request) {
        $page = $request->get_param('page') ?: 1;
        $per_page = $request->get_param('per_page') ?: 20;
        
        $entityManager = \App\Database\EntityManager::getInstance();
        $repository = $entityManager->getRepository(\App\Model\Entity\WorkflowHistory::class);
        
        $result = $repository->findPaginated($page, $per_page);
        
        // Procesar resultados para incluir información de usuario
        $records = [];
        foreach ($result['records'] as $record) {
            $user = get_user_by('id', $record->getUserId());
            $item = [
                'id' => $record->getId(),
                'object_id' => $record->getObjectId(),
                'object_type' => $record->getObjectType(),
                'workflow' => $record->getWorkflow(),
                'transition' => $record->getTransition(),
                'from_state' => $record->getFromState(),
                'to_state' => $record->getToState(),
                'user_id' => $record->getUserId(),
                'user_name' => $user ? $user->display_name : 'Sistema',
                'metadata' => $record->getMetadata(),
                'created_at' => $record->getCreatedAt()->format('Y-m-d H:i:s')
            ];
            $records[] = $item;
        }
        
        return rest_ensure_response([
            'total' => $result['total'],
            'total_pages' => ceil($result['total'] / $per_page),
            'page' => (int)$page,
            'per_page' => (int)$per_page,
            'records' => $records
        ]);
    }

    public function register_rest_endpoints() {
        // Endpoint existente para el historial de workflow
        register_rest_route('mi-plugin/v1', '/orders/(?P<id>\d+)/workflow-history', [
            'methods' => 'GET',
            'callback' => [$this, 'get_order_workflow_history'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ]);
        
        // Endpoint existente para el historial general de workflows
        register_rest_route('mi-plugin/v1', '/workflow-history', [
            'methods' => 'GET',
            'callback' => [$this, 'get_workflow_history'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ]);
        
        // NUEVO: Endpoint para verificar workflows
        register_rest_route('mi-plugin/v1', '/workflows/check', [
            'methods' => 'GET',
            'callback' => [$this, 'check_workflows'],
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ]);
    }
    
    /**
     * NUEVO: Método para verificar workflows
     */
    public function check_workflows() {
        // Simulación de resultados
        $workflows = [
            [
                'name' => 'OrderWorkflow', 
                'status' => 'active', 
                'transitions' => [
                    ['from' => 'created', 'to' => 'processing', 'valid' => true],
                    ['from' => 'processing', 'to' => 'completed', 'valid' => true]
                ],
                'issues' => []
            ],
            [
                'name' => 'ProductWorkflow', 
                'status' => 'warning',
                'transitions' => [
                    ['from' => 'draft', 'to' => 'published', 'valid' => true],
                    ['from' => 'published', 'to' => 'archived', 'valid' => false, 'error' => 'Missing handler']
                ],
                'issues' => ['Incomplete transition configuration']
            ]
        ];
        
        return rest_ensure_response([
            'status' => 'success',
            'workflows' => $workflows
        ]);
    }


   
}