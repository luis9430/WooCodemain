<?php
// src/Core/Plugin.php
namespace WooCodePlugin\Core; // Asegúrate que este sea el namespace que estás usando aquí.

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference; // Puede que ya no sea necesario aquí directamente

class Plugin {
    /**
     * @var ContainerBuilder
     */
    private $container;

    public function __construct() {
        $this->container = new ContainerBuilder();
        $this->setup_container();
    }

    private function setup_container() {
        if (defined('MI_PLUGIN_PATH') && file_exists(MI_PLUGIN_PATH . 'config/services.php')) {
            $globalServicesConfigurator = require MI_PLUGIN_PATH . 'config/services.php';
            if (is_callable($globalServicesConfigurator)) {
                // Asume que config/services.php también devuelve una función que acepta ContainerBuilder
                $globalServicesConfigurator($this->container);
            }
        }

        // Cargar servicios específicos del Workflow
        // MI_PLUGIN_PATH debería estar definido en tu archivo Plugin.php raíz.
        $workflowServicesPath = '';
        if (defined('MI_PLUGIN_PATH')) {
            $workflowServicesPath = MI_PLUGIN_PATH . 'src/Workflow/Config/services.php';
        } else {
                  error_log("MI_PLUGIN_PATH no está definido al intentar cargar services.php de workflow.");
            return;
        }
        
        if (file_exists($workflowServicesPath)) {
            $workflowConfigurator = require $workflowServicesPath;
            if (is_callable($workflowConfigurator)) {
                // La línea 49 (o alrededor) donde ocurría el error estaba intentando
                // procesar un array de factorías. Ahora llamamos a la función configuradora.
                $workflowConfigurator($this->container); // $this->container es el ContainerBuilder
            }
        } else {
            error_log("No se encontró el archivo de servicios de workflow en: " . $workflowServicesPath);
        }

    }

    /**
     * Obtener servicio del contenedor
     */
    public function get($id) {
        if (!$this->container->has($id)) {
            // Opcional: Log o lanzar una excepción más informativa.
            error_log("Servicio no encontrado en el contenedor: " . $id);
            // throw new \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException($id);
            return null; 
        }
        try {
            return $this->container->get($id);
        } catch (\Symfony\Component\DependencyInjection\Exception\ExceptionInterface $e) {
            error_log("Error al obtener el servicio '$id': " . $e->getMessage());
            // throw $e; // O manejar de otra forma
            return null;
        }
    }

    /**
     * Obtener el contenedor
     */
    public function get_container(): ContainerBuilder { // Es bueno tipar el retorno
        return $this->container;
    }
}