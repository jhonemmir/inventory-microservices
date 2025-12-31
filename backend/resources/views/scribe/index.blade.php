<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Laravel API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
                    body .content .php-example code { display: none; }
                    body .content .python-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.6.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.6.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;,&quot;php&quot;,&quot;python&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                                            <button type="button" class="lang-button" data-language-name="php">php</button>
                                            <button type="button" class="lang-button" data-language-name="python">python</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-gestion-de-inventario" class="tocify-header">
                <li class="tocify-item level-1" data-unique="gestion-de-inventario">
                    <a href="#gestion-de-inventario">Gestión de Inventario</a>
                </li>
                                    <ul id="tocify-subheader-gestion-de-inventario" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="gestion-de-inventario-GETapi-products--id-">
                                <a href="#gestion-de-inventario-GETapi-products--id-">Consultar Producto</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="gestion-de-inventario-POSTapi-products--id--restock">
                                <a href="#gestion-de-inventario-POSTapi-products--id--restock">Reabastecer Stock</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-gestion-de-ventas" class="tocify-header">
                <li class="tocify-item level-1" data-unique="gestion-de-ventas">
                    <a href="#gestion-de-ventas">Gestión de Ventas</a>
                </li>
                                    <ul id="tocify-subheader-gestion-de-ventas" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="gestion-de-ventas-POSTapi-sales">
                                <a href="#gestion-de-ventas-POSTapi-sales">Registrar Nueva Venta</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-sistema" class="tocify-header">
                <li class="tocify-item level-1" data-unique="sistema">
                    <a href="#sistema">Sistema</a>
                </li>
                                    <ul id="tocify-subheader-sistema" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="sistema-POSTapi-reset">
                                <a href="#sistema-POSTapi-reset">Reiniciar Simulación</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: December 31, 2025</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<p>API REST para sistema de inventario con predicción de stock mediante IA. Arquitectura de microservicios con Laravel (backend), Python (AI Worker), RabbitMQ (mensajería), Redis (caché) y MySQL (base de datos).</p>
<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>
<pre><code># Sistema de Inventario Inteligente

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

&lt;aside&gt;Al hacer scroll, verás ejemplos de código para trabajar con la API en diferentes lenguajes de programación en el área oscura a la derecha (o como parte del contenido en móvil).
Puedes cambiar el lenguaje usado con las pestañas en la esquina superior derecha (o desde el menú de navegación en la parte superior izquierda en móvil).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>This API is not authenticated.</p>

        <h1 id="gestion-de-inventario">Gestión de Inventario</h1>

    <p>APIs para consultar y gestionar productos, stock y predicciones de agotamiento.</p>
<p>Las predicciones de IA se almacenan tanto en Redis (para acceso rápido) como en MySQL
(para persistencia). El endpoint muestra de dónde proviene la predicción actual.</p>

                                <h2 id="gestion-de-inventario-GETapi-products--id-">Consultar Producto</h2>

<p>
</p>

<p>Obtiene la información actual del producto, incluyendo la predicción de agotamiento de stock
calculada por el AI Worker y la fuente de los datos (Redis o MySQL).</p>
<p>La respuesta incluye:</p>
<ul>
<li>Información básica del producto (nombre, precio, stock actual)</li>
<li>Predicción de IA sobre cuándo se agotará el inventario</li>
<li>Fuente de la predicción (Redis para acceso rápido o MySQL como respaldo)</li>
</ul>

