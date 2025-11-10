<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil — Plataforma Universitaria INF342</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col font-sans antialiased">

    <!-- Barra superior -->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-3">
            <h1 class="text-lg md:text-xl font-semibold text-gray-700 tracking-wide">
                Plataforma Universitaria
            </h1>

            <div class="flex items-center gap-4">
                <div class="hidden sm:block text-right">
                    <p class="font-medium text-gray-800">{{ $user['nomb_comp'] ?? 'Usuario' }}</p>
                    <p class="text-xs text-gray-500">{{ isset($user['rol']) ? ucfirst($user['rol']) : 'Sin rol' }}</p>
                </div>

                <!-- Botón de inicio -->
                <a href="/"
                   class="text-sm bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-700 px-4 py-2 rounded-md font-medium transition">
                    Inicio
                </a>
            </div>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="flex-1 max-w-4xl mx-auto w-full py-10 px-6">

        <!-- Encabezado -->
        <div class="mb-8">
            <h2 class="text-2xl md:text-3xl font-semibold text-gray-800 mb-2">Mi Perfil</h2>
            <p class="text-gray-500 text-sm">Administra tu información personal y configuración de cuenta</p>
        </div>

        <!-- Tarjeta de información personal -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <!-- Header de la tarjeta -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border-b border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-2xl shadow-lg">
                        {{ isset($user['nomb_comp']) ? strtoupper(substr($user['nomb_comp'], 0, 1)) : '?' }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $user['nomb_comp'] ?? 'Sin nombre' }}</h3>
                        <p class="text-sm text-gray-600">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ isset($user['rol']) ? ucfirst($user['rol']) : 'Sin rol' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información personal (solo lectura) -->
            <div class="p-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Información Personal</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo</label>
                        <input type="text" value="{{ $user['nomb_comp'] ?? '' }}" readonly
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-50 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">Este campo no se puede editar</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cédula de Identidad</label>
                        <input type="text" value="{{ $user['ci'] ?? '' }}" readonly
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-50 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">Este campo no se puede editar</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Código de Usuario</label>
                        <input type="text" value="{{ $user['codigo'] ?? '' }}" readonly
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-50 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">Este campo no se puede editar</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rol en el Sistema</label>
                        <input type="text" value="{{ isset($user['rol']) ? ucfirst($user['rol']) : '' }}" readonly
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-50 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">Este campo no se puede editar</p>
                    </div>

                    @if(isset($user['profesion']) && !empty($user['profesion']))
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Profesión</label>
                        <input type="text" value="{{ $user['profesion'] }}" readonly
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-50 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">Este campo no se puede editar</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Formulario de datos editables -->
        <form id="form-perfil" class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="p-6 border-b border-gray-200">
                <h4 class="text-lg font-semibold text-gray-900">Información de Contacto</h4>
                <p class="text-sm text-gray-600 mt-1">Actualiza tu información de contacto</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                            Teléfono <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="telefono" name="telefono" value="{{ $user['tel'] ?? '' }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               placeholder="Ej: 7654321">
                    </div>

                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700 mb-2">
                            Correo Electrónico <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="correo" name="correo" value="{{ $user['correo'] ?? '' }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               placeholder="usuario@ejemplo.com">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" id="btn-guardar-contacto"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>

        <!-- Formulario de cambio de contraseña -->
        <form id="form-password" class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h4 class="text-lg font-semibold text-gray-900">Seguridad de la Cuenta</h4>
                <p class="text-sm text-gray-600 mt-1">Cambia tu contraseña para mantener tu cuenta segura</p>
            </div>

            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label for="password-actual" class="block text-sm font-medium text-gray-700 mb-2">
                            Contraseña Actual <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password-actual" name="password_actual"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               placeholder="Ingresa tu contraseña actual">
                    </div>

                    <div>
                        <label for="password-nueva" class="block text-sm font-medium text-gray-700 mb-2">
                            Nueva Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password-nueva" name="password_nueva"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               placeholder="Mínimo 6 caracteres">
                    </div>

                    <div>
                        <label for="password-confirmar" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmar Nueva Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password-confirmar" name="password_confirmar"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500"
                               placeholder="Repite la nueva contraseña">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" id="btn-cambiar-password"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                        Cambiar Contraseña
                    </button>
                </div>
            </div>
        </form>

    </main>

    <!-- Pie de página -->
    <footer class="text-center py-4 text-xs text-gray-500 border-t border-gray-200 mt-auto">
        © 2025 Plataforma Universitaria — Todos los derechos reservados
    </footer>

    <!-- JavaScript Externo -->
    <script src="/static/scripts/perfil.js"></script>

</body>
</html>
