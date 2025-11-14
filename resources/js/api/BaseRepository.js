import apiClient from './http/axiosConfig';

/**
 * Clase base para todos los repositorios
 * Proporciona métodos CRUD estándar para interactuar con la API
 */
export default class BaseRepository {
    /**
     * @param {string} resource - Nombre del recurso (ej: 'users', 'products')
     * @param {Object} options - Opciones adicionales
     * @param {string} [options.basePath=''] - Ruta base personalizada
     */
    constructor(resource, { basePath = '' } = {}) {
        this.resource = resource;
        this.basePath = basePath;
    }

    /**
     * Construye la URL completa para el recurso
     * @param {string|number} [id] - ID del recurso (opcional)
     * @param {string} [suffix] - Sufijo adicional (opcional)
     * @returns {string}
     */
    buildUrl(id, suffix = '') {
        const parts = [this.basePath, this.resource];
        if (id !== undefined) parts.push(id);
        if (suffix) parts.push(suffix);
        const path = parts.filter(Boolean).join('/');
        return path.startsWith('/') ? path : '/' + path;
    }

    /**
     * Obtener todos los registros
     * @param {Object} params - Parámetros de consulta
     * @param {Object} config - Configuración adicional de axios
     * @returns {Promise<{data: Array, meta: Object}>}
     */
    async all(params = {}, config = {}) {
        return apiClient.get(this.buildUrl(), { ...config, params });
    }

    /**
     * Obtener un registro por ID
     * @param {string|number} id - ID del recurso
     * @param {Object} params - Parámetros de consulta
     * @param {Object} config - Configuración adicional de axios
     * @returns {Promise<Object>}
     */
    async find(id, params = {}, config = {}) {
        return apiClient.get(this.buildUrl(id), { ...config, params });
    }

    /**
     * Crear un nuevo registro
     * @param {Object} data - Datos del recurso a crear
     * @param {Object} config - Configuración adicional de axios
     * @returns {Promise<Object>}
     */
    async create(data, config = {}) {
        return apiClient.post(this.buildUrl(), data, config);
    }

    /**
     * Actualizar un registro existente
     * @param {string|number} id - ID del recurso a actualizar
     * @param {Object} data - Datos a actualizar
     * @param {Object} config - Configuración adicional de axios
     * @returns {Promise<Object>}
     */
    async update(id, data, config = {}) {
        return apiClient.put(this.buildUrl(id), data, config);
    }

    /**
     * Eliminar un registro
     * @param {string|number} id - ID del recurso a eliminar
     * @param {Object} config - Configuración adicional de axios
     * @returns {Promise<void>}
     */
    async delete(id, config = {}) {
        return apiClient.delete(this.buildUrl(id), config);
    }

    /**
     * Realizar una petición personalizada
     * @param {Object} options - Opciones de la petición
     * @param {string} [options.method='get'] - Método HTTP (get, post, put, delete, etc.)
     * @param {string} [options.suffix] - Sufijo para la URL
     * @param {string} [options.url] - URL completa (sobrescribe la generada)
     * @param {*} [options.data] - Datos a enviar en el cuerpo
     * @param {Object} [options.params] - Parámetros de consulta
     * @param {Object} [options.headers] - Cabeceras adicionales
     * @returns {Promise<*>}
     */
    async customRequest({
        method = 'get',
        suffix = '',
        url,
        data,
        params = {},
        headers = {},
        ...rest
    } = {}) {
        const requestUrl = url || this.buildUrl(undefined, suffix);
        return apiClient({
            method,
            url: requestUrl,
            data,
            params,
            headers: {
                'Content-Type': 'application/json',
                ...headers,
            },
            ...rest,
        });
    }
}
