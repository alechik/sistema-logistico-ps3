import BaseRepository from '../BaseRepository';

export default class AuthRepository extends BaseRepository {
    constructor() {
        super('auth');
    }

    /**
     * Iniciar sesión
     * @param {string} email 
     * @param {string} password 
     * @returns {Promise<Object>}
     */
    async login(email, password) {
        return this.customRequest({
            method: 'post',
            url: '/login',
            data: { email, password }
        });
    }

    /**
     * Cerrar sesión
     * @returns {Promise<void>}
     */
    async logout() {
        return this.customRequest({
            method: 'post',
            url: '/logout'
        });
    }

    /**
     * Obtener usuario autenticado
     * @returns {Promise<Object>}
     */
    async me() {
        return this.customRequest({
            url: '/user'
        });
    }

    /**
     * Restablecer contraseña
     * @param {Object} credentials 
     * @returns {Promise<void>}
     */
    async forgotPassword(credentials) {
        return this.customRequest({
            method: 'post',
            url: '/forgot-password',
            data: credentials
        });
    }

    /**
     * Restablecer contraseña con token
     * @param {Object} credentials 
     * @returns {Promise<void>}
     */
    async resetPassword(credentials) {
        return this.customRequest({
            method: 'post',
            url: '/reset-password',
            data: credentials
        });
    }

    /**
     * Actualizar perfil del usuario
     * @param {Object} userData 
     * @returns {Promise<Object>}
     */
    async updateProfile(userData) {
        return this.customRequest({
            method: 'put',
            url: '/user/profile-information',
            data: userData
        });
    }

    /**
     * Actualizar contraseña
     * @param {Object} passwordData 
     * @returns {Promise<void>}
     */
    async updatePassword(passwordData) {
        return this.customRequest({
            method: 'put',
            url: '/user/password',
            data: passwordData
        });
    }
}
