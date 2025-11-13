import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function Create({ roles }) {
    const { data, setData, post, processing, errors } = useForm({
        rol_id: '',
        nombre: '',
        apellido: '',
        cedula_identidad: '',
        email: '',
        telefono: '',
        contrasena: '',
        contrasena_confirmation: '',
        debe_cambiar_contrasena: true,
        estado: 'activo',
    });

    const [showPassword, setShowPassword] = useState(false);
    const [showPasswordConfirm, setShowPasswordConfirm] = useState(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/usuarios');
    };

    return (
        <AuthenticatedLayout>
            <Head title="Nuevo Usuario" />

            <div className="py-6">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="mb-6">
                        <Link
                            href="/usuarios"
                            className="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4"
                        >
                            <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                            </svg>
                            Volver a Usuarios
                        </Link>
                        <h1 className="text-3xl font-bold text-gray-900">Nuevo Usuario</h1>
                        <p className="mt-1 text-sm text-gray-600">
                            Completa los datos para registrar un nuevo usuario
                        </p>
                    </div>

                    {/* Formulario */}
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <form onSubmit={handleSubmit}>
                            <div className="space-y-6">
                                {/* Sección: Información Personal */}
                                <div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">
                                        Información Personal
                                    </h3>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                                placeholder="Ej: Juan"
                                            />
                                            {errors.nombre && (
                                                <p className="mt-1 text-sm text-red-600">{errors.nombre}</p>
                                            )}
                                        </div>

                                        {/* Apellido */}
                                        <div>
                                            <label htmlFor="apellido" className="block text-sm font-medium text-gray-700 mb-2">
                                                Apellido <span className="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                id="apellido"
                                                value={data.apellido}
                                                onChange={(e) => setData('apellido', e.target.value)}
                                                className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                                    errors.apellido ? 'border-red-500' : 'border-gray-300'
                                                }`}
                                                placeholder="Ej: Pérez"
                                            />
                                            {errors.apellido && (
                                                <p className="mt-1 text-sm text-red-600">{errors.apellido}</p>
                                            )}
                                        </div>

                                        {/* Cédula de Identidad */}
                                        <div>
                                            <label htmlFor="cedula_identidad" className="block text-sm font-medium text-gray-700 mb-2">
                                                Cédula de Identidad
                                            </label>
                                            <input
                                                type="text"
                                                id="cedula_identidad"
                                                value={data.cedula_identidad}
                                                onChange={(e) => setData('cedula_identidad', e.target.value)}
                                                className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                                    errors.cedula_identidad ? 'border-red-500' : 'border-gray-300'
                                                }`}
                                                placeholder="Ej: 12345678"
                                            />
                                            {errors.cedula_identidad && (
                                                <p className="mt-1 text-sm text-red-600">{errors.cedula_identidad}</p>
                                            )}
                                        </div>

                                        {/* Teléfono */}
                                        <div>
                                            <label htmlFor="telefono" className="block text-sm font-medium text-gray-700 mb-2">
                                                Teléfono
                                            </label>
                                            <input
                                                type="text"
                                                id="telefono"
                                                value={data.telefono}
                                                onChange={(e) => setData('telefono', e.target.value)}
                                                className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                                    errors.telefono ? 'border-red-500' : 'border-gray-300'
                                                }`}
                                                placeholder="Ej: 70123456"
                                            />
                                            {errors.telefono && (
                                                <p className="mt-1 text-sm text-red-600">{errors.telefono}</p>
                                            )}
                                        </div>
                                    </div>
                                </div>

                                {/* Sección: Información de Cuenta */}
                                <div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">
                                        Información de Cuenta
                                    </h3>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {/* Email */}
                                        <div className="md:col-span-2">
                                            <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
                                                Email <span className="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="email"
                                                id="email"
                                                value={data.email}
                                                onChange={(e) => setData('email', e.target.value)}
                                                className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                                    errors.email ? 'border-red-500' : 'border-gray-300'
                                                }`}
                                                placeholder="Ej: usuario@ficct.edu.bo"
                                            />
                                            {errors.email && (
                                                <p className="mt-1 text-sm text-red-600">{errors.email}</p>
                                            )}
                                        </div>

                                        {/* Contraseña */}
                                        <div>
                                            <label htmlFor="contrasena" className="block text-sm font-medium text-gray-700 mb-2">
                                                Contraseña <span className="text-red-500">*</span>
                                            </label>
                                            <div className="relative">
                                                <input
                                                    type={showPassword ? 'text' : 'password'}
                                                    id="contrasena"
                                                    value={data.contrasena}
                                                    onChange={(e) => setData('contrasena', e.target.value)}
                                                    className={`w-full px-4 py-2 pr-10 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                                        errors.contrasena ? 'border-red-500' : 'border-gray-300'
                                                    }`}
                                                    placeholder="Mínimo 8 caracteres"
                                                />
                                                <button
                                                    type="button"
                                                    onClick={() => setShowPassword(!showPassword)}
                                                    className="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700"
                                                >
                                                    {showPassword ? (
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                        </svg>
                                                    ) : (
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    )}
                                                </button>
                                            </div>
                                            {errors.contrasena && (
                                                <p className="mt-1 text-sm text-red-600">{errors.contrasena}</p>
                                            )}
                                            <p className="mt-1 text-xs text-gray-500">
                                                Debe contener al menos 8 caracteres, letras y números
                                            </p>
                                        </div>

                                        {/* Confirmar Contraseña */}
                                        <div>
                                            <label htmlFor="contrasena_confirmation" className="block text-sm font-medium text-gray-700 mb-2">
                                                Confirmar Contraseña <span className="text-red-500">*</span>
                                            </label>
                                            <div className="relative">
                                                <input
                                                    type={showPasswordConfirm ? 'text' : 'password'}
                                                    id="contrasena_confirmation"
                                                    value={data.contrasena_confirmation}
                                                    onChange={(e) => setData('contrasena_confirmation', e.target.value)}
                                                    className={`w-full px-4 py-2 pr-10 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                                        errors.contrasena_confirmation ? 'border-red-500' : 'border-gray-300'
                                                    }`}
                                                    placeholder="Repita la contraseña"
                                                />
                                                <button
                                                    type="button"
                                                    onClick={() => setShowPasswordConfirm(!showPasswordConfirm)}
                                                    className="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700"
                                                >
                                                    {showPasswordConfirm ? (
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                        </svg>
                                                    ) : (
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    )}
                                                </button>
                                            </div>
                                            {errors.contrasena_confirmation && (
                                                <p className="mt-1 text-sm text-red-600">{errors.contrasena_confirmation}</p>
                                            )}
                                        </div>
                                    </div>
                                </div>

                                {/* Sección: Rol y Estado */}
                                <div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">
                                        Rol y Estado
                                    </h3>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {/* Rol */}
                                        <div>
                                            <label htmlFor="rol_id" className="block text-sm font-medium text-gray-700 mb-2">
                                                Rol <span className="text-red-500">*</span>
                                            </label>
                                            <select
                                                id="rol_id"
                                                value={data.rol_id}
                                                onChange={(e) => setData('rol_id', e.target.value)}
                                                className={`w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 ${
                                                    errors.rol_id ? 'border-red-500' : 'border-gray-300'
                                                }`}
                                            >
                                                <option value="">Seleccione un rol</option>
                                                {roles.map((rol) => (
                                                    <option key={rol.id} value={rol.id}>
                                                        {rol.nombre}
                                                    </option>
                                                ))}
                                            </select>
                                            {errors.rol_id && (
                                                <p className="mt-1 text-sm text-red-600">{errors.rol_id}</p>
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
                                                <option value="activo">Activo</option>
                                                <option value="inactivo">Inactivo</option>
                                                <option value="pendiente_activacion">Pendiente Activación</option>
                                                <option value="suspendido">Suspendido</option>
                                            </select>
                                            {errors.estado && (
                                                <p className="mt-1 text-sm text-red-600">{errors.estado}</p>
                                            )}
                                        </div>
                                    </div>

                                    {/* Checkbox: Debe cambiar contraseña */}
                                    <div className="mt-4">
                                        <label className="flex items-center">
                                            <input
                                                type="checkbox"
                                                checked={data.debe_cambiar_contrasena}
                                                onChange={(e) => setData('debe_cambiar_contrasena', e.target.checked)}
                                                className="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            />
                                            <span className="ml-2 text-sm text-gray-600">
                                                El usuario debe cambiar su contraseña en el próximo inicio de sesión
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {/* Buttons */}
                            <div className="flex items-center justify-end space-x-3 mt-6">
                                <Link
                                    href="/usuarios"
                                    className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                                >
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                >
                                    {processing ? 'Guardando...' : 'Guardar Usuario'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
