import BaseRepository from '../BaseRepository';

export default class ProductRepository extends BaseRepository {
    constructor() {
        super('products');
    }

    /**
     * Obtener productos por categoría
     * @param {number} categoryId - ID de la categoría
     * @returns {Promise<Array>}
     */
    async getByCategory(categoryId) {
        return this.customRequest({
            method: 'get',
            suffix: `/category/${categoryId}`
        });
    }

    /**
     * Obtener productos con stock bajo
     * @returns {Promise<Array>}
     */
    async getLowStock() {
        return this.customRequest({
            suffix: '/low-stock'
        });
    }
}
