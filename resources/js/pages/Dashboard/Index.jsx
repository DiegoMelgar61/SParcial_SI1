import AuthenticatedLayout from '@/layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Dashboard({ auth, stats }) {
    const statCards = [
        {
            title: 'Total Docentes',
            value: stats?.total_docentes || 0,
            icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            color: 'from-blue-500 to-blue-600',
            bgColor: 'from-blue-50 to-blue-100',
            textColor: 'text-blue-600',
            link: '/docentes'
        },
        {
            title: 'Total Materias',
            value: stats?.total_materias || 0,
            icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            color: 'from-green-500 to-emerald-600',
            bgColor: 'from-green-50 to-green-100',
            textColor: 'text-green-600',
            link: '/materias'
        },
        {
            title: 'Total Grupos',
            value: stats?.total_grupos || 0,
            icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            color: 'from-purple-500 to-purple-600',
            bgColor: 'from-purple-50 to-purple-100',
            textColor: 'text-purple-600',
            link: '/grupos'
        },
        {
            title: 'Total Aulas',
            value: stats?.total_aulas || 0,
            icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            color: 'from-amber-500 to-orange-600',
            bgColor: 'from-amber-50 to-orange-100',
            textColor: 'text-amber-600',
            link: '/aulas'
        },
    ];

    const quickActions = [
        {
            name: 'Ver Horarios',
            href: '/horarios',
            icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            color: 'text-blue-600',
            bgColor: 'bg-blue-50 hover:bg-blue-100'
        },
        {
            name: 'Gestionar Materias',
            href: '/materias',
            icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            color: 'text-green-600',
            bgColor: 'bg-green-50 hover:bg-green-100'
        },
        {
            name: 'Ver Grupos',
            href: '/grupos',
            icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            color: 'text-purple-600',
            bgColor: 'bg-purple-50 hover:bg-purple-100'
        },
        {
            name: 'Gestionar Docentes',
            href: '/docentes',
            icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            color: 'text-indigo-600',
            bgColor: 'bg-indigo-50 hover:bg-indigo-100'
        },
    ];

    return (
        <AuthenticatedLayout>
            <Head title="Dashboard" />

            <div className="p-4 sm:p-6 lg:p-8">
                <div className="max-w-7xl mx-auto">
                    {/* Welcome Header */}
                    <div className="mb-8">
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h1 className="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-900 to-purple-900 bg-clip-text text-transparent">
                                    ¡Bienvenido de nuevo! 👋
                                </h1>
                                <p className="mt-2 text-sm sm:text-base text-gray-600">
                                    {auth.user?.nombre} {auth.user?.apellido}
                                    <span className="mx-2 text-gray-400">•</span>
                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {auth.user?.rol?.nombre || 'Usuario'}
                                    </span>
                                </p>
                            </div>
                            <div className="flex items-center space-x-2">
                                <div className="text-right hidden sm:block">
                                    <p className="text-sm text-gray-500">Hoy es</p>
                                    <p className="text-lg font-semibold text-gray-900">
                                        {new Date().toLocaleDateString('es-ES', {
                                            weekday: 'short',
                                            day: 'numeric',
                                            month: 'short'
                                        })}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Stats Cards */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                        {statCards.map((stat, index) => (
                            <Link
                                key={index}
                                href={stat.link}
                                className="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden"
                            >
                                {/* Background Pattern */}
                                <div className={`absolute inset-0 bg-gradient-to-br ${stat.bgColor} opacity-50`} />
                                <div className="absolute top-0 right-0 w-32 h-32 transform translate-x-8 -translate-y-8">
                                    <div className={`w-full h-full rounded-full bg-gradient-to-br ${stat.color} opacity-10`} />
                                </div>

                                {/* Content */}
                                <div className="relative p-6">
                                    <div className="flex items-start justify-between">
                                        <div className="flex-1">
                                            <p className="text-sm font-medium text-gray-600 mb-1">{stat.title}</p>
                                            <p className="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">
                                                {stat.value}
                                            </p>
                                            <p className={`text-xs font-medium ${stat.textColor} flex items-center`}>
                                                Ver detalles
                                                <svg className="w-3 h-3 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                                </svg>
                                            </p>
                                        </div>
                                        <div className={`w-12 h-12 rounded-xl bg-gradient-to-br ${stat.color} flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform`}>
                                            <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={stat.icon} />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        ))}
                    </div>

                    {/* Main Content Grid */}
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {/* Welcome Card */}
                        <div className="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 sm:p-8">
                            <div className="flex items-start space-x-4">
                                <div className="flex-shrink-0">
                                    <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                                        <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                </div>
                                <div className="flex-1">
                                    <h3 className="text-xl sm:text-2xl font-bold text-gray-900 mb-2">
                                        Sistema Académico FICCT
                                    </h3>
                                    <p className="text-sm sm:text-base text-gray-600 leading-relaxed mb-4">
                                        Sistema de Control de Horarios y Asistencia Docente. Gestiona materias, grupos,
                                        horarios y el personal docente de manera eficiente y centralizada.
                                    </p>
                                    <div className="flex flex-wrap gap-2">
                                        <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            ✨ Intuitivo
                                        </span>
                                        <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            🚀 Rápido
                                        </span>
                                        <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            🔒 Seguro
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Quick Stats */}
                        <div className="bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl shadow-sm p-6 sm:p-8 text-white">
                            <h3 className="text-lg font-semibold mb-4 flex items-center">
                                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Resumen Rápido
                            </h3>
                            <div className="space-y-4">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-blue-100">Total Registros</span>
                                    <span className="text-2xl font-bold">
                                        {(stats?.total_docentes || 0) + (stats?.total_materias || 0) + (stats?.total_grupos || 0) + (stats?.total_aulas || 0)}
                                    </span>
                                </div>
                                <div className="h-px bg-white/20" />
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-blue-100">Gestión Completa</span>
                                    <span className="text-2xl font-bold">100%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Quick Actions */}
                    <div className="mt-8 bg-white rounded-2xl shadow-sm p-6 sm:p-8">
                        <h3 className="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg className="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Acciones Rápidas
                        </h3>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            {quickActions.map((action, index) => (
                                <Link
                                    key={index}
                                    href={action.href}
                                    className={`group flex items-center p-4 rounded-xl ${action.bgColor} transition-all duration-200`}
                                >
                                    <div className={`w-10 h-10 rounded-lg ${action.bgColor.replace('hover:', '')} flex items-center justify-center mr-3 group-hover:scale-110 transition-transform`}>
                                        <svg className={`w-5 h-5 ${action.color}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={action.icon} />
                                        </svg>
                                    </div>
                                    <div className="flex-1">
                                        <p className={`text-sm font-semibold ${action.color}`}>{action.name}</p>
                                    </div>
                                    <svg className={`w-4 h-4 ${action.color} group-hover:translate-x-1 transition-transform`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            ))}
                        </div>
                    </div>

                    {/* Help Section */}
                    <div className="mt-8 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-6 sm:p-8">
                        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div className="flex items-start space-x-4">
                                <div className="flex-shrink-0">
                                    <div className="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center">
                                        <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-900 mb-1">¿Necesitas Ayuda?</h3>
                                    <p className="text-sm text-gray-600">
                                        Nuestro equipo está disponible para asistirte en cualquier momento.
                                    </p>
                                </div>
                            </div>
                            <button className="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 whitespace-nowrap">
                                Contactar Soporte
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
