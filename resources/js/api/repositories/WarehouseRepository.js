import BaseRepository from '../BaseRepository';

export default class WarehouseRepository extends BaseRepository {
    constructor() {
        super('warehouses');
    }

    /**
     * Obtener tipos de almacén activos
     * @returns {Promise<Array>}
     */
    async getActiveTypes() {
        return this.customRequest({
            suffix: '/types/active'
        });
    }

    /**
     * Obtener inventario de un almacén
     * @param {number} warehouseId - ID del almacén
     * @returns {Promise<Array>}
     */
    async getInventory(warehouseId) {
        return this.customRequest({
            suffix: `/${warehouseId}/inventory`
        });
    }

    /**
     * Transferir inventario entre almacenes
     * @param {number} fromWarehouseId - ID del almacén de origen
     * @param {number} toWarehouseId - ID del almacén de destino
     * @param {number} productId - ID del producto
     * @param {number} quantity - Cantidad a transferir
     * @param {string} notes - Notas adicionales
     * @returns {Promise<Object>}
     */
    async transferInventory(fromWarehouseId, toWarehouseId, productId, quantity, notes = '') {
        return this.customRequest({
            method: 'post',
            suffix: `/${fromWarehouseId}/transfer`,
            data: {
                to_warehouse_id: toWarehouseId,
                product_id: productId,
                quantity: quantity,
                notes: notes
            }
        });
    }

    /**
     * Obtener productos con stock bajo en un almacén
     * @param {number} warehouseId - ID del almacén
     * @returns {Promise<Array>}
     */
    async getLowStockItems(warehouseId) {
        return this.customRequest({
            suffix: `/${warehouseId}/low-stock`
        });
    }

    /**
     * Actualizar el stock de un producto en un almacén
     * @param {number} warehouseId - ID del almacén
     * @param {number} productId - ID del producto
     * @param {number} quantity - Nueva cantidad
     * @param {string} reason - Razón del ajuste
     * @returns {Promise<Object>}
     */
    async updateStock(warehouseId, productId, quantity, reason = 'Ajuste de inventario') {
        return this.customRequest({
            method: 'post',
            suffix: `/${warehouseId}/update-stock`,
            data: {
                product_id: productId,
                quantity: quantity,
                reason: reason
            }
        });
    }

    /**
     * Obtener el historial de movimientos de un almacén
     * @param {number} warehouseId - ID del almacén
     * @param {Object} filters - Filtros opcionales (fechas, tipo de movimiento, etc.)
     * @returns {Promise<Array>}
     */
    async getMovementHistory(warehouseId, filters = {}) {
        return this.customRequest({
            suffix: `/${warehouseId}/movements`,
            params: filters
        });
    }

    /**
     * Obtener estadísticas del almacén
     * @param {number} warehouseId - ID del almacén
     * @returns {Promise<Object>}
     */
    async getStats(warehouseId) {
        return this.customRequest({
            suffix: `/${warehouseId}/stats`
        });
    }
}
