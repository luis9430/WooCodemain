<?php
namespace App\Core;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * La clase principal del Plugin
 */
class Plugin {
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * Constructor
     */
    public function __construct() {
        $this->container = new ContainerBuilder();
        $this->setup_container();
    }

    /**
     * Configurar el contenedor de dependencias
     */
    private function setup_container() {
        // Registrar servicios
        if (file_exists(MI_PLUGIN_PATH . 'config/services.php')) {
            require_once MI_PLUGIN_PATH . 'config/services.php';
            register_services($this->container);
        }
    }

    /**
     * Obtener servicio del contenedor
     */
    public function get($id) {
        return $this->container->get($id);
    }

    /**
     * Obtener el contenedor
     */
    public function get_container() {
        return $this->container;
    }
}