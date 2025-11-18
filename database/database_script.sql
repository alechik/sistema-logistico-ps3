-- Script de base de datos para el Sistema Logístico
-- Generado automáticamente el 14/11/2025

-- Crear la base de datos (descomenta si es necesario)
-- CREATE DATABASE IF NOT EXISTS sistema_logistico;
-- USE sistema_logistico;

-- Deshabilitar restricciones de clave foránea temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- Tabla: user_types
DROP TABLE IF EXISTS `user_types`;
CREATE TABLE `user_types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_types_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos iniciales para user_types
INSERT INTO `user_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', NOW(), NOW()),
(2, 'Manager', NOW(), NOW()),
(3, 'Employee', NOW(), NOW());

-- Tabla: warehouse_types
DROP TABLE IF EXISTS `warehouse_types`;
CREATE TABLE `warehouse_types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `warehouse_types_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: users
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type_id` bigint UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_user_type_id_foreign` (`user_type_id`),
  CONSTRAINT `users_user_type_id_foreign` FOREIGN KEY (`user_type_id`) REFERENCES `user_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos iniciales para users (usuario administrador por defecto)
-- Contraseña: password123 (hasheada con bcrypt)
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `user_type_id`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, NULL, NOW(), NOW());

-- Tabla: warehouses
DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE `warehouses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_type_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouses_warehouse_type_id_foreign` (`warehouse_type_id`),
  CONSTRAINT `warehouses_warehouse_type_id_foreign` FOREIGN KEY (`warehouse_type_id`) REFERENCES `warehouse_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: product_categories
DROP TABLE IF EXISTS `product_categories`;
CREATE TABLE `product_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: products
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cost` decimal(10,2) DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `min_stock` int NOT NULL DEFAULT '0',
  `category_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: inventories
DROP TABLE IF EXISTS `inventories`;
CREATE TABLE `inventories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventories_product_id_warehouse_id_unique` (`product_id`,`warehouse_id`),
  KEY `inventories_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `inventories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventories_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: transaction_types
DROP TABLE IF EXISTS `transaction_types`;
CREATE TABLE `transaction_types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos iniciales para transaction_types
INSERT INTO `transaction_types` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Entrada', 'Entrada de inventario', NOW(), NOW()),
(2, 'Salida', 'Salida de inventario', NOW(), NOW()),
(3, 'Ajuste de inventario', 'Ajuste manual de inventario', NOW(), NOW());

-- Tabla: sales
DROP TABLE IF EXISTS `sales`;
CREATE TABLE `sale_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_items_sale_id_foreign` (`sale_id`),
  KEY `sale_items_product_id_foreign` (`product_id`),
  CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: sale_items
DROP TABLE IF EXISTS `sale_items`;
CREATE TABLE `sale_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_items_sale_id_foreign` (`sale_id`),
  KEY `sale_items_product_id_foreign` (`product_id`),
  CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: inventory_transactions
DROP TABLE IF EXISTS `inventory_transactions`;
CREATE TABLE `inventory_transactions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `transaction_type_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED NOT NULL,
  `reference_id` bigint UNSIGNED DEFAULT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_transactions_product_id_foreign` (`product_id`),
  KEY `inventory_transactions_warehouse_id_foreign` (`warehouse_id`),
  KEY `inventory_transactions_transaction_type_id_foreign` (`transaction_type_id`),
  KEY `inventory_transactions_user_id_foreign` (`user_id`),
  CONSTRAINT `inventory_transactions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `inventory_transactions_transaction_type_id_foreign` FOREIGN KEY (`transaction_type_id`) REFERENCES `transaction_types` (`id`),
  CONSTRAINT `inventory_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `inventory_transactions_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Restablecer restricciones de clave foránea
SET FOREIGN_KEY_CHECKS = 1;

-- Crear índices adicionales para mejorar el rendimiento
CREATE INDEX `inventories_quantity_index` ON `inventories` (`quantity`);
CREATE INDEX `products_price_index` ON `products` (`price`);
CREATE INDEX `products_stock_index` ON `products` (`stock`);
CREATE INDEX `sales_date_index` ON `sales` (`sale_date`);

