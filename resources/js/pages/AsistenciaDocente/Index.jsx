import { Head, Link, usePage, router } from "@inertiajs/react";
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout";
import { useState } from "react";

export default function Index({ asistencias, estadisticas, filtros, estados }) {
    const { flash } = usePage().props;
    const [fechaInicio, setFechaInicio] = useState(filtros.fecha_inicio);
    const [fechaFin, setFechaFin] = useState(filtros.fecha_fin);
    const [estado, setEstado] = useState(filtros.estado || "todos");

    const handleFiltrar = () => {
        router.get(
            "/asistencia-docente",
            {
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin,
                estado: estado !== "todos" ? estado : null,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };

    const handleLimpiar = () => {
        const hoy = new Date();
        const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        const finMes = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);

        setFechaInicio(inicioMes.toISOString().split("T")[0]);
        setFechaFin(finMes.toISOString().split("T")[0]);
        setEstado("todos");

        router.get(
            "/asistencia-docente",
            {
                fecha_inicio: inicioMes.toISOString().split("T")[0],
                fecha_fin: finMes.toISOString().split("T")[0],
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };

    const getEstadoBadge = (estado) => {
        const badges = {
            presente:
                "px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800",
            ausente:
                "px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800",
            licencia:
                "px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800",
            justificado:
                "px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800",
        };
        return (
            badges[estado] ||
            "px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"
        );
    };

    const formatFecha = (fecha) => {
        return new Date(fecha).toLocaleDateString("es-ES", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
        });
    };

    const formatHora = (hora) => {
        return new Date(hora).toLocaleTimeString("es-ES", {
            hour: "2-digit",
            minute: "2-digit",
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Mi Asistencia" />

            <div className="py-6">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="flex justify-between items-center mb-6">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900">
                                Mi Asistencia
                            </h1>
                            <p className="mt-1 text-sm text-gray-600">
                                Consulta y gestiona tu registro de asistencia
                            </p>
                        </div>
                        <Link
                            href="/asistencia-docente/crear"
                            className="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                        >
                            <svg
                                className="w-5 h-5 mr-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                            Registrar Asistencia
                        </Link>
                    </div>

                    {/* Flash Messages */}
                    {flash?.success && (
                        <div className="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center">
                            <svg
                                className="w-5 h-5 text-green-600 mr-3"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fillRule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clipRule="evenodd"
                                />
                            </svg>
                            <span className="text-green-800 font-medium">
                                {flash.success}
                            </span>
                        </div>
                    )}

                    {flash?.error && (
                        <div className="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center">
                            <svg
                                className="w-5 h-5 text-red-600 mr-3"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fillRule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clipRule="evenodd"
                                />
                            </svg>
                            <span className="text-red-800 font-medium">
                                {flash.error}
                            </span>
                        </div>
                    )}

                    {/* Estadísticas */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        {/* Total Registros */}
                        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-medium text-gray-600">
                                        Total Registros
                                    </p>
                                    <p className="text-2xl font-bold text-gray-900 mt-1">
                                        {estadisticas.total_registros}
                                    </p>
                                </div>
                                <div className="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <svg
                                        className="w-6 h-6 text-gray-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {/* Presentes */}
                        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-medium text-gray-600">
                                        Presentes
                                    </p>
                                    <p className="text-2xl font-bold text-green-600 mt-1">
                                        {estadisticas.total_presentes}
                                    </p>
                                </div>
                                <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg
                                        className="w-6 h-6 text-green-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {/* Ausentes */}
                        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-medium text-gray-600">
                                        Ausentes
                                    </p>
                                    <p className="text-2xl font-bold text-red-600 mt-1">
                                        {estadisticas.total_ausentes}
                                    </p>
                                </div>
                                <div className="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                    <svg
                                        className="w-6 h-6 text-red-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {/* Porcentaje */}
                        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-medium text-gray-600">
                                        % Asistencia
                                    </p>
                                    <p className="text-2xl font-bold text-blue-600 mt-1">
                                        {estadisticas.porcentaje_asistencia}%
                                    </p>
                                </div>
                                <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg
                                        className="w-6 h-6 text-blue-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Filtros */}
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">
                            Filtros
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha Inicio
                                </label>
                                <input
                                    type="date"
                                    value={fechaInicio}
                                    onChange={(e) =>
                                        setFechaInicio(e.target.value)
                                    }
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha Fin
                                </label>
                                <input
                                    type="date"
                                    value={fechaFin}
                                    onChange={(e) =>
                                        setFechaFin(e.target.value)
                                    }
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Estado
                                </label>
                                <select
                                    value={estado}
                                    onChange={(e) => setEstado(e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="todos">Todos</option>
                                    {Object.entries(estados).map(
                                        ([value, label]) => (
                                            <option key={value} value={value}>
                                                {label}
                                            </option>
                                        )
                                    )}
                                </select>
                            </div>

                            <div className="flex items-end gap-2">
                                <button
                                    onClick={handleFiltrar}
                                    className="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                                >
                                    Filtrar
                                </button>
                                <button
                                    onClick={handleLimpiar}
                                    className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 transition"
                                >
                                    Limpiar
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Tabla de Asistencias */}
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        {asistencias.data.length === 0 ? (
                            <div className="text-center py-12">
                                <svg
                                    className="mx-auto h-12 w-12 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                    />
                                </svg>
                                <h3 className="mt-2 text-sm font-medium text-gray-900">
                                    No hay registros de asistencia
                                </h3>
                                <p className="mt-1 text-sm text-gray-500">
                                    Comienza registrando tu primera asistencia.
                                </p>
                                <div className="mt-6">
                                    <Link
                                        href="/asistencia-docente/crear"
                                        className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                                    >
                                        <svg
                                            className="w-5 h-5 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth={2}
                                                d="M12 4v16m8-8H4"
                                            />
                                        </svg>
                                        Registrar Asistencia
                                    </Link>
                                </div>
                            </div>
                        ) : (
                            <>
                                <div className="overflow-x-auto">
                                    <table className="min-w-full divide-y divide-gray-200">
                                        <thead className="bg-gray-50">
                                            <tr>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Fecha
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Hora
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Materia
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Grupo
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Aula
                                                </th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Estado
                                                </th>
                                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Acciones
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="bg-white divide-y divide-gray-200">
                                            {asistencias.data.map(
                                                (asistencia) => (
                                                    <tr
                                                        key={asistencia.id}
                                                        className="hover:bg-gray-50"
                                                    >
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <div className="text-sm text-gray-900">
                                                                {formatFecha(
                                                                    asistencia.fecha
                                                                )}
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <div className="text-sm text-gray-500">
                                                                {formatHora(
                                                                    asistencia.hora_registro
                                                                )}
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <div className="text-sm font-medium text-gray-900">
                                                                {asistencia
                                                                    .horario
                                                                    ?.materia
                                                                    ?.nombre ||
                                                                    "N/A"}
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <div className="text-sm text-gray-500">
                                                                {asistencia
                                                                    .horario
                                                                    ?.grupo
                                                                    ?.nombre ||
                                                                    "N/A"}
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <div className="text-sm text-gray-500">
                                                                {asistencia
                                                                    .horario
                                                                    ?.aula
                                                                    ?.nombre ||
                                                                    "N/A"}
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap">
                                                            <span
                                                                className={getEstadoBadge(
                                                                    asistencia.estado
                                                                )}
                                                            >
                                                                {
                                                                    estados[
                                                                        asistencia
                                                                            .estado
                                                                    ]
                                                                }
                                                            </span>
                                                        </td>
                                                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <Link
                                                                href={`/asistencia-docente/${asistencia.uuid}`}
                                                                className="text-blue-600 hover:text-blue-900"
                                                            >
                                                                Ver detalle
                                                            </Link>
                                                        </td>
                                                    </tr>
                                                )
                                            )}
                                        </tbody>
                                    </table>
                                </div>

                                {/* Paginación */}
                                {asistencias.links.length > 3 && (
                                    <div className="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                                        <div className="flex-1 flex justify-between sm:hidden">
                                            {asistencias.prev_page_url && (
                                                <Link
                                                    href={
                                                        asistencias.prev_page_url
                                                    }
                                                    className="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                                >
                                                    Anterior
                                                </Link>
                                            )}
                                            {asistencias.next_page_url && (
                                                <Link
                                                    href={
                                                        asistencias.next_page_url
                                                    }
                                                    className="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                                >
                                                    Siguiente
                                                </Link>
                                            )}
                                        </div>
                                        <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                            <div>
                                                <p className="text-sm text-gray-700">
                                                    Mostrando{" "}
                                                    <span className="font-medium">
                                                        {asistencias.from}
                                                    </span>{" "}
                                                    a{" "}
                                                    <span className="font-medium">
                                                        {asistencias.to}
                                                    </span>{" "}
                                                    de{" "}
                                                    <span className="font-medium">
                                                        {asistencias.total}
                                                    </span>{" "}
                                                    resultados
                                                </p>
                                            </div>
                                            <div>
                                                <nav className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                                    {asistencias.links.map(
                                                        (link, index) => (
                                                            <Link
                                                                key={index}
                                                                href={
                                                                    link.url ||
                                                                    "#"
                                                                }
                                                                className={`relative inline-flex items-center px-4 py-2 border text-sm font-medium ${
                                                                    link.active
                                                                        ? "z-10 bg-blue-50 border-blue-500 text-blue-600"
                                                                        : "bg-white border-gray-300 text-gray-500 hover:bg-gray-50"
                                                                } ${
                                                                    !link.url
                                                                        ? "cursor-not-allowed opacity-50"
                                                                        : ""
                                                                }`}
                                                                dangerouslySetInnerHTML={{
                                                                    __html: link.label,
                                                                }}
                                                            />
                                                        )
                                                    )}
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
