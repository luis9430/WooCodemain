<?php
// src/Workflow/Config/services.php

use Psr\Container\ContainerInterface; 
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Workflow\StateMachine;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use WooCodePlugin\Workflow\Config\OrderWorkflow;
use WooCodePlugin\Workflow\Registry as WorkflowRegistry;
use WooCodePlugin\Workflow\Handler\OrderStateHandler;
use WooCodePlugin\Workflow\Subscriber\OrderWorkflowSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use WooCodePlugin\Database\EntityManagerFactory; 


return static function (ContainerBuilder $container) {

    // Definición del Workflow de Pedidos
    $container->register('workflow.order.definition', \Symfony\Component\Workflow\Definition::class)
        ->setFactory([OrderWorkflow::class, 'getDefinition']); // Llama al método estático

    // Marking Store para el Workflow de Pedidos
    $container->register('workflow.order.marking_store', MethodMarkingStore::class)
        ->setArguments([
            true,   // single_state
            'status' // property name
        ]);

    // Servicio StateMachine para los Pedidos
    $container->register('workflow.order', StateMachine::class)
        ->setArguments([
            new Reference('workflow.order.definition'),
            new Reference('workflow.order.marking_store'),
            new Reference(EventDispatcher::class), // Asume que EventDispatcher está registrado o se registrará
            OrderWorkflow::getName() // Nombre del workflow
        ]);

    // Suscriptor de Eventos para el Workflow de Pedidos
    $container->register(OrderWorkflowSubscriber::class, OrderWorkflowSubscriber::class)
        // Si tu suscriptor tiene dependencias, configúralas aquí:
        // ->addArgument(new Reference(EntityManagerInterface::class))
        // ->addArgument(new Reference(LoggerInterface::class))
        ->setAutowired(true)    // Intenta autoconectar dependencias basadas en type-hints
        ->setAutoconfigured(true); // Permite que se procesen tags como 'kernel.event_subscriber' si usaras el HttpKernel

    // Registro de Workflows
    $container->register(WorkflowRegistry::class, WorkflowRegistry::class)
        ->setAutowired(true)
        ->setAutoconfigured(true);
        // Si necesitas añadir el workflow al registro explícitamente:
        // ->addMethodCall('addWorkflow', [new Reference('workflow.order')]); // Ajusta según tu método en Registry

    // Handler de Estado de Pedidos (Opcional)
    $container->register(OrderStateHandler::class, OrderStateHandler::class)
        ->setAutowired(true)
        ->setAutoconfigured(true);

    // Asegúrate de que EventDispatcher esté registrado si no se hace globalmente
    // Si no está definido en otro lugar y lo necesitas específicamente para los workflows:
    if (!$container->has(EventDispatcher::class)) {
        $container->register(EventDispatcher::class, EventDispatcher::class);
    }

    if (!$container->has(EntityManagerInterface::class)) {
    $container->register(EntityManagerInterface::class)
        ->setFactory([EntityManagerFactory::class, 'create']); 
}


};