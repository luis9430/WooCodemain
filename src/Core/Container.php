<?php

namespace WooCodePlugin\Core;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Simple DI Container implementing PSR-11.
 * Wraps Symfony's ContainerBuilder for more complex scenarios if needed,
 * or acts as a simple service locator.
 */
class Container implements PsrContainerInterface
{
    private ContainerBuilder $symfonyContainer;
    private array $services = [];
    private array $factories = [];

    public function __construct()
    {
        $this->symfonyContainer = new ContainerBuilder();
        // Puedes registrar algunos servicios base aquí si es necesario
        $this->symfonyContainer->set(ContainerBuilder::class, $this->symfonyContainer);
        $this->symfonyContainer->set(PsrContainerInterface::class, $this);
    }

    public function get(string $id)
    {
        if (isset($this->factories[$id])) {
            // Cache the service instance after first creation
            if (!isset($this->services[$id])) {
                $this->services[$id] = $this->factories[$id]($this);
            }
            return $this->services[$id];
        }

        if ($this->symfonyContainer->has($id)) {
            try {
                return $this->symfonyContainer->get($id);
            } catch (ServiceNotFoundException $e) {
                // Fallback or re-throw with more context if needed
                throw new \WooCodePlugin\Exception\ServiceNotFoundException(sprintf('Service "%s" not found in Symfony container: %s', $id, $e->getMessage()), 0, $e);
            }
        }
        
        if (!$this->has($id)) {
            throw new \WooCodePlugin\Exception\ServiceNotFoundException(sprintf('Service "%s" not found in custom container.', $id));
        }

        return $this->services[$id]; // Should not be reached if factory is not set and not in symfony container
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]) || isset($this->factories[$id]) || $this->symfonyContainer->has($id);
    }

    /**
     * Set a service instance directly.
     *
     * @param string $id
     * @param mixed  $service
     * @return void
     */
    public function set(string $id, $service): void
    {
        if (is_callable($service) && !is_object($service)) { // Check if it's a factory closure but not an already instantiated callable object
            $this->factories[$id] = $service;
            unset($this->services[$id]); // Remove any cached instance
        } else {
            $this->services[$id] = $service;
            unset($this->factories[$id]); // Remove any factory if a direct instance is set
        }
    }
    
    /**
     * Access Symfony's ContainerBuilder for more advanced configuration
     * like loading service definition files, compiler passes etc.
     *
     * @return ContainerBuilder
     */
    public function getSymfonyContainerBuilder(): ContainerBuilder
    {
        return $this->symfonyContainer;
    }
}