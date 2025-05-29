<?php

namespace WooCodePlugin\Workflow\Config; // Asegúrate de que este sea el namespace correcto

use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Transition;

class OrderWorkflow
{
    /**
     * Construye y devuelve la definición del workflow para los pedidos.
     *
     * @return Definition El objeto de definición del workflow.
     */
    public static function getDefinition(): Definition
    {
        $builder = new DefinitionBuilder();

        // Estados (Places)
        // Se incluyen estados estándar de WooCommerce y algunos personalizados/internos.
        $places = [
            'pending',        // Pedido pendiente de pago
            'failed',         // Pago fallido o error en el pedido
            'processing',     // Pago recibido (o contra reembolso), stock reducido, pedido esperando ser completado/enviado
            'completed',      // Pedido completado y usualmente enviado
            'on-hold',        // Pedido en espera (ej. esperando confirmación de stock o pago manual)
            'cancelled',      // Pedido cancelado por un administrador o el cliente
            'refunded',       // Pedido reembolsado total o parcialmente
            'draft',          // Borrador de pedido (uso interno antes de formalizar)
            'checkout-draft', // Estado interno de WooCommerce para carritos abandonados que se convierten en pedidos
        ];
        $builder->addPlaces($places);

        // Definición de Transiciones
        // Desde 'pending' (Pendiente de pago)
        $builder->addTransition(new Transition('process_payment', 'pending', 'processing')); // Cliente paga con éxito (ej. pasarela online) o admin marca como procesando
        $builder->addTransition(new Transition('payment_failed', 'pending', 'failed'));    // Pago falla
        $builder->addTransition(new Transition('hold_order_pending', 'pending', 'on-hold')); // Admin pone en espera manualmente
        $builder->addTransition(new Transition('cancel_pending', 'pending', 'cancelled')); // Admin o cliente cancela

        // Desde 'failed' (Fallido)
        $builder->addTransition(new Transition('retry_payment', 'failed', 'pending'));    // Cliente reintenta el pago (vuelve a pendiente)
        $builder->addTransition(new Transition('cancel_failed', 'failed', 'cancelled'));  // Admin cancela un pedido fallido

        // Desde 'processing' (En proceso)
        $builder->addTransition(new Transition('complete_order', 'processing', 'completed')); // Admin marca como completado (y usualmente enviado)
        $builder->addTransition(new Transition('hold_order_processing', 'processing', 'on-hold')); // Admin pone en espera
        $builder->addTransition(new Transition('cancel_processing', 'processing', 'cancelled'));// Admin o cliente cancela
        $builder->addTransition(new Transition('issue_refund_processing', 'processing', 'refunded'));// Admin emite reembolso

        // Desde 'on-hold' (En espera)
        $builder->addTransition(new Transition('start_processing_from_hold', 'on-hold', 'processing')); // Admin decide procesar
        $builder->addTransition(new Transition('cancel_on_hold', 'on-hold', 'cancelled'));   // Admin o cliente cancela
        $builder->addTransition(new Transition('fail_from_hold', 'on-hold', 'failed'));       // El pedido falla estando en espera (ej. no hay stock)

        // Desde 'completed' (Completado)
        $builder->addTransition(new Transition('issue_refund_completed', 'completed', 'refunded'));// Admin emite reembolso sobre un pedido ya completado

        // Desde 'cancelled' (Cancelado)
        // Podría haber transiciones para reactivar, pero son menos comunes y dependen del caso de uso.
        // $builder->addTransition(new Transition('reactivate_cancelled', 'cancelled', 'pending'));

        // Desde 'refunded' (Reembolsado)
        // Generalmente es un estado final para el flujo principal.

        // Transiciones para estados de borrador
        $builder->addTransition(new Transition('create_order_from_draft', 'draft', 'pending')); // Admin crea un pedido desde un borrador
        $builder->addTransition(new Transition('convert_checkout_to_pending', 'checkout-draft', 'pending')); // Carrito abandonado se convierte a pedido pendiente

        // Estado inicial del workflow
        $builder->setInitialPlaces(['pending']);
        // Alternativamente, podría ser 'draft' o 'checkout-draft' si los pedidos no siempre empiezan listos para pago.

        return $builder->build();
    }

    /**
     * Obtener el nombre del workflow (útil para registrarlo o referenciarlo).
     * Este método puede seguir existiendo si lo necesitas en otro lugar,
     * aunque la definición del nombre del workflow también se pasa al construir
     * el objeto StateMachine/Workflow en services.php.
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'order_workflow'; // Es común usar un nombre estático aquí
    }
}