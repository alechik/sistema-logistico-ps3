import BaseRepository from '../BaseRepository';

export default class InventoryTransactionRepository extends BaseRepository {
    constructor() {
        super('inventory-transactions');
    }

    /**
     * Obtener transacciones por producto
     * @param {number} productId 
     * @param {Object} filters 
     * @returns {Promise<Array>}
     */
    async getByProduct(productId, filters = {}) {
        return this.customRequest({
            suffix: `/product/${productId}`,
            params: filters
        });
    }

    /**
     * Obtener transacciones por almacén
     * @param {number} warehouseId 
     * @param {Object} filters 
     * @returns {Promise<Array>}
     */
    async getByWarehouse(warehouseId, filters = {}) {
        return this.customRequest({
            suffix: `/warehouse/${warehouseId}`,
            params: filters
        });
    }

    /**
     * Obtener resumen de transacciones
     * @param {Object} filters 
     * @returns {Promise<Object>}
     */
    async getSummary(filters = {}) {
        return this.customRequest({
            suffix: '/summary',
            params: filters
        });
    }

    /**
     * Revertir una transacción
     * @param {number} transactionId 
     * @param {string} reason 
     * @returns {Promise<Object>}
     */
    async revert(transactionId, reason = '') {
        return this.customRequest({
            method: 'post',
            suffix: `/${transactionId}/revert`,
            data: { reason }
        });
    }
}
