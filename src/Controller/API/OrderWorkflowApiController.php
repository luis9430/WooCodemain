<?php
// src/Controller/API/OrderWorkflowApiController.php
namespace WooCodePlugin\Controller\API;

use Psr\Container\ContainerInterface; 
use Symfony\Component\Workflow\StateMachineInterface;
//use WooCodePlugin\Model\Repository\WorkflowHistoryRepository; // Asumiendo que tienes este repo
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class OrderWorkflowApiController
{
    private ContainerInterface $container;
    private StateMachineInterface $orderStateMachine; // El StateMachine para pedidos

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        // Obtener el StateMachine del contenedor. Asegúrate que 'workflow.order' esté definido en services.php
        // y devuelva la instancia de StateMachine.
        if ($this->container->has('workflow.order')) {
            $this->orderStateMachine = $this->container->get('workflow.order');
        } else {
            // Manejar el caso en que el servicio no esté definido, quizás lanzar una excepción
            // o loguear un error crítico.
            throw new \RuntimeException('Servicio workflow.order no encontrado en el contenedor.');
        }
    }

    /**
     * Registra las rutas de la API REST para los workflows.
     * Este método se llamaría desde tu Bootstrap.php o similar, en el hook 'rest_api_init'.
     */
    public function register_routes(): void
    {
        register_rest_route('woocode-plugin/v1', '/orders/(?P<order_id>\d+)/workflow', [
            'methods' => 'GET',
            'callback' => [$this, 'get_order_workflow_status'],
            'args' => [
                'order_id' => [
                    'validate_callback' => 'is_numeric',
                    'required' => true,
                ],
            ],
            'permission_callback' => [$this, 'check_admin_permissions'],
        ]);

        register_rest_route('woocode-plugin/v1', '/orders/(?P<order_id>\d+)/workflow/apply/(?P<transition_name>[a-zA-Z0-9_]+)', [
            'methods' => 'POST',
            'callback' => [$this, 'apply_order_transition'],
            'args' => [
                'order_id' => [
                    'validate_callback' => 'is_numeric',
                    'required' => true,
                ],
                'transition_name' => [
                    'type' => 'string',
                    'required' => true,
                ],
            ],
            'permission_callback' => [$this, 'check_admin_permissions'],
        ]);

        register_rest_route('woocode-plugin/v1', '/orders/(?P<order_id>\d+)/workflow/history', [
            'methods' => 'GET',
            'callback' => [$this, 'get_order_workflow_history'],
            'args' => [
                'order_id' => [
                    'validate_callback' => 'is_numeric',
                    'required' => true,
                ],
            ],
            'permission_callback' => [$this, 'check_admin_permissions'],
        ]);
    }

    /**
     * Callback de permisos para las rutas.
     */
    public function check_admin_permissions(): bool
    {
        return current_user_can('manage_woocommerce'); // O una capacidad más específica
    }

    /**
     * Obtiene el estado actual y las transiciones disponibles para un pedido.
     */
    public function get_order_workflow_status(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $order_id = (int) $request->get_param('order_id');
        $order = wc_get_order($order_id);

        if (!$order) {
            return new WP_Error('order_not_found', 'Pedido no encontrado', ['status' => 404]);
        }

        try {
            $marking = $this->orderStateMachine->getMarking($order);
            $enabledTransitions = $this->orderStateMachine->getEnabledTransitions($order);

            $transitions_data = [];
            foreach ($enabledTransitions as $transition) {
                $transitions_data[] = [
                    'name' => $transition->getName(),
                    // Puedes obtener metadatos de la transición si los definiste (ej. un label)
                    'label' => $this->orderStateMachine->getMetadataStore()->getTransitionMetadata($transition, $order)['label'] ?? ucfirst(str_replace('_', ' ', $transition->getName())),
                ];
            }

            return new WP_REST_Response([
                'current_place' => array_key_first($marking->getPlaces()), // Asume un solo estado
                'available_transitions' => $transitions_data,
            ], 200);
        } catch (\Exception $e) {
            error_log("Error al obtener estado de workflow para pedido #{$order_id}: " . $e->getMessage());
            return new WP_Error('workflow_error', 'Error al procesar el workflow del pedido: ' . $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * Aplica una transición a un pedido.
     */
    public function apply_order_transition(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $order_id = (int) $request->get_param('order_id');
        $transition_name = $request->get_param('transition_name');
        $order = wc_get_order($order_id);

        if (!$order) {
            return new WP_Error('order_not_found', 'Pedido no encontrado', ['status' => 404]);
        }

        try {
            if ($this->orderStateMachine->can($order, $transition_name)) {
                $this->orderStateMachine->apply($order, $transition_name);
                // El OrderWorkflowSubscriber debería encargarse de guardar el pedido si es necesario,
                // persistir el historial, y otros efectos secundarios.
                
                // Opcional: guardar explícitamente el pedido si el marking store no lo hace
                // y el subscriber tampoco (aunque es mejor que lo haga el subscriber).
                // $order->save(); 

                return new WP_REST_Response(['success' => true, 'message' => "Transición '{$transition_name}' aplicada al pedido #{$order_id}."], 200);
            } else {
                return new WP_Error('transition_not_applicable', "La transición '{$transition_name}' no se puede aplicar al pedido #{$order_id} en su estado actual.", ['status' => 400]);
            }
        } catch (\Exception $e) {
            error_log("Error al aplicar transición '{$transition_name}' al pedido #{$order_id}: " . $e->getMessage());
            return new WP_Error('workflow_error', 'Error al aplicar la transición: ' . $e->getMessage(), ['status' => 500]);
        }
    }

    /**
     * Obtiene el historial de workflow para un pedido.
     */
    public function get_order_workflow_history(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $order_id = (int) $request->get_param('order_id');
        
        // Asumimos que WorkflowHistoryRepository está en el contenedor
        if (!$this->container->has(WorkflowHistoryRepository::class)) {
             return new WP_Error('service_unavailable', 'Servicio de historial no disponible.', ['status' => 500]);
        }
        
        /** @var WorkflowHistoryRepository $historyRepo */
        $historyRepo = $this->container->get(WorkflowHistoryRepository::class);

        try {
            $history_entries = $historyRepo->findBy(['objectId' => $order_id, 'workflow' => OrderWorkflow::getName()], ['createdAt' => 'ASC']);
            
            $formatted_history = array_map(function($entry) {
                // Asegúrate que tu entidad WorkflowHistory tenga estos getters
                return [
                    'from_state' => $entry->getFromState(),
                    'to_state' => $entry->getToState(),
                    'transition' => $entry->getTransition(),
                    'user_id' => $entry->getUserId(), // Podrías querer cargar el nombre del usuario
                    'metadata' => $entry->getMetadata() ? json_decode($entry->getMetadata(), true) : null,
                    'created_at' => $entry->getCreatedAt() ? $entry->getCreatedAt()->format('Y-m-d H:i:s') : null,
                ];
            }, $history_entries);

            return new WP_REST_Response($formatted_history, 200);
        } catch (\Exception $e) {
            error_log("Error al obtener historial de workflow para pedido #{$order_id}: " . $e->getMessage());
            return new WP_Error('history_error', 'Error al obtener el historial del workflow.', ['status' => 500]);
        }
    }
}