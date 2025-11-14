import BaseRepository from '../BaseRepository';

export default class WarehouseTypeRepository extends BaseRepository {
    constructor() {
        super('warehouse-types');
    }

    /**
     * Obtener tipos de almacén activos
     * @returns {Promise<Array>}
     */
    async getActive() {
        return this.customRequest({
            suffix: '/active/list'
        });
    }

    /**
     * Obtener estadísticas de tipos de almacén
     * @returns {Promise<Object>}
     */
    async getStats() {
        return this.customRequest({
            suffix: '/stats'
        });
    }

    /**
     * Obtener almacenes por tipo
     * @param {number} typeId 
     * @returns {Promise<Array>}
     */
    async getWarehouses(typeId) {
        return this.customRequest({
            suffix: `/${typeId}/warehouses`
        });
    }
}
