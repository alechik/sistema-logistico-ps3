import BaseRepository from '../BaseRepository';

export default class UserRepository extends BaseRepository {
    constructor() {
        super('users');
    }

    /**
     * Obtener usuarios activos
     * @returns {Promise<Array>}
     */
    async getActiveUsers() {
        return this.customRequest({
            params: { status: 'active' }
        });
    }

    /**
     * Buscar usuarios por nombre o email
     * @param {string} query 
     * @returns {Promise<Array>}
     */
    async search(query) {
        return this.customRequest({
            params: { search: query }
        });
    }

    /**
     * Actualizar el estado de un usuario
     * @param {number} userId 
     * @param {boolean} isActive 
     * @returns {Promise<Object>}
     */
    async updateStatus(userId, isActive) {
        return this.customRequest({
            method: 'put',
            suffix: `/${userId}/status`,
            data: { is_active: isActive }
        });
    }

    /**
     * Obtener los permisos de un usuario
     * @param {number} userId 
     * @returns {Promise<Array>}
     */
    async getPermissions(userId) {
        return this.customRequest({
            suffix: `/${userId}/permissions`
        });
    }

    /**
     * Actualizar los permisos de un usuario
     * @param {number} userId 
     * @param {Array} permissions 
     * @returns {Promise<Object>}
     */
    async updatePermissions(userId, permissions) {
        return this.customRequest({
            method: 'put',
            suffix: `/${userId}/permissions`,
            data: { permissions }
        });
    }
}
