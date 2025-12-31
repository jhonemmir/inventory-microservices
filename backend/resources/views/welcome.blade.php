<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory AI Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Scrollbar oscura para la terminal */
        #terminal-log::-webkit-scrollbar { width: 8px; }
        #terminal-log::-webkit-scrollbar-track { background: #1f2937; }
        #terminal-log::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen p-8">

    <div class="max-w-6xl mx-auto mb-6 p-6 bg-gray-800 border-b border-gray-700 rounded-xl flex justify-between items-center shadow-lg">
        <div>
            <h2 class="text-xl font-bold text-gray-100 flex items-center gap-2">
                📦 Control de Inventario
                <span class="text-xs font-normal px-2 py-1 bg-blue-900 text-blue-200 rounded-full border border-blue-700">Microservicios</span>
            </h2>
            <p class="text-gray-400 text-sm mt-1">Sistema Híbrido: Laravel + Python + Redis</p>
        </div>
        
        <button onclick="resetSimulation()" class="text-xs bg-red-900/50 hover:bg-red-900 text-red-200 border border-red-700 px-3 py-2 rounded transition flex items-center gap-2">
            <span>🗑️</span> Reset Demo
        </button>
    </div>    

    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="md:col-span-1 space-y-6">
            
            <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">📦 Producto #001</h2>
                    <span id="data-source" class="text-xs px-2 py-1 rounded border bg-gray-700 text-gray-400">Loading...</span>
                </div>
                
                <div class="text-center py-4">
                    <p class="text-gray-400 text-sm uppercase tracking-wider">Stock Actual</p>
                    <p id="stock-count" class="text-6xl font-bold text-white tracking-tighter">--</p>
                </div>

                <div id="ai-box" class="bg-indigo-900/30 border border-indigo-500/50 rounded-lg p-3 mt-4 transition-all duration-300">
                    <p class="text-xs text-indigo-400 font-bold uppercase flex items-center gap-1">
                        🤖 IA Prediction
                    </p>
                    <p id="prediction-text" class="text-sm font-medium mt-1">Cargando...</p>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-4">
                    <button id="btn-sell" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-2 rounded-lg transition shadow-lg shadow-indigo-500/30 active:scale-95 flex justify-center items-center gap-2 text-sm">
                        <span>💸 Vender (5)</span>
                    </button>

                    <button id="btn-restock" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 px-2 rounded-lg transition shadow-lg shadow-emerald-500/30 active:scale-95 flex justify-center items-center gap-2 text-sm">
                        <span>🚛 Surtir (20)</span>
                    </button>
                </div>
                <p class="text-xs text-center text-gray-500 mt-3">Eventos asíncronos vía RabbitMQ</p>
            </div>

            <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 p-6">
                <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">Infrastructure Status</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm">🐬 MySQL Database</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">Connected</span>
                            <span class="w-3 h-3 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm">⚡ Redis Cache</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">Hit</span>
                            <span class="w-3 h-3 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm">🐰 RabbitMQ Worker</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">Listening</span>
                            <span class="w-3 h-3 rounded-full bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">
            
            <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 p-6">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                    📉 Tendencia de Stock (Live)
                    <span class="text-xs font-normal text-gray-500 border border-gray-600 px-2 rounded">Real-time</span>
                </h3>
                <div id="stockChart" style="min-height: 250px;"></div>
            </div>

            <div class="bg-black rounded-xl shadow-lg border border-gray-800 p-4 font-mono text-sm h-48 overflow-y-auto" id="terminal-log">
                <div class="text-green-500">root@inventory-system:~$ ./init_dashboard.sh</div>
                <div class="text-gray-400 mt-1">[INFO] Connecting to RabbitMQ exchange... [OK]</div>
                <div class="text-gray-400">[INFO] Redis cache warming up... [OK]</div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>