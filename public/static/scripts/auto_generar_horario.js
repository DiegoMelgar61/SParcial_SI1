/**
 * AUTO GENERAR HORARIO - JAVASCRIPT
 * Sistema inteligente de generación automática de horarios
 */

// ===================== ESTADO GLOBAL =====================
let datosGlobales = {
    gestionId: null,
    materias: [],
    grupos: [],
    docentes: [],
    aulas: [],
    horarios: [],
    materiaGrupos: [], // Combinaciones materia-grupo existentes
    asignacionesTemporales: {} // Para almacenar asignaciones en proceso
};

// ===================== INICIALIZACIÓN =====================
document.addEventListener('DOMContentLoaded', () => {
    inicializarEventos();
    obtenerCSRFToken();
});

function inicializarEventos() {
    // Selector de gestión
    const selectGestion = document.getElementById('select-gestion');
    selectGestion?.addEventListener('change', handleGestionChange);

    // Botones principales
    document.getElementById('btn-cargar-datos')?.addEventListener('click', cargarDatos);
    
    // GENERACIÓN AUTOMÁTICA DESHABILITADA - TODO: Habilitar cuando funcione correctamente
    // document.getElementById('btn-generar-auto')?.addEventListener('click', generarHorarioAutomatico);
    
    document.getElementById('btn-asignacion-manual')?.addEventListener('click', mostrarPanelManual);
    document.getElementById('btn-ver-horario')?.addEventListener('click', verHorarioGenerado);
    document.getElementById('btn-cerrar-modal-horario')?.addEventListener('click', cerrarModalHorario);
}

function handleGestionChange(e) {
    const gestionId = e.target.value;
    const btnCargar = document.getElementById('btn-cargar-datos');
    
    if (gestionId) {
        btnCargar.disabled = false;
        datosGlobales.gestionId = gestionId;
    } else {
        btnCargar.disabled = true;
        datosGlobales.gestionId = null;
        ocultarPanelDatos();
    }
}

// ===================== CSRF TOKEN =====================
function obtenerCSRFToken() {
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    return tokenMeta ? tokenMeta.getAttribute('content') : '';
}

