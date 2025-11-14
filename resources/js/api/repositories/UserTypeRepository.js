import BaseRepository from '../BaseRepository';

export default class UserTypeRepository extends BaseRepository {
    constructor() {
        super('user-types');
    }

    /**
     * Obtener tipos de usuario activos
     * @returns {Promise<Array>}
     */
    async getActive() {
        return this.customRequest({
            suffix: '/active/list'
        });
    }

    /**
     * Obtener permisos por tipo de usuario
     * @param {number} userTypeId 
     * @returns {Promise<Array>}
     */
    async getPermissions(userTypeId) {
        return this.customRequest({
            suffix: `/${userTypeId}/permissions`
        });
    }

    /**
     * Actualizar permisos de un tipo de usuario
     * @param {number} userTypeId 
     * @param {Array} permissions 
     * @returns {Promise<Object>}
     */
    async updatePermissions(userTypeId, permissions) {
        return this.customRequest({
            method: 'put',
            suffix: `/${userTypeId}/permissions`,
            data: { permissions }
        });
    }
}
