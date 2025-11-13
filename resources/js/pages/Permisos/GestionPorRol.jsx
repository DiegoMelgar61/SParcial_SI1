import { Head, Link, usePage, router, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function GestionPorRol({ roles, permisos }) {
    const { flash } = usePage().props;
    const [rolSeleccionado, setRolSeleccionado] = useState(null);
    const [permisosSeleccionados, setPermisosSeleccionados] = useState([]);
    const [showEditModal, setShowEditModal] = useState(false);

    // Convertir permisos agrupados por módulo a un formato más fácil de usar
    // Los permisos vienen como un objeto donde las claves son los módulos
    const permisosPorModulo = permisos || {};
    
    // Asegurarse de que cada módulo tenga un array de permisos
    const permisosFormateados = Object.keys(permisosPorModulo).reduce((acc, modulo) => {
        const permisosModulo = permisosPorModulo[modulo];
        // Si es un array, usarlo directamente; si es un objeto con índices, convertirlo a array
        if (permisosModulo) {
            acc[modulo] = Array.isArray(permisosModulo) 
                ? permisosModulo 
                : Object.values(permisosModulo);
        }
        return acc;
    }, {});

    // Debug: verificar datos recibidos
    console.log('Permisos recibidos:', permisos);
    console.log('Permisos formateados:', permisosFormateados);

    const handleEditarRol = (rol) => {
        console.log('Editando rol:', rol);
        console.log('Permisos disponibles:', permisosFormateados);
        setRolSeleccionado(rol);
        // Cargar los permisos actuales del rol
        setPermisosSeleccionados(rol.permisos?.map(p => p.id) || []);
        setShowEditModal(true);
    };

    const handleTogglePermiso = (permisoId) => {
        setPermisosSeleccionados(prev => {
            if (prev.includes(permisoId)) {
                return prev.filter(id => id !== permisoId);
            } else {
                return [...prev, permisoId];
            }
        });
    };

    const handleGuardarPermisos = (e) => {
        e.preventDefault();
        if (!rolSeleccionado) return;

        router.post(`/permisos/roles/${rolSeleccionado.id}/asignar`, {
            permisos: permisosSeleccionados
        }, {
            onSuccess: () => {
                setShowEditModal(false);
                setRolSeleccionado(null);
                setPermisosSeleccionados([]);
            }
        });
    };

    const handleCancelar = () => {
        setShowEditModal(false);
        setRolSeleccionado(null);
        setPermisosSeleccionados([]);
    };

    // Roles principales que mencionaste
    const rolesPrincipales = [
        'Super Administrador',
        'Administrador',
        'Coordinador de Carrera',
        'Docente',
        'Autoridad/Decano'
    ];

    // Filtrar y ordenar roles
    const rolesOrdenados = [...roles].sort((a, b) => {
        const indexA = rolesPrincipales.indexOf(a.nombre);
        const indexB = rolesPrincipales.indexOf(b.nombre);
        if (indexA === -1 && indexB === -1) return a.nombre.localeCompare(b.nombre);
        if (indexA === -1) return 1;
        if (indexB === -1) return -1;
        return indexA - indexB;
    });

    return (
        <AuthenticatedLayout>
            <Head title="Gestión de Permisos por Rol" />

            <div className="py-6">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="flex justify-between items-center mb-6">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900">Gestión de Permisos por Rol</h1>
                            <p className="mt-1 text-sm text-gray-600">
                                Asigna y gestiona los permisos para cada rol del sistema
                            </p>
                        </div>
                        <Link
                            href="/permisos"
                            className="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-white hover:bg-gray-700 transition"
                        >
                            <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver a Permisos
                        </Link>
                    </div>

                    {/* Messages */}
                    {flash?.success && (
                        <div className="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center">
                            <svg className="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                            </svg>
                            <span className="text-green-800 font-medium">{flash.success}</span>
                        </div>
                    )}

                    {flash?.error && (
                        <div className="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center">
                            <svg className="w-5 h-5 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                            </svg>
                            <span className="text-red-800 font-medium">{flash.error}</span>
                        </div>
                    )}

                    {/* Lista de Roles con sus Permisos */}
                    <div className="space-y-4">
                        {rolesOrdenados.map((rol) => (
                            <div key={rol.id} className="bg-white rounded-lg shadow-md overflow-hidden">
                                <div className="p-6">
                                    <div className="flex items-center justify-between mb-4">
                                        <div>
                                            <h3 className="text-xl font-semibold text-gray-900">{rol.nombre}</h3>
                                            {rol.descripcion && (
                                                <p className="text-sm text-gray-600 mt-1">{rol.descripcion}</p>
                                            )}
                                        </div>
                                        <div className="flex items-center gap-3">
                                            <span className={`px-3 py-1 text-xs font-semibold rounded-full ${
                                                rol.esta_activo 
                                                    ? 'bg-green-100 text-green-800' 
                                                    : 'bg-gray-100 text-gray-800'
                                            }`}>
                                                {rol.esta_activo ? 'Activo' : 'Inactivo'}
                                            </span>
                                            <button
                                                onClick={() => handleEditarRol(rol)}
                                                className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                                            >
                                                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Editar Permisos
                                            </button>
                                        </div>
                                    </div>

                                    {/* Permisos asignados al rol */}
                                    {rol.permisos && rol.permisos.length > 0 ? (
                                        <div>
                                            <h4 className="text-sm font-medium text-gray-700 mb-3">
                                                Permisos asignados ({rol.permisos.length})
                                            </h4>
                                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                                {Object.entries(
                                                    rol.permisos.reduce((acc, permiso) => {
                                                        if (!acc[permiso.modulo]) {
                                                            acc[permiso.modulo] = [];
                                                        }
                                                        acc[permiso.modulo].push(permiso);
                                                        return acc;
                                                    }, {})
                                                ).map(([modulo, permisosModulo]) => (
                                                    <div key={modulo} className="border border-gray-200 rounded-lg p-3">
                                                        <div className="text-xs font-semibold text-blue-600 mb-2 uppercase">
                                                            {modulo}
                                                        </div>
                                                        <div className="space-y-1">
                                                            {permisosModulo.map((permiso) => (
                                                                <div key={permiso.id} className="text-sm text-gray-700">
                                                                    • {permiso.nombre}
                                                                </div>
                                                            ))}
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="text-center py-8 text-gray-500">
                                            <svg className="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            <p className="text-sm">Este rol no tiene permisos asignados</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {/* Modal de Edición de Permisos */}
            {showEditModal && rolSeleccionado && (
                <div className="fixed z-50 inset-0 overflow-y-auto">
                    <div className="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div className="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onClick={handleCancelar}></div>
                        <span className="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                        <div className="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full relative z-50">
                            <form onSubmit={handleGuardarPermisos}>
                                <div className="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div className="w-full">
                                        <div className="flex items-center justify-between mb-4 pb-4 border-b">
                                            <h3 className="text-xl font-semibold text-gray-900">
                                                Editar Permisos: {rolSeleccionado?.nombre}
                                            </h3>
                                            <button
                                                type="button"
                                                onClick={handleCancelar}
                                                className="text-gray-400 hover:text-gray-600"
                                            >
                                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        {/* Permisos agrupados por módulo */}
                                        <div className="max-h-96 overflow-y-auto space-y-4">
                                            {Object.keys(permisosFormateados).length === 0 ? (
                                                <div className="text-center py-8 text-gray-500">
                                                    <svg className="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                    <p className="text-sm">No hay permisos disponibles en el sistema</p>
                                                    <p className="text-xs text-gray-400 mt-1">Crea permisos primero desde la página de Permisos</p>
                                                </div>
                                            ) : (
                                                Object.entries(permisosFormateados).map(([modulo, permisosModulo]) => (
                                                    <div key={modulo} className="border border-gray-200 rounded-lg p-4">
                                                        <div className="flex items-center justify-between mb-3">
                                                            <h4 className="text-sm font-semibold text-gray-900 uppercase">
                                                                {modulo}
                                                            </h4>
                                                            <button
                                                                type="button"
                                                                onClick={() => {
                                                                    const todosSeleccionados = permisosModulo.every(p => permisosSeleccionados.includes(p.id));
                                                                    if (todosSeleccionados) {
                                                                        // Deseleccionar todos
                                                                        setPermisosSeleccionados(prev => 
                                                                            prev.filter(id => !permisosModulo.some(p => p.id === id))
                                                                        );
                                                                    } else {
                                                                        // Seleccionar todos
                                                                        const idsModulo = permisosModulo.map(p => p.id);
                                                                        setPermisosSeleccionados(prev => {
                                                                            const nuevos = [...prev];
                                                                            idsModulo.forEach(id => {
                                                                                if (!nuevos.includes(id)) {
                                                                                    nuevos.push(id);
                                                                                }
                                                                            });
                                                                            return nuevos;
                                                                        });
                                                                    }
                                                                }}
                                                                className="text-xs text-blue-600 hover:text-blue-800"
                                                            >
                                                                {permisosModulo.every(p => permisosSeleccionados.includes(p.id))
                                                                    ? 'Deseleccionar todos' 
                                                                    : 'Seleccionar todos'}
                                                            </button>
                                                        </div>
                                                        <div className="space-y-2">
                                                            {permisosModulo.map((permiso) => (
                                                                <label
                                                                    key={permiso.id}
                                                                    className="flex items-start p-2 rounded hover:bg-gray-50 cursor-pointer"
                                                                >
                                                                    <input
                                                                        type="checkbox"
                                                                        checked={permisosSeleccionados.includes(permiso.id)}
                                                                        onChange={() => handleTogglePermiso(permiso.id)}
                                                                        className="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                                    />
                                                                    <div className="ml-3 flex-1">
                                                                        <div className="text-sm font-medium text-gray-900">
                                                                            {permiso.nombre}
                                                                        </div>
                                                                        {permiso.descripcion && (
                                                                            <div className="text-xs text-gray-500 mt-1">
                                                                                {permiso.descripcion}
                                                                            </div>
                                                                        )}
                                                                    </div>
                                                                </label>
                                                            ))}
                                                        </div>
                                                    </div>
                                                ))
                                            )}
                                        </div>
                                    </div>
                                </div>
                                <div className="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button
                                        type="submit"
                                        className="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm"
                                    >
                                        Guardar Permisos
                                    </button>
                                    <button
                                        type="button"
                                        onClick={handleCancelar}
                                        className="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                    >
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}

