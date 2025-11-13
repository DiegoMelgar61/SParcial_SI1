import { Head, Link } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";

export default function Show({ asistencia }) {
    const getEstadoBadge = (estado) => {
        const badges = {
            presente:
                "px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800",
            ausente:
                "px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800",
            licencia:
                "px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800",
            justificado:
                "px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800",
        };
        return (
            badges[estado] ||
            "px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800"
        );
    };

    const getEstadoNombre = (estado) => {
        const nombres = {
            presente: "Presente",
            ausente: "Ausente",
            licencia: "Licencia",
            justificado: "Justificado",
        };
        return nombres[estado] || "Desconocido";
    };

    const getTipoAusenciaNombre = (tipo) => {
        const nombres = {
            ninguna: "Ninguna",
            enfermedad: "Enfermedad",
            personal: "Personal",
            oficial: "Oficial",
            duelo: "Duelo",
            otra: "Otra",
        };
        return nombres[tipo] || "Ninguna";
    };

    const formatFecha = (fecha) => {
        return new Date(fecha).toLocaleDateString("es-ES", {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric",
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
            <Head title="Detalle de Asistencia" />

            <div className="py-6">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Breadcrumb */}
                    <div className="mb-6">
                        <Link
                            href="/asistencia-docente"
                            className="inline-flex items-center text-sm text-gray-600 hover:text-gray-900"
                        >
                            <svg
                                className="w-4 h-4 mr-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>
                            Volver a Mi Asistencia
                        </Link>
                    </div>

                    {/* Header */}
                    <div className="mb-6 flex justify-between items-start">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900">
                                Detalle de Asistencia
                            </h1>
                            <p className="mt-1 text-sm text-gray-600">
                                Información completa del registro de asistencia
                            </p>
                        </div>
                        <span className={getEstadoBadge(asistencia.estado)}>
                            {getEstadoNombre(asistencia.estado)}
                        </span>
                    </div>

                    {/* Card Principal */}
                    <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div className="p-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {/* Fecha */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-500 mb-1">
                                        Fecha
                                    </label>
                                    <div className="flex items-center text-gray-900">
                                        <svg
                                            className="w-5 h-5 text-gray-400 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth={2}
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>
                                        <span className="font-medium capitalize">
                                            {formatFecha(asistencia.fecha)}
                                        </span>
                                    </div>
                                </div>

                                {/* Hora de Registro */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-500 mb-1">
                                        Hora de registro
                                    </label>
                                    <div className="flex items-center text-gray-900">
                                        <svg
                                            className="w-5 h-5 text-gray-400 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth={2}
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        <span className="font-medium">
                                            {formatHora(
                                                asistencia.hora_registro
                                            )}
                                        </span>
                                    </div>
                                </div>

                                {/* Materia */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-500 mb-1">
                                        Materia
                                    </label>
                                    <div className="flex items-center text-gray-900">
                                        <svg
                                            className="w-5 h-5 text-gray-400 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth={2}
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                            />
                                        </svg>
                                        <span className="font-medium">
                                            {asistencia.horario?.materia
                                                ?.nombre || "N/A"}
                                        </span>
                                    </div>
                                </div>

                                {/* Grupo */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-500 mb-1">
                                        Grupo
                                    </label>
                                    <div className="flex items-center text-gray-900">
                                        <svg
                                            className="w-5 h-5 text-gray-400 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth={2}
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                            />
                                        </svg>
                                        <span className="font-medium">
                                            {asistencia.horario?.grupo
                                                ?.nombre || "N/A"}
                                        </span>
                                    </div>
                                </div>

                                {/* Aula */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-500 mb-1">
                                        Aula
                                    </label>
                                    <div className="flex items-center text-gray-900">
                                        <svg
                                            className="w-5 h-5 text-gray-400 mr-2"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth={2}
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                            />
                                        </svg>
                                        <span className="font-medium">
                                            {asistencia.horario?.aula?.nombre ||
                                                "N/A"}
                                        </span>
                                    </div>
                                </div>

                                {/* Estado */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-500 mb-1">
                                        Estado de asistencia
                                    </label>
                                    <span
                                        className={getEstadoBadge(
                                            asistencia.estado
                                        )}
                                    >
                                        {getEstadoNombre(asistencia.estado)}
                                    </span>
                                </div>

                                {/* Tipo de Ausencia (si aplica) */}
                                {asistencia.tipo_ausencia !== "ninguna" && (
                                    <div className="md:col-span-2">
                                        <label className="block text-sm font-medium text-gray-500 mb-1">
                                            Tipo de ausencia
                                        </label>
                                        <div className="flex items-center text-gray-900">
                                            <svg
                                                className="w-5 h-5 text-gray-400 mr-2"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth={2}
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                                />
                                            </svg>
                                            <span className="font-medium">
                                                {getTipoAusenciaNombre(
                                                    asistencia.tipo_ausencia
                                                )}
                                            </span>
                                        </div>
                                    </div>
                                )}

                                {/* Observaciones (si hay) */}
                                {asistencia.observaciones && (
                                    <div className="md:col-span-2">
                                        <label className="block text-sm font-medium text-gray-500 mb-2">
                                            Observaciones
                                        </label>
                                        <div className="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                            <p className="text-gray-700 whitespace-pre-wrap">
                                                {asistencia.observaciones}
                                            </p>
                                        </div>
                                    </div>
                                )}

                                {/* Documento Respaldo (si existe) */}
                                {asistencia.documento_respaldo && (
                                    <div className="md:col-span-2">
                                        <label className="block text-sm font-medium text-gray-500 mb-2">
                                            Documento de respaldo
                                        </label>
                                        <a
                                            href={`/storage/${asistencia.documento_respaldo}`}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition"
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
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                />
                                            </svg>
                                            Descargar documento
                                        </a>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Footer con información adicional */}
                        <div className="bg-gray-50 px-6 py-4 border-t border-gray-200">
                            <div className="flex items-center justify-between text-sm text-gray-500">
                                <div className="flex items-center">
                                    <svg
                                        className="w-4 h-4 mr-1"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                        />
                                    </svg>
                                    <span>
                                        Registrado por:{" "}
                                        {asistencia.registrado_por?.nombre ||
                                            "Sistema"}
                                    </span>
                                </div>
                                {asistencia.ip_registro && (
                                    <div className="flex items-center">
                                        <svg
                                            className="w-4 h-4 mr-1"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth={2}
                                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"
                                            />
                                        </svg>
                                        <span>
                                            IP: {asistencia.ip_registro}
                                        </span>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Botón de regreso */}
                    <div className="mt-6">
                        <Link
                            href="/asistencia-docente"
                            className="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition"
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
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"
                                />
                            </svg>
                            Volver al listado
                        </Link>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
