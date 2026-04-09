-- Tambah kolom shipping ke tabel orders
ALTER TABLE `orders` 
ADD COLUMN `shipping_expedition` VARCHAR(50) DEFAULT NULL AFTER `payment_proof`,
ADD COLUMN `shipping_type` VARCHAR(50) DEFAULT NULL AFTER `shipping_expedition`,
ADD COLUMN `shipping_cost` DECIMAL(10,2) DEFAULT 0 AFTER `shipping_type`;
