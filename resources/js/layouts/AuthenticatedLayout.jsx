import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function AuthenticatedLayout({ children }) {
    const { auth } = usePage().props;
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);
    const [sidebarOpen, setSidebarOpen] = useState(true);

    return (
        <div className="min-h-screen bg-gray-100">
            {/* Navigation */}
            <nav className="bg-white border-b border-gray-200 fixed w-full z-30 top-0">
                <div className="px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        <div className="flex">
                            {/* Logo */}
                            <div className="shrink-0 flex items-center">
                                <Link href="/dashboard" className="flex items-center space-x-3">
                                    <div className="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                                        <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <span className="text-xl font-bold text-gray-800">FICCT</span>
                                </Link>
                            </div>
                        </div>

                        {/* Settings Dropdown */}
                        <div className="hidden sm:flex sm:items-center sm:ml-6">
                            <div className="ml-3 relative">
                                <button
                                    onClick={() => setShowingNavigationDropdown(!showingNavigationDropdown)}
                                    className="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                >
                                    <span>{auth.user.name}</span>
                                    <svg className="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fillRule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clipRule="evenodd" />
                                    </svg>
                                </button>

                                {showingNavigationDropdown && (
                                    <div className="absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5">
                                        <Link
                                            href="/logout"
                                            method="post"
                                            as="button"
                                            className="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >
                                            Cerrar Sesión
                                        </Link>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            {/* Sidebar */}
            <aside className={`fixed top-16 left-0 z-20 h-full transition-transform ${sidebarOpen ? 'translate-x-0' : '-translate-x-full'} bg-white border-r border-gray-200 w-64`}>
                <div className="h-full px-3 py-4 overflow-y-auto">
                    <ul className="space-y-2 font-medium">
                        <li>
                            <Link
                                href="/dashboard"
                                className="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group"
                            >
                                <svg className="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                                <span className="ml-3">Dashboard</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/usuarios"
                                className="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group"
                            >
                                <svg className="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                </svg>
                                <span className="ml-3">Usuarios</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/materias"
                                className="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group"
                            >
                                <svg className="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                                </svg>
                                <span className="ml-3">Materias</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/grupos"
                                className="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group"
                            >
                                <svg className="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                </svg>
                                <span className="ml-3">Grupos</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/aulas"
                                className="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group"
                            >
                                <svg className="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clipRule="evenodd" />
                                </svg>
                                <span className="ml-3">Aulas</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/horarios"
                                className="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group"
                            >
                                <svg className="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clipRule="evenodd" />
                                </svg>
                                <span className="ml-3">Horarios</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/docentes"
                                className="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group"
                            >
                                <svg className="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                                </svg>
                                <span className="ml-3">Docentes</span>
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/carreras"
                                className="flex items-center p-2 text-gray-900 rounded-lg hover:bg-gray-100 group"
                            >
                                <svg className="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1H4zm1 2h10v10H5V5zm2 1v2h6V6H7z" />
                                </svg>
                                <span className="ml-3">Carreras</span>
                            </Link>
                        </li>

                    </ul>
                </div>
            </aside>

            {/* Main Content */}
            <main className={`p-4 ${sidebarOpen ? 'ml-64' : 'ml-0'} mt-16 transition-all`}>
                {children}
            </main>
        </div>
    );
}