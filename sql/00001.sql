CREATE TABLE `com_zeapps_crm_product_categories` (
  `id` int(10) unsigned NOT NULL,
  `id_parent` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nb_products` int(10) unsigned NOT NULL DEFAULT '0',
  `nb_products_r` int(10) unsigned NOT NULL DEFAULT '0',
  `sort` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `com_zeapps_crm_product_categories`
--
ALTER TABLE `com_zeapps_crm_product_categories`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `com_zeapps_crm_product_categories`
--
ALTER TABLE `com_zeapps_crm_product_categories`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

CREATE TABLE `com_zeapps_crm_product_products` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `desc_short` varchar(140) NOT NULL,
  `category` int(11) NOT NULL,
  `price` decimal(7,2) NOT NULL DEFAULT '0.00',
  `account` int(10) unsigned NOT NULL,
  `taxe` int(10) unsigned NOT NULL,
  `desc_long` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `com_zeapps_crm_product_products`
--
ALTER TABLE `com_zeapps_crm_product_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `com_zeapps_crm_product_products`
--
ALTER TABLE `com_zeapps_crm_product_products`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

CREATE TABLE `zeapps_quotes` (
  `id` int(10) unsigned NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `numerotation` varchar(255) NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `id_company` int(10) unsigned NOT NULL,
  `id_contact` int(10) unsigned NOT NULL,
  `billing_address_1` varchar(100) NOT NULL,
  `billing_address_2` varchar(100) NOT NULL,
  `billing_address_3` varchar(100) NOT NULL,
  `billing_city` varchar(100) NOT NULL,
  `billing_zipcode` varchar(50) NOT NULL,
  `billing_state` varchar(100) NOT NULL,
  `billing_country_id` int(10) NOT NULL,
  `billing_country_name` varchar(100) NOT NULL,
  `delivery_address_1` varchar(100) NOT NULL,
  `delivery_address_2` varchar(100) NOT NULL,
  `delivery_address_3` varchar(100) NOT NULL,
  `delivery_city` varchar(100) NOT NULL,
  `delivery_zipcode` varchar(50) NOT NULL,
  `delivery_state` varchar(100) NOT NULL,
  `delivery_country_id` int(10) NOT NULL,
  `delivery_country_namev` varchar(100) NOT NULL,
  `accounting_number` varchar(255) NOT NULL,
  `global_discount` float(4,2) NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_limit` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modalities` varchar(255) NOT NULL,
  `reference_client` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `zeapps_quotes`
--
ALTER TABLE `zeapps_quotes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `zeapps_quotes`
--
ALTER TABLE `zeapps_quotes`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

CREATE TABLE `zeapps_quote_activities` (
  `id` int(10) unsigned NOT NULL,
  `id_quote` int(10) unsigned NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `deadline` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reminder` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `validation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `zeapps_quote_activities`
--
ALTER TABLE `zeapps_quote_activities`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `zeapps_quote_activities`
--
ALTER TABLE `zeapps_quote_activities`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

CREATE TABLE `zeapps_quote_companies` (
  `id` int(10) unsigned NOT NULL,
  `id_quote` int(10) unsigned NOT NULL,
  `id_user_account_manager` int(10) unsigned NOT NULL,
  `name_user_account_manager` varchar(100) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `id_parent_company` int(10) unsigned NOT NULL,
  `name_parent_company` varchar(255) NOT NULL,
  `id_type_account` int(10) unsigned NOT NULL,
  `name_type_account` int(100) NOT NULL,
  `id_activity_area` int(10) unsigned NOT NULL,
  `name_activity_area` varchar(100) NOT NULL,
  `turnover` bigint(20) NOT NULL,
  `billing_address_1` varchar(100) NOT NULL,
  `billing_address_2` varchar(100) NOT NULL,
  `billing_address_3` varchar(100) NOT NULL,
  `billing_city` varchar(100) NOT NULL,
  `billing_zipcode` varchar(50) NOT NULL,
  `billing_state` varchar(100) NOT NULL,
  `billing_country_id` int(10) unsigned NOT NULL,
  `billing_country_name` varchar(100) NOT NULL,
  `delivery_address_1` varchar(100) NOT NULL,
  `delivery_address_2` varchar(100) NOT NULL,
  `delivery_address_3` varchar(100) NOT NULL,
  `delivery_city` varchar(100) NOT NULL,
  `delivery_zipcode` varchar(50) NOT NULL,
  `delivery_state` varchar(100) NOT NULL,
  `delivery_country_id` int(10) unsigned NOT NULL,
  `delivery_country_name` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `phone` varchar(25) NOT NULL,
  `fax` varchar(25) NOT NULL,
  `website_url` varchar(255) NOT NULL,
  `code_naf` varchar(15) NOT NULL,
  `code_naf_libelle` varchar(255) NOT NULL,
  `company_number` varchar(30) NOT NULL,
  `accounting_number` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `zeapps_quote_companies`
--
ALTER TABLE `zeapps_quote_companies`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `zeapps_quote_companies`
--
ALTER TABLE `zeapps_quote_companies`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

CREATE TABLE `zeapps_quote_contacts` (
  `id` int(10) unsigned NOT NULL,
  `id_quote` int(10) unsigned NOT NULL,
  `id_user_account_manager` int(10) unsigned NOT NULL,
  `name_user_account_manager` varchar(100) NOT NULL,
  `id_company` int(10) unsigned NOT NULL,
  `name_company` varchar(255) NOT NULL DEFAULT '',
  `title_name` varchar(30) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `other_phone` varchar(25) NOT NULL,
  `mobile` varchar(25) NOT NULL,
  `fax` varchar(25) NOT NULL,
  `assistant` varchar(70) NOT NULL,
  `assistant_phone` varchar(25) NOT NULL,
  `department` varchar(100) NOT NULL,
  `job` varchar(100) NOT NULL,
  `email_opt_out` enum('Y','N') NOT NULL DEFAULT 'N',
  `skype_id` varchar(100) NOT NULL,
  `twitter` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL DEFAULT '0000-00-00',
  `address_1` varchar(100) NOT NULL,
  `address_2` varchar(100) NOT NULL,
  `address_3` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `zipcode` varchar(50) NOT NULL,
  `state` varchar(100) NOT NULL,
  `country_id` int(10) unsigned NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `website_url` varchar(255) NOT NULL,
  `accounting_number` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `zeapps_quote_contacts`
--
ALTER TABLE `zeapps_quote_contacts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `zeapps_quote_contacts`
--
ALTER TABLE `zeapps_quote_contacts`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

CREATE TABLE `zeapps_quote_documents` (
  `id` int(10) unsigned NOT NULL,
  `id_quote` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `zeapps_quote_documents`
--
ALTER TABLE `zeapps_quote_documents`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `zeapps_quote_documents`
--
ALTER TABLE `zeapps_quote_documents`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

CREATE TABLE `zeapps_quote_lines` (
  `id` int(10) unsigned NOT NULL,
  `id_quote` int(10) unsigned NOT NULL,
  `num` varchar(255) NOT NULL,
  `designation_title` varchar(255) NOT NULL,
  `designation_desc` text NOT NULL,
  `qty` float(11,3) NOT NULL,
  `discount` float(4,2) NOT NULL,
  `price_unit` float(9,2) NOT NULL,
  `taxe` float(4,2) NOT NULL,
  `sort` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `zeapps_quote_lines`
--
ALTER TABLE `zeapps_quote_lines`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `zeapps_quote_lines`
--
ALTER TABLE `zeapps_quote_lines`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;