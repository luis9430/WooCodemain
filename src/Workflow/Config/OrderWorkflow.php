<?php
namespace App\Workflow\Config;

class OrderWorkflow {
    /**
     * Obtener el nombre del workflow
     *
     * @return string
     */
    public function getName() {
        return 'order';
    }
    
    /**
     * Obtener los lugares (estados) del workflow
     *
     * @return array
     */
    public function getPlaces() {
        return [
            'pending',           // pedido pendiente
            'processing',        // en procesamiento
            'on-hold',           // en espera
            'completed',         // completado
            'cancelled',         // cancelado
            'refunded',          // reembolsado
            'failed'             // fallido
        ];
    }
    
    /**
     * Obtener las transiciones del workflow
     *
     * @return array
     */
    public function getTransitions() {
        return [
            'process' => [
                'from' => 'pending',
                'to' => 'processing'
            ],
            'hold' => [
                'from' => ['pending', 'processing'],
                'to' => 'on-hold'
            ],
            'complete' => [
                'from' => ['processing', 'on-hold'],
                'to' => 'completed'
            ],
            'cancel' => [
                'from' => ['pending', 'processing', 'on-hold'],
                'to' => 'cancelled'
            ],
            'refund' => [
                'from' => ['processing', 'completed'],
                'to' => 'refunded'
            ],
            'fail' => [
                'from' => ['pending', 'processing'],
                'to' => 'failed'
            ],
        ];
    }
}