<span id="example-requests-GETapi-products--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/products/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/products/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/products/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost/api/products/1'
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-products--id-">
            <blockquote>
            <p>Example response (200, Producto con predicción en Redis):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;product&quot;: &quot;Camisa Laravel&quot;,
    &quot;price&quot;: &quot;25.00&quot;,
    &quot;stock&quot;: 35,
    &quot;ai_prediction&quot;: &quot;Se agota en 13.5 d&iacute;as&quot;,
    &quot;source&quot;: &quot;Redis (RAM)&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (200, Producto con predicción en MySQL):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;product&quot;: &quot;Pantal&oacute;n Django&quot;,
    &quot;price&quot;: &quot;45.00&quot;,
    &quot;stock&quot;: 20,
    &quot;ai_prediction&quot;: &quot;⚠️ &iexcl;CR&Iacute;TICO! Se agota hoy&quot;,
    &quot;source&quot;: &quot;MySQL (Disk)&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (200, Producto sin predicción aún):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;product&quot;: &quot;Zapatos FastAPI&quot;,
    &quot;price&quot;: &quot;60.00&quot;,
    &quot;stock&quot;: 50,
    &quot;ai_prediction&quot;: &quot;Calculando...&quot;,
    &quot;source&quot;: &quot;MySQL (Disk)&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Producto no encontrado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;No query results for model [App\\Models\\Product] 999&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-products--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-products--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-products--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-products--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-products--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-products--id-" data-method="GET"
      data-path="api/products/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-products--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-products--id-"
                    onclick="tryItOut('GETapi-products--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-products--id-"
                    onclick="cancelTryOut('GETapi-products--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-products--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/products/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-products--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-products--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-products--id-"
               value="1"
               data-component="url">
    <br>
<p>ID del producto a consultar. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="gestion-de-inventario-POSTapi-products--id--restock">Reabastecer Stock</h2>

<p>
</p>

<p>Agrega unidades al inventario del producto y dispara un evento a RabbitMQ para que
el AI Worker recalcule la predicción de agotamiento basándose en el nuevo stock.</p>
<p>Este endpoint es útil para simular reabastecimientos y ver cómo afectan a las
predicciones de la IA sobre el agotamiento del inventario.</p>

