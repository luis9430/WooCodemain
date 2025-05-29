<?php

namespace WooCodePlugin\Workflow\Config; // Asumiendo que este es el namespace para tus archivos de configuración de servicios

use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher; // O la clase concreta que uses
use Symfony\Component\Workflow\StateMachine;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use WooCodePlugin\Workflow\Config\OrderWorkflow; // La clase que refactorizamos
use WooCodePlugin\Workflow\Registry as WorkflowRegistry; // Tu clase Registry
use WooCodePlugin\Workflow\Handler\OrderStateHandler;
use WooCodePlugin\Workflow\Subscriber\OrderWorkflowSubscriber;

return [

    /**
     * Definición del Workflow de Pedidos.
     * Utiliza el método estático de la clase OrderWorkflow refactorizada.
     */
    'workflow.order.definition' => static function (ContainerInterface $c) {
        return OrderWorkflow::getDefinition();
    },

    /**
     * Marking Store para el Workflow de Pedidos.
     * Almacena el estado del pedido en una propiedad 'status' del objeto pedido.
     * El objeto debe tener getStatus() y setStatus().
     * El 'true' indica single_state=true, adecuado para StateMachine.
     */
    'workflow.order.marking_store' => static function (ContainerInterface $c) {
        return new MethodMarkingStore(true, 'status');
    },

    /**
     * Servicio StateMachine para los Pedidos.
     * Este es el servicio principal del workflow que utilizarás en tu aplicación.
     */
    'workflow.order' => static function (ContainerInterface $c) {
        $definition = $c->get('workflow.order.definition');
        $markingStore = $c->get('workflow.order.marking_store');
        
        // Asumimos que EventDispatcher está registrado en el contenedor.
        // Si usas el EventDispatcher de Symfony directamente:
        $eventDispatcher = $c->has(EventDispatcher::class) ? $c->get(EventDispatcher::class) : new EventDispatcher();
        // O si tienes una entrada específica para el dispatcher del workflow:
        // $eventDispatcher = $c->get('workflow.event_dispatcher');


        return new StateMachine(
            $definition,
            $markingStore,
            $eventDispatcher,
            OrderWorkflow::getName() // Nombre del workflow, ej: 'order_workflow'
        );
    },

    /**
     * Suscriptor de Eventos para el Workflow de Pedidos.
     * Aquí es donde se manejan las acciones secundarias (emails, logs, etc.)
     * cuando ocurren transiciones o cambios de estado.
     */
    OrderWorkflowSubscriber::class => static function (ContainerInterface $c) {
        // Inyecta las dependencias que necesite tu suscriptor, por ejemplo, un logger o EntityManager.
        // $logger = $c->get(\Psr\Log\LoggerInterface::class);
        // $entityManager = $c->get(\Doctrine\ORM\EntityManagerInterface::class);
        // return new OrderWorkflowSubscriber($entityManager, $logger);
        return new OrderWorkflowSubscriber(); // Ejemplo simple
    },

    // --- Otros Servicios Relacionados con Workflows (Opcional) ---

    /**
     * Registro de Workflows.
     * Permite acceder a los workflows por nombre desde cualquier parte de la aplicación.
     */
    WorkflowRegistry::class => static function (ContainerInterface $c) {
        // Si tu Registry tiene dependencias (como el propio Container para obtener workflows), inyéctalas.
        $registry = new WorkflowRegistry();
        // Es buena práctica registrar los workflows aquí si usas el Registry activamente.
        // $registry->addWorkflow($c->get('workflow.order')); // Esto depende de tu implementación de Registry
        return $registry;
    },
    

    /**
     * Handler de Estado de Pedidos (Opcional).
     * Podría usarse para lógica de negocio centralizada al cambiar estados,
     * aunque mucha de esta lógica puede vivir en los Suscriptores.
     */
    OrderStateHandler::class => static function (ContainerInterface $c) {
        // Inyecta dependencias si es necesario, por ejemplo, EntityManager.
        // $entityManager = $c->get(\Doctrine\ORM\EntityManagerInterface::class);
        // return new OrderStateHandler($entityManager);
        return new OrderStateHandler();
    },

 

   
];