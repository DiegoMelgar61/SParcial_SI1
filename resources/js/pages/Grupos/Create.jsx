import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout';

export default function Create({ gestiones, carreras, turnos }) {
    const { data, setData, post, processing, errors } = useForm({
        gestion_academica_id: '',
        carrera_id: '',
        sigla: '',
        codigo: '',
        nombre: '',
        semestre: '',
        capacidad: 30,
        turno: '',
        observaciones: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/grupos');
    };

    return (
        <AuthenticatedLayout>
            <Head title="Nuevo Grupo" />

            <div className="py-6">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="mb-6">
                        <Link
                            href="/grupos"
                            className="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4"
                        >
                            <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Volver a Grupos
                        </Link>
                        <h1 className="text-3xl font-bold text-gray-900">Nuevo Grupo</h1>
                        <p className="mt-1 text-sm text-gray-600">
                            Completa los datos para registrar un nuevo grupo académico
                        </p>
                    </div>

                    {/* Form */}
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <form onSubmit={handleSubmit}>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {/* Gestión Académica */}
                                <div className="md:col-span-2">
                                    <label htmlFor="gestion_academica_id" className="block text-sm font-medium text-gray-700 mb-2">
                                        Gestión Académica <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="gestion_academica_id"
                                        value={data.gestion_academica_id}
                                        onChange={(e) => setData('gestion_academica_id', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.gestion_academica_id ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    >
                                        <option value="">Seleccionar gestión académica</option>
                                        {gestiones.map(gestion => (
                                            <option key={gestion.id} value={gestion.id}>
                                                {gestion.nombre} ({gestion.codigo})
                                            </option>
                                        ))}
                                    </select>
                                    {errors.gestion_academica_id && (
                                        <p className="mt-1 text-sm text-red-600">{errors.gestion_academica_id}</p>
                                    )}
                                </div>

                                {/* Carrera */}
                                <div className="md:col-span-2">
                                    <label htmlFor="carrera_id" className="block text-sm font-medium text-gray-700 mb-2">
                                        Carrera <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="carrera_id"
                                        value={data.carrera_id}
                                        onChange={(e) => setData('carrera_id', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.carrera_id ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    >
                                        <option value="">Seleccionar carrera</option>
                                        {carreras.map(carrera => (
                                            <option key={carrera.id} value={carrera.id}>
                                                {carrera.nombre}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.carrera_id && (
                                        <p className="mt-1 text-sm text-red-600">{errors.carrera_id}</p>
                                    )}
                                </div>

                                {/* Sigla */}
                                <div>
                                    <label htmlFor="sigla" className="block text-sm font-medium text-gray-700 mb-2">
                                        Sigla <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="sigla"
                                        value={data.sigla}
                                        onChange={(e) => setData('sigla', e.target.value.toUpperCase())}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.sigla ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: G1"
                                    />
                                    {errors.sigla && (
                                        <p className="mt-1 text-sm text-red-600">{errors.sigla}</p>
                                    )}
                                </div>

                                {/* Código */}
                                <div>
                                    <label htmlFor="codigo" className="block text-sm font-medium text-gray-700 mb-2">
                                        Código <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="codigo"
                                        value={data.codigo}
                                        onChange={(e) => setData('codigo', e.target.value.toUpperCase())}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.codigo ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: ING-2025-G1"
                                    />
                                    {errors.codigo && (
                                        <p className="mt-1 text-sm text-red-600">{errors.codigo}</p>
                                    )}
                                </div>

                                {/* Nombre */}
                                <div className="md:col-span-2">
                                    <label htmlFor="nombre" className="block text-sm font-medium text-gray-700 mb-2">
                                        Nombre del Grupo <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="nombre"
                                        value={data.nombre}
                                        onChange={(e) => setData('nombre', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.nombre ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: Grupo 1 - Ingeniería de Sistemas"
                                    />
                                    {errors.nombre && (
                                        <p className="mt-1 text-sm text-red-600">{errors.nombre}</p>
                                    )}
                                </div>

                                {/* Semestre */}
                                <div>
                                    <label htmlFor="semestre" className="block text-sm font-medium text-gray-700 mb-2">
                                        Semestre <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="semestre"
                                        value={data.semestre}
                                        onChange={(e) => setData('semestre', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.semestre ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    >
                                        <option value="">Seleccionar semestre</option>
                                        {[1, 2, 3, 4, 5, 6, 7, 8, 9, 10].map(sem => (
                                            <option key={sem} value={sem}>{sem}° Semestre</option>
                                        ))}
                                    </select>
                                    {errors.semestre && (
                                        <p className="mt-1 text-sm text-red-600">{errors.semestre}</p>
                                    )}
                                </div>

                                {/* Turno */}
                                <div>
                                    <label htmlFor="turno" className="block text-sm font-medium text-gray-700 mb-2">
                                        Turno <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="turno"
                                        value={data.turno}
                                        onChange={(e) => setData('turno', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.turno ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    >
                                        <option value="">Seleccionar turno</option>
                                        {Object.entries(turnos).map(([key, value]) => (
                                            <option key={key} value={key}>{value}</option>
                                        ))}
                                    </select>
                                    {errors.turno && (
                                        <p className="mt-1 text-sm text-red-600">{errors.turno}</p>
                                    )}
                                </div>

                                {/* Capacidad */}
                                <div className="md:col-span-2">
                                    <label htmlFor="capacidad" className="block text-sm font-medium text-gray-700 mb-2">
                                        Capacidad Máxima <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="capacidad"
                                        value={data.capacidad}
                                        onChange={(e) => setData('capacidad', e.target.value)}
                                        min="1"
                                        max="100"
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.capacidad ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Número de estudiantes"
                                    />
                                    {errors.capacidad && (
                                        <p className="mt-1 text-sm text-red-600">{errors.capacidad}</p>
                                    )}
                                    <p className="mt-1 text-xs text-gray-500">Número máximo de estudiantes que pueden inscribirse</p>
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
                                        placeholder="Información adicional sobre el grupo..."
                                    />
                                    {errors.observaciones && (
                                        <p className="mt-1 text-sm text-red-600">{errors.observaciones}</p>
                                    )}
                                </div>
                            </div>

                            {/* Buttons */}
                            <div className="flex items-center justify-end space-x-3 mt-6">
                                <Link
                                    href="/grupos"
                                    className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {processing ? 'Guardando...' : 'Guardar Grupo'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}