import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function Create({ modulosSugeridos }) {
    const { data, setData, post, processing, errors } = useForm({
        nombre: '',
        slug: '',
        descripcion: '',
        modulo: '',
    });

    const [autoGenerateSlug, setAutoGenerateSlug] = useState(true);

    // Función para generar slug automáticamente
    const generateSlug = (text) => {
        return text
            .toLowerCase()
            .replace(/[áàäâ]/g, 'a')
            .replace(/[éèëê]/g, 'e')
            .replace(/[íìïî]/g, 'i')
            .replace(/[óòöô]/g, 'o')
            .replace(/[úùüû]/g, 'u')
            .replace(/ñ/g, 'n')
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/^_+|_+$/g, '');
    };

    // Manejar cambio de nombre
    const handleNombreChange = (e) => {
        const value = e.target.value;
        setData('nombre', value);

        // Generar slug automáticamente si está habilitado
        if (autoGenerateSlug && value) {
            setData('slug', generateSlug(value));
        }
    };

    // Manejar cambio de slug manual
    const handleSlugChange = (e) => {
        setAutoGenerateSlug(false);
        setData('slug', e.target.value);
    };

    // Enviar formulario
    const handleSubmit = (e) => {
        e.preventDefault();
        post('/permisos');
    };

    return (
        <AuthenticatedLayout>
            <Head title="Nuevo Permiso" />

            <div className="py-6">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="mb-6">
                        <Link
                            href="/permisos"
                            className="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4"
                        >
                            <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Volver a Permisos
                        </Link>
                        <h1 className="text-3xl font-bold text-gray-900">Nuevo Permiso</h1>
                        <p className="mt-1 text-sm text-gray-600">
                            Completa los datos para registrar un nuevo permiso
                        </p>
                    </div>

                    {/* Formulario */}
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <form onSubmit={handleSubmit}>
                            <div className="space-y-6">
                                {/* Nombre */}
                                <div>
                                    <label htmlFor="nombre" className="block text-sm font-medium text-gray-700 mb-2">
                                        Nombre del Permiso <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="nombre"
                                        value={data.nombre}
                                        onChange={handleNombreChange}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.nombre ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: Ver usuarios"
                                    />
                                    {errors.nombre && (
                                        <p className="mt-1 text-sm text-red-600">{errors.nombre}</p>
                                    )}
                                    <p className="mt-1 text-xs text-gray-500">
                                        Nombre descriptivo del permiso (será visible para los usuarios)
                                    </p>
                                </div>

                                {/* Slug */}
                                <div>
                                    <label htmlFor="slug" className="block text-sm font-medium text-gray-700 mb-2">
                                        Slug
                                    </label>
                                    <div className="flex gap-2">
                                        <input
                                            type="text"
                                            id="slug"
                                            value={data.slug}
                                            onChange={handleSlugChange}
                                            className={`flex-1 px-4 py-2 border rounded-lg font-mono text-sm focus:ring-2 focus:ring-blue-500 ${
                                                errors.slug ? 'border-red-500' : 'border-gray-300'
                                            }`}
                                            placeholder="ver_usuarios"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setAutoGenerateSlug(true);
                                                setData('slug', generateSlug(data.nombre));
                                            }}
                                            className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
                                        >
                                            Auto
                                        </button>
                                    </div>
                                    {errors.slug && (
                                        <p className="mt-1 text-sm text-red-600">{errors.slug}</p>
                                    )}
                                    <p className="mt-1 text-xs text-gray-500">
                                        Identificador único en formato snake_case (se genera automáticamente)
                                    </p>
                                </div>

                                {/* Módulo */}
                                <div>
                                    <label htmlFor="modulo" className="block text-sm font-medium text-gray-700 mb-2">
                                        Módulo <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="modulo"
                                        list="modulos-sugeridos"
                                        value={data.modulo}
                                        onChange={(e) => setData('modulo', e.target.value)}
                                        className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                            errors.modulo ? 'border-red-500' : 'border-gray-300'
                                        }`}
                                        placeholder="Ej: Usuarios, Horarios, Asistencias..."
                                    />
                                    <datalist id="modulos-sugeridos">
                                        {modulosSugeridos.map((modulo) => (
                                            <option key={modulo} value={modulo} />
                                        ))}
                                    </datalist>
                                    {errors.modulo && (
                                        <p className="mt-1 text-sm text-red-600">{errors.modulo}</p>
                                    )}
                                    <p className="mt-1 text-xs text-gray-500">
                                        Módulo o sección del sistema al que pertenece el permiso
                                    </p>
                                </div>

                                {/* Descripción */}
                                <div>
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
                                        placeholder="Descripción detallada del permiso (opcional)"
                                    />
                                    {errors.descripcion && (
                                        <p className="mt-1 text-sm text-red-600">{errors.descripcion}</p>
                                    )}
                                </div>

                                {/* Info Box */}
                                <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div className="flex">
                                        <div className="flex-shrink-0">
                                            <svg className="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd" />
                                            </svg>
                                        </div>
                                        <div className="ml-3">
                                            <h3 className="text-sm font-medium text-blue-800">Información importante</h3>
                                            <div className="mt-2 text-sm text-blue-700">
                                                <ul className="list-disc list-inside space-y-1">
                                                    <li>El slug se genera automáticamente desde el nombre</li>
                                                    <li>Puedes editar el slug manualmente si es necesario</li>
                                                    <li>Los permisos se asignan a roles para controlar el acceso</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Buttons */}
                            <div className="flex items-center justify-end space-x-3 mt-6">
                                <Link
                                    href="/permisos"
                                    className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {processing ? 'Guardando...' : 'Guardar Permiso'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
