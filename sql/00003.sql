CREATE TABLE `zeapps_accounting_entries` (
  `id` int(10) unsigned NOT NULL,
  `id_invoice` int(10) unsigned NOT NULL,
  `accounting_number` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `debit` float(9,2) NOT NULL,
  `credit` float(9,2) NOT NULL,
  `code` varchar(4) NOT NULL,
  `date_writing` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_limit` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `zeapps_accounting_entries`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `zeapps_accounting_entries`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;