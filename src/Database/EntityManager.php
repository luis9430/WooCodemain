<?php
namespace App\Database;

use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\Common\Annotations\AnnotationRegistry;

class EntityManager {
    /**
     * @var DoctrineEntityManager
     */
    private static $instance;
    
    /**
     * Obtener instancia del EntityManager
     *
     * @return DoctrineEntityManager
     */
    public static function getInstance() {
        if (self::$instance === null) {
            // Rutas de entidades
            $paths = [MI_PLUGIN_PATH . 'src/Model/Entity'];
            
            // Configuración para desarrollo o producción
            $isDevMode = defined('WP_DEBUG') && WP_DEBUG;
            
            // Obtener conexión de WordPress
            global $wpdb;
            
            // Configurar conexión para Doctrine
            $connectionParams = [
                'driver'   => 'pdo_mysql',
                'host'     => DB_HOST,
                'user'     => DB_USER,
                'password' => DB_PASSWORD,
                'dbname'   => DB_NAME,
                'charset'  => DB_CHARSET,
                'prefix'   => $wpdb->prefix,
            ];
            
            // Crear configuración
            $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
            
            // Crear EntityManager
            self::$instance = DoctrineEntityManager::create($connectionParams, $config);
        }
        
        return self::$instance;
    }
}