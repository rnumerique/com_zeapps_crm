app.config(["$routeProvider",
	function ($routeProvider) {
		$routeProvider
		// MODALITIES
			.when("/ng/com_zeapps/modalities", {
				templateUrl: "/com_zeapps_crm/modalities/config",
				controller: "ComZeappsCrmModalityConfigCtrl"
			})

			.when("/ng/com_zeapps/modalities/new", {
				templateUrl: "/com_zeapps_crm/modalities/config_form",
				controller: "ComZeappsCrmModalityConfigFormCtrl"
			})

			.when("/ng/com_zeapps/modalities/edit/:id", {
				templateUrl: "/com_zeapps_crm/modalities/config_form",
				controller: "ComZeappsCrmModalityConfigFormCtrl"
			})

		// TAXES
			.when("/ng/com_zeapps/taxes", {
				templateUrl: "/com_zeapps_crm/taxes/config",
				controller: "ComZeappsCrmTaxeConfigCtrl"
			})

		// QUOTE
			.when("/ng/com_zeapps_crm/quote", {
				templateUrl: "/com_zeapps_crm/quotes/lists",
				controller: "ComZeappsCrmQuoteListsCtrl"
			})

			.when("/ng/com_zeapps_crm/quote/:id", {
				templateUrl: "/com_zeapps_crm/quotes/view",
				controller: "ComZeappsCrmQuoteViewCtrl"
			})

			.when("/ng/com_zeapps/quote", {
				templateUrl: "/com_zeapps_crm/quotes/config",
				controller: "ComZeappsCrmQuoteConfigCtrl"
			})

		// ORDER
			.when("/ng/com_zeapps_crm/order", {
				templateUrl: "/com_zeapps_crm/orders/lists",
				controller: "ComZeappsCrmOrderListsCtrl"
			})

			.when("/ng/com_zeapps_crm/order/:id", {
				templateUrl: "/com_zeapps_crm/orders/view",
				controller: "ComZeappsCrmOrderViewCtrl"
			})

			.when("/ng/com_zeapps/order", {
				templateUrl: "/com_zeapps_crm/orders/config",
				controller: "ComZeappsCrmOrderConfigCtrl"
			})

		// DELIVERY
			.when("/ng/com_zeapps_crm/delivery", {
				templateUrl: "/com_zeapps_crm/deliveries/lists",
				controller: "ComZeappsCrmDeliveryListsCtrl"
			})

			.when("/ng/com_zeapps_crm/delivery/:id", {
				templateUrl: "/com_zeapps_crm/deliveries/view",
				controller: "ComZeappsCrmDeliveryViewCtrl"
			})

			.when("/ng/com_zeapps/delivery", {
				templateUrl: "/com_zeapps_crm/deliveries/config",
				controller: "ComZeappsCrmDeliveryConfigCtrl"
			})

		// INVOICE
			.when("/ng/com_zeapps_crm/invoice", {
				templateUrl: "/com_zeapps_crm/invoices/lists",
				controller: "ComZeappsCrmInvoiceListsCtrl"
			})

            .when("/ng/com_zeapps_crm/invoice/:id", {
                templateUrl: "/com_zeapps_crm/invoices/view",
                controller: "ComZeappsCrmInvoiceViewCtrl"
            })

			.when("/ng/com_zeapps/invoice", {
				templateUrl: "/com_zeapps_crm/invoices/config",
				controller: "ComZeappsCrmInvoiceConfigCtrl"
			})

		// CREDIT BALANCE
			.when("/ng/com_zeapps_crm/credit_balances", {
				templateUrl: "/com_zeapps_crm/credit_balances/lists",
				controller: "ComZeappsCrmCreditBalanceListsCtrl"
			})

            .when("/ng/com_zeapps_crm/credit_balances/:id", {
                templateUrl: "/com_zeapps_crm/credit_balances/view",
                controller: "ComZeappsCrmCreditBalanceViewCtrl"
            })

		// PRODUCT
			.when("/ng/com_zeapps_crm/product/", {
				templateUrl: "/com_zeapps_crm/product/view",
				controller: "ComZeappsCrmProductViewCtrl"
			})

			.when("/ng/com_zeapps_crm/product/category/:id", {
				templateUrl: "/com_zeapps_crm/product/view",
				controller: "ComZeappsCrmProductViewCtrl"
			})

			.when("/ng/com_zeapps_crm/product/:id", {
				templateUrl: "/com_zeapps_crm/product/form",
				controller: "ComZeappsCrmProductFormCtrl"
			})

			.when("/ng/com_zeapps_crm/product/new_product/:category", {
				templateUrl: "/com_zeapps_crm/product/form",
				controller: "ComZeappsCrmProductFormCtrl"
			})

			.when("/ng/com_zeapps_crm/product_compose/:id", {
				templateUrl: "/com_zeapps_crm/product/form/true",
				controller: "ComZeappsCrmProductComposeFormCtrl"
			})

			.when("/ng/com_zeapps_crm/product/new_product_compose/:category", {
				templateUrl: "/com_zeapps_crm/product/form/true",
				controller: "ComZeappsCrmProductComposeFormCtrl"
			})

			.when("/ng/com_zeapps_crm/product/new_category/:id_parent", {
				templateUrl: "/com_zeapps_crm/categories/form",
				controller: "ComZeappsCrmProductFormCategoryCtrl"
			})

			.when("/ng/com_zeapps_crm/product/category/:id/edit", {
				templateUrl: "/com_zeapps_crm/categories/form",
				controller: "ComZeappsCrmProductFormCategoryCtrl"
			})

			.when("/ng/com_zeapps/produits", {
				templateUrl: "/com_zeapps_crm/product/config",
				controller: "ComZeappsCrmProductConfigCtrl"
			})

		// STOCK
			.when("/ng/com_zeapps_crm/stock/", {
				templateUrl: "/com_zeapps_crm/stock/view",
				controller: "ComZeappsCrmStockViewCtrl"
			})

			.when("/ng/com_zeapps_crm/stock/:id", {
				templateUrl: "/com_zeapps_crm/stock/details",
				controller: "ComZeappsCrmStockDetailsCtrl"
			})

			.when("/ng/com_zeapps/warehouses", {
				templateUrl: "/com_zeapps_crm/warehouse/config",
				controller: "ComZeappsCrmWarehouseConfigCtrl"
			})

		// POTENTIAL ORDERS
			.when("/ng/com_zeapps_crm/potential_orders/", {
				templateUrl: "/com_zeapps_crm/potential_orders/view",
				controller: "ComZeappsCrmPotentialOrderViewCtrl"
			})
		;
	}]);

