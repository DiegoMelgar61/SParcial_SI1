import { useForm } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import FICCTLogo from '../../components/ui/images/FICCT.png';

export default function Login() {
  const { data, setData, post, processing, errors, reset } = useForm({
    email: '',
    password: '',
    remember: false,
  });

  const [showPassword, setShowPassword] = useState(false);
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
    return () => reset('password');
  }, []);

  const submit = (e) => {
    e.preventDefault();
    post('/login');
  };

  return (
    <main
      className="min-h-screen relative overflow-hidden bg-[#0b1120] text-white"
      aria-label="Inicio de sesión del Sistema Académico FICCT"
    >
      {/* --- BACKGROUND --- */}
      <div aria-hidden="true">
        {/* Gradiente principal azules/dorados */}
        <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(1200px_600px_at_10%_-10%,#1e3a8a_10%,transparent_60%),radial-gradient(900px_500px_at_110%_0%,#0ea5e9_5%,transparent_50%),radial-gradient(700px_500px_at_50%_120%,#f59e0b22_15%,transparent_60%)]" />
        {/* “Vidriado” sutil */}
        <div className="pointer-events-none absolute inset-0 bg-[linear-gradient(115deg,#ffffff0a,transparent_40%)]" />
        {/* Retícula fina */}
        <div className="pointer-events-none absolute inset-0 opacity-[0.07] bg-[linear-gradient(to_right,#ffffff1a_1px,transparent_1px),linear-gradient(to_bottom,#ffffff1a_1px,transparent_1px)] bg-[size:24px_24px]" />
        {/* Estrellas/partículas (optimizado) */}
        <div className="pointer-events-none absolute inset-0">
          {[...Array(14)].map((_, i) => (
            <span
              key={i}
              className="absolute block w-[2px] h-[2px] rounded-full bg-white/50 will-change-transform"
              style={{
                left: `${(i * 71) % 100}%`,
                top: `${(i * 37) % 100}%`,
                animation: `float ${8 + (i % 5)}s ease-in-out ${i * 0.35}s infinite`,
              }}
            />
          ))}
        </div>
      </div>

      {/* --- CONTENIDO --- */}
      <section className="relative min-h-screen flex items-center justify-center px-4 sm:px-6 py-12">
        <div
          className={`w-full max-w-md transition-all duration-700 ${
            mounted ? 'translate-y-0 opacity-100' : 'translate-y-6 opacity-0'
          }`}
        >
          {/* Tarjeta glassmorphism */}
          <div className="relative rounded-2xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-[0_10px_50px_rgba(2,6,23,0.35)]">
            {/* Borde luminoso animado (sutil, respeta reduced motion) */}
            <div className="absolute -inset-px rounded-2xl bg-gradient-to-r from-blue-500/40 via-cyan-400/30 to-amber-400/40 opacity-60 blur-sm" aria-hidden="true" />
            <div className="relative z-10 p-7 sm:p-9">
              {/* Encabezado con logo (no tocar) */}
              <header className="text-center mb-8">
                <div className="mx-auto w-24 h-24 relative">
                  <div className="absolute inset-0 rounded-full bg-gradient-to-tr from-blue-500/30 to-amber-400/30 blur-xl" />
                  <img
                    src={FICCTLogo}
                    alt="FICCT Logo"
                    className="relative w-full h-full object-contain drop-shadow"
                    draggable="false"
                />
                </div>

                <h1 className="mt-4 text-2xl sm:text-3xl font-extrabold tracking-tight">
                  Sistema Académico <span className="text-amber-300">FICCT</span>
                </h1>
                <p className="mt-1 text-sm text-blue-100/80">
                  Universidad Autónoma Gabriel René Moreno
                </p>
              </header>

              {/* Formulario */}
              <form onSubmit={submit} noValidate className="space-y-5" aria-describedby="form-help">
                {/* Email */}
                <div>
                  <label htmlFor="email" className="block text-sm font-medium text-blue-50 mb-2">
                    Correo institucional
                  </label>
                  <div className="relative">
                    <span className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-100">
                      {/* mail icon */}
                      <svg className="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" d="M3 7l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                    </span>
                    <input
                      id="email"
                      type="email"
                      value={data.email}
                      onChange={(e) => setData('email', e.target.value)}
                      className={`w-full pl-10 pr-3 py-3 rounded-lg bg-white/10 placeholder-blue-100/60
                        text-white border focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 focus-visible:border-transparent
                        ${errors.email ? 'border-red-400' : 'border-white/20 hover:border-white/30'}
                      `}
                      inputMode="email"
                      autoComplete="email"
                      placeholder="tu@ficct.edu.bo"
                      required
                      aria-invalid={!!errors.email}
                      aria-describedby={errors.email ? 'email-error' : undefined}
                    />
                  </div>
                  {errors.email && (
                    <p id="email-error" className="mt-2 text-sm text-red-300 flex items-center">
                      <svg className="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fillRule="evenodd" d="M18 10A8 8 0 112 10a8 8 0 0116 0zm-7 4a1 1 0 10-2 0 1 1 0 002 0zM9 7a1 1 0 012 0v4a1 1 0 11-2 0V7z" clipRule="evenodd" />
                      </svg>
                      {errors.email}
                    </p>
                  )}
                </div>

                {/* Password */}
                <div>
                  <label htmlFor="password" className="block text-sm font-medium text-blue-50 mb-2">
                    Contraseña
                  </label>
                  <div className="relative">
                    <span className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-100">
                      {/* lock icon */}
                      <svg className="h-5 w-5 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" d="M12 17a2 2 0 100-4 2 2 0 000 4zm6 4H6a2 2 0 01-2-2v-6a2 2 0 012-2h12a2 2 0 012 2v6a2 2 0 01-2 2zM8 9V7a4 4 0 118 0v2" />
                      </svg>
                    </span>

                    <input
                      id="password"
                      type={showPassword ? 'text' : 'password'}
                      value={data.password}
                      onChange={(e) => setData('password', e.target.value)}
                      className={`w-full pl-10 pr-12 py-3 rounded-lg bg-white/10 placeholder-blue-100/60
                        text-white border focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 focus-visible:border-transparent
                        ${errors.password ? 'border-red-400' : 'border-white/20 hover:border-white/30'}
                      `}
                      autoComplete="current-password"
                      placeholder="••••••••"
                      required
                      aria-invalid={!!errors.password}
                      aria-describedby={errors.password ? 'password-error' : undefined}
                    />

                    <button
                      type="button"
                      onClick={() => setShowPassword((v) => !v)}
                      className="absolute inset-y-0 right-0 pr-3 flex items-center text-blue-100 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 rounded-md"
                      aria-label={showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'}
                      aria-pressed={showPassword}
                    >
                      {showPassword ? (
                        <svg className="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                          <path strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" d="M3 3l18 18M9.88 9.88A3 3 0 0012 15a3 3 0 002.12-.88M6.11 6.11C4.25 7.39 2.86 9.07 2 12c1.27 4.06 5.06 7 9.5 7 2.02 0 3.9-.6 5.47-1.62M14.12 9.88l.01-.01M21.9 12c-.76-2.43-2.44-4.1-4.4-5.21" />
                        </svg>
                      ) : (
                        <svg className="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                          <path strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" d="M2.46 12C3.73 7.94 7.52 5 12 5s8.27 2.94 9.54 7c-1.27 4.06-5.06 7-9.54 7S3.73 16.06 2.46 12z" />
                          <circle cx="12" cy="12" r="3" strokeWidth="1.8" />
                        </svg>
                      )}
                    </button>
                  </div>

                  {errors.password && (
                    <p id="password-error" className="mt-2 text-sm text-red-300 flex items-center">
                      <svg className="w-4 h-4 mr-1" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fillRule="evenodd" d="M18 10A8 8 0 112 10a8 8 0 0116 0zm-7 4a1 1 0 10-2 0 1 1 0 002 0zM9 7a1 1 0 012 0v4a1 1 0 11-2 0V7z" clipRule="evenodd" />
                      </svg>
                      {errors.password}
                    </p>
                  )}
                </div>

                {/* Recordarme / Olvidé contraseña */}
                <div className="flex items-center justify-between">
                  <label className="inline-flex items-center gap-2 select-none">
                    <input
                      id="remember"
                      type="checkbox"
                      checked={data.remember}
                      onChange={(e) => setData('remember', e.target.checked)}
                      className="h-4 w-4 rounded border-white/30 bg-white/10 text-amber-400 focus:ring-amber-400 focus:ring-offset-0"
                    />
                    <span className="text-sm text-blue-50">Recordarme</span>
                  </label>
                  <a
                    href="/forgot-password"
                    className="text-sm text-amber-300 hover:text-amber-200 underline-offset-2 hover:underline"
                  >
                    ¿Olvidaste tu contraseña?
                  </a>
                </div>

                {/* Botón enviar */}
                <button
                  type="submit"
                  disabled={processing}
                  aria-busy={processing}
                  className="group relative w-full py-3 px-4 rounded-lg font-semibold
                    text-white bg-gradient-to-r from-blue-600 to-indigo-600
                    hover:from-blue-500 hover:to-indigo-500
                    focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-400
                    disabled:opacity-60 disabled:cursor-not-allowed"
                >
                  {/* brillo barrido */}
                  <span
                    aria-hidden="true"
                    className="pointer-events-none absolute inset-0 rounded-lg overflow-hidden"
                  >
                    <span className="absolute -inset-1 bg-gradient-to-r from-transparent via-white/20 to-transparent translate-x-[-120%] group-hover:translate-x-[120%] transition-transform duration-700 ease-out" />
                  </span>

                  <span className="relative flex items-center justify-center">
                    {processing ? (
                      <>
                        <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                          <circle className="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                          <path className="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8v4A4 4 0 004 12z" />
                        </svg>
                        Iniciando sesión…
                      </>
                    ) : (
                      <>
                        Iniciar sesión
                        <svg className="ml-2 w-5 h-5 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                          <path strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" d="M13 7l5 5-5 5M6 12h12" />
                        </svg>
                      </>
                    )}
                  </span>
                </button>

                {/* Ayuda accesible */}
                <p id="form-help" className="sr-only">
                  Todos los campos son obligatorios. El correo debe ser institucional. El botón mostrar/ocultar contraseña cambia la visibilidad del campo contraseña.
                </p>
              </form>

              {/* Credenciales demo (contenedor glass liviano) */}
              <aside className="mt-6 rounded-lg border border-white/10 bg-white/5 p-4">
                <div className="flex items-start gap-2">
                  <svg className="w-5 h-5 text-amber-300 mt-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM9 9h2v5H9V9zm0-3h2v2H9V6z" />
                  </svg>
                  <div className="text-sm">
                    <p className="text-blue-50/90 font-medium">Credenciales de prueba</p>
                    <p className="text-blue-100/80">
                      <span className="text-blue-100/70">Email:</span>{' '}
                      <span className="font-mono text-amber-300">admin@ficct.edu.bo</span>
                    </p>
                    <p className="text-blue-100/80">
                      <span className="text-blue-100/70">Contraseña:</span>{' '}
                      <span className="font-mono text-amber-300">admin123456</span>
                    </p>
                  </div>
                </div>
              </aside>
            </div>
          </div>

          {/* Footer */}
          <footer className="text-center text-blue-100/70 text-xs sm:text-sm mt-8">
            © 2025 FICCT — Universidad Autónoma Gabriel René Moreno
          </footer>
        </div>
      </section>

      {/* --- Animaciones & accesibilidad extra --- */}
      <style>{`
        @keyframes float {
          0%,100% { transform: translateY(0); opacity: .35; }
          50% { transform: translateY(-10px); opacity: .7; }
        }

        /* Respeta usuarios con motion reducido */
        @media (prefers-reduced-motion: reduce) {
          [style*="animation"], .group:hover [style*="translate"] {
            animation: none !important;
            transition: none !important;
            transform: none !important;
          }
        }
      `}</style>
    </main>
  );
}
