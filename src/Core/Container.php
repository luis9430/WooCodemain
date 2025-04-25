<?php
namespace App\Core;

/**
 * Contenedor simple de dependencias
 */
class Container {
    /**
     * @var array Servicios registrados
     */
    private $services = [];
    
    /**
     * @var array Instancias creadas
     */
    private $instances = [];
    
    /**
     * Registrar un servicio
     *
     * @param string $id Identificador del servicio
     * @param mixed $service Definición del servicio (objeto o callable)
     */
    public function set($id, $service) {
        $this->services[$id] = $service;
    }
    
    /**
     * Obtener un servicio
     *
     * @param string $id Identificador del servicio
     * @return mixed El servicio solicitado
     * @throws \Exception Si el servicio no existe
     */
    public function get($id) {
        if (!isset($this->instances[$id])) {
            if (!isset($this->services[$id])) {
                throw new \Exception("Servicio '$id' no encontrado en el contenedor");
            }
            
            $this->instances[$id] = is_callable($this->services[$id]) 
                ? $this->services[$id]($this)
                : $this->services[$id];
        }
        
        return $this->instances[$id];
    }
    
    /**
     * Verificar si existe un servicio
     *
     * @param string $id Identificador del servicio
     * @return bool True si existe, false en caso contrario
     */
    public function has($id) {
        return isset($this->services[$id]);
    }
}