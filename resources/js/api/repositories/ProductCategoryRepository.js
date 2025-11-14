import BaseRepository from '../BaseRepository';

export default class ProductCategoryRepository extends BaseRepository {
    constructor() {
        super('product-categories');
    }

    /**
     * Obtener categorías activas
     * @returns {Promise<Array>}
     */
    async getActive() {
        return this.customRequest({
            suffix: '/active/list'
        });
    }

    /**
     * Obtener categorías con sus productos
     * @param {Object} params 
     * @returns {Promise<Array>}
     */
    async withProducts(params = {}) {
        return this.customRequest({
            suffix: '/with-products',
            params
        });
    }

    /**
     * Obtener el árbol de categorías
     * @returns {Promise<Array>}
     */
    async tree() {
        return this.customRequest({
            suffix: '/tree'
        });
    }

    /**
     * Obtener estadísticas de categorías
     * @returns {Promise<Object>}
     */
    async stats() {
        return this.customRequest({
            suffix: '/stats'
        });
    }
}
