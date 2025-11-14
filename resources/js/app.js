import './bootstrap';
import axios from 'axios';
import Alpine from 'alpinejs';
import './api-client';

// Configuración global de Axios
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

// Configurar la URL base de la API desde las variables de entorno
const apiUrl = process.env.MIX_API_URL || '/api';
window.axios.defaults.baseURL = apiUrl;

// Inicializar Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Exportar la instancia de Axios para uso en otros módulos
export const http = axios.create({
    baseURL: apiUrl,
    withCredentials: true,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    }
});
