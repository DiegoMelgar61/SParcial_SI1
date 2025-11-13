import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout';

export default function Create({ carreras }) {
    const { data, setData, post, processing, errors } = useForm({
        carrera_id: '',
        sigla: '',
        codigo: '',
        nombre: '',
        nombre_corto: '',
        descripcion: '',
        semestre: '',
        horas_semanales: '',
        creditos: '',
        es_electiva: false,
        requiere_laboratorio: false,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/materias');
    };

    return (
        <AuthenticatedLayout>
            <Head title="Nueva Materia" />

            <div className="py-6">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="mb-6">
                        <Link
                            href="/materias"
                            className="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4"
                        >
                            <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Volver a Materias
                        </Link>
                        <h1 className="text-3xl font-bold text-gray-900">Nueva Materia</h1>
                        <p className="mt-1 text-sm text-gray-600">
                            Completa los datos para registrar una nueva materia
                        </p>
                    </div>

                    {/* Form */}
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <form onSubmit={handleSubmit}>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                        Sigla
                                    </label>
                                    <input
                                        type="text"
                                        id="sigla"
                                        value={data.sigla}
                                        onChange={(e) => setData('sigla', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.sigla ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: PROG1"
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
                                        onChange={(e) => setData('codigo', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.codigo ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: INF-111"
                                    />
                                    {errors.codigo && (
                                        <p className="mt-1 text-sm text-red-600">{errors.codigo}</p>
                                    )}
                                </div>

                                {/* Nombre */}
                                <div className="md:col-span-2">
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
                                        placeholder="Ej: Programación I"
                                    />
                                    {errors.nombre && (
                                        <p className="mt-1 text-sm text-red-600">{errors.nombre}</p>
                                    )}
                                </div>

                                {/* Nombre Corto */}
                                <div>
                                    <label htmlFor="nombre_corto" className="block text-sm font-medium text-gray-700 mb-2">
                                        Nombre Corto
                                    </label>
                                    <input
                                        type="text"
                                        id="nombre_corto"
                                        value={data.nombre_corto}
                                        onChange={(e) => setData('nombre_corto', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.nombre_corto ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: Prog I"
                                    />
                                    {errors.nombre_corto && (
                                        <p className="mt-1 text-sm text-red-600">{errors.nombre_corto}</p>
                                    )}
                                </div>

                                {/* Semestre */}
                                <div>
                                    <label htmlFor="semestre" className="block text-sm font-medium text-gray-700 mb-2">
                                        Semestre <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="semestre"
                                        value={data.semestre}
                                        onChange={(e) => setData('semestre', e.target.value)}
                                        min="1"
                                        max="12"
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.semestre ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: 1"
                                    />
                                    {errors.semestre && (
                                        <p className="mt-1 text-sm text-red-600">{errors.semestre}</p>
                                    )}
                                </div>

                                {/* Horas Semanales */}
                                <div>
                                    <label htmlFor="horas_semanales" className="block text-sm font-medium text-gray-700 mb-2">
                                        Horas Semanales <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="horas_semanales"
                                        value={data.horas_semanales}
                                        onChange={(e) => setData('horas_semanales', e.target.value)}
                                        min="1"
                                        max="40"
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.horas_semanales ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: 6"
                                    />
                                    {errors.horas_semanales && (
                                        <p className="mt-1 text-sm text-red-600">{errors.horas_semanales}</p>
                                    )}
                                </div>

                                {/* Créditos */}
                                <div>
                                    <label htmlFor="creditos" className="block text-sm font-medium text-gray-700 mb-2">
                                        Créditos <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="creditos"
                                        value={data.creditos}
                                        onChange={(e) => setData('creditos', e.target.value)}
                                        min="1"
                                        max="10"
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.creditos ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: 4"
                                    />
                                    {errors.creditos && (
                                        <p className="mt-1 text-sm text-red-600">{errors.creditos}</p>
                                    )}
                                </div>

                                {/* Checkboxes */}
                                <div className="md:col-span-2 space-y-4">
                                    <div className="flex items-center">
                                        <input
                                            type="checkbox"
                                            id="es_electiva"
                                            checked={data.es_electiva}
                                            onChange={(e) => setData('es_electiva', e.target.checked)}
                                            className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <label htmlFor="es_electiva" className="ml-2 block text-sm text-gray-700">
                                            Es materia electiva
                                        </label>
                                    </div>

                                    <div className="flex items-center">
                                        <input
                                            type="checkbox"
                                            id="requiere_laboratorio"
                                            checked={data.requiere_laboratorio}
                                            onChange={(e) => setData('requiere_laboratorio', e.target.checked)}
                                            className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <label htmlFor="requiere_laboratorio" className="ml-2 block text-sm text-gray-700">
                                            Requiere laboratorio
                                        </label>
                                    </div>
                                </div>

                                {/* Descripción */}
                                <div className="md:col-span-2">
                                    <label htmlFor="descripcion" className="block text-sm font-medium text-gray-700 mb-2">
                                        Descripción
                                    </label>
                                    <textarea
                                        id="descripcion"
                                        value={data.descripcion}
                                        onChange={(e) => setData('descripcion', e.target.value)}
                                        rows={4}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.descripcion ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Breve descripción de la materia..."
                                    />
                                    {errors.descripcion && (
                                        <p className="mt-1 text-sm text-red-600">{errors.descripcion}</p>
                                    )}
                                </div>
                            </div>

                            {/* Buttons */}
                            <div className="flex items-center justify-end space-x-3 mt-6">
                                <Link
                                    href="/materias"
                                    className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {processing ? 'Guardando...' : 'Guardar Materia'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}