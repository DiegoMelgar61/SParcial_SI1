# Sistema de Gestión Docente — INF342

Aplicación web desarrollada con **PHP** usando una arquitectura **modular inspirada en Flask Application Factory**.  
El sistema permite gestionar **asistencias docentes**, **licencias**, **aulas**, **materias** y **horarios**, integrando control de usuarios, autenticación y bitácora de acciones.

---

## 🚀 Tecnologías utilizadas
- **Backend:** PHP (arquitectura modular inspirada en Laravel y Flask)
- **Frontend:** HTML5, TailwindCSS, JavaScript
- **Base de datos:** PostgreSQL
- **Autenticación:** Sesiones y validación de roles
- **Bitácora:** Registro automático de acciones en la base de datos
- **Servidor local:** CLI con `php run.php`
- **Entorno recomendado:** PHP 8.x o superior

---

## 🌐 Despliegue en Render (CI/CD)

El sistema está desplegado en **Render** con un flujo de **Integración y Despliegue Continuo (CI/CD)**.  
Cada vez que se realiza un *push* a la rama principal (`main`), Render reconstruye automáticamente el entorno y publica la nueva versión del sistema.

**Detalles del entorno de despliegue:**
- **Proveedor:** Render  
- **Runtime:** PHP 8.x  
- **Base de datos:** PostgreSQL (servicio Render Database)  
- **Rama de despliegue:** `main`  
- **Punto de entrada:** `run.php`  
- **Puerto gestionado automáticamente por Render**

🔗 **Aplicación en producción:**  
[https://exa2-inf342.onrender.com/](https://exa2-inf342.onrender.com/)

---
## 🧩 Estructura del proyecto

```
INF342_2EXA/
│
├── app/
│   ├── Classes/                # Clases PHP reutilizables (modelos, helpers, etc.)
│   ├── Http/                   # Controladores HTTP y lógica de rutas
│   ├── Providers/              # Servicios, inicializadores, middlewares
│   ├── services/               # Funciones o módulos independientes (bitácora, auth, etc.)
│   ├── static/                 # Recursos estáticos: scripts JS, CSS, imágenes
│   ├── templates/              # Plantillas HTML o Blade
│   ├── __init__.php            # Archivo de inicialización modular
│   └── Config.php              # Configuración principal (conexión BD, constantes)
│
├── bootstrap/                  # Archivos de arranque y carga del sistema
├── config/                     # Archivos de configuración global del proyecto
├── docs/                       # Documentación, diagramas o imágenes
├── public/                     # Carpeta accesible públicamente (punto de entrada web)
├── resources/                  # Archivos fuente del frontend (Tailwind, vistas)
├── routes/                     # Definición de rutas por módulo
├── storage/                    # Archivos temporales, logs o caché
├── vendor/                     # Dependencias instaladas por Composer
│
├── .env                        # Variables de entorno (configuración local)
├── .gitignore                  # Archivos y carpetas que Git debe ignorar
├── artisan                     # CLI interna de Laravel (si se usa para comandos)
├── composer.json               # Definición de dependencias PHP
├── composer.lock               # Bloqueo de versiones de dependencias
├── dockerfile                  # Configuración para entorno Docker
├── hash_pass.php               # Script auxiliar para generar hashes de contraseñas
├── run.php                     # Punto de entrada principal de la aplicación
└── README.md                   # Documentación general del proyecto
```
---

## 🧠 Módulos principales

- **Módulo Docencia:** Panel principal del docente con acceso a sus herramientas.
- **Módulo Asistencia:** Registro mediante formulario o QR, con cálculo automático del estado según hora.
- **Módulo Licencias:** Solicitud y aprobación de licencias docentes.
- **Módulo Aulas:** Clasificación por tipo (teórica, laboratorio, auditorio).
- **Bitácora:** Registro automático de cada acción realizada por los usuarios.

---

## 🧾 Base de datos

**Motor:** PostgreSQL  
**Esquema principal:** `ex_g32`

**Tablas principales:**
- `usuario`
- `clase`
- `materia`
- `materia_grupo`
- `horario`
- `asistencia`
- `bitacora`

Cada acción realizada en los módulos se registra con fecha, usuario, descripción y estado en la tabla `bitacora`.

---

## 🧑‍💻 Equipo de desarrollo

**Proyecto Grupo 32 — INF342**  
Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones  
**Universidad Autónoma Gabriel René Moreno (UAGRM)**

- Auad Castillo Miguel Andres
- Marces Gutierrez Erick Miguel
---

© 2025 Grupo 32 — FICCT UAGRM | Proyecto académico INF342-SA