document.addEventListener('DOMContentLoaded', () => {
    console.log('📘 reportes.js activo');

    const clock = document.getElementById('clock');
    if (clock) {
        const updateClock = () => {
            const now = new Date();
            clock.textContent = now.toLocaleString('es-BO', { dateStyle: 'medium', timeStyle: 'short' });
        };
        updateClock();
        setInterval(updateClock, 60000);
    }

    // --- Ver reportes en la web ---
    document.getElementById('btn-ver-asistencia')?.addEventListener('click', () => {
        window.location.href = '/reportes/asistencia/ver';
    });

    document.getElementById('btn-ver-licencia')?.addEventListener('click', () => {
        window.location.href = '/reportes/licencia/ver';
    });

    // --- Descargas ---
    document.getElementById('btn-descargar-asistencia')?.addEventListener('click', () => {
        f_abrir_modal('asistencia');
    });

    document.getElementById('btn-descargar-licencia')?.addEventListener('click', () => {
        f_abrir_modal('licencia');
    });

    // --- Modal de descarga ---
    const modal = document.createElement('div');
    modal.innerHTML = `
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" id="modal-download">
            <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">Descargar reporte</h3>
                    <p class="text-sm text-gray-500">Seleccione el formato de descarga</p>
                </div>
                <div class="p-6 flex justify-center gap-4">
                    <button id="btn-pdf" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium">PDF</button>
                    <button id="btn-excel" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium">Excel</button>
                </div>
                <div class="p-4 border-t border-gray-100 text-center">
                    <button id="btn-cerrar" class="text-sm text-gray-600 hover:text-indigo-600 font-medium">Cancelar</button>
                </div>
            </div>
        </div>`;
    
    document.body.appendChild(modal);
    modal.classList.add('hidden');

    let tipoActual = '';

    function f_abrir_modal(tipo) {
        tipoActual = tipo;
        modal.classList.remove('hidden');
    }

    document.getElementById('btn-cerrar').addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    document.getElementById('btn-pdf').addEventListener('click', () => {
        window.open(`/api/reportes/${tipoActual}/pdf`, '_blank');
        modal.classList.add('hidden');
    });

    document.getElementById('btn-excel').addEventListener('click', () => {
        window.open(`/api/reportes/${tipoActual}/excel`, '_blank');
        modal.classList.add('hidden');
    });
});
