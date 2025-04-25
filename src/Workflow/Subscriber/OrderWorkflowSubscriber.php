<?php
namespace App\Workflow\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Events;
use App\Core\Container;
use App\Workflow\Handler\OrderStateHandler;

class OrderWorkflowSubscriber implements EventSubscriberInterface {
    /**
     * @var Container
     */
    private $container;
    
    /**
     * Constructor
     *
     * @param Container $container
     */
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    /**
     * Eventos suscritos
     *
     * @return array
     */
    public static function getSubscribedEvents() {
        return [
            // Eventos globales
            Events::TRANSITION => 'onTransition',
            
            // Eventos específicos
            'workflow.order.transition.process' => 'onProcess',
            'workflow.order.transition.complete' => 'onComplete',
            'workflow.order.transition.cancel' => 'onCancel',
            'workflow.order.transition.hold' => 'onHold',
        ];
    }
    /**
     * Manejador para todas las transiciones
     *
     * @param Event $event
     */
    public function onTransition(Event $event) {
        $subject = $event->getSubject();
        
        // Solo procesar órdenes de WooCommerce
        if (!($subject instanceof \WC_Order)) {
            return;
        }
        
        // Registrar la transición en la base de datos
        $workflow = $event->getWorkflow();
        $transition = $event->getTransition();

        $entityManager = EntityManager::getInstance();

        $history = new WorkflowHistory();
        $history->setObjectId($subject->get_id())
                ->setObjectType('order')
                ->setWorkflow($workflow->getName())
                ->setTransition($transition->getName())
                ->setFromState(implode(',', $transition->getFroms()))
                ->setToState(implode(',', $transition->getTos()))
                ->setUserId(get_current_user_id())
                ->setMetadata(json_encode([
                    'timestamp' => current_time('mysql')
                ]));
        
        $entityManager->persist($history);
        $entityManager->flush();
    }
    
    /**
     * Manejador para transición a procesando
     *
     * @param Event $event
     */
    public function onProcess(Event $event) {
        $order = $event->getSubject();
        $handler = new OrderStateHandler();
        $handler->handleProcessing($order);
    }
    
    /**
     * Manejador para transición a completado
     *
     * @param Event $event
     */
    public function onComplete(Event $event) {
        $order = $event->getSubject();
        $handler = new OrderStateHandler();
        $handler->handleCompleted($order);
    }
    
    /**
     * Manejador para transición a cancelado
     *
     * @param Event $event
     */
    public function onCancel(Event $event) {
        $order = $event->getSubject();
        $handler = new OrderStateHandler();
        $handler->handleCancelled($order);
    }
    
    /**
     * Manejador para transición a en espera
     *
     * @param Event $event
     */
    public function onHold(Event $event) {
        $order = $event->getSubject();
        $handler = new OrderStateHandler();
        $handler->handleOnHold($order);
    }
}