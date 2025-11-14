import BaseRepository from '../BaseRepository';

export default class SaleItemRepository extends BaseRepository {
    constructor() {
        super('sale-items');
    }

    /**
     * Obtener ítems de una venta
     * @param {number} saleId 
     * @returns {Promise<Array>}
     */
    async getBySale(saleId) {
        return this.customRequest({
            suffix: `/sale/${saleId}`
        });
    }

    /**
     * Agregar ítem a una venta
     * @param {number} saleId 
     * @param {Object} itemData 
     * @returns {Promise<Object>}
     */
    async addToSale(saleId, itemData) {
        return this.customRequest({
            method: 'post',
            suffix: `/${saleId}/items`,
            data: itemData
        });
    }

    /**
     * Actualizar cantidad de un ítem
     * @param {number} itemId 
     * @param {number} quantity 
     * @returns {Promise<Object>}
     */
    async updateQuantity(itemId, quantity) {
        return this.customRequest({
            method: 'put',
            suffix: `/${itemId}/quantity`,
            data: { quantity }
        });
    }

    /**
     * Aplicar descuento a un ítem
     * @param {number} itemId 
     * @param {number} discount 
     * @returns {Promise<Object>}
     */
    async applyDiscount(itemId, discount) {
        return this.customRequest({
            method: 'put',
            suffix: `/${itemId}/discount`,
            data: { discount }
        });
    }
}