<span id="example-requests-POSTapi-products--id--restock">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/products/1/restock" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"quantity\": 20
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/products/1/restock"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "quantity": 20
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/products/1/restock';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'quantity' =&gt; 20,
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost/api/products/1/restock'
payload = {
    "quantity": 20
}
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('POST', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-POSTapi-products--id--restock">
            <blockquote>
            <p>Example response (200, Stock reabastecido exitosamente):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Stock actualizado&quot;,
    &quot;new_stock&quot;: 70
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Producto no encontrado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;No query results for model [App\\Models\\Product] 999&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Validación fallida):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;The given data was invalid.&quot;,
    &quot;errors&quot;: {
        &quot;quantity&quot;: [
            &quot;The quantity field is required.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-products--id--restock" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-products--id--restock"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-products--id--restock"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-products--id--restock" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-products--id--restock">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-products--id--restock" data-method="POST"
      data-path="api/products/{id}/restock"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-products--id--restock', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-products--id--restock"
                    onclick="tryItOut('POSTapi-products--id--restock');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-products--id--restock"
                    onclick="cancelTryOut('POSTapi-products--id--restock');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-products--id--restock"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/products/{id}/restock</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-products--id--restock"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-products--id--restock"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="POSTapi-products--id--restock"
               value="1"
               data-component="url">
    <br>
<p>ID del producto a reabastecer. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>quantity</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="quantity"                data-endpoint="POSTapi-products--id--restock"
               value="20"
               data-component="body">
    <br>
<p>Cantidad de unidades a agregar al stock. Debe ser mayor a 0. Example: <code>20</code></p>
        </div>
        </form>

                <h1 id="gestion-de-ventas">Gestión de Ventas</h1>

    <p>Endpoints para procesar transacciones de ventas y eventos asíncronos.</p>
<p>Cuando se registra una venta, el sistema:</p>
<ol>
<li>Guarda la transacción en MySQL</li>
<li>Actualiza el stock del producto</li>
<li>Envía un evento a RabbitMQ para procesamiento asíncrono por el AI Worker</li>
<li>El AI Worker calcula y actualiza la predicción de agotamiento de stock</li>
</ol>

                                <h2 id="gestion-de-ventas-POSTapi-sales">Registrar Nueva Venta</h2>

<p>
</p>

<p>Este endpoint recibe una orden de compra, la guarda en MySQL y envía un evento asíncrono
a RabbitMQ ('sales_queue') para que el AI Worker procese la predicción de stock.</p>
<p>El evento en RabbitMQ contiene información sobre la venta y permite al worker de IA
recalcular las predicciones de cuándo se agotará el inventario basándose en la velocidad de ventas.</p>

<span id="example-requests-POSTapi-sales">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/sales" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"product_id\": 1,
    \"quantity\": 5
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/sales"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "product_id": 1,
    "quantity": 5
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/sales';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'product_id' =&gt; 1,
            'quantity' =&gt; 5,
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost/api/sales'
payload = {
    "product_id": 1,
    "quantity": 5
}
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('POST', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-POSTapi-sales">
            <blockquote>
            <p>Example response (201, Venta registrada exitosamente):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Venta registrada y notificada a la IA&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 15,
        &quot;product_id&quot;: 1,
        &quot;quantity&quot;: 5,
        &quot;created_at&quot;: &quot;2024-01-15T10:30:00.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2024-01-15T10:30:00.000000Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Producto no encontrado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;No query results for model [App\\Models\\Product] 999&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Validación fallida):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;The given data was invalid.&quot;,
    &quot;errors&quot;: {
        &quot;product_id&quot;: [
            &quot;The product id field is required.&quot;
        ],
        &quot;quantity&quot;: [
            &quot;The quantity must be at least 1.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-sales" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-sales"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-sales"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-sales" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-sales">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-sales" data-method="POST"
      data-path="api/sales"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-sales', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-sales"
                    onclick="tryItOut('POSTapi-sales');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-sales"
                    onclick="cancelTryOut('POSTapi-sales');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-sales"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/sales</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-sales"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-sales"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>product_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="product_id"                data-endpoint="POSTapi-sales"
               value="1"
               data-component="body">
    <br>
<p>ID del producto a vender. Debe existir en la base de datos. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>quantity</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="quantity"                data-endpoint="POSTapi-sales"
               value="5"
               data-component="body">
    <br>
<p>Cantidad de unidades a vender. Debe ser mayor a 0. Example: <code>5</code></p>
        </div>
        </form>

                <h1 id="sistema">Sistema</h1>

    

                                <h2 id="sistema-POSTapi-reset">Reiniciar Simulación</h2>

<p>
</p>

<p>⚠️ <strong>ZONA DE PELIGRO</strong> ⚠️</p>
<p>Este endpoint borra todo el historial de ventas en MySQL, restablece el stock del producto
con ID 1 a 50 unidades, elimina la predicción de ventas y borra la memoria caché de Redis.</p>
<p><strong>Se usa únicamente para reiniciar la demo desde cero.</strong> No usar en producción.</p>
<p>Acciones realizadas:</p>
<ul>
<li>Elimina todas las ventas registradas (truncate en tabla <code>sales</code>)</li>
<li>Restablece el stock del producto ID 1 a 50 unidades</li>
<li>Elimina la predicción de ventas del producto</li>
<li>Borra la caché de Redis para la predicción del producto</li>
</ul>

<span id="example-requests-POSTapi-reset">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/reset" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/reset"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/api/reset';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://localhost/api/reset'
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json'
}

response = requests.request('POST', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-POSTapi-reset">
            <blockquote>
            <p>Example response (200, Simulación reiniciada exitosamente):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Simulaci&oacute;n reiniciada&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-reset" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-reset"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-reset"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-reset" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-reset">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-reset" data-method="POST"
      data-path="api/reset"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-reset', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-reset"
                    onclick="tryItOut('POSTapi-reset');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-reset"
                    onclick="cancelTryOut('POSTapi-reset');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-reset"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/reset</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-reset"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-reset"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                                                        <button type="button" class="lang-button" data-language-name="php">php</button>
                                                        <button type="button" class="lang-button" data-language-name="python">python</button>
                            </div>
            </div>
</div>
</body>
</html>
