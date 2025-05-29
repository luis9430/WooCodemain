<?php
namespace App\Workflow;

use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Registry as SymfonyRegistry;
use Symfony\Component\EventDispatcher\EventDispatcher;
use App\Workflow\Config\OrderWorkflow;
use App\Workflow\Subscriber\OrderWorkflowSubscriber;
use Symfony\Component\Workflow\StateMachine;

use App\Core\Container;

class Registry {
    /**
     * @var Container
     */
    private $container;
    
    /**
     * @var SymfonyRegistry
     */
    private $registry;
    
    /**
     * Constructor
     *
     * @param Container $container
     */
    public function __construct($container) {
        $this->container = $container;
        $this->registry = new SymfonyRegistry();
        
        $this->registerWorkflows();
    }
    
    /**
     * Registrar los workflows disponibles
     */
    private function registerWorkflows() {
        // Registrar el workflow de pedidos
        $orderWorkflowConfig = new OrderWorkflow();
        $orderWorkflow = $this->createWorkflow($orderWorkflowConfig);
        
        // Registrar suscriptor específico para pedidos
        $dispatcher = $orderWorkflow->getEventDispatcher();
        $dispatcher->addSubscriber(new OrderWorkflowSubscriber($this->container));
        
        // Asociar el workflow con la clase WC_Order
        if (class_exists('\WC_Order')) {
            $this->registry->add($orderWorkflow, \WC_Order::class);
        }
    }
    
    /**
     * Obtener un workflow por nombre
     *
     * @param string $name Nombre del workflow
     * @return Workflow|null
     */
    public function get($name) {
        foreach ($this->registry->all() as $workflow) {
            if ($workflow->getName() === $name) {
                return $workflow;
            }
        }
        return null;
    }
    
    /**
     * Crear un workflow a partir de su configuración
     *
     * @param mixed $config Configuración del workflow
     * @return Workflow
     */
    private function createWorkflow($config) {
        $builder = new DefinitionBuilder();
        
        // Agregar lugares (estados)
        $builder->addPlaces($config->getPlaces());
        
        // Agregar transiciones
        foreach ($config->getTransitions() as $name => $transition) {
            $builder->addTransition(new Transition(
                $name, 
                $transition['from'], 
                $transition['to']
            ));
        }
        
        // Crear event dispatcher
        $dispatcher = new EventDispatcher();
        
        // Crear el almacén de marcado que usará la propiedad 'status' de WC_Order
        $markingStore = new MethodMarkingStore(
            true, 
            'status'  // WooCommerce usa 'status' para el estado del pedido
        );
        
        // Crear y devolver el workflow
        return new Workflow(
            $builder->build(), 
            $markingStore, 
            $dispatcher, 
            $config->getName()
        );
    }
}