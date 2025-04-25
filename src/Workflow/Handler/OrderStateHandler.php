<?php
namespace App\Workflow\Handler;

use WC_Order;

class OrderStateHandler {
    /**
     * Manejar transición a procesando
     *
     * @param WC_Order $order
     * @return void
     */
    public function handleProcessing(WC_Order $order) {
        // Aquí puedes implementar lógica adicional cuando un pedido pasa a estado "processing"
        // Por ejemplo, enviar emails, actualizar inventario, etc.
        
        // Ejemplos de acciones:
        $order->add_order_note('Pedido pasó a estado de procesamiento automáticamente por el workflow.');
        
        // También puedes disparar acciones para que otros plugins reaccionen
        do_action('mi_plugin_order_processing', $order);
    }
    
    /**
     * Manejar transición a completado
     *
     * @param WC_Order $order
     * @return void
     */
    public function handleCompleted(WC_Order $order) {
        // Acciones cuando un pedido se completa
        $order->add_order_note('Pedido completado automáticamente por el workflow.');
        do_action('mi_plugin_order_completed', $order);
    }
    
    /**
     * Manejar transición a cancelado
     *
     * @param WC_Order $order
     * @return void
     */
    public function handleCancelled(WC_Order $order) {
        // Acciones cuando un pedido se cancela
        $order->add_order_note('Pedido cancelado automáticamente por el workflow.');
        do_action('mi_plugin_order_cancelled', $order);
    }
    
    /**
     * Manejar transición a en espera
     *
     * @param WC_Order $order
     * @return void
     */
    public function handleOnHold(WC_Order $order) {
        // Acciones cuando un pedido pasa a estar en espera
        $order->add_order_note('Pedido puesto en espera automáticamente por el workflow.');
        do_action('mi_plugin_order_on_hold', $order);
    }
}