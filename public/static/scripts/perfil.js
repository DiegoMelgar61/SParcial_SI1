/**
 * perfil.js - Gestión del perfil de usuario
 * Maneja la visualización y actualización de información personal
 */

document.addEventListener("DOMContentLoaded", () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
    
    // ========================================
    // MÓDULO 1: Panel de Usuario (Avatar Dropdown)
    // ========================================
    const userAvatar = document.getElementById("user-avatar");
    const userAside = document.getElementById("user-aside");

    if (userAvatar && userAside) {
        // Toggle del panel al hacer clic en el avatar
        userAvatar.addEventListener("click", (e) => {
            e.stopPropagation();
            
            if (userAside.classList.contains("opacity-0")) {
                // Mostrar panel
                userAside.classList.remove("hidden");
                setTimeout(() => {
                    userAside.classList.remove("opacity-0", "scale-95");
                    userAside.classList.add("opacity-100", "scale-100");
                }, 10);
            } else {
                // Ocultar panel
                ocultarPanelUsuario();
            }
        });

        // Cerrar panel al hacer clic fuera
        document.addEventListener("click", (e) => {
            if (!userAside.contains(e.target) && 
                !userAvatar.contains(e.target) && 
                !userAside.classList.contains("opacity-0")) {
                ocultarPanelUsuario();
            }
        });

        function ocultarPanelUsuario() {
            userAside.classList.add("opacity-0", "scale-95");
            userAside.classList.remove("opacity-100", "scale-100");
            setTimeout(() => {
                userAside.classList.add("hidden");
            }, 300);
        }
    }

    // ========================================
    // MÓDULO 2: Formulario de Información de Contacto
    // ========================================
    const formPerfil = document.getElementById("form-perfil");
    const btnGuardarContacto = document.getElementById("btn-guardar-contacto");

    if (formPerfil) {
        formPerfil.addEventListener("submit", async (e) => {
            e.preventDefault();

            const telefono = document.getElementById("telefono").value.trim();
            const correo = document.getElementById("correo").value.trim();

            // Validaciones
            if (!telefono || !correo) {
                mostrarAlerta("Por favor, completa todos los campos obligatorios.", "error");
                return;
            }

            if (!validarEmail(correo)) {
                mostrarAlerta("Por favor, ingresa un correo electrónico válido.", "error");
                return;
            }

            // Deshabilitar botón durante el envío
            btnGuardarContacto.disabled = true;
            btnGuardarContacto.textContent = "Guardando...";

            try {
                const response = await fetch("/perfil/actualizar", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: JSON.stringify({
                        telefono: telefono,
                        correo: correo
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    mostrarAlerta(result.message || "Información actualizada correctamente.", "success");
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    mostrarAlerta(result.message || "Error al actualizar la información.", "error");
                }
            } catch (error) {
                console.error("Error:", error);
                mostrarAlerta("Error de conexión al actualizar la información.", "error");
            } finally {
                btnGuardarContacto.disabled = false;
                btnGuardarContacto.textContent = "Guardar Cambios";
            }
        });
    }

    // ========================================
    // MÓDULO 3: Formulario de Cambio de Contraseña
    // ========================================
    const formPassword = document.getElementById("form-password");
    const btnCambiarPassword = document.getElementById("btn-cambiar-password");

    if (formPassword) {
        formPassword.addEventListener("submit", async (e) => {
            e.preventDefault();

            const passwordActual = document.getElementById("password-actual").value;
            const passwordNueva = document.getElementById("password-nueva").value;
            const passwordConfirmar = document.getElementById("password-confirmar").value;

            // Validaciones
            if (!passwordActual || !passwordNueva || !passwordConfirmar) {
                mostrarAlerta("Por favor, completa todos los campos de contraseña.", "error");
                return;
            }

            if (passwordNueva.length < 6) {
                mostrarAlerta("La nueva contraseña debe tener al menos 6 caracteres.", "error");
                return;
            }

            if (passwordNueva !== passwordConfirmar) {
                mostrarAlerta("Las contraseñas no coinciden.", "error");
                return;
            }

            if (passwordActual === passwordNueva) {
                mostrarAlerta("La nueva contraseña debe ser diferente a la actual.", "error");
                return;
            }

            // Deshabilitar botón durante el envío
            btnCambiarPassword.disabled = true;
            btnCambiarPassword.textContent = "Cambiando...";

            try {
                const response = await fetch("/perfil/actualizar", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: JSON.stringify({
                        password_actual: passwordActual,
                        password_nueva: passwordNueva
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    mostrarAlerta(result.message || "Contraseña actualizada correctamente.", "success");
                    formPassword.reset();
                } else {
                    mostrarAlerta(result.message || "Error al cambiar la contraseña.", "error");
                }
            } catch (error) {
                console.error("Error:", error);
                mostrarAlerta("Error de conexión al cambiar la contraseña.", "error");
            } finally {
                btnCambiarPassword.disabled = false;
                btnCambiarPassword.textContent = "Cambiar Contraseña";
            }
        });
    }

    // ========================================
    // FUNCIONES AUXILIARES
    // ========================================

    /**
     * Valida formato de correo electrónico
     * @param {string} email - Correo a validar
     * @returns {boolean} - True si es válido
     */
    function validarEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    /**
     * Muestra mensaje de alerta al usuario
     * @param {string} mensaje - Mensaje a mostrar
     * @param {string} tipo - Tipo de alerta: 'success', 'error', 'warning'
     */
    function mostrarAlerta(mensaje, tipo = "info") {
        // Por ahora usa alert() nativo
        // Puedes reemplazar con un sistema de notificaciones más sofisticado
        alert(mensaje);
    }
});
