<?php

namespace WooCodePlugin\Tests\Workflow\Config;

use PHPUnit\Framework\TestCase;
use WooCodePlugin\Workflow\Config\OrderWorkflow;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Transition;

class OrderWorkflowDefinitionTest extends TestCase
{
    public function testGetDefinitionCreatesValidDefinition(): void
    {
        $definition = OrderWorkflow::getDefinition();

        $this->assertInstanceOf(Definition::class, $definition);

        // Verificar estados esperados
        $expectedPlaces = [
            'pending', 'failed', 'processing', 'completed', 'on-hold', 'cancelled', 'refunded', 'draft', 'checkout-draft'
        ];
        $this->assertEqualsCanonicalizing($expectedPlaces, $definition->getPlaces());

        // Verificar estado inicial
        $this->assertEquals(['pending'], $definition->getInitialPlaces());

        // Verificar una transición específica como ejemplo
        $processPaymentTransition = null;
        foreach ($definition->getTransitions() as $transition) {
            if ($transition->getName() === 'process_payment') {
                $processPaymentTransition = $transition;
                break;
            }
        }
        $this->assertNotNull($processPaymentTransition, "Transición 'process_payment' no encontrada.");
        $this->assertInstanceOf(Transition::class, $processPaymentTransition);
        $this->assertEquals(['pending'], $processPaymentTransition->getFroms());
        $this->assertEquals(['processing'], $processPaymentTransition->getTos());

        // Puedes añadir más aserciones para otras transiciones importantes
    }

    public function testGetNameReturnsCorrectName(): void
    {
        $this->assertEquals('order_workflow', OrderWorkflow::getName());
    }
}