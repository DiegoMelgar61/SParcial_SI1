import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout';

export default function Create({ usuarios, carreras, tiposContrato, turnos }) {
    const { data, setData, post, processing, errors } = useForm({
        usuario_id: '',
        carrera_id: '',
        codigo_empleado: '',
        titulo_academico: '',
        especializacion: '',
        tipo_contrato: '',
        fecha_contratacion: '',
        horas_semanales_max: '',
        turnos_preferidos: [],
        observaciones: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/docentes');
    };

    const handleTurnoChange = (turnoKey) => {
        const newTurnos = data.turnos_preferidos.includes(turnoKey)
            ? data.turnos_preferidos.filter(t => t !== turnoKey)
            : [...data.turnos_preferidos, turnoKey];
        setData('turnos_preferidos', newTurnos);
    };

    return (
        <AuthenticatedLayout>
            <Head title="Nuevo Docente" />

            <div className="py-6">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="mb-6">
                        <Link
                            href="/docentes"
                            className="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4"
                        >
                            <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Volver a Docentes
                        </Link>
                        <h1 className="text-3xl font-bold text-gray-900">Nuevo Docente</h1>
                        <p className="mt-1 text-sm text-gray-600">
                            Completa los datos para registrar un nuevo docente
                        </p>
                    </div>

                    {/* Form */}
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <form onSubmit={handleSubmit}>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {/* Usuario */}
                                <div className="md:col-span-2">
                                    <label htmlFor="usuario_id" className="block text-sm font-medium text-gray-700 mb-2">
                                        Usuario <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="usuario_id"
                                        value={data.usuario_id}
                                        onChange={(e) => setData('usuario_id', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.usuario_id ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    >
                                        <option value="">Seleccionar usuario</option>
                                        {usuarios.map(usuario => (
                                            <option key={usuario.id} value={usuario.id}>
                                                {usuario.nombre} {usuario.apellido} - {usuario.email}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.usuario_id && (
                                        <p className="mt-1 text-sm text-red-600">{errors.usuario_id}</p>
                                    )}
                                </div>

                                {/* Código Empleado */}
                                <div>
                                    <label htmlFor="codigo_empleado" className="block text-sm font-medium text-gray-700 mb-2">
                                        Código de Empleado <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="codigo_empleado"
                                        value={data.codigo_empleado}
                                        onChange={(e) => setData('codigo_empleado', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.codigo_empleado ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: DOC-001"
                                    />
                                    {errors.codigo_empleado && (
                                        <p className="mt-1 text-sm text-red-600">{errors.codigo_empleado}</p>
                                    )}
                                </div>

                                {/* Carrera */}
                                <div>
                                    <label htmlFor="carrera_id" className="block text-sm font-medium text-gray-700 mb-2">
                                        Carrera Principal <span className="text-red-500">*</span>
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
                                            <option key={carrera.id} value={carrera.id}>{carrera.nombre}</option>
                                        ))}
                                    </select>
                                    {errors.carrera_id && (
                                        <p className="mt-1 text-sm text-red-600">{errors.carrera_id}</p>
                                    )}
                                </div>

                                {/* Título Académico */}
                                <div>
                                    <label htmlFor="titulo_academico" className="block text-sm font-medium text-gray-700 mb-2">
                                        Título Académico <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="titulo_academico"
                                        value={data.titulo_academico}
                                        onChange={(e) => setData('titulo_academico', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.titulo_academico ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: Licenciado en Informática"
                                    />
                                    {errors.titulo_academico && (
                                        <p className="mt-1 text-sm text-red-600">{errors.titulo_academico}</p>
                                    )}
                                </div>

                                {/* Especialización */}
                                <div>
                                    <label htmlFor="especializacion" className="block text-sm font-medium text-gray-700 mb-2">
                                        Especialización
                                    </label>
                                    <input
                                        type="text"
                                        id="especializacion"
                                        value={data.especializacion}
                                        onChange={(e) => setData('especializacion', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.especializacion ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: Inteligencia Artificial"
                                    />
                                    {errors.especializacion && (
                                        <p className="mt-1 text-sm text-red-600">{errors.especializacion}</p>
                                    )}
                                </div>

                                {/* Tipo de Contrato */}
                                <div>
                                    <label htmlFor="tipo_contrato" className="block text-sm font-medium text-gray-700 mb-2">
                                        Tipo de Contrato <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="tipo_contrato"
                                        value={data.tipo_contrato}
                                        onChange={(e) => setData('tipo_contrato', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.tipo_contrato ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    >
                                        <option value="">Seleccionar tipo</option>
                                        {Object.entries(tiposContrato).map(([key, value]) => (
                                            <option key={key} value={key}>{value}</option>
                                        ))}
                                    </select>
                                    {errors.tipo_contrato && (
                                        <p className="mt-1 text-sm text-red-600">{errors.tipo_contrato}</p>
                                    )}
                                </div>

                                {/* Fecha de Contratación */}
                                <div>
                                    <label htmlFor="fecha_contratacion" className="block text-sm font-medium text-gray-700 mb-2">
                                        Fecha de Contratación <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        id="fecha_contratacion"
                                        value={data.fecha_contratacion}
                                        onChange={(e) => setData('fecha_contratacion', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.fecha_contratacion ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    />
                                    {errors.fecha_contratacion && (
                                        <p className="mt-1 text-sm text-red-600">{errors.fecha_contratacion}</p>
                                    )}
                                </div>

                                {/* Horas Semanales Máximas */}
                                <div>
                                    <label htmlFor="horas_semanales_max" className="block text-sm font-medium text-gray-700 mb-2">
                                        Horas Semanales Máximas <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="horas_semanales_max"
                                        value={data.horas_semanales_max}
                                        onChange={(e) => setData('horas_semanales_max', e.target.value)}
                                        min="1"
                                        max="48"
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.horas_semanales_max ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: 40"
                                    />
                                    {errors.horas_semanales_max && (
                                        <p className="mt-1 text-sm text-red-600">{errors.horas_semanales_max}</p>
                                    )}
                                </div>

                                {/* Turnos Preferidos */}
                                <div className="md:col-span-2">
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Turnos Preferidos <span className="text-red-500">*</span>
                                    </label>
                                    <div className="flex gap-4">
                                        {Object.entries(turnos).map(([key, value]) => (
                                            <label key={key} className="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    checked={data.turnos_preferidos.includes(key)}
                                                    onChange={() => handleTurnoChange(key)}
                                                    className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                />
                                                <span className="ml-2 text-sm text-gray-700">{value}</span>
                                            </label>
                                        ))}
                                    </div>
                                    {errors.turnos_preferidos && (
                                        <p className="mt-1 text-sm text-red-600">{errors.turnos_preferidos}</p>
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
                                        placeholder="Información adicional sobre el docente..."
                                    />
                                    {errors.observaciones && (
                                        <p className="mt-1 text-sm text-red-600">{errors.observaciones}</p>
                                    )}
                                </div>
                            </div>

                            {/* Buttons */}
                            <div className="flex items-center justify-end space-x-3 mt-6">
                                <Link
                                    href="/docentes"
                                    className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {processing ? 'Guardando...' : 'Guardar Docente'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}