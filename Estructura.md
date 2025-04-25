mi-plugin/
├── composer.json               # Dependencias PHP
├── package.json                # Dependencias JS
├── mi-plugin.php               # Archivo principal del plugin
├── vite.config.js              # Configuración de Vite (alternativa)
├── src/                        # Código fuente PHP
|   Workflow/
|   ├── Config/              # Configuraciones de flujos de trabajo
|   │   └── OrderWorkflow.php  # Definición del flujo para pedidos
|   ├── Handler/             # Manejadores de eventos/transiciones
|   │   └── OrderStateHandler.php
|   ├── Registry.php         # Registro de flujos de trabajo 
|   └── Subscriber/          # Suscriptores a eventos de workflow
|   |    └── OrderWorkflowSubscriber.php 
│   ├── Core/                   # Núcleo del plugin
│   │   ├── Plugin.php          # Clase principal 
│   │   └── Bootstrap.php       # Inicialización
│   ├── Controller/             # Controladores MVC
│   │   └── AdminController.php # Ejemplo de controlador
│   ├── Model/                  # Modelos
│   │   ├── Entity/             # Entidades
│   │   └── Repository/         # Repositorios
│   ├── Database/               # Configuración de base de datos
│   │   ├── Migration/          # Migraciones
│   │   └── Seeds/              # Datos iniciales
│   ├── PostType/               # Definiciones de CPT
│   │   └── ProductPostType.php # Ejemplo de CPT
│   ├── Taxonomy/               # Definiciones de taxonomías
│   │   └── ProductCategory.php # Ejemplo de taxonomía
│   └── View/                   # Vistas PHP (templates)
├── assets/                     # Archivos estáticos
│   ├── js/                     # JavaScript
│   │   ├── admin/              # Scripts de admin
|   |   |    ├── pages/     
|   |   |    |     ├── Workflows/   
|   |   |    |──────────WorkflowDiagnostic.php 
│   │   │    └── index.jsx        # Punto de entrada de React 
│   │   │    └── App.jsx        # APP de  React 
│   │   └── frontend/           # Scripts de frontend
│   ├── css/                    # Estilos
│   └── images/                 # Imágenes
├── dist/                       # Archivos compilados
├── config/                     # Configuraciones
│   ├── config.php              # Configuración general
│   └── services.php            # Definición de servicios
├── templates/                  # Templates PHP
└── vendor/                     # Dependencias de Composer