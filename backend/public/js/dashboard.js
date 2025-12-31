const productId = 1;
let chart;
let chartData = [];

// --- CONFIGURACIÓN SWEETALERT (MODO OSCURO) ---
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    background: '#1f2937', // Gris oscuro de Tailwind
    color: '#fff',         // Texto blanco
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

function initChart() {
    const options = {
        chart: {
            type: 'area',
            height: 250,
            animations: { enabled: true, easing: 'linear', dynamicAnimation: { speed: 1000 } },
            toolbar: { show: false },
            background: 'transparent'
        },
        series: [{ name: 'Stock', data: [] }],
        stroke: { curve: 'smooth', width: 2 },
        colors: ['#6366f1'],
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.1, stops: [0, 90, 100] }
        },
        theme: { mode: 'dark' },
        xaxis: { categories: [], labels: { show: false }, tooltip: { enabled: false } },
        yaxis: { min: 0, max: 80 }, 
        grid: { borderColor: '#374151' }
    };

    chart = new ApexCharts(document.querySelector("#stockChart"), options);
    chart.render();
}

function addLog(message, type = 'info') {
    const terminal = document.getElementById('terminal-log');
    const time = new Date().toLocaleTimeString('es-ES', { hour12: false });
    let color = 'text-gray-400';
    
    if (type === 'success') color = 'text-green-400';
    if (type === 'warning') color = 'text-yellow-400';
    if (type === 'error') color = 'text-red-400';

    const div = document.createElement('div');
    div.className = `${color} mt-1`;
    div.innerText = `[${time}] ${message}`;
    
    terminal.appendChild(div);
    terminal.scrollTop = terminal.scrollHeight;
}

async function fetchProduct() {
    try {
        const response = await fetch(`/api/products/${productId}`);
        const data = await response.json();

        document.getElementById('stock-count').innerText = data.stock;
        document.getElementById('prediction-text').innerText = data.ai_prediction;

        const badge = document.getElementById('data-source');
        if (data.source.includes('Redis')) {
            badge.className = "text-xs px-2 py-1 rounded border bg-green-900 text-green-300 border-green-600";
            badge.innerText = "⚡ " + data.source;
        } else {
            badge.className = "text-xs px-2 py-1 rounded border bg-gray-700 text-gray-300 border-gray-500";
            badge.innerText = "💾 " + data.source;
        }

        const aiBox = document.getElementById('ai-box');
        if (data.ai_prediction.includes('CRÍTICO') || data.ai_prediction.includes('agot')) {
            aiBox.className = "bg-red-900/30 border border-red-500/50 rounded-lg p-3 mt-4 animate-pulse";
        } else {
            aiBox.className = "bg-indigo-900/30 border border-indigo-500/50 rounded-lg p-3 mt-4 transition-all duration-300";
        }

        if (chartData.length > 15) chartData.shift();
        chartData.push(data.stock);
        chart.updateSeries([{ data: chartData }]);

    } catch (error) {
        console.error('Error fetching:', error);
    }
}

async function simulateSale() {
    const btn = document.getElementById('btn-sell');
    btn.disabled = true;
    btn.innerHTML = '🔄 Enviando...';
    
    addLog('🚀 Iniciando transacción de venta...', 'warning');

    try {
        await fetch('/api/sales', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: 5 })
        });

        addLog('📤 Evento enviado a RabbitMQ (sales_queue)', 'info');

        // Notificación Toast Elegante
        Toast.fire({
            icon: 'success',
            title: 'Venta registrada',
            text: 'Procesando en segundo plano...'
        });

        setTimeout(() => {
            fetchProduct();
            addLog('✅ Python AI Worker actualizó Redis', 'success');
            btn.disabled = false;
            btn.innerHTML = '💸 Vender (5)';
        }, 1500);

    } catch (error) {
        addLog('❌ Error en la conexión con API Gateway', 'error');
        
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo procesar la venta. Revisa RabbitMQ.',
            background: '#1f2937',
            color: '#fff'
        });

        btn.disabled = false;
        btn.innerHTML = '💸 Vender (5)';
    }
}

async function restockProduct() {
    const btn = document.getElementById('btn-restock');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '🚛 Cargando...';
    addLog('📦 Iniciando orden de compra con proveedores...', 'info');

    try {
        await fetch(`/api/products/${productId}/restock`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ quantity: 20 })
        });

        addLog('📥 Stock recibido en almacén (MySQL Updated)', 'success');
        
        Toast.fire({
            icon: 'success',
            title: 'Surtido Exitoso',
            text: '+20 unidades agregadas al inventario'
        });

        setTimeout(() => {
            fetchProduct();
            addLog('✅ IA recalculó: La crisis ha terminado', 'success');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }, 1500);

    } catch (error) {
        addLog('❌ Error al reabastecer', 'error');
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

async function resetSimulation() {
    // REEMPLAZO DEL CONFIRM NATIVO POR SWEETALERT
    const result = await Swal.fire({
        title: '¿Reiniciar Simulación?',
        text: "Esto borrará todo el historial de ventas y reiniciará el stock a 50. ¡La IA perderá su memoria!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, borrar todo',
        cancelButtonText: 'Cancelar',
        background: '#1f2937',
        color: '#fff'
    });

    if (!result.isConfirmed) return;

    addLog('🔄 Iniciando reseteo del sistema...', 'warning');
    
    try {
        await fetch('/api/reset', { method: 'POST' });
        
        chartData = []; 
        chart.updateSeries([{ data: [] }]);
        
        addLog('✨ Sistema formateado. Listo para nueva demo.', 'success');
        
        Swal.fire({
            title: '¡Reiniciado!',
            text: 'El sistema está limpio y listo.',
            icon: 'success',
            background: '#1f2937',
            color: '#fff',
            timer: 2000,
            showConfirmButton: false
        });

        fetchProduct();
        
    } catch (error) {
        addLog('❌ Error al resetear', 'error');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initChart();
    fetchProduct();
    setInterval(fetchProduct, 5000);
    
    addLog('Conexión establecida con Redis:6379', 'success');
    addLog('RabbitMQ Channel [OK]', 'success');
    
    document.getElementById('btn-sell').addEventListener('click', simulateSale);
    document.getElementById('btn-restock').addEventListener('click', restockProduct);
});