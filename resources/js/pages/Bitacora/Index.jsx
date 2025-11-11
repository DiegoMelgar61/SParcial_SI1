import { Head, Link, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function Index({ bitacora, filters, acciones, tablas }) {
    const { flash } = usePage().props;
    const [buscar, setBuscar] = useState(filters.buscar || '');
    const [accion, setAccion] = useState(filters.accion || '');
    const [tabla, setTabla] = useState(filters.tabla || '');
    const [fechaDesde, setFechaDesde] = useState(filters.fecha_desde || '');
    const [fechaHasta, setFechaHasta] = useState(filters.fecha_hasta || '');

    const handleFilter = () => {
        router.get('/bitacora', {
            buscar,
            accion,
            tabla,
            fecha_desde: fechaDesde,
            fecha_hasta: fechaHasta,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleReset = () => {
        setBuscar('');
        setAccion('');
        setTabla('');
        setFechaDesde('');
        setFechaHasta('');
        router.get('/bitacora');
    };

    const getAccionColor = (accion) => {
        const colores = {
            'crear': 'bg-green-100 text-green-800',
            'editar': 'bg-blue-100 text-blue-800',
            'eliminar': 'bg-red-100 text-red-800',
            'login': 'bg-purple-100 text-purple-800',
            'logout': 'bg-gray-100 text-gray-800',
            'cambiar_contrasena': 'bg-yellow-100 text-yellow-800',
            'cambiar_estado': 'bg-indigo-100 text-indigo-800',
            'asignar_permisos': 'bg-pink-100 text-pink-800',
        };
        return colores[accion] || 'bg-gray-100 text-gray-800';
    };

    const formatearFecha = (fecha) => {
        if (!fecha) return '-';
        const fechaObj = typeof fecha === 'string' ? new Date(fecha) : fecha;
        return fechaObj.toLocaleString('es-ES', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Historial de Acciones (Bitácora)" />

            <div className="py-6">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="mb-6">
                        <h1 className="text-3xl font-bold text-gray-900">Historial de Acciones</h1>
                        <p className="mt-1 text-sm text-gray-600">
                            Registro completo de todas las acciones realizadas en el sistema
                        </p>
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

                    {/* Filtros */}
                    <div className="bg-white rounded-lg shadow-md p-6 mb-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            {/* Búsqueda general */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Buscar
                                </label>
                                <input
                                    type="text"
                                    value={buscar}
                                    onChange={(e) => setBuscar(e.target.value)}
                                    placeholder="Buscar en descripción, acción o tabla..."
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>

                            {/* Filtro por acción */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Acción
                                </label>
                                <select
                                    value={accion}
                                    onChange={(e) => setAccion(e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Todas las acciones</option>
                                    {acciones.map((acc) => (
                                        <option key={acc} value={acc}>
                                            {acc.charAt(0).toUpperCase() + acc.slice(1)}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* Filtro por tabla */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Tabla
                                </label>
                                <select
                                    value={tabla}
                                    onChange={(e) => setTabla(e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Todas las tablas</option>
                                    {tablas.map((tab) => (
                                        <option key={tab} value={tab}>
                                            {tab}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* Fecha desde */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha Desde
                                </label>
                                <input
                                    type="date"
                                    value={fechaDesde}
                                    onChange={(e) => setFechaDesde(e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>

                            {/* Fecha hasta */}
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha Hasta
                                </label>
                                <input
                                    type="date"
                                    value={fechaHasta}
                                    onChange={(e) => setFechaHasta(e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>

                            {/* Botones */}
                            <div className="flex items-end gap-2">
                                <button
                                    onClick={handleFilter}
                                    className="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                                >
                                    Filtrar
                                </button>
                                <button
                                    onClick={handleReset}
                                    className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                                >
                                    Limpiar
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Tabla de Bitácora */}
                    <div className="bg-white rounded-lg shadow-md overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fecha/Hora
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Usuario
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acción
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tabla
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Descripción
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            IP
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {bitacora.data && bitacora.data.length > 0 ? (
                                        bitacora.data.map((registro) => (
                                            <tr key={registro.id} className="hover:bg-gray-50">
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {formatearFecha(registro.fecha_creacion || registro.created_at)}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    {registro.usuario ? (
                                                        <div className="text-sm">
                                                            <div className="font-medium text-gray-900">
                                                                {registro.usuario.nombre} {registro.usuario.apellido}
                                                            </div>
                                                            <div className="text-gray-500 text-xs">
                                                                {registro.usuario.email}
                                                            </div>
                                                        </div>
                                                    ) : (
                                                        <span className="text-sm text-gray-400">Sistema</span>
                                                    )}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className={`px-2 py-1 text-xs font-semibold rounded-full ${getAccionColor(registro.accion)}`}>
                                                        {registro.accion}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {registro.tabla || '-'}
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-900">
                                                    {registro.descripcion}
                                                    {registro.registro_id && (
                                                        <span className="text-gray-400 ml-2">
                                                            (ID: {registro.registro_id})
                                                        </span>
                                                    )}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {registro.ip_address || '-'}
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="6" className="px-6 py-12 text-center text-gray-500">
                                                <svg className="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p className="text-sm">No se encontraron registros en la bitácora</p>
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* Paginación */}
                        {bitacora.links && bitacora.links.length > 3 && (
                            <div className="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                                <div className="flex items-center justify-between">
                                    <div className="text-sm text-gray-700">
                                        Mostrando{' '}
                                        <span className="font-medium">{bitacora.from}</span> a{' '}
                                        <span className="font-medium">{bitacora.to}</span> de{' '}
                                        <span className="font-medium">{bitacora.total}</span> registros
                                    </div>
                                    <div className="flex gap-2">
                                        {bitacora.links.map((link, index) => (
                                            <Link
                                                key={index}
                                                href={link.url || '#'}
                                                className={`px-3 py-2 text-sm rounded-lg ${
                                                    link.active
                                                        ? 'bg-blue-600 text-white'
                                                        : link.url
                                                        ? 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                                                        : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                                }`}
                                                dangerouslySetInnerHTML={{ __html: link.label }}
                                            />
                                        ))}
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

