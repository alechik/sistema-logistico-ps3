import axios from 'axios';

// Create axios instance with base URL from environment variables
const apiClient = axios.create({
    baseURL: process.env.MIX_APP_URL + '/api/v1',
    withCredentials: true,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

// Request interceptor
apiClient.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        // Ensure we always get JSON responses
        config.headers.Accept = 'application/json';
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor
apiClient.interceptors.response.use(
    (response) => {
        // Return only the data part of the response
        return response.data;
    },
    (error) => {
        const response = error.response;
        
        if (response) {
            const { status, data } = response;
            const errorMessage = data?.message || 'Error en la petición';
            const errors = data?.errors || {};
            
            switch (status) {
                case 401:
                    // Clear auth data
                    localStorage.removeItem('auth_token');
                    sessionStorage.removeItem('auth_token');
                    // Redirect to login if not already there
                    if (!window.location.pathname.includes('login')) {
                        window.location.href = '/login';
                    }
                    break;
                    
                case 403:
                    console.error('Acceso denegado: No tiene permisos para realizar esta acción');
                    break;
                    
                case 404:
                    console.error('Recurso no encontrado');
                    break;
                    
                case 419:
                    // CSRF token mismatch
                    console.error('La sesión ha expirado. Por favor, recargue la página.');
                    window.location.reload();
                    break;
                    
                case 422:
                    // Validation errors
                    return Promise.reject({
                        message: 'Error de validación',
                        errors: errors
                    });
                    
                case 429:
                    // Too many requests
                    console.error('Demasiadas peticiones. Por favor, espere un momento.');
                    break;
                    
                case 500:
                    console.error('Error interno del servidor');
                    break;
                    
                default:
                    console.error(`Error ${status}: ${errorMessage}`);
            }
            
            return Promise.reject({
                status,
                message: errorMessage,
                errors: errors,
                data: data
            });
        } else if (error.request) {
            // The request was made but no response was received
            console.error('No se pudo conectar con el servidor. Verifique su conexión a internet.');
            return Promise.reject({
                message: 'Error de conexión',
                details: 'No se pudo establecer conexión con el servidor'
            });
        } else {
            // Something happened in setting up the request
            console.error('Error al configurar la petición:', error.message);
            return Promise.reject({
                message: 'Error en la configuración de la petición',
                details: error.message
            });
        }
    }
);

export default apiClient;
