import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout';

export default function Edit({ aula, tiposAula }) {
    const { data, setData, put, processing, errors } = useForm({
        codigo: aula.codigo || '',
        nombre: aula.nombre || '',
        piso: aula.piso || '',
        tipo: aula.tipo || '',
        capacidad: aula.capacidad || '',
        tiene_computadoras: aula.tiene_computadoras ?? false,
        cantidad_computadoras: aula.cantidad_computadoras || '',
        observaciones: aula.observaciones || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(`/aulas/${aula.id}`);
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Editar ${aula.nombre}`} />

            <div className="py-6">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="mb-6">
                        <Link
                            href="/aulas"
                            className="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4"
                        >
                            <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Volver a Aulas
                        </Link>
                        <h1 className="text-3xl font-bold text-gray-900">Editar Aula</h1>
                        <p className="mt-1 text-sm text-gray-600">
                            Modifica los datos del aula
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
                                        placeholder="Ej: A-301"
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
                                        placeholder="Ej: Aula Magna"
                                    />
                                    {errors.nombre && (
                                        <p className="mt-1 text-sm text-red-600">{errors.nombre}</p>
                                    )}
                                </div>

                                {/* Tipo */}
                                <div>
                                    <label htmlFor="tipo" className="block text-sm font-medium text-gray-700 mb-2">
                                        Tipo de Aula <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="tipo"
                                        value={data.tipo}
                                        onChange={(e) => setData('tipo', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.tipo ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                    >
                                        <option value="">Seleccionar tipo</option>
                                        {Object.entries(tiposAula).map(([key, value]) => (
                                            <option key={key} value={key}>{value}</option>
                                        ))}
                                    </select>
                                    {errors.tipo && (
                                        <p className="mt-1 text-sm text-red-600">{errors.tipo}</p>
                                    )}
                                </div>

                                {/* Piso */}
                                <div>
                                    <label htmlFor="piso" className="block text-sm font-medium text-gray-700 mb-2">
                                        Piso <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="piso"
                                        value={data.piso}
                                        onChange={(e) => setData('piso', e.target.value)}
                                        min="0"
                                        max="20"
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.piso ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: 3"
                                    />
                                    {errors.piso && (
                                        <p className="mt-1 text-sm text-red-600">{errors.piso}</p>
                                    )}
                                </div>

                                {/* Capacidad */}
                                <div>
                                    <label htmlFor="capacidad" className="block text-sm font-medium text-gray-700 mb-2">
                                        Capacidad <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="capacidad"
                                        value={data.capacidad}
                                        onChange={(e) => setData('capacidad', e.target.value)}
                                        min="1"
                                        max="500"
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.capacidad ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: 30"
                                    />
                                    {errors.capacidad && (
                                        <p className="mt-1 text-sm text-red-600">{errors.capacidad}</p>
                                    )}
                                </div>

                                {/* Tiene Computadoras */}
                                <div className="md:col-span-2">
                                    <div className="flex items-center">
                                        <input
                                            type="checkbox"
                                            id="tiene_computadoras"
                                            checked={data.tiene_computadoras}
                                            onChange={(e) => {
                                                setData('tiene_computadoras', e.target.checked);
                                                if (!e.target.checked) {
                                                    setData('cantidad_computadoras', '');
                                                }
                                            }}
                                            className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        />
                                        <label htmlFor="tiene_computadoras" className="ml-2 block text-sm text-gray-700">
                                            Tiene computadoras
                                        </label>
                                    </div>
                                    {errors.tiene_computadoras && (
                                        <p className="mt-1 text-sm text-red-600">{errors.tiene_computadoras}</p>
                                    )}
                                </div>

                                {/* Cantidad de Computadoras */}
                                {data.tiene_computadoras && (
                                    <div>
                                        <label htmlFor="cantidad_computadoras" className="block text-sm font-medium text-gray-700 mb-2">
                                            Cantidad de Computadoras
                                        </label>
                                        <input
                                            type="number"
                                            id="cantidad_computadoras"
                                            value={data.cantidad_computadoras}
                                            onChange={(e) => setData('cantidad_computadoras', e.target.value)}
                                            min="0"
                                            max="100"
                                            className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                                errors.cantidad_computadoras ? 'border-red-500' : 'border-gray-300'
                                            }`}
                                            placeholder="Ej: 20"
                                        />
                                        {errors.cantidad_computadoras && (
                                            <p className="mt-1 text-sm text-red-600">{errors.cantidad_computadoras}</p>
                                        )}
                                    </div>
                                )}

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
                                        placeholder="Características adicionales del aula..."
                                    />
                                    {errors.observaciones && (
                                        <p className="mt-1 text-sm text-red-600">{errors.observaciones}</p>
                                    )}
                                </div>

                                {/* Info del código QR */}
                                <div className="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                                    <div className="flex items-start">
                                        <svg className="w-5 h-5 text-gray-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
                                        </svg>
                                        <div>
                                            <p className="text-sm text-gray-700 font-medium">Código QR</p>
                                            <p className="text-xs text-gray-500 mt-1">
                                                Este aula tiene un código QR único: <span className="font-mono">{aula.codigo_qr}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Buttons */}
                            <div className="flex items-center justify-end space-x-3 mt-6">
                                <Link
                                    href="/aulas"
                                    className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {processing ? 'Actualizando...' : 'Actualizar Aula'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}