ALTER TABLE `zeapps_invoices` ADD `id_origin` INT UNSIGNED NOT NULL AFTER `numerotation`;
ALTER TABLE `zeapps_deliveries` ADD `id_origin` INT UNSIGNED NOT NULL AFTER `numerotation`;
ALTER TABLE `zeapps_orders` ADD `id_origin` INT UNSIGNED NOT NULL AFTER `numerotation`;
ALTER TABLE `zeapps_quotes` ADD `id_origin` INT UNSIGNED NOT NULL AFTER `numerotation`;