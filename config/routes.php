<?php
// config/routes.php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use WooCodePlugin\Controller\Admin\ProductController; // Ejemplo de controlador para productos
use WooCodePlugin\Controller\Admin\SettingsController; // Ejemplo de controlador para ajustes

// Esta función será llamada para configurar las rutas.
return static function (RouteCollection $routes) {
    // Ruta para la lista de productos del plugin
    $routes->add('woocode_admin_products_list', new Route(
        '/products', // El path que se pasará en &route_path=/products
        ['_controller' => [ProductController::class, 'listAction']],
        [], [], '', [], ['GET']
    ));

    // Ruta para crear un nuevo producto
    $routes->add('woocode_admin_product_new', new Route(
        '/products/new',
        ['_controller' => [ProductController::class, 'newAction']],
        [], [], '', [], ['GET', 'POST']
    ));

    // Ruta para editar un producto existente
    $routes->add('woocode_admin_product_edit', new Route(
        '/products/edit/{id}', // {id} es un parámetro de ruta
        ['_controller' => [ProductController::class, 'editAction']],
        ['id' => '\d+'], // Requisito: id debe ser un entero positivo
        [], [], '', [], ['GET', 'POST']
    ));

    // Ruta para la página de ajustes del plugin
    $routes->add('woocode_admin_settings', new Route(
        '/settings',
        ['_controller' => [SettingsController::class, 'indexAction']],
        [], [], '', [], ['GET', 'POST']
    ));

    // Puedes añadir una ruta "raíz" para tu dashboard del plugin si es necesario
    $routes->add('woocode_admin_dashboard', new Route(
        '/', // Ruta para la página principal del plugin
        ['_controller' => [ProductController::class, 'dashboardAction']], // O un DashboardController dedicado
        [], [], '', [], ['GET']
    ));
};