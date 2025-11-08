import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout';

export default function Create({ estados, semestres }) {
    const { data, setData, post, processing, errors } = useForm({
        codigo: '',
        nombre: '',
        anio: new Date().getFullYear(),
        semestre: '',
        fecha_inicio: '',
        fecha_fin: '',
        fecha_inicio_inscripciones: '',
        fecha_fin_inscripciones: '',
        fecha_inicio_clases: '',
        fecha_fin_clases: '',
        estado: 'planificacion',
        observaciones: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/gestiones-academicas');
    };

    return (
        <AuthenticatedLayout>
            <Head title="Nueva Gestión Académica" />

            <div className="py-6">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="mb-6">
                        <Link
                            href="/gestiones-academicas"
                            className="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4"
                        >
                            <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Volver a Semestres
                        </Link>
                        <h1 className="text-3xl font-bold text-gray-900">Nueva Gestión Académica</h1>
                        <p className="mt-1 text-sm text-gray-600">
                            Completa los datos para registrar un nuevo período académico
                        </p>
                    </div>

                    {/* Form */}
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <form onSubmit={handleSubmit}>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {/* Código */}
                                <div>
                                    <label htmlFor="codigo" className="block text-sm font-medium text-gray-700 mb-2">
                                        Código <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="codigo"
                                        value={data.codigo}
                                        onChange={(e) => setData('codigo', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.codigo ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: 2025-II"
                                    />
                                    {errors.codigo && (
                                        <p className="mt-1 text-sm text-red-600">{errors.codigo}</p>
                                    )}
                                </div>

                                {/* Nombre */}
                                <div>
                                    <label htmlFor="nombre" className="block text-sm font-medium text-gray-700 mb-2">
                                        Nombre <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="nombre"
                                        value={data.nombre}
                                        onChange={(e) => setData('nombre', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.nombre ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: Segundo Semestre 2025"
                                    />
                                    {errors.nombre && (
                                        <p className="mt-1 text-sm text-red-600">{errors.nombre}</p>
                                    )}
                                </div>

                                {/* Año */}
                                <div>
                                    <label htmlFor="anio" className="block text-sm font-medium text-gray-700 mb-2">
                                        Año <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="anio"
                                        value={data.anio}
                                        onChange={(e) => setData('anio', e.target.value)}
                                        min="2020"
                                        max="2100"
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.anio ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    />
                                    {errors.anio && (
                                        <p className="mt-1 text-sm text-red-600">{errors.anio}</p>
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
                                        {Object.entries(semestres).map(([key, value]) => (
                                            <option key={key} value={key}>{value}</option>
                                        ))}
                                    </select>
                                    {errors.semestre && (
                                        <p className="mt-1 text-sm text-red-600">{errors.semestre}</p>
                                    )}
                                </div>

                                {/* Estado */}
                                <div>
                                    <label htmlFor="estado" className="block text-sm font-medium text-gray-700 mb-2">
                                        Estado <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="estado"
                                        value={data.estado}
                                        onChange={(e) => setData('estado', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.estado ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    >
                                        {Object.entries(estados).map(([key, value]) => (
                                            <option key={key} value={key}>{value}</option>
                                        ))}
                                    </select>
                                    {errors.estado && (
                                        <p className="mt-1 text-sm text-red-600">{errors.estado}</p>
                                    )}
                                </div>

                                {/* Separador */}
                                <div className="md:col-span-2">
                                    <hr className="my-4" />
                                    <h3 className="text-lg font-semibold text-gray-900 mb-4">Fechas del Período Académico</h3>
                                </div>

                                {/* Fecha Inicio */}
                                <div>
                                    <label htmlFor="fecha_inicio" className="block text-sm font-medium text-gray-700 mb-2">
                                        Fecha de Inicio <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        id="fecha_inicio"
                                        value={data.fecha_inicio}
                                        onChange={(e) => setData('fecha_inicio', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.fecha_inicio ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    />
                                    {errors.fecha_inicio && (
                                        <p className="mt-1 text-sm text-red-600">{errors.fecha_inicio}</p>
                                    )}
                                </div>

                                {/* Fecha Fin */}
                                <div>
                                    <label htmlFor="fecha_fin" className="block text-sm font-medium text-gray-700 mb-2">
                                        Fecha de Fin <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        id="fecha_fin"
                                        value={data.fecha_fin}
                                        onChange={(e) => setData('fecha_fin', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.fecha_fin ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    />
                                    {errors.fecha_fin && (
                                        <p className="mt-1 text-sm text-red-600">{errors.fecha_fin}</p>
                                    )}
                                </div>

                                {/* Separador */}
                                <div className="md:col-span-2">
                                    <hr className="my-4" />
                                    <h3 className="text-lg font-semibold text-gray-900 mb-4">Fechas de Inscripciones</h3>
                                </div>

                                {/* Inicio Inscripciones */}
                                <div>
                                    <label htmlFor="fecha_inicio_inscripciones" className="block text-sm font-medium text-gray-700 mb-2">
                                        Inicio de Inscripciones
                                    </label>
                                    <input
                                        type="date"
                                        id="fecha_inicio_inscripciones"
                                        value={data.fecha_inicio_inscripciones}
                                        onChange={(e) => setData('fecha_inicio_inscripciones', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.fecha_inicio_inscripciones ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    />
                                    {errors.fecha_inicio_inscripciones && (
                                        <p className="mt-1 text-sm text-red-600">{errors.fecha_inicio_inscripciones}</p>
                                    )}
                                </div>

                                {/* Fin Inscripciones */}
                                <div>
                                    <label htmlFor="fecha_fin_inscripciones" className="block text-sm font-medium text-gray-700 mb-2">
                                        Fin de Inscripciones
                                    </label>
                                    <input
                                        type="date"
                                        id="fecha_fin_inscripciones"
                                        value={data.fecha_fin_inscripciones}
                                        onChange={(e) => setData('fecha_fin_inscripciones', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.fecha_fin_inscripciones ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    />
                                    {errors.fecha_fin_inscripciones && (
                                        <p className="mt-1 text-sm text-red-600">{errors.fecha_fin_inscripciones}</p>
                                    )}
                                </div>

                                {/* Separador */}
                                <div className="md:col-span-2">
                                    <hr className="my-4" />
                                    <h3 className="text-lg font-semibold text-gray-900 mb-4">Fechas de Clases</h3>
                                </div>

                                {/* Inicio Clases */}
                                <div>
                                    <label htmlFor="fecha_inicio_clases" className="block text-sm font-medium text-gray-700 mb-2">
                                        Inicio de Clases
                                    </label>
                                    <input
                                        type="date"
                                        id="fecha_inicio_clases"
                                        value={data.fecha_inicio_clases}
                                        onChange={(e) => setData('fecha_inicio_clases', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.fecha_inicio_clases ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    />
                                    {errors.fecha_inicio_clases && (
                                        <p className="mt-1 text-sm text-red-600">{errors.fecha_inicio_clases}</p>
                                    )}
                                </div>

                                {/* Fin Clases */}
                                <div>
                                    <label htmlFor="fecha_fin_clases" className="block text-sm font-medium text-gray-700 mb-2">
                                        Fin de Clases
                                    </label>
                                    <input
                                        type="date"
                                        id="fecha_fin_clases"
                                        value={data.fecha_fin_clases}
                                        onChange={(e) => setData('fecha_fin_clases', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.fecha_fin_clases ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    />
                                    {errors.fecha_fin_clases && (
                                        <p className="mt-1 text-sm text-red-600">{errors.fecha_fin_clases}</p>
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
                                        placeholder="Información adicional sobre la gestión académica..."
                                    />
                                    {errors.observaciones && (
                                        <p className="mt-1 text-sm text-red-600">{errors.observaciones}</p>
                                    )}
                                </div>
                            </div>

                            {/* Buttons */}
                            <div className="flex items-center justify-end space-x-3 mt-6">
                                <Link
                                    href="/gestiones-academicas"
                                    className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {processing ? 'Guardando...' : 'Guardar Gestión'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}