<?php 

return [
    // Otros servicios...
    
    // Registro de Workflows
    'workflow.registry' => function($container) {
        return new \App\Workflow\Registry($container);
    },
    
    // Workflow para pedidos de WooCommerce
    'workflow.order' => function($container) {
        return $container->get('workflow.registry')->get('order');
    }
];