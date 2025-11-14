import BaseRepository from '../BaseRepository';

export default class SaleRepository extends BaseRepository {
    constructor() {
        super('sales');
    }

    /**
     * Cancelar una venta
     * @param {number} saleId - ID de la venta
     * @returns {Promise<Object>}
     */
    async cancel(saleId) {
        return this.customRequest({
            method: 'post',
            suffix: `/${saleId}/cancel`
        });
    }

    /**
     * Obtener ventas por rango de fechas
     * @param {string} startDate - Fecha de inicio (YYYY-MM-DD)
     * @param {string} endDate - Fecha de fin (YYYY-MM-DD)
     * @returns {Promise<Array>}
     */
    async getByDateRange(startDate, endDate) {
        return this.customRequest({
            suffix: '/date-range',
            params: { start_date: startDate, end_date: endDate }
        });
    }

    /**
     * Obtener resumen de ventas
     * @returns {Promise<Object>}
     */
    async getSummary() {
        return this.customRequest({
            suffix: '/summary'
        });
    }

    /**
     * Agregar ítem a una venta
     * @param {number} saleId - ID de la venta
     * @param {Object} itemData - Datos del ítem
     * @returns {Promise<Object>}
     */
    async addItem(saleId, itemData) {
        return this.customRequest({
            method: 'post',
            suffix: `/${saleId}/items`,
            data: itemData
        });
    }

    /**
     * Actualizar ítem de una venta
     * @param {number} saleId - ID de la venta
     * @param {number} itemId - ID del ítem
     * @param {Object} itemData - Datos actualizados del ítem
     * @returns {Promise<Object>}
     */
    async updateItem(saleId, itemId, itemData) {
        return this.customRequest({
            method: 'put',
            suffix: `/${saleId}/items/${itemId}`,
            data: itemData
        });
    }

    /**
     * Eliminar ítem de una venta
     * @param {number} saleId - ID de la venta
     * @param {number} itemId - ID del ítem
     * @returns {Promise<void>}
     */
    async removeItem(saleId, itemId) {
        return this.customRequest({
            method: 'delete',
            suffix: `/${saleId}/items/${itemId}`
        });
    }
}