-- Crear vistas útiles
CREATE OR REPLACE VIEW `vw_low_stock_products` AS
SELECT 
    p.id,
    p.name AS product_name,
    p.sku,
    p.stock,
    p.min_stock,
    pc.name AS category_name,
    w.name AS warehouse_name,
    i.quantity AS warehouse_quantity
FROM 
    products p
    LEFT JOIN product_categories pc ON p.category_id = pc.id
    LEFT JOIN inventories i ON p.id = i.product_id
    LEFT JOIN warehouses w ON i.warehouse_id = w.id
WHERE 
    p.stock <= p.min_stock
    AND p.deleted_at IS NULL;

-- Crear procedimientos almacenados útiles
DELIMITER //
CREATE PROCEDURE sp_get_product_history(IN p_product_id BIGINT)
BEGIN
    SELECT 
        it.*,
        tt.name AS transaction_type,
        u.name AS user_name,
        w.name AS warehouse_name
    FROM 
        inventory_transactions it
        JOIN transaction_types tt ON it.transaction_type_id = tt.id
        JOIN users u ON it.user_id = u.id
        JOIN warehouses w ON it.warehouse_id = w.id
    WHERE 
        it.product_id = p_product_id
    ORDER BY 
        it.created_at DESC;
END //

CREATE PROCEDURE sp_get_daily_sales(IN p_date DATE)
BEGIN
    SELECT 
        DATE(s.sale_date) AS sale_day,
        COUNT(DISTINCT s.id) AS total_sales,
        SUM(s.total_amount) AS total_revenue,
        SUM(si.quantity) AS total_items_sold
    FROM 
        sales s
        JOIN sale_items si ON s.id = si.sale_id
    WHERE 
        DATE(s.sale_date) = p_date
    GROUP BY 
        DATE(s.sale_date);
END //

DELIMITER ;

-- Insertar datos de ejemplo (opcional)
-- Descomenta y ejecuta esta sección si deseas datos de prueba
/*
-- Insertar tipos de almacén
INSERT INTO `warehouse_types` (`name`, `created_at`, `updated_at`) VALUES
('Principal', NOW(), NOW()),
('Secundario', NOW(), NOW()),
('Bodega Fría', NOW(), NOW());

-- Insertar almacenes
INSERT INTO `warehouses` (`name`, `location`, `warehouse_type_id`, `created_at`, `updated_at`) VALUES
('Almacén Central', 'Edificio A, Piso 1', 1, NOW(), NOW()),
('Almacén Secundario', 'Edificio B, Sótano', 2, NOW(), NOW());

-- Insertar categorías de productos
INSERT INTO `product_categories` (`name`, `description`, `created_at`, `updated_at`) VALUES
('Electrónicos', 'Productos electrónicos y dispositivos', NOW(), NOW()),
('Ropa', 'Prendas de vestir', NOW(), NOW()),
('Alimentos', 'Productos alimenticios', NOW(), NOW());

-- Insertar productos de ejemplo
INSERT INTO `products` (`name`, `description`, `sku`, `price`, `cost`, `stock`, `min_stock`, `category_id`, `created_at`, `updated_at`) VALUES
('Laptop HP ProBook', 'Laptop de 15 pulgadas, 8GB RAM, 512GB SSD', 'LP-HP-001', 1200.00, 950.00, 15, 5, 1, NOW(), NOW()),
('Smartphone Samsung S21', 'Teléfono inteligente de gama alta', 'PH-SS-S21', 899.99, 700.00, 30, 10, 1, NOW(), NOW()),
('Camiseta Algodón', 'Camiseta 100% algodón talla M', 'CT-ALG-M', 19.99, 8.50, 100, 20, 2, NOW(), NOW());

-- Insertar inventario inicial
INSERT INTO `inventories` (`product_id`, `warehouse_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 10, NOW(), NOW()),
(2, 1, 20, NOW(), NOW()),
(3, 2, 80, NOW(), NOW());
*/

-- Mensaje de finalización
SELECT 'Base de datos del Sistema Logístico creada exitosamente' AS message;
