import { Head } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";

export default function MisMaterias({ materias, docente }) {
    const diasSemana = {
        Lunes: "Lun",
        Martes: "Mar",
        Miércoles: "Mié",
        Jueves: "Jue",
        Viernes: "Vie",
        Sábado: "Sáb",
        Domingo: "Dom",
    };

    return (
        <AuthenticatedLayout>
            <Head title="Mis Materias" />

            <div className="py-6">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Header */}
                    <div className="mb-8">
                        <h1 className="text-3xl font-bold text-gray-900">
                            {docente.nombre_completo}
                        </h1>
                        <p className="mt-1 text-lg text-gray-600">
                            {docente.titulo_academico}
                        </p>
                        {docente.especializacion && (
                            <p className="mt-1 text-sm text-gray-500">
                                Especialización: {docente.especializacion}
                            </p>
                        )}
                    </div>

                    {/* Materias Grid */}
                    {materias.length === 0 ? (
                        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                            <svg
                                className="mx-auto h-16 w-16 text-gray-400 mb-4"
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
                            <h3 className="text-lg font-medium text-gray-900 mb-2">
                                No tienes materias asignadas
                            </h3>
                            <p className="text-gray-500">
                                Contacta con el administrador para que te
                                asignen materias y horarios.
                            </p>
                        </div>
                    ) : (
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {materias.map((materia) => (
                                <div
                                    key={materia.id}
                                    className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
                                >
                                    {/* Header con degradado */}
                                    <div className="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                                        <h3 className="text-xl font-bold text-white">
                                            {materia.nombre}
                                        </h3>
                                        <p className="text-blue-100 text-sm mt-1">
                                            Código: {materia.sigla}
                                        </p>
                                    </div>

                                    {/* Cuerpo del card */}
                                    <div className="p-6 space-y-4">
                                        {/* Horas semanales */}
                                        <div className="flex items-center justify-center bg-blue-50 rounded-lg p-4">
                                            <svg
                                                className="w-6 h-6 text-blue-600 mr-2"
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
                                            <span className="text-2xl font-bold text-blue-600">
                                                {materia.horas_semanales}
                                            </span>
                                            <span className="text-sm text-gray-600 ml-2">
                                                horas semanales
                                            </span>
                                        </div>

                                        {/* Grupos */}
                                        <div>
                                            <label className="block text-sm font-semibold text-gray-700 mb-2">
                                                Grupos asignados
                                            </label>
                                            <div className="flex flex-wrap gap-2">
                                                {materia.grupos.map((grupo) => (
                                                    <span
                                                        key={grupo.id}
                                                        className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                                                    >
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
                                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                                            />
                                                        </svg>
                                                        Grupo {grupo.nombre}
                                                    </span>
                                                ))}
                                            </div>
                                        </div>

                                        {/* Horarios */}
                                        <div>
                                            <label className="block text-sm font-semibold text-gray-700 mb-3">
                                                Horarios de clase
                                            </label>
                                            <div className="space-y-2">
                                                {materia.horarios.map(
                                                    (horario) => (
                                                        <div
                                                            key={horario.id}
                                                            className="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200"
                                                        >
                                                            <div className="flex items-center space-x-3">
                                                                <div className="flex-shrink-0">
                                                                    <svg
                                                                        className="w-5 h-5 text-gray-500"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            strokeLinecap="round"
                                                                            strokeLinejoin="round"
                                                                            strokeWidth={
                                                                                2
                                                                            }
                                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                                        />
                                                                    </svg>
                                                                </div>
                                                                <div>
                                                                    <p className="text-sm font-medium text-gray-900">
                                                                        {diasSemana[
                                                                            horario
                                                                                .dia_semana
                                                                        ] ||
                                                                            horario.dia_semana}
                                                                    </p>
                                                                    <p className="text-xs text-gray-500">
                                                                        {
                                                                            horario.hora_inicio
                                                                        }{" "}
                                                                        -{" "}
                                                                        {
                                                                            horario.hora_fin
                                                                        }
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            {horario.aula && (
                                                                <div className="flex items-center text-sm text-gray-600">
                                                                    <svg
                                                                        className="w-4 h-4 mr-1"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            strokeLinecap="round"
                                                                            strokeLinejoin="round"
                                                                            strokeWidth={
                                                                                2
                                                                            }
                                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                                                        />
                                                                    </svg>
                                                                    <span className="font-medium">
                                                                        {
                                                                            horario
                                                                                .aula
                                                                                .nombre
                                                                        }
                                                                    </span>
                                                                    {horario
                                                                        .aula
                                                                        .edificio && (
                                                                        <span className="ml-1 text-gray-500">
                                                                            (
                                                                            {
                                                                                horario
                                                                                    .aula
                                                                                    .edificio
                                                                            }
                                                                            )
                                                                        </span>
                                                                    )}
                                                                </div>
                                                            )}
                                                        </div>
                                                    )
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}

                    {/* Información adicional */}
                    {materias.length > 0 && (
                        <div className="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div className="flex items-start">
                                <svg
                                    className="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fillRule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clipRule="evenodd"
                                    />
                                </svg>
                                <div>
                                    <h4 className="text-sm font-semibold text-blue-900 mb-1">
                                        Información importante
                                    </h4>
                                    <p className="text-sm text-blue-700">
                                        Recuerda registrar tu asistencia
                                        puntualmente para cada clase. Puedes
                                        hacerlo desde la sección "Registrar
                                        Asistencia" en el menú principal.
                                    </p>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Estadísticas resumen */}
                    {materias.length > 0 && (
                        <div className="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <svg
                                            className="w-8 h-8 text-blue-600"
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
                                    </div>
                                    <div className="ml-4">
                                        <p className="text-sm font-medium text-gray-500">
                                            Total Materias
                                        </p>
                                        <p className="text-2xl font-bold text-gray-900">
                                            {materias.length}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <svg
                                            className="w-8 h-8 text-purple-600"
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
                                    </div>
                                    <div className="ml-4">
                                        <p className="text-sm font-medium text-gray-500">
                                            Total Grupos
                                        </p>
                                        <p className="text-2xl font-bold text-gray-900">
                                            {materias.reduce(
                                                (acc, materia) =>
                                                    acc + materia.grupos.length,
                                                0
                                            )}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <svg
                                            className="w-8 h-8 text-green-600"
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
                                    </div>
                                    <div className="ml-4">
                                        <p className="text-sm font-medium text-gray-500">
                                            Horas Semanales
                                        </p>
                                        <p className="text-2xl font-bold text-gray-900">
                                            {materias.reduce(
                                                (acc, materia) =>
                                                    acc +
                                                    materia.horas_semanales,
                                                0
                                            )}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
