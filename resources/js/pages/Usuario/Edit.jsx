import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function Edit({ usuario, roles }) {
    const { data, setData, put, processing, errors } = useForm({
        rol_id: usuario.rol_id || '',
        nombre: usuario.nombre || '',
        apellido: usuario.apellido || '',
        telefono: usuario.telefono || '',
        estado: usuario.estado || 'activo',
    });

    const [showPasswordModal, setShowPasswordModal] = useState(false);
    const [passwordData, setPasswordData] = useState({
        contrasena: '',
        contrasena_confirmation: '',
        debe_cambiar_contrasena: false,
    });
    const [showPassword, setShowPassword] = useState(false);
    const [showPasswordConfirm, setShowPasswordConfirm] = useState(false);
    const [passwordProcessing, setPasswordProcessing] = useState(false);

    const handleSubmit = (e) => {
        e.preventDefault();
        put(`/usuarios/${usuario.id}`);
    };

    const handleChangePassword = (e) => {
        e.preventDefault();
        setPasswordProcessing(true);
        router.post(`/usuarios/${usuario.id}/cambiar-contrasena`, passwordData, {
            onFinish: () => {
                setPasswordProcessing(false);
                setShowPasswordModal(false);
                setPasswordData({
                    contrasena: '',
                    contrasena_confirmation: '',
                    debe_cambiar_contrasena: false,
                });
            }
        });
    };

    const getEstadoBadge = (estado) => {
        const badges = {
            'activo': 'bg-green-100 text-green-800',
            'inactivo': 'bg-gray-100 text-gray-800',
            'pendiente_activacion': 'bg-yellow-100 text-yellow-800',
            'suspendido': 'bg-red-100 text-red-800',
        };
        return badges[estado] || 'bg-gray-100 text-gray-800';
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Editar Usuario: ${usuario.nombre} ${usuario.apellido}`} />

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
                        <h1 className="text-3xl font-bold text-gray-900">Editar Usuario</h1>
                        <p className="mt-1 text-sm text-gray-600">
                            Modifica los datos del usuario
                        </p>
                    </div>

                    {/* Formulario */}
                    <div className="bg-white rounded-lg shadow-md p-6">
                        <form onSubmit={handleSubmit}>
                            <div className="space-y-6">
                                {/* Información del usuario */}
                                <div className="bg-gray-50 rounded-lg p-4">
                                    <div className="flex items-center justify-between">
                                        <div>
                                            <h3 className="text-sm font-medium text-gray-700 mb-2">
                                                Información del Usuario
                                            </h3>
                                            <div className="grid grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <span className="text-gray-500">ID:</span>
                                                    <span className="ml-2 font-mono">{usuario.id}</span>
                                                </div>
                                                <div>
                                                    <span className="text-gray-500">Email:</span>
                                                    <span className="ml-2">{usuario.email}</span>
                                                </div>
                                                {usuario.cedula_identidad && (
                                                    <div>
                                                        <span className="text-gray-500">CI:</span>
                                                        <span className="ml-2">{usuario.cedula_identidad}</span>
                                                    </div>
                                                )}
                                                <div>
                                                    <span className="text-gray-500">Creado:</span>
                                                    <span className="ml-2">
                                                        {new Date(usuario.fecha_creacion).toLocaleDateString('es-ES')}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <span className={`px-3 py-1 text-xs font-semibold rounded-full ${getEstadoBadge(usuario.estado)}`}>
                                                {usuario.estado}
                                            </span>
                                        </div>
                                    </div>
                                </div>

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

                                        {/* Email (Solo lectura) */}
                                        <div>
                                            <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
                                                Email
                                            </label>
                                            <input
                                                type="email"
                                                id="email"
                                                value={usuario.email}
                                                disabled
                                                className="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed"
                                            />
                                            <p className="mt-1 text-xs text-gray-500">
                                                El email no puede ser modificado
                                            </p>
                                        </div>

                                        {/* Cédula (Solo lectura) */}
                                        <div>
                                            <label htmlFor="cedula_identidad" className="block text-sm font-medium text-gray-700 mb-2">
                                                Cédula de Identidad
                                            </label>
                                            <input
                                                type="text"
                                                id="cedula_identidad"
                                                value={usuario.cedula_identidad || 'No registrada'}
                                                disabled
                                                className="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed"
                                            />
                                            <p className="mt-1 text-xs text-gray-500">
                                                La cédula no puede ser modificada
                                            </p>
                                        </div>

                                        {/* Teléfono */}
                                        <div className="md:col-span-2">
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
                                </div>

                                {/* Sección: Seguridad */}
                                <div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">
                                        Seguridad
                                    </h3>
                                    <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <h4 className="text-sm font-medium text-blue-900">Contraseña</h4>
                                                <p className="text-sm text-blue-700 mt-1">
                                                    {usuario.debe_cambiar_contrasena 
                                                        ? 'El usuario debe cambiar su contraseña en el próximo login'
                                                        : 'La contraseña está configurada'}
                                                </p>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={() => setShowPasswordModal(true)}
                                                className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm"
                                            >
                                                Cambiar Contraseña
                                            </button>
                                        </div>
                                    </div>

                                    {/* Información adicional */}
                                    {usuario.ultimo_login && (
                                        <div className="mt-4 text-sm text-gray-600">
                                            <p>
                                                <strong>Último acceso:</strong>{' '}
                                                {new Date(usuario.ultimo_login).toLocaleString('es-ES')}
                                                {usuario.ultimo_login_ip && ` desde ${usuario.ultimo_login_ip}`}
                                            </p>
                                        </div>
                                    )}

                                    {usuario.intentos_fallidos_login > 0 && (
                                        <div className="mt-2 text-sm">
                                            <p className="text-orange-600">
                                                <strong>Intentos fallidos:</strong> {usuario.intentos_fallidos_login}
                                            </p>
                                        </div>
                                    )}
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
                                    {processing ? 'Actualizando...' : 'Actualizar Usuario'}
                                </button>
                            </div>
                        </form>
                    </div>

                    {/* Advertencia */}
                    <div className="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h3 className="font-semibold text-yellow-900 mb-2">⚠️ Nota Importante</h3>
                        <ul className="text-sm text-yellow-800 space-y-1">
                            <li>• El <strong>email</strong> y la <strong>cédula de identidad</strong> no pueden ser modificados por seguridad</li>
                            <li>• Los cambios en el <strong>rol</strong> afectarán inmediatamente los permisos del usuario</li>
                            <li>• Cambiar el <strong>estado</strong> a "Inactivo" o "Suspendido" impedirá el acceso al sistema</li>
                        </ul>
                    </div>
                </div>
            </div>

            {/* Modal de cambio de contraseña */}
            {showPasswordModal && (
                <div className="fixed z-10 inset-0 overflow-y-auto">
                    <div className="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div className="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onClick={() => setShowPasswordModal(false)}></div>
                        <span className="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                        <div className="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div className="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 className="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Cambiar Contraseña
                                </h3>
                                <form onSubmit={handleChangePassword} className="space-y-4">
                                    {/* Nueva contraseña */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Nueva Contraseña
                                        </label>
                                        <div className="relative">
                                            <input
                                                type={showPassword ? 'text' : 'password'}
                                                value={passwordData.contrasena}
                                                onChange={(e) => setPasswordData({...passwordData, contrasena: e.target.value})}
                                                className="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="Mínimo 8 caracteres"
                                                required
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
                                    </div>

                                    {/* Confirmar contraseña */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Confirmar Contraseña
                                        </label>
                                        <div className="relative">
                                            <input
                                                type={showPasswordConfirm ? 'text' : 'password'}
                                                value={passwordData.contrasena_confirmation}
                                                onChange={(e) => setPasswordData({...passwordData, contrasena_confirmation: e.target.value})}
                                                className="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="Repita la contraseña"
                                                required
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
                                    </div>

                                    {/* Checkbox */}
                                    <div>
                                        <label className="flex items-center">
                                            <input
                                                type="checkbox"
                                                checked={passwordData.debe_cambiar_contrasena}
                                                onChange={(e) => setPasswordData({...passwordData, debe_cambiar_contrasena: e.target.checked})}
                                                className="rounded border-gray-300 text-blue-600"
                                            />
                                            <span className="ml-2 text-sm text-gray-600">
                                                Forzar cambio en próximo login
                                            </span>
                                        </label>
                                    </div>

                                    {/* Botones */}
                                    <div className="flex gap-4 justify-end mt-4">
                                        <button
                                            type="button"
                                            onClick={() => setShowPasswordModal(false)}
                                            className="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400"
                                        >
                                            Cancelar
                                        </button>
                                        <button
                                            type="submit"
                                            disabled={passwordProcessing}
                                            className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                        >
                                            {passwordProcessing ? 'Cambiando...' : 'Cambiar Contraseña'}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
