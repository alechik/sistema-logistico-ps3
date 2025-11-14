// Importar todos los repositorios
import AuthRepository from './repositories/AuthRepository';
import UserRepository from './repositories/UserRepository';
import UserTypeRepository from './repositories/UserTypeRepository';
import ProductRepository from './repositories/ProductRepository';
import ProductCategoryRepository from './repositories/ProductCategoryRepository';
import SaleRepository from './repositories/SaleRepository';
import SaleItemRepository from './repositories/SaleItemRepository';
import WarehouseRepository from './repositories/WarehouseRepository';
import WarehouseTypeRepository from './repositories/WarehouseTypeRepository';
import InventoryRepository from './repositories/InventoryRepository';
import InventoryTransactionRepository from './repositories/InventoryTransactionRepository';
import TransactionTypeRepository from './repositories/TransactionTypeRepository';

// Crear instancias de los repositorios
export const authRepository = new AuthRepository();
export const userRepository = new UserRepository();
export const userTypeRepository = new UserTypeRepository();
export const productRepository = new ProductRepository();
export const productCategoryRepository = new ProductCategoryRepository();
export const saleRepository = new SaleRepository();
export const saleItemRepository = new SaleItemRepository();
export const warehouseRepository = new WarehouseRepository();
export const warehouseTypeRepository = new WarehouseTypeRepository();
export const inventoryRepository = new InventoryRepository();
export const inventoryTransactionRepository = new InventoryTransactionRepository();
export const transactionTypeRepository = new TransactionTypeRepository();

// Exportar todos los repositorios como un objeto
export default {
    auth: authRepository,
    users: userRepository,
    userTypes: userTypeRepository,
    products: productRepository,
    productCategories: productCategoryRepository,
    sales: saleRepository,
    saleItems: saleItemRepository,
    warehouses: warehouseRepository,
    warehouseTypes: warehouseTypeRepository,
    inventories: inventoryRepository,
    inventoryTransactions: inventoryTransactionRepository,
    transactionTypes: transactionTypeRepository
};
