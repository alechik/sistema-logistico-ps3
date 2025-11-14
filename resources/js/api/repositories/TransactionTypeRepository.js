import BaseRepository from '../BaseRepository';

export default class TransactionTypeRepository extends BaseRepository {
    constructor() {
        super('transaction-types');
    }

    /**
     * Obtener tipos de transacción activos
     * @returns {Promise<Array>}
     */
    async getActive() {
        return this.customRequest({
            suffix: '/active/list'
        });
    }

    /**
     * Obtener tipos de transacción por categoría
     * @param {string} category 
     * @returns {Promise<Array>}
     */
    async getByCategory(category) {
        return this.customRequest({
            suffix: '/by-category',
            params: { category }
        });
    }

    /**
     * Obtener estadísticas de uso de tipos de transacción
     * @returns {Promise<Object>}
     */
    async getUsageStats() {
        return this.customRequest({
            suffix: '/usage-stats'
        });
    }
}