// ===================== CARGAR DATOS =====================
async function cargarDatos() {
    if (!datosGlobales.gestionId) {
        mostrarAlerta('Por favor seleccione una gestión', 'warning');
        return;
    }

    mostrarLoader('Cargando datos...');

    try {
        const response = await fetch(`/auto/generar-horario/datos?gestion_id=${datosGlobales.gestionId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': obtenerCSRFToken()
            }
        });

        const data = await response.json();

        if (data.success) {
            datosGlobales.materias = data.materias || [];
            datosGlobales.grupos = data.grupos || [];
            datosGlobales.docentes = data.docentes || [];
            datosGlobales.aulas = data.aulas || [];
            datosGlobales.horarios = data.horarios || [];
            datosGlobales.materiaGrupos = data.materia_grupos || [];

            actualizarEstadisticas();
            // cargarSelectoresGeneracionAuto(); // DESHABILITADO - Generación automática no disponible
            mostrarPanelDatos();
            mostrarAlerta('Datos cargados exitosamente', 'success');
        } else {
            mostrarAlerta(data.message || 'Error al cargar datos', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarAlerta('Error de conexión al cargar datos', 'error');
    } finally {
        ocultarLoader();
    }
}

function cargarSelectoresGeneracionAuto() {
    // Cargar materias
    const selectMateria = document.getElementById('auto-materia');
    selectMateria.innerHTML = '<option value="">-- Seleccione materia --</option>';
    datosGlobales.materias.forEach(m => {
        selectMateria.innerHTML += `<option value="${m.sigla}" data-carga="${m.carga_horaria}">${m.nombre} (${m.sigla}) - ${m.carga_horaria} hrs</option>`;
    });

    // Cargar grupos
    const selectGrupo = document.getElementById('auto-grupo');
    selectGrupo.innerHTML = '<option value="">-- Seleccione grupo --</option>';
    datosGlobales.grupos.forEach(g => {
        selectGrupo.innerHTML += `<option value="${g.sigla}">${g.sigla}</option>`;
    });

    // Cargar docentes
    const selectDocente = document.getElementById('auto-docente');
    selectDocente.innerHTML = '<option value="">-- Seleccione docente --</option>';
    datosGlobales.docentes.forEach(d => {
        selectDocente.innerHTML += `<option value="${d.codigo}">${d.nomb_comp}</option>`;
    });
}

function actualizarEstadisticas() {
    document.getElementById('stat-materias').textContent = datosGlobales.materias.length;
    document.getElementById('stat-docentes').textContent = datosGlobales.docentes.length;
    document.getElementById('stat-aulas').textContent = datosGlobales.aulas.length;
    document.getElementById('stat-horarios').textContent = datosGlobales.horarios.length;
}

function mostrarPanelDatos() {
    document.getElementById('panel-datos')?.classList.remove('hidden');
}

function ocultarPanelDatos() {
    document.getElementById('panel-datos')?.classList.add('hidden');
}

// ===================== GENERACIÓN AUTOMÁTICA =====================
// DESHABILITADO TEMPORALMENTE - Funcionalidad en desarrollo
// TODO: Revisar y habilitar cuando esté completamente funcional
/*
function cargarSelectoresGeneracionAuto() {
    // Cargar materias
    const selectMateria = document.getElementById('auto-materia');
    if (!selectMateria) return;
    selectMateria.innerHTML = '<option value="">-- Seleccione materia --</option>';
    datosGlobales.materias.forEach(m => {
        selectMateria.innerHTML += `<option value="${m.sigla}" data-carga="${m.carga_horaria}">${m.nombre} (${m.sigla}) - ${m.carga_horaria} hrs</option>`;
    });

    // Cargar grupos
    const selectGrupo = document.getElementById('auto-grupo');
    if (!selectGrupo) return;
    selectGrupo.innerHTML = '<option value="">-- Seleccione grupo --</option>';
    datosGlobales.grupos.forEach(g => {
        selectGrupo.innerHTML += `<option value="${g.sigla}">${g.sigla}</option>`;
    });

    // Cargar docentes
    const selectDocente = document.getElementById('auto-docente');
    if (!selectDocente) return;
    selectDocente.innerHTML = '<option value="">-- Seleccione docente --</option>';
    datosGlobales.docentes.forEach(d => {
        selectDocente.innerHTML += `<option value="${d.codigo}">${d.nomb_comp}</option>`;
    });
}

async function generarHorarioAutomatico() {
    const materiaSelect = document.getElementById('auto-materia');
    const grupoSigla = document.getElementById('auto-grupo').value;
    const docenteCodigo = document.getElementById('auto-docente').value;

    if (!materiaSelect.value || !grupoSigla || !docenteCodigo) {
        mostrarAlerta('Complete todos los campos (Materia, Grupo y Docente)', 'warning');
        return;
    }

    const materiaSigla = materiaSelect.value;
    const cargaHoraria = parseInt(materiaSelect.selectedOptions[0].dataset.carga);
    const nombreMateria = materiaSelect.selectedOptions[0].text;

    // Calcular horas semanales necesarias según carga horaria
    let horasSemanales = 0;
    let tipoAsignacion = '';
    
    if (cargaHoraria === 135) {
        horasSemanales = 4.5;
        tipoAsignacion = 'Estándar (60% materias)';
    } else if (cargaHoraria > 135) {
        horasSemanales = 6;
        tipoAsignacion = 'Con laboratorio (25% materias)';
    } else if (cargaHoraria === 90) {
        // Puede ser 3, 3.75 o 5.25
        horasSemanales = 3;
        tipoAsignacion = 'Electiva (10% materias)';
    } else {
        // Calcular proporción
        horasSemanales = Math.round((cargaHoraria / 30) * 10) / 10;
        tipoAsignacion = 'Personalizado';
    }

    const mensaje = `Se generarán horarios para:\n\n` +
                   `Materia: ${nombreMateria}\n` +
                   `Grupo: ${grupoSigla}\n` +
                   `Carga Total: ${cargaHoraria} horas\n` +
                   `Horas Semanales: ${horasSemanales} hrs\n` +
                   `Tipo: ${tipoAsignacion}\n\n` +
                   `El sistema buscará automáticamente:\n` +
                   `✓ Combinaciones de horarios en DIFERENTES DÍAS\n` +
                   `✓ Aulas DISTINTAS para cada clase\n` +
                   `✓ Sin conflictos de horarios\n\n` +
                   `¿Desea continuar?`;

    if (!confirm(mensaje)) {
        return;
    }

    mostrarLoader('Generando horarios automáticamente...');

    try {
        const response = await fetch('/auto/generar-horario/generar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': obtenerCSRFToken()
            },
            body: JSON.stringify({
                gestion_id: datosGlobales.gestionId,
                materia_sigla: materiaSigla,
                grupo_sigla: grupoSigla,
                docente_codigo: docenteCodigo,
                horas_semanales: horasSemanales,
                carga_horaria: cargaHoraria
            })
        });

        const data = await response.json();

        if (data.success) {
            mostrarResultadoGeneracion(data.resultado);
            mostrarAlerta(`Horarios generados exitosamente: ${data.resultado.clases_generadas} clases`, 'success');
            
            // Limpiar selectores
            document.getElementById('auto-materia').value = '';
            document.getElementById('auto-grupo').value = '';
            document.getElementById('auto-docente').value = '';
        } else {
            mostrarAlerta(data.message || 'Error al generar horario', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarAlerta('Error de conexión al generar horario', 'error');
    } finally {
        ocultarLoader();
    }
}

function mostrarResultadoGeneracion(resultado) {
    const panelResultado = document.getElementById('panel-resultado');
    const contenido = document.getElementById('resultado-contenido');

    let html = `
        <div class="space-y-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="font-semibold text-green-800 mb-2">✅ Generación Completada</h4>
                <div class="text-sm text-green-700 space-y-1">
                    <p><strong>Clases generadas:</strong> ${resultado.clases_generadas}</p>
                    <p><strong>Horas semanales:</strong> ${resultado.horas_semanales} hrs</p>
                    <p class="text-xs mt-2">✓ Distribuidas en diferentes días</p>
                    <p class="text-xs">✓ Aulas únicas para cada clase</p>
                    <p class="text-xs">✓ Sin conflictos de horarios</p>
                </div>
            </div>
    `;

    if (resultado.advertencias && resultado.advertencias.length > 0) {
        html += `
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h4 class="font-semibold text-yellow-800 mb-2">⚠️ Advertencias</h4>
                <ul class="text-sm text-yellow-700 list-disc list-inside">
                    ${resultado.advertencias.map(adv => `<li>${adv}</li>`).join('')}
                </ul>
            </div>
        `;
    }

    if (resultado.errores && resultado.errores.length > 0) {
        html += `
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="font-semibold text-red-800 mb-2">❌ Errores</h4>
                <ul class="text-sm text-red-700 list-disc list-inside">
                    ${resultado.errores.map(err => `<li>${err}</li>`).join('')}
                </ul>
            </div>
        `;
    }

    html += `
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-700">
                    💡 <strong>Consejo:</strong> Use el botón "Ver Horario Generado" para visualizar todas las clases asignadas.
                </p>
            </div>
        </div>
    `;

    contenido.innerHTML = html;
    panelResultado.classList.remove('hidden');
    
    // Scroll hacia el resultado
    panelResultado.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
*/
// FIN GENERACIÓN AUTOMÁTICA DESHABILITADA

// ===================== ASIGNACIÓN MANUAL =====================
function mostrarPanelManual() {
    const panelManual = document.getElementById('panel-manual');
    
    if (panelManual.classList.contains('hidden')) {
        cargarSelectoresAsignacionManual();
        panelManual.classList.remove('hidden');
    } else {
        panelManual.classList.add('hidden');
    }
}

function cargarSelectoresAsignacionManual() {
    // Cargar selectores de materia, grupo, docente
    const selectMateria = document.getElementById('manual-materia');
    selectMateria.innerHTML = '<option value="">-- Seleccione materia --</option>';
    datosGlobales.materias.forEach(m => {
        selectMateria.innerHTML += `<option value="${m.sigla}" data-carga="${m.carga_horaria}" data-nombre="${m.nombre}">${m.nombre} (${m.sigla}) - ${m.carga_horaria} hrs</option>`;
    });

    const selectGrupo = document.getElementById('manual-grupo');
    selectGrupo.innerHTML = '<option value="">-- Seleccione grupo --</option>';
    datosGlobales.grupos.forEach(g => {
        selectGrupo.innerHTML += `<option value="${g.sigla}">${g.sigla}</option>`;
    });

    const selectDocente = document.getElementById('manual-docente');
    selectDocente.innerHTML = '<option value="">-- Seleccione docente --</option>';
    datosGlobales.docentes.forEach(d => {
        selectDocente.innerHTML += `<option value="${d.codigo}">${d.nomb_comp}</option>`;
    });

    // Cargar los 4 selectores de horarios
    for (let i = 1; i <= 4; i++) {
        const selectHorario = document.getElementById(`manual-horario-${i}`);
        selectHorario.innerHTML = '<option value="">-- Opcional --</option>';
        datosGlobales.horarios.forEach(h => {
            const horas = parseFloat(h.duracion_horas).toFixed(1);
            selectHorario.innerHTML += `<option value="${h.id}" data-dia="${h.dia}" data-duracion="${h.duracion_horas}">${h.dia} ${h.hora_i}-${h.hora_f} (${horas}hrs)</option>`;
        });

        // Agregar evento para calcular horas al cambiar
        selectHorario.addEventListener('change', calcularHorasManual);
    }

    // Botón de asignar
    const btnAsignar = document.getElementById('btn-asignar-manual');
    btnAsignar.removeEventListener('click', asignarClaseManual);
    btnAsignar.addEventListener('click', asignarClaseManual);
}

function calcularHorasManual() {
    const horariosSeleccionados = [];
    const diasUsados = [];
    let horasSemanales = 0;

    for (let i = 1; i <= 4; i++) {
        const select = document.getElementById(`manual-horario-${i}`);
        if (select.value) {
            const option = select.selectedOptions[0];
            const duracion = parseFloat(option.dataset.duracion || 0);
            const dia = option.dataset.dia;
            
            horasSemanales += duracion;
            horariosSeleccionados.push(select.value);
            diasUsados.push(dia);
        }
    }

    // Mostrar info si hay al menos un horario
    const infoHoras = document.getElementById('info-horas-manual');
    if (horariosSeleccionados.length > 0) {
        infoHoras.classList.remove('hidden');
        document.getElementById('horas-seleccionadas').textContent = horasSemanales.toFixed(1);

        // Validar días diferentes
        const diasUnicos = new Set(diasUsados);
        if (diasUsados.length !== diasUnicos.size) {
            document.getElementById('validacion-horas-mensaje').innerHTML = 
                '<span class="text-red-600">⚠️ ERROR: Todos los horarios deben ser en días DIFERENTES</span>';
            return;
        }

        // Validar según materia seleccionada
        const selectMateria = document.getElementById('manual-materia');
        if (selectMateria.value) {
            const cargaHoraria = parseInt(selectMateria.selectedOptions[0].dataset.carga);
            const nombreMateria = selectMateria.selectedOptions[0].dataset.nombre.toUpperCase();
            const esModalidadGraduacion = nombreMateria.includes('MODALIDAD DE GRADUACION') || 
                                          nombreMateria.includes('MODALIDAD GRADUACION');

            let mensaje = '';
            let valido = true;

            if (esModalidadGraduacion) {
                if (horasSemanales < 5 || horasSemanales > 7) {
                    mensaje = `⚠️ Modalidad de Graduación requiere entre 5-7 hrs/semana`;
                    valido = false;
                } else {
                    mensaje = `✅ Válido para Modalidad de Graduación (5-7 hrs permitidas)`;
                }
            } else if (cargaHoraria == 135) {
                if (Math.abs(horasSemanales - 4.5) > 0.25) {
                    mensaje = `⚠️ Materias de 135 hrs requieren exactamente 4.5 hrs/semana`;
                    valido = false;
                } else {
                    mensaje = `✅ Válido para 135 hrs de carga (4.5 hrs/semana)`;
                }
            } else if (cargaHoraria > 135) {
                if (Math.abs(horasSemanales - 6) > 0.25) {
                    mensaje = `⚠️ Materias con >135 hrs requieren exactamente 6 hrs/semana`;
                    valido = false;
                } else {
                    mensaje = `✅ Válido para >135 hrs de carga (6 hrs/semana)`;
                }
            } else if (cargaHoraria == 90) {
                if (Math.abs(horasSemanales - 3) > 0.25) {
                    mensaje = `⚠️ Materias de 90 hrs requieren exactamente 3 hrs/semana`;
                    valido = false;
                } else {
                    mensaje = `✅ Válido para 90 hrs de carga (3 hrs/semana)`;
                }
            }

            const color = valido ? 'text-green-600' : 'text-red-600';
            document.getElementById('validacion-horas-mensaje').innerHTML = 
                `<span class="${color}">${mensaje}</span>`;
        }
    } else {
        infoHoras.classList.add('hidden');
    }
}

async function asignarClaseManual() {
    const materiaSigla = document.getElementById('manual-materia').value;
    const grupoSigla = document.getElementById('manual-grupo').value;
    const docenteCodigo = document.getElementById('manual-docente').value;

    if (!materiaSigla || !grupoSigla || !docenteCodigo) {
        mostrarAlerta('Complete materia, grupo y docente', 'warning');
        return;
    }

    // Recoger horarios seleccionados
    const horarioIds = [];
    for (let i = 1; i <= 4; i++) {
        const valor = document.getElementById(`manual-horario-${i}`).value;
        horarioIds.push(valor || null);
    }

    // Filtrar solo los que tienen valor
    const horariosValidos = horarioIds.filter(id => id !== null);
    if (horariosValidos.length === 0) {
        mostrarAlerta('Seleccione al menos un horario', 'warning');
        return;
    }

    mostrarLoader('Asignando clases manualmente...');

    try {
        const response = await fetch('/auto/generar-horario/asignar-clase', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': obtenerCSRFToken()
            },
            body: JSON.stringify({
                sigla_materia: materiaSigla,
                sigla_grupo: grupoSigla,
                docente_codigo: docenteCodigo,
                horario_ids: horarioIds,
                gestion_id: datosGlobales.gestionId
            })
        });

        const data = await response.json();

        if (data.success) {
            mostrarAlerta(data.message, 'success');
            
            // Limpiar formulario
            document.getElementById('manual-materia').value = '';
            document.getElementById('manual-grupo').value = '';
            document.getElementById('manual-docente').value = '';
            for (let i = 1; i <= 4; i++) {
                document.getElementById(`manual-horario-${i}`).value = '';
            }
            document.getElementById('info-horas-manual').classList.add('hidden');
        } else {
            mostrarAlerta(data.message || 'Error al asignar clases', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarAlerta('Error de conexión al asignar clases', 'error');
    } finally {
        ocultarLoader();
    }
}

// ===================== VER HORARIO GENERADO =====================
async function verHorarioGenerado() {
    // Intentar obtener gestionId del selector si no está en datosGlobales
    const selectGestion = document.getElementById('select-gestion');
    const gestionId = datosGlobales.gestionId || selectGestion?.value;
    
    console.log('🔍 Ver horario - gestionId:', gestionId);
    
    if (!gestionId) {
        mostrarAlerta('Seleccione una gestión primero', 'warning');
        return;
    }

    mostrarLoader('Cargando horario...');

    try {
        const url = `/auto/generar-horario/ver/${gestionId}`;
        console.log('📡 Llamando a:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': obtenerCSRFToken()
            }
        });

        console.log('📥 Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('📊 Datos recibidos:', data);

        if (data.success) {
            if (!data.clases || data.clases.length === 0) {
                ocultarLoader();
                mostrarAlerta('No hay clases generadas para esta gestión todavía', 'info');
                return;
            }
            console.log('✅ Renderizando', data.clases.length, 'clases');
            renderizarHorarioGenerado(data.clases);
            document.getElementById('modal-horario')?.classList.remove('hidden');
        } else {
            mostrarAlerta(data.message || 'Error al cargar horario', 'error');
        }
    } catch (error) {
        console.error('❌ Error al cargar horario:', error);
        mostrarAlerta('Error de conexión al cargar horario. Revise la consola para más detalles.', 'error');
    } finally {
        ocultarLoader();
    }
}

function renderizarHorarioGenerado(clases) {
    const contenedor = document.getElementById('contenedor-horario-generado');
    
    console.log('🎨 Renderizando horario');
    console.log('📦 Contenedor encontrado:', contenedor ? 'Sí' : 'No');
    console.log('📚 Total clases:', clases?.length || 0);
    
    if (!contenedor) {
        console.error('❌ No se encontró el contenedor #contenedor-horario-generado');
        mostrarAlerta('Error: No se encontró el contenedor del horario', 'error');
        return;
    }
    
    if (!clases || clases.length === 0) {
        contenedor.innerHTML = '<p class="text-center text-gray-500 py-8">No hay clases asignadas para esta gestión</p>';
        console.log('ℹ️ No hay clases para mostrar');
        return;
    }

    // ========================================================================
    // AGRUPAR CLASES POR MATERIA-GRUPO
    // ========================================================================
    // Estructura: { "MAT101-F1": [clase1, clase2, ...], ... }
    const gruposMaterias = {};
    
    clases.forEach(clase => {
        const key = `${clase.sigla_materia}-${clase.sigla_grupo}`;
        if (!gruposMaterias[key]) {
            gruposMaterias[key] = {
                sigla_materia: clase.sigla_materia,
                sigla_grupo: clase.sigla_grupo,
                nombre_materia: clase.nombre_materia,
                docente: clase.docente,
                semestre: clase.semestre,
                horarios: []
            };
        }
        gruposMaterias[key].horarios.push({
            dia: clase.dia,
            hora_i: clase.hora_i,
            hora_f: clase.hora_f,
            nro_aula: clase.nro_aula,
            tipo_aula: clase.tipo_aula
        });
    });

    // ========================================================================
    // RENDERIZAR TABLA AGRUPADA
    // ========================================================================
    let html = `
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-collapse">
                <thead class="bg-gray-800 text-white sticky top-0">
                    <tr>
                        <th class="border border-gray-300 px-3 py-2 text-center font-semibold">SEM</th>
                        <th class="border border-gray-300 px-3 py-2 text-center font-semibold">SIGLA</th>
                        <th class="border border-gray-300 px-3 py-2 text-center font-semibold">GR</th>
                        <th class="border border-gray-300 px-3 py-2 text-left font-semibold">NOMBRE MATERIA</th>
                        <th class="border border-gray-300 px-3 py-2 text-left font-semibold">NOMBRE DOCENTE</th>
                        <th class="border border-gray-300 px-3 py-2 text-center font-semibold">DÍA</th>
                        <th class="border border-gray-300 px-3 py-2 text-center font-semibold">HORA</th>
                        <th class="border border-gray-300 px-3 py-2 text-center font-semibold">DÍA</th>
                        <th class="border border-gray-300 px-3 py-2 text-center font-semibold">HORA</th>
                        <th class="border border-gray-300 px-3 py-2 text-center font-semibold">DÍA</th>
                        <th class="border border-gray-300 px-3 py-2 text-center font-semibold">HORA</th>
                        <th class="border border-gray-300 px-3 py-2 text-center font-semibold">DÍA</th>
                        <th class="border border-gray-300 px-3 py-2 text-center font-semibold">HORA</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
    `;

    // Ordenar grupos por semestre y luego por sigla
    const gruposOrdenados = Object.entries(gruposMaterias).sort((a, b) => {
        const semestreA = a[1].semestre;
        const semestreB = b[1].semestre;
        if (semestreA !== semestreB) return semestreA - semestreB;
        return a[1].sigla_materia.localeCompare(b[1].sigla_materia);
    });

    gruposOrdenados.forEach(([key, grupo], index) => {
        const bgColor = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
        
        // Ordenar horarios por día
        const diasOrden = { 'Lun': 1, 'Mar': 2, 'Mie': 3, 'Jue': 4, 'Vie': 5, 'Sab': 6, 'Dom': 7 };
        const horariosOrdenados = grupo.horarios.sort((a, b) => {
            return (diasOrden[a.dia] || 99) - (diasOrden[b.dia] || 99);
        });

        html += `
            <tr class="${bgColor} hover:bg-blue-50">
                <td class="border border-gray-300 px-2 py-2 text-center font-semibold">${grupo.semestre}</td>
                <td class="border border-gray-300 px-2 py-2 text-center font-mono font-semibold text-indigo-700">${grupo.sigla_materia}</td>
                <td class="border border-gray-300 px-2 py-2 text-center font-bold text-purple-600">${grupo.sigla_grupo}</td>
                <td class="border border-gray-300 px-2 py-2 font-medium">${grupo.nombre_materia}</td>
                <td class="border border-gray-300 px-2 py-2 text-gray-700">${grupo.docente}</td>
        `;

        // Agregar hasta 4 horarios (columnas de día y hora)
        for (let i = 0; i < 4; i++) {
            if (i < horariosOrdenados.length) {
                const h = horariosOrdenados[i];
                // Mapear días a abreviatura de 3 letras
                const diaMap = {
                    'Lun': 'Lun', 'Mar': 'Mar', 'Mie': 'Mie', 
                    'Jue': 'Jue', 'Vie': 'Vie', 'Sab': 'Sab'
                };
                const diaAbrev = diaMap[h.dia] || h.dia;
                
                html += `
                    <td class="border border-gray-300 px-2 py-2 text-center font-semibold text-green-700">${diaAbrev}</td>
                    <td class="border border-gray-300 px-2 py-2 text-center text-xs">
                        <div class="font-medium">${h.hora_i}-${h.hora_f}</div>
                        <div class="text-gray-500 text-[10px]">Aula ${h.nro_aula}</div>
                    </td>
                `;
            } else {
                // Celdas vacías si no hay más horarios
                html += `
                    <td class="border border-gray-300 px-2 py-2 bg-gray-100"></td>
                    <td class="border border-gray-300 px-2 py-2 bg-gray-100"></td>
                `;
            }
        }

        html += `</tr>`;
    });

    html += `
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex justify-between items-center text-sm text-gray-600">
            <div>
                <strong>Total de materias-grupo:</strong> ${gruposOrdenados.length}
            </div>
            <div>
                <strong>Total de clases:</strong> ${clases.length}
            </div>
        </div>
    `;

    contenedor.innerHTML = html;
    console.log(`✅ Horario renderizado: ${gruposOrdenados.length} materias-grupo, ${clases.length} clases totales`);
}

function cerrarModalHorario() {
    document.getElementById('modal-horario')?.classList.add('hidden');
}

// ===================== UTILIDADES =====================
function mostrarAlerta(mensaje, tipo = 'info') {
    const iconos = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    
    const colores = {
        success: 'bg-green-100 border-green-400 text-green-700',
        error: 'bg-red-100 border-red-400 text-red-700',
        warning: 'bg-yellow-100 border-yellow-400 text-yellow-700',
        info: 'bg-blue-100 border-blue-400 text-blue-700'
    };

    // Crear alerta visual en lugar de alert()
    const alertaDiv = document.createElement('div');
    alertaDiv.className = `fixed top-4 right-4 z-50 ${colores[tipo]} border px-4 py-3 rounded-lg shadow-lg max-w-md animate-fade-in`;
    alertaDiv.innerHTML = `
        <div class="flex items-start">
            <span class="text-2xl mr-3">${iconos[tipo]}</span>
            <div class="flex-1">
                <p class="font-medium">${mensaje}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-xl font-bold hover:opacity-70">×</button>
        </div>
    `;
    
    document.body.appendChild(alertaDiv);
    
    // Auto-eliminar después de 5 segundos
    setTimeout(() => {
        alertaDiv.remove();
    }, 5000);
}

function mostrarLoader(mensaje = 'Cargando...') {
    let loader = document.getElementById('loader-overlay');
    
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'loader-overlay';
        loader.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
        loader.innerHTML = `
            <div class="bg-white rounded-lg p-6 shadow-xl flex items-center space-x-4">
                <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-700 font-medium" id="loader-message">${mensaje}</span>
            </div>
        `;
        document.body.appendChild(loader);
    } else {
        document.getElementById('loader-message').textContent = mensaje;
        loader.classList.remove('hidden');
    }
}

function ocultarLoader() {
    const loader = document.getElementById('loader-overlay');
    if (loader) {
        loader.classList.add('hidden');
    }
}

// ========================================
// MÓDULO ADICIONAL: Sidebar Toggle y Reloj
// ========================================

// Verificar que el contenedor existe al cargar
document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('contenedor-horario-generado');
    console.log('✅ Contenedor horario encontrado al cargar:', contenedor ? 'Sí' : 'No');
    
    // Toggle sidebar en móviles
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    menuToggle?.addEventListener('click', () => {
        if (sidebar) sidebar.classList.toggle('-translate-x-full');
        if (overlay) overlay.classList.toggle('hidden');
    });

    overlay?.addEventListener('click', () => {
        if (sidebar) sidebar.classList.add('-translate-x-full');
        if (overlay) overlay.classList.add('hidden');
    });
});

// Reloj en tiempo real
function updateClock() {
    const now = new Date();
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    };
    const clockElement = document.getElementById('clock');
    if (clockElement) {
        clockElement.textContent = now.toLocaleDateString('es-ES', options);
    }
}
setInterval(updateClock, 1000);
updateClock();
