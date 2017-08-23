ALTER TABLE `zeapps_quote_documents` CHANGE `name` `label` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `zeapps_quote_documents` ADD `description` TEXT NOT NULL AFTER `label`;
ALTER TABLE `zeapps_quote_documents` ADD `id_user` INT UNSIGNED NOT NULL AFTER `id_quote`, ADD `name_user` VARCHAR(255) NOT NULL AFTER `id_user`;
ALTER TABLE `zeapps_quote_documents` ADD `date` TIMESTAMP NOT NULL DEFAULT '0000-00-00' AFTER `path`;

ALTER TABLE `zeapps_quote_activities` ADD `id_user` INT UNSIGNED NOT NULL AFTER `id_quote`, ADD `name_user` VARCHAR(255) NOT NULL AFTER `id_user`;
ALTER TABLE `zeapps_quote_activities` ADD `date` TIMESTAMP NOT NULL DEFAULT '0000-00-00' AFTER `description`;

ALTER TABLE `zeapps_quotes` ADD `name_contact` VARCHAR(255) NOT NULL AFTER `id_contact`;
ALTER TABLE `zeapps_quotes` ADD `name_company` VARCHAR(255) NOT NULL AFTER `id_company`;
ALTER TABLE `zeapps_quotes` ADD `name_user` VARCHAR(255) NOT NULL AFTER `id_user`;
ALTER TABLE `zeapps_quotes` ADD `total_tva` FLOAT(9,2) NOT NULL AFTER `total_ht`;
ALTER TABLE `zeapps_quotes` CHANGE `name_user` `name_user_account_manager` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;




ALTER TABLE `zeapps_invoice_documents` CHANGE `name` `label` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `zeapps_invoice_documents` ADD `description` TEXT NOT NULL AFTER `label`;
ALTER TABLE `zeapps_invoice_documents` ADD `id_user` INT UNSIGNED NOT NULL AFTER `id_invoice`, ADD `name_user` VARCHAR(255) NOT NULL AFTER `id_user`;
ALTER TABLE `zeapps_invoice_documents` ADD `date` TIMESTAMP NOT NULL DEFAULT '0000-00-00' AFTER `path`;

ALTER TABLE `zeapps_invoice_activities` ADD `id_user` INT UNSIGNED NOT NULL AFTER `id_invoice`, ADD `name_user` VARCHAR(255) NOT NULL AFTER `id_user`;
ALTER TABLE `zeapps_invoice_activities` ADD `date` TIMESTAMP NOT NULL DEFAULT '0000-00-00' AFTER `description`;

ALTER TABLE `zeapps_invoices` ADD `name_contact` VARCHAR(255) NOT NULL AFTER `id_contact`;
ALTER TABLE `zeapps_invoices` ADD `name_company` VARCHAR(255) NOT NULL AFTER `id_company`;
ALTER TABLE `zeapps_invoices` ADD `name_user` VARCHAR(255) NOT NULL AFTER `id_user`;
ALTER TABLE `zeapps_invoices` ADD `total_tva` FLOAT(9,2) NOT NULL AFTER `total_ht`;
ALTER TABLE `zeapps_invoices` CHANGE `name_user` `name_user_account_manager` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;





ALTER TABLE `zeapps_order_documents` CHANGE `name` `label` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `zeapps_order_documents` ADD `description` TEXT NOT NULL AFTER `label`;
ALTER TABLE `zeapps_order_documents` ADD `id_user` INT UNSIGNED NOT NULL AFTER `id_order`, ADD `name_user` VARCHAR(255) NOT NULL AFTER `id_user`;
ALTER TABLE `zeapps_order_documents` ADD `date` TIMESTAMP NOT NULL DEFAULT '0000-00-00' AFTER `path`;

ALTER TABLE `zeapps_order_activities` ADD `id_user` INT UNSIGNED NOT NULL AFTER `id_order`, ADD `name_user` VARCHAR(255) NOT NULL AFTER `id_user`;
ALTER TABLE `zeapps_order_activities` ADD `date` TIMESTAMP NOT NULL DEFAULT '0000-00-00' AFTER `description`;

ALTER TABLE `zeapps_orders` ADD `name_contact` VARCHAR(255) NOT NULL AFTER `id_contact`;
ALTER TABLE `zeapps_orders` ADD `name_company` VARCHAR(255) NOT NULL AFTER `id_company`;
ALTER TABLE `zeapps_orders` ADD `name_user` VARCHAR(255) NOT NULL AFTER `id_user`;
ALTER TABLE `zeapps_orders` ADD `total_tva` FLOAT(9,2) NOT NULL AFTER `total_ht`;
ALTER TABLE `zeapps_orders` CHANGE `name_user` `name_user_account_manager` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;




ALTER TABLE `zeapps_delivery_documents` CHANGE `name` `label` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `zeapps_delivery_documents` ADD `description` TEXT NOT NULL AFTER `label`;
ALTER TABLE `zeapps_delivery_documents` ADD `id_user` INT UNSIGNED NOT NULL AFTER `id_delivery`, ADD `name_user` VARCHAR(255) NOT NULL AFTER `id_user`;
ALTER TABLE `zeapps_delivery_documents` ADD `date` TIMESTAMP NOT NULL DEFAULT '0000-00-00' AFTER `path`;

ALTER TABLE `zeapps_delivery_activities` ADD `id_user` INT UNSIGNED NOT NULL AFTER `id_delivery`, ADD `name_user` VARCHAR(255) NOT NULL AFTER `id_user`;
ALTER TABLE `zeapps_delivery_activities` ADD `date` TIMESTAMP NOT NULL DEFAULT '0000-00-00' AFTER `description`;

ALTER TABLE `zeapps_deliveries` ADD `name_contact` VARCHAR(255) NOT NULL AFTER `id_contact`;
ALTER TABLE `zeapps_deliveries` ADD `name_company` VARCHAR(255) NOT NULL AFTER `id_company`;
ALTER TABLE `zeapps_deliveries` ADD `name_user` VARCHAR(255) NOT NULL AFTER `id_user`;
ALTER TABLE `zeapps_deliveries` ADD `total_tva` FLOAT(9,2) NOT NULL AFTER `total_ht`;
ALTER TABLE `zeapps_deliveries` CHANGE `name_user` `name_user_account_manager` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;