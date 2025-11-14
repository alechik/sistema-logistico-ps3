import BaseRepository from '../BaseRepository';

export default class InventoryRepository extends BaseRepository {
    constructor() {
        super('inventories');
    }

    /**
     * Obtener inventario por producto
     * @param {number} productId 
     * @returns {Promise<Array>}
     */
    async getByProduct(productId) {
        return this.customRequest({
            suffix: `/product/${productId}`
        });
    }

    /**
     * Obtener inventario por almacén
     * @param {number} warehouseId 
     * @returns {Promise<Array>}
     */
    async getByWarehouse(warehouseId) {
        return this.customRequest({
            suffix: `/warehouse/${warehouseId}`
        });
    }

    /**
     * Obtener productos con stock bajo
     * @param {number} threshold 
     * @returns {Promise<Array>}
     */
    async getLowStock(threshold = 10) {
        return this.customRequest({
            suffix: '/low-stock',
            params: { threshold }
        });
    }

    /**
     * Actualizar stock
     * @param {number} inventoryId 
     * @param {Object} data 
     * @returns {Promise<Object>}
     */
    async updateStock(inventoryId, data) {
        return this.customRequest({
            method: 'put',
            suffix: `/${inventoryId}/update-stock`,
            data
        });
    }

    /**
     * Obtener historial de movimientos
     * @param {Object} filters 
     * @returns {Promise<Array>}
     */
    async getMovementHistory(filters = {}) {
        return this.customRequest({
            suffix: '/movement-history',
            params: filters
        });
    }
}
