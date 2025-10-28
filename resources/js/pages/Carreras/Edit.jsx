import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout';

export default function Edit({ carrera }) {
    const { data, setData, put, processing, errors } = useForm({
        nombre: carrera.nombre || '',
        descripcion: carrera.descripcion || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(`/carreras/${carrera.id}`);
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Editar ${carrera.nombre}`} />

            <div className="py-6">
                <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="mb-6">
                        <Link
                            href="/carreras"
                            className="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4"
                        >
                            <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Volver a Carreras
                        </Link>
                        <h1 className="text-3xl font-bold text-gray-900">Editar Carrera</h1>
                        <p className="mt-1 text-sm text-gray-600">
                            Modifica los datos de la carrera
                        </p>
                    </div>

                    {/* Form */}
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <form onSubmit={handleSubmit}>
                            {/* Nombre */}
                            <div className="mb-6">
                                <label htmlFor="nombre" className="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre de la Carrera <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nombre"
                                    value={data.nombre}
                                    onChange={(e) => setData('nombre', e.target.value)}
                                    className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent ${
                                        errors.nombre ? 'border-red-500' : 'border-gray-300'
                                    }`}
                                    placeholder="Ej: Ingeniería de Sistemas"
                                />
                                {errors.nombre && (
                                    <p className="mt-1 text-sm text-red-600">{errors.nombre}</p>
                                )}
                            </div>

                            {/* Descripción */}
                            <div className="mb-6">
                                <label htmlFor="descripcion" className="block text-sm font-medium text-gray-700 mb-2">
                                    Descripción (Opcional)
                                </label>
                                <textarea
                                    id="descripcion"
                                    value={data.descripcion}
                                    onChange={(e) => setData('descripcion', e.target.value)}
                                    rows={4}
                                    className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent ${
                                        errors.descripcion ? 'border-red-500' : 'border-gray-300'
                                    }`}
                                    placeholder="Breve descripción de la carrera..."
                                />
                                {errors.descripcion && (
                                    <p className="mt-1 text-sm text-red-600">{errors.descripcion}</p>
                                )}
                            </div>

                            {/* Buttons */}
                            <div className="flex items-center justify-end space-x-3">
                                <Link
                                    href="/carreras"
                                    className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {processing ? 'Actualizando...' : 'Actualizar Carrera'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}