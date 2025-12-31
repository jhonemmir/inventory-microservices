# Introduction

API REST para sistema de inventario con predicción de stock mediante IA. Arquitectura de microservicios con Laravel (backend), Python (AI Worker), RabbitMQ (mensajería), Redis (caché) y MySQL (base de datos).

<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>

    # Sistema de Inventario Inteligente
    
    Esta API permite gestionar un inventario de productos con capacidades de predicción de stock mediante inteligencia artificial.
    
    ## Arquitectura
    
    El sistema está compuesto por varios microservicios:
    
    - **Backend (Laravel)**: API REST principal que gestiona ventas, productos y stock
    - **AI Worker (Python)**: Procesa eventos de ventas desde RabbitMQ y calcula predicciones de agotamiento de stock
    - **RabbitMQ**: Sistema de mensajería asíncrona para eventos de ventas
    - **Redis**: Caché en memoria para predicciones de IA
    - **MySQL**: Base de datos persistente para productos y ventas
    
    ## Flujo de Trabajo
    
    1. Se registra una venta mediante `POST /api/sales`
    2. El stock se actualiza en MySQL
    3. Se envía un evento a RabbitMQ
    4. El AI Worker procesa el evento y calcula cuándo se agotará el stock
    5. La predicción se almacena en MySQL y Redis para acceso rápido
    
    ## Endpoints Principales
    
    - **Ventas**: Registrar nuevas transacciones
    - **Productos**: Consultar información y predicciones de stock
    - **Inventario**: Reabastecer stock de productos
    - **Sistema**: Reiniciar simulación (útil para demos)
    
    <aside>Al hacer scroll, verás ejemplos de código para trabajar con la API en diferentes lenguajes de programación en el área oscura a la derecha (o como parte del contenido en móvil).
    Puedes cambiar el lenguaje usado con las pestañas en la esquina superior derecha (o desde el menú de navegación en la parte superior izquierda en móvil).</aside>

