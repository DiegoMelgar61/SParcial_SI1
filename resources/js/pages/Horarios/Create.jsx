import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function Create({ grupos, materias, docentes, aulas, diasSemana, bloquesHorarios }) {
    const { data, setData, post, processing, errors } = useForm({
        grupo_id: '',
        materia_id: '',
        docente_id: '',
        aula_id: '',
        bloque_horario_id: '',
        dia: '',
        observaciones: '',
    });

    const [grupoSeleccionado, setGrupoSeleccionado] = useState(null);

    const handleGrupoChange = (e) => {
        const grupoId = e.target.value;
        setData('grupo_id', grupoId);
        const grupo = grupos.find(g => g.id == grupoId);
        setGrupoSeleccionado(grupo);
    };

    const handleBloqueChange = (e) => {
        const bloqueId = e.target.value;
        setData('bloque_horario_id', bloqueId);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/horarios');
    };


    return (
        <AuthenticatedLayout>
            <Head title="Nuevo Horario" />

            <div className="py-6">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="mb-6">
                        <Link
                            href="/horarios"
                            className="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4"
                        >
                            <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Volver a Horarios
                        </Link>
                        <h1 className="text-3xl font-bold text-gray-900">Nuevo Horario</h1>
                        <p className="mt-1 text-sm text-gray-600">
                            Asigna un nuevo horario de clase
                        </p>
                    </div>

                    {/* Form */}
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <form onSubmit={handleSubmit}>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {/* Grupo */}
                                <div className="md:col-span-2">
                                    <label htmlFor="grupo_id" className="block text-sm font-medium text-gray-700 mb-2">
                                        Grupo <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="grupo_id"
                                        value={data.grupo_id}
                                        onChange={handleGrupoChange}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.grupo_id ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    >
                                        <option value="">Seleccionar grupo</option>
                                        {grupos.map(grupo => (
                                            <option key={grupo.id} value={grupo.id}>
                                                {grupo.codigo} - {grupo.nombre} ({grupo.carrera?.nombre}) - {grupo.turno}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.grupo_id && (
                                        <p className="mt-1 text-sm text-red-600">{errors.grupo_id}</p>
                                    )}
                                    {grupoSeleccionado && (
                                        <div className="mt-2 p-3 bg-blue-50 rounded-lg">
                                            <p className="text-sm text-blue-800">
                                                <strong>Turno:</strong> {grupoSeleccionado.turno.charAt(0).toUpperCase() + grupoSeleccionado.turno.slice(1)} | 
                                                <strong> Semestre:</strong> {grupoSeleccionado.semestre}° | 
                                                <strong> Gestión:</strong> {grupoSeleccionado.gestion_academica?.codigo}
                                            </p>
                                        </div>
                                    )}
                                </div>

                                {/* Materia */}
                                <div className="md:col-span-2">
                                    <label htmlFor="materia_id" className="block text-sm font-medium text-gray-700 mb-2">
                                        Materia <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="materia_id"
                                        value={data.materia_id}
                                        onChange={(e) => setData('materia_id', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.materia_id ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    >
                                        <option value="">Seleccionar materia</option>
                                        {materias.map(materia => (
                                            <option key={materia.id} value={materia.id}>
                                                {materia.nombre} ({materia.codigo})
                                            </option>
                                        ))}
                                    </select>
                                    {errors.materia_id && (
                                        <p className="mt-1 text-sm text-red-600">{errors.materia_id}</p>
                                    )}
                                </div>

                                {/* Docente */}
                                <div className="md:col-span-2">
                                    <label htmlFor="docente_id" className="block text-sm font-medium text-gray-700 mb-2">
                                        Docente <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="docente_id"
                                        value={data.docente_id}
                                        onChange={(e) => setData('docente_id', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.docente_id ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    >
                                        <option value="">Seleccionar docente</option>
                                        {docentes.map(docente => (
                                            <option key={docente.id} value={docente.id}>
                                                {docente.apellido_paterno} {docente.apellido_materno}, {docente.nombre}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.docente_id && (
                                        <p className="mt-1 text-sm text-red-600">{errors.docente_id}</p>
                                    )}
                                </div>

                                {/* Aula */}
                                <div className="md:col-span-2">
                                    <label htmlFor="aula_id" className="block text-sm font-medium text-gray-700 mb-2">
                                        Aula <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="aula_id"
                                        value={data.aula_id}
                                        onChange={(e) => setData('aula_id', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.aula_id ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    >
                                        <option value="">Seleccionar aula</option>
                                        {aulas.map(aula => (
                                            <option key={aula.id} value={aula.id}>
                                                {aula.nombre} - Capacidad: {aula.capacidad}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.aula_id && (
                                        <p className="mt-1 text-sm text-red-600">{errors.aula_id}</p>
                                    )}
                                </div>

                                {/* Separador */}
                                <div className="md:col-span-2">
                                    <hr className="my-4" />
                                    <h3 className="text-lg font-semibold text-gray-900 mb-4">Horario de Clase</h3>
                                </div>

                                {/* Día de la semana */}
                                <div>
                                    <label htmlFor="dia" className="block text-sm font-medium text-gray-700 mb-2">
                                        Día de la Semana <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="dia"
                                        value={data.dia}
                                        onChange={(e) => setData('dia', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.dia ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    >
                                        <option value="">Seleccionar día</option>
                                        {Object.entries(diasSemana).map(([key, value]) => (
                                            <option key={key} value={key}>{value}</option>
                                        ))}
                                    </select>
                                    {errors.dia && (
                                        <p className="mt-1 text-sm text-red-600">{errors.dia}</p>
                                    )}
                                </div>

                                {/* Bloque Horario */}
                                <div>
                                    <label htmlFor="bloque_horario_id" className="block text-sm font-medium text-gray-700 mb-2">
                                        Bloque Horario <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="bloque_horario_id"
                                        value={data.bloque_horario_id}
                                        onChange={handleBloqueChange}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.bloque_horario_id ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    >
                                        <option value="">Seleccionar bloque horario</option>
                                        {bloquesHorarios.map((bloque) => (
                                            <option key={bloque.id} value={bloque.id}>
                                                {bloque.nombre} ({bloque.hora_inicio?.substring(0, 5) || ''} - {bloque.hora_fin?.substring(0, 5) || ''})
                                            </option>
                                        ))}
                                    </select>
                                    {errors.bloque_horario_id && (
                                        <p className="mt-1 text-sm text-red-600">{errors.bloque_horario_id}</p>
                                    )}
                                </div>

                                {/* Observaciones */}
                                <div className="md:col-span-2">
                                    <label htmlFor="observaciones" className="block text-sm font-medium text-gray-700 mb-2">
                                        Observaciones
                                    </label>
                                    <textarea
                                        id="observaciones"
                                        value={data.observaciones}
                                        onChange={(e) => setData('observaciones', e.target.value)}
                                        rows={4}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.observaciones ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Información adicional sobre este horario..."
                                    />
                                    {errors.observaciones && (
                                        <p className="mt-1 text-sm text-red-600">{errors.observaciones}</p>
                                    )}
                                </div>
                            </div>

                            {/* Buttons */}
                            <div className="flex items-center justify-end space-x-3 mt-6">
                                <Link
                                    href="/horarios"
                                    className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {processing ? 'Guardando...' : 'Guardar Horario'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}