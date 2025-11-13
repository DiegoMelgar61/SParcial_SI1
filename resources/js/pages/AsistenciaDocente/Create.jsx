import { Head, Link, useForm, usePage } from "@inertiajs/react";
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout";
import { useState } from "react";

export default function Create({ horarios, tiposAusencia, estados }) {
    const { flash } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        horario_id: "",
        estado: "",
        tipo_ausencia: "ninguna",
        observaciones: "",
        documento_respaldo: null,
        latitud: null,
        longitud: null,
    });

    const [selectedEstado, setSelectedEstado] = useState("");

    // Obtener geolocalización
    const obtenerUbicacion = () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    setData({
                        ...data,
                        latitud: position.coords.latitude,
                        longitud: position.coords.longitude,
                    });
                },
                (error) => {
                    console.error("Error al obtener ubicación:", error);
                }
            );
        }
    };

    const handleEstadoClick = (estado) => {
        setSelectedEstado(estado);
        setData("estado", estado);

        // Si el estado es presente, resetear tipo de ausencia
        if (estado === "presente") {
            setData("tipo_ausencia", "ninguna");
        }

        // Intentar obtener ubicación
        obtenerUbicacion();
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post("/asistencia-docente", {
            forceFormData: true,
        });
    };

    const estadosConfig = {
        presente: {
            label: "Presente",
            color: "green",
            icon: (
                <svg
                    className="w-8 h-8"
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
            ),
        },
        ausente: {
            label: "Ausente",
            color: "red",
            icon: (
                <svg
                    className="w-8 h-8"
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
            ),
        },
        licencia: {
            label: "Licencia",
            color: "yellow",
            icon: (
                <svg
                    className="w-8 h-8"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth={2}
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                </svg>
            ),
        },
        justificado: {
            label: "Justificado",
            color: "blue",
            icon: (
                <svg
                    className="w-8 h-8"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth={2}
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
            ),
        },
    };

    const getColorClasses = (color, isSelected) => {
        const colors = {
            green: isSelected
                ? "border-green-500 bg-green-50 text-green-700"
                : "border-gray-200 hover:border-green-400 text-gray-700",
            red: isSelected
                ? "border-red-500 bg-red-50 text-red-700"
                : "border-gray-200 hover:border-red-400 text-gray-700",
            yellow: isSelected
                ? "border-yellow-500 bg-yellow-50 text-yellow-700"
                : "border-gray-200 hover:border-yellow-400 text-gray-700",
            blue: isSelected
                ? "border-blue-500 bg-blue-50 text-blue-700"
                : "border-gray-200 hover:border-blue-400 text-gray-700",
        };
        return colors[color] || colors.green;
    };

    return (
        <AuthenticatedLayout>
            <Head title="Registrar Asistencia" />

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
                    <div className="mb-6">
                        <h1 className="text-3xl font-bold text-gray-900">
                            Registrar Asistencia
                        </h1>
                        <p className="mt-1 text-sm text-gray-600">
                            Registra tu asistencia para la clase de hoy
                        </p>
                    </div>

                    {/* Flash Messages */}
                    {flash?.error && (
                        <div className="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center">
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

                    {/* Formulario */}
                    {horarios.length === 0 ? (
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
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <h3 className="text-lg font-medium text-gray-900 mb-2">
                                No tienes clases hoy
                            </h3>
                            <p className="text-gray-500 mb-6">
                                No hay horarios programados para hoy o ya
                                registraste toda tu asistencia.
                            </p>
                            <Link
                                href="/asistencia-docente"
                                className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                            >
                                Volver a Mi Asistencia
                            </Link>
                        </div>
                    ) : (
                        <form onSubmit={handleSubmit}>
                            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
                                {/* Select Horario */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Selecciona tu clase{" "}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        value={data.horario_id}
                                        onChange={(e) =>
                                            setData(
                                                "horario_id",
                                                e.target.value
                                            )
                                        }
                                        className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        required
                                    >
                                        <option value="">
                                            Selecciona un horario...
                                        </option>
                                        {horarios.map((horario) => (
                                            <option
                                                key={horario.id}
                                                value={horario.id}
                                            >
                                                {horario.hora_inicio} -{" "}
                                                {horario.materia?.nombre} (Grupo{" "}
                                                {horario.grupo?.nombre}) - Aula{" "}
                                                {horario.aula?.nombre}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.horario_id && (
                                        <p className="mt-1 text-sm text-red-600">
                                            {errors.horario_id}
                                        </p>
                                    )}
                                </div>

                                {/* Botones de Estado */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-3">
                                        Estado de asistencia{" "}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        {Object.entries(estadosConfig).map(
                                            ([estado, config]) => (
                                                <button
                                                    key={estado}
                                                    type="button"
                                                    onClick={() =>
                                                        handleEstadoClick(
                                                            estado
                                                        )
                                                    }
                                                    className={`p-6 border-2 rounded-xl transition-all duration-200 cursor-pointer hover:shadow-md ${getColorClasses(
                                                        config.color,
                                                        selectedEstado ===
                                                            estado
                                                    )}`}
                                                >
                                                    <div className="flex flex-col items-center space-y-2">
                                                        {config.icon}
                                                        <span className="font-semibold text-sm">
                                                            {config.label}
                                                        </span>
                                                    </div>
                                                </button>
                                            )
                                        )}
                                    </div>
                                    {errors.estado && (
                                        <p className="mt-2 text-sm text-red-600">
                                            {errors.estado}
                                        </p>
                                    )}
                                </div>

                                {/* Tipo de Ausencia (condicional) */}
                                {selectedEstado &&
                                    selectedEstado !== "presente" && (
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                                Tipo de ausencia{" "}
                                                <span className="text-red-500">
                                                    *
                                                </span>
                                            </label>
                                            <select
                                                value={data.tipo_ausencia}
                                                onChange={(e) =>
                                                    setData(
                                                        "tipo_ausencia",
                                                        e.target.value
                                                    )
                                                }
                                                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                required
                                            >
                                                {Object.entries(
                                                    tiposAusencia
                                                ).map(([value, label]) => (
                                                    <option
                                                        key={value}
                                                        value={value}
                                                    >
                                                        {label}
                                                    </option>
                                                ))}
                                            </select>
                                            {errors.tipo_ausencia && (
                                                <p className="mt-1 text-sm text-red-600">
                                                    {errors.tipo_ausencia}
                                                </p>
                                            )}
                                        </div>
                                    )}

                                {/* Observaciones */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Observaciones (opcional)
                                    </label>
                                    <textarea
                                        value={data.observaciones}
                                        onChange={(e) =>
                                            setData(
                                                "observaciones",
                                                e.target.value
                                            )
                                        }
                                        rows={4}
                                        className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Agrega cualquier observación adicional..."
                                    />
                                    {errors.observaciones && (
                                        <p className="mt-1 text-sm text-red-600">
                                            {errors.observaciones}
                                        </p>
                                    )}
                                </div>

                                {/* Documento Respaldo (condicional) */}
                                {(selectedEstado === "licencia" ||
                                    selectedEstado === "justificado") && (
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2">
                                            Documento de respaldo
                                        </label>
                                        <div className="mt-1 flex items-center">
                                            <input
                                                type="file"
                                                onChange={(e) =>
                                                    setData(
                                                        "documento_respaldo",
                                                        e.target.files[0]
                                                    )
                                                }
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                className="block w-full text-sm text-gray-500
                                                    file:mr-4 file:py-2 file:px-4
                                                    file:rounded-lg file:border-0
                                                    file:text-sm file:font-semibold
                                                    file:bg-blue-50 file:text-blue-700
                                                    hover:file:bg-blue-100
                                                    cursor-pointer"
                                            />
                                        </div>
                                        <p className="mt-2 text-sm text-gray-500">
                                            PDF, JPG, JPEG o PNG (máx. 2MB)
                                        </p>
                                        {errors.documento_respaldo && (
                                            <p className="mt-1 text-sm text-red-600">
                                                {errors.documento_respaldo}
                                            </p>
                                        )}
                                    </div>
                                )}

                                {/* Botones */}
                                <div className="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                                    <Link
                                        href="/asistencia-docente"
                                        className="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                                    >
                                        Cancelar
                                    </Link>
                                    <button
                                        type="submit"
                                        disabled={
                                            processing ||
                                            !data.horario_id ||
                                            !data.estado
                                        }
                                        className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                                    >
                                        {processing
                                            ? "Registrando..."
                                            : "Registrar Asistencia"}
                                    </button>
                                </div>
                            </div>
                        </form>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
