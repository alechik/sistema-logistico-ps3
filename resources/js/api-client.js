/**
 * Módulo cliente de API
 * Expone los repositorios y utilidades para uso en las vistas Blade
 */
import apiRepositories from './api/index';

// Hacer los repositorios disponibles globalmente
window.api = apiRepositories;

// Utilidades para manejo de errores y notificaciones
window.apiUtils = {
    /**
     * Maneja errores de la API y muestra notificaciones
     * @param {Error} error - Error de la API
     * @param {string} defaultMessage - Mensaje por defecto
     */
    handleError(error, defaultMessage = 'Ha ocurrido un error') {
        let message = defaultMessage;
        let errors = {};

        if (error.errors) {
            errors = error.errors;
            message = 'Error de validación';
        } else if (error.message) {
            message = error.message;
        }

        // Mostrar notificación
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        } else {
            console.error(message, errors);
            alert(message);
        }

        return { message, errors };
    },

    /**
     * Muestra un mensaje de éxito
     * @param {string} message - Mensaje a mostrar
     */
    showSuccess(message) {
        if (typeof toastr !== 'undefined') {
            toastr.success(message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: message,
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            console.log(message);
            alert(message);
        }
    },

    /**
     * Muestra un mensaje de información
     * @param {string} message - Mensaje a mostrar
     */
    showInfo(message) {
        if (typeof toastr !== 'undefined') {
            toastr.info(message);
        } else {
            console.info(message);
        }
    },

    /**
     * Muestra un mensaje de advertencia
     * @param {string} message - Mensaje a mostrar
     */
    showWarning(message) {
        if (typeof toastr !== 'undefined') {
            toastr.warning(message);
        } else {
            console.warn(message);
        }
    },

    /**
     * Formatea un número como moneda
     * @param {number} amount - Cantidad a formatear
     * @param {string} currency - Símbolo de moneda (default: 'S/')
     * @returns {string}
     */
    formatCurrency(amount, currency = 'S/') {
        return `${currency} ${parseFloat(amount || 0).toFixed(2)}`;
    },

    /**
     * Formatea una fecha
     * @param {string|Date} date - Fecha a formatear
     * @param {string} format - Formato (default: 'DD/MM/YYYY HH:mm')
     * @returns {string}
     */
    formatDate(date, format = 'DD/MM/YYYY HH:mm') {
        if (!date) return '';
        if (typeof moment !== 'undefined') {
            return moment(date).format(format);
        }
        const d = new Date(date);
        return d.toLocaleDateString('es-PE') + ' ' + d.toLocaleTimeString('es-PE');
    },

    /**
     * Confirma una acción con el usuario
     * @param {string} message - Mensaje de confirmación
     * @param {string} title - Título del diálogo
     * @returns {Promise<boolean>}
     */
    async confirm(message, title = '¿Está seguro?') {
        if (typeof Swal !== 'undefined') {
            const result = await Swal.fire({
                title: title,
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí',
                cancelButtonText: 'No'
            });
            return result.isConfirmed;
        }
        return confirm(message);
    }
};

// Exportar para uso en módulos ES6
export default apiRepositories;
export { apiUtils };